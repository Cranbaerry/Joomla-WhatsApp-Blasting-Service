<?php
namespace Joomla\Plugin\Ajax\WhatsAppWebhook\Extension;
// // Enable full error reporting.
// error_reporting(E_ALL);

// // Disable display of errors to the user.
// ini_set('display_errors', '0');

// // Set the error log file to 'error.log' in the current working directory.
// $currentDir = getcwd();
// ini_set('error_log', $currentDir . '/error.log');
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\Result\ResultAwareInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

/**
 * WhatsAppWebhook Plugin Class
 *
 * Handles AJAX calls, webhook verification, logging, proxying of payloads,
 * and updates template status on message_template_status_update.
 */
class WhatsAppWebhook extends CMSPlugin implements SubscriberInterface
{
    const STATUS_QUEUED = 'QUEUED';
    const STATUS_PROCESSED = 'PROCESSED';
    const STATUS_ERROR = 'ERROR';
    const STATUS_IGNORED = 'IGNORED';

    /**
     * Store the original raw payload.
     *
     * @var string
     */
    protected $originalPayload = '';

    /**
     * Returns the events this plugin is subscribed to.
     *
     * @return array<string, mixed> Associative array of events.
     */
    public static function getSubscribedEvents(): array
    {
        return ['onAjaxWhatsappwebhook' => 'handleAjax'];
    }

    /**
     * Dynamically handle the AJAX request based on the provided method and parameters.
     *
     * @param Event $event The event triggering the AJAX call.
     *
     * @return void
     *
     * @throws \Exception If the specified method does not exist or required parameters are missing.
     */
    public function handleAjax(Event $event)
    {
        $app = $this->getApplication();
        $input = $app->input;
        $method = $input->get('method', '', 'cmd');

        $params = $input->getArray();
        unset($params['option'], $params['plugin'], $params['method'], $params['format']);

        if (!method_exists($this, $method)) {
            throw new \Exception('Invalid method specified');
        }

        $reflection = new \ReflectionMethod($this, $method);
        if (!$reflection->isPublic()) {
            throw new \Exception('Invalid method specified');
        }

        $orderedParams = [];
        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            $orderedParams[] = $params[$name] ?? ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : throw new \Exception("Missing required parameter: $name"));
        }

        $result = $reflection->invokeArgs($this, $orderedParams);

        if ($event instanceof ResultAwareInterface) {
            $event->addResult($result);
        } else {
            $results = $event->getArgument('result') ?? [];
            $results[] = $result;
            $event->setArgument('result', $results);
        }
    }

    /**
     * Retrieve configuration for a given user ID.
     *
     * @param string $uid The user ID.
     *
     * @return array|null Associative array with keys: dreamztrack_key, dreamztrack_endpoint, forward_url;
     *                    null if config not found or error occurs.
     */
    protected function getConfig(string $uid): ?array
    {
        if (empty($uid)) {
            return null;
        }
        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName(['dreamztrack_key', 'dreamztrack_endpoint', 'dreamztrack_branch', 'forward_url']))
                ->from($db->quoteName('#__dt_whatsapp_tenants_configs'))
                ->where($db->quoteName('user_id') . ' = ' . $db->quote($uid));
            return $db->setQuery($query)->loadAssoc();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Update the contact's keywords_tags field only if the message text contains any active keyword.
     * Uses the existing $contact data to avoid an extra query for keywords_tags.
     * Also updates the contact's last_updated field.
     *
     * Additionally, for each matched keyword, if the keyword record has a scheduled_message_json,
     * parse that JSON and insert scheduled blasting records into the dt_whatsapp_tenants_scheduled_messages table
     * (computing the send time as now plus the interval, adjusted by unit).
     *
     * @param array  $contact     The contact array containing id, name, and keywords_tags.
     * @param string $uid         The user identifier (used in created_by).
     * @param string $messageText The incoming message text.
     *
     * @return array Object containing matched keyword names and logs.
     */
    protected function updateContactKeywordsIfMatched(array $contact, string $uid, string $messageText): array
    {
        $db = Factory::getDbo();
        $logs = [];
        $logs[] = 'Started processing keywords for config ID: ' . ($contact['id'] ?? 'unknown');

        // Retrieve active keywords (including scheduled_message_json) for this user.
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'name', 'scheduled_message_json']))
            ->from($db->quoteName('#__dt_whatsapp_tenants_keywords'))
            ->where($db->quoteName('created_by') . ' = ' . $db->quote($uid))
            ->where($db->quoteName('state') . ' = 1');
        $db->setQuery($query);
        $activeKeywords = $db->loadAssocList();
        $logs[] = 'Found ' . count($activeKeywords) . ' active keywords';

        $matchedKeywordIds = [];
        $matchedKeywordNames = [];

        if ($activeKeywords) {
            foreach ($activeKeywords as $keyword) {
                // Check if the keyword is found in the message text (case-insensitive)
                if (stripos($messageText, $keyword['name']) !== false) {
                    $matchedKeywordIds[] = $keyword['id'];
                    $matchedKeywordNames[] = $keyword['name'];
                    $logs[] = 'Matched keyword: ' . $keyword['name'];

                    // Insert scheduled messages for this matched keyword.
                    if (!empty($keyword['scheduled_message_json'])) {
                        $scheduleItems = json_decode($keyword['scheduled_message_json'], true);
                        if (is_array($scheduleItems)) {
                            $logs[] = 'Processing ' . count($scheduleItems) . ' scheduled messages for keyword: ' . $keyword['name'];
                            foreach ($scheduleItems as $item) {
                                // Validate required fields.
                                if (empty($item['message']) || empty($item['interval']) || empty($item['unit'])) {
                                    $logs[] = 'Skipped scheduled item - missing required fields';
                                    continue;
                                }
                                $intervalValue = (int) $item['interval'];
                                $unit = strtolower($item['unit']);

                                // Determine multiplier (in seconds) based on the unit.
                                switch ($unit) {
                                    case 'seconds':
                                        $multiplier = 1;
                                        break;
                                    case 'minutes':
                                        $multiplier = 60;
                                        break;
                                    case 'hours':
                                        $multiplier = 3600;
                                        break;
                                    case 'days':
                                        $multiplier = 86400;
                                        break;
                                    default:
                                        // Skip if unit is unknown.
                                        $logs[] = 'Skipped scheduled item - unknown time unit: ' . $unit;
                                        continue 2;
                                }

                                // Compute the scheduled send time.
                                $sendAtTimestamp = time() + ($intervalValue * $multiplier);
                                $sendAt = date('Y-m-d H:i:s', $sendAtTimestamp);

                                // Insert scheduled message record
                                $data = [
                                    'created_by' => $uid,
                                    'target_phone_number' => $contact['phone_number'] ?? '',
                                    'template_id' => 0,
                                    'status' => self::STATUS_QUEUED,
                                    'raw_response' => '',
                                    'blasting_id' => 0,
                                    'type' => 'KEYWORD',
                                    'keyword_id' => $keyword['id'],
                                    'scheduled_time' => $sendAt,
                                    'keyword_message' => $item['message']
                                ];

                                $query = $db->getQuery(true)
                                    ->insert($db->quoteName('#__dt_whatsapp_tenants_scheduled_messages'))
                                    ->columns($db->quoteName(array_keys($data)))
                                    ->values(implode(',', array_map([$db, 'quote'], $data)));
                                $db->setQuery($query)->execute();
                                $logs[] = 'Scheduled message for ' . $sendAt . ' (' . $intervalValue . ' ' . $unit . ' from now)';
                            }
                        }
                    }
                }
            }
        }

        // Only update the contact if at least one active keyword was matched.
        if (empty($matchedKeywordIds)) {
            $logs[] = 'No keywords matched, not updating contact';
            return [
                'keywords' => [],
                'logs' => $logs
            ];
        }

        // Use existing keywords_tags from $contact.
        $currentTags = $contact['keywords_tags'] ?? '';
        $currentKeywordIds = [];
        if (!empty($currentTags)) {
            $currentKeywordIds = array_map('trim', explode(',', $currentTags));
        }
        $logs[] = 'Current keyword tags: ' . ($currentTags ?: 'none');

        // Merge new matched keyword IDs with the current ones and remove duplicates.
        $mergedIds = array_unique(array_merge($currentKeywordIds, $matchedKeywordIds));
        $mergedIdsStr = implode(',', $mergedIds);

        // Update the contact record with the new keywords_tags and update last_updated.
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__dt_whatsapp_tenants_contacts'))
            ->set($db->quoteName('keywords_tags') . ' = ' . $db->quote($mergedIdsStr))
            ->set($db->quoteName('last_updated') . ' = ' . $db->quote(date('Y-m-d H:i:s')))
            ->where($db->quoteName('id') . ' = ' . $db->quote($contact['id']));
        $db->setQuery($query);
        $db->execute();
        $logs[] = 'Contact updated with new keywords: ' . $mergedIdsStr;

        return [
            'keywords' => $matchedKeywordNames,
            'logs' => $logs
        ];
    }


    /**
     * Process incoming webhook events, log them, forward the payload to an external URL,
     * and update template status if the field is message_template_status_update.
     *
     * @return array<string, mixed> Result of processing the webhook.
     *
     * @throws \Exception If webhook verification fails.
     */
    public function processWebhook()
    {
        $app = $this->getApplication();
        $input = $app->input;

        // Read raw input once for both logging and processing.
        $rawData = file_get_contents('php://input');
        $this->originalPayload = $rawData;

        // Handle GET requests for webhook verification.
        if ($app->input->getMethod() === 'GET') {
            $hub_mode = $input->get('hub_mode', '', 'STRING');
            $hub_verify_token = $input->get('hub_verify_token', '', 'STRING');
            $hub_challenge = $input->get('hub_challenge', '', 'STRING');
            $expectedToken = $this->params->get('verify_token', '');
            if ($hub_mode === 'subscribe' && $hub_verify_token === $expectedToken) {
                return $hub_challenge;
            }
            throw new \Exception('Webhook verification failed.');
        }

        $data = json_decode($rawData, true);
        if (empty($data)) {
            return ['status' => self::STATUS_ERROR, 'message' => 'No data received.'];
        }

        $results = [];

        // Check if the payload contains 'entry' with 'changes'.
        if (isset($data['entry']) && is_array($data['entry'])) {
            foreach ($data['entry'] as $entry) {
                if (isset($entry['changes']) && is_array($entry['changes'])) {
                    foreach ($entry['changes'] as $change) {
                        $results[] = $this->processChange($change);
                    }
                } else {
                    // Fallback: process the entry as a single change if 'changes' key is not present.
                    $results[] = $this->processChange($entry);
                }
            }
        } elseif (isset($data['changes']) && is_array($data['changes'])) {
            // Fallback for older payload structure.
            foreach ($data['changes'] as $change) {
                $results[] = $this->processChange($change);
            }
        } else {
            // Fallback: process the entire payload as a single change.
            $results[] = $this->processChange($data);
        }

        return $results;
    }

    /**
     * Process a single change.
     *
     * @param array $change The change data containing 'field' and 'value'.
     * @return array The result of processing the change.
     */
    protected function processChange(array $change): array
    {
        $field = $change['field'] ?? '';
        $value = $change['value'] ?? [];

        // Initialize result structure.
        $result = [
            'status' => self::STATUS_PROCESSED,
            'type' => '',
            'data' => [],
            'errors' => [],
            'leads' => null,
            'forward' => null,
            'logs' => []
        ];

        switch ($field) {
            case 'message_template_components_update':
                $result['type'] = 'components_update';
                $result['data'] = [
                    'template_id' => $value['message_template_id'] ?? null,
                    'template_name' => $value['message_template_name'] ?? null,
                ];
                break;

            case 'message_template_quality_update':
                $result['type'] = 'quality_update';
                $result['data'] = [
                    'previous_quality' => $value['previous_quality_score'] ?? null,
                    'new_quality' => $value['new_quality_score'] ?? null,
                ];
                break;

            case 'message_template_status_update':
                $result['type'] = 'status_update';
                $result['data'] = [
                    'event' => $value['event'] ?? null,
                    'reason' => $value['reason'] ?? null,
                ];
                $templateId = $value['message_template_id'] ?? '';
                if (!empty($templateId)) {
                    try {
                        $db = Factory::getDbo();
                        $query = $db->getQuery(true);
                        $fields = [$db->quoteName('status') . ' = ' . $db->quote($value['event'])];
                        $conditions = [$db->quoteName('template_id') . ' = ' . $db->quote($templateId)];
                        $query->update($db->quoteName('#__dt_whatsapp_tenants_templates'))
                            ->set(implode(', ', $fields))
                            ->where(implode(' AND ', $conditions));
                        $db->setQuery($query);
                        $db->execute();
                    } catch (\Exception $e) {
                        $result['errors'][] = 'Template update error: ' . $e->getMessage();
                    }
                }
                break;

            case 'messages':
                $messages = $value['messages'][0] ?? [];
                if (empty($messages) || (($messages['type'] ?? '') !== 'text')) {
                    $result['type'] = 'message';
                    $result['data'] = ['message' => 'No valid text message found'];
                } else {
                    $from = $messages['from'] ?? null;
                    $text = $messages['text']['body'] ?? '';
                    $result['type'] = 'message';
                    $result['data'] = ['from' => $from, 'text' => $text];

                    $contact = null;
                    
                    // Extract contact info from the parsed value
                    $contactInfo = null;
                    if (isset($value['contacts']) && is_array($value['contacts']) && !empty($value['contacts'])) {
                        $contactInfo = $value['contacts'][0] ?? null;
                    }
                    
                    try {
                        $db = Factory::getDbo();
                        $normalizedFrom = ltrim($from, '+');
                        
                        // Check if contact exists
                        $query = $db->getQuery(true)
                            ->select($db->quoteName(['id', 'name', 'keywords_tags', 'phone_number']))
                            ->from($db->quoteName('#__dt_whatsapp_tenants_contacts'))
                            ->where('REPLACE(' . $db->quoteName('phone_number') . ", '+', '' ) = " . $db->quote($normalizedFrom));
                        $contact = $db->setQuery($query)->loadAssoc();
                        
                        $result['logs']['contact'] = [];
                        $result['logs']['contact'][] = 'Looking up contact with number: ' . $normalizedFrom;
                        
                        if ($contact) {
                            $result['logs']['contact'][] = 'Found existing contact: ' . ($contact['name'] ?? 'unnamed');
                            // Update existing contact if we have new info from the payload
                            if ($contactInfo && isset($contactInfo['profile']['name']) && $contactInfo['profile']['name'] !== $contact['name']) {
                                $db->setQuery(
                                    $db->getQuery(true)
                                        ->update($db->quoteName('#__dt_whatsapp_tenants_contacts'))
                                        ->set($db->quoteName('name') . ' = ' . $db->quote($contactInfo['profile']['name']))
                                        ->set($db->quoteName('last_updated') . ' = ' . $db->quote(date('Y-m-d H:i:s')))
                                        ->where($db->quoteName('id') . ' = ' . $db->quote($contact['id']))
                                )->execute();
                                
                                // Update name in current contact data
                                $contact['name'] = $contactInfo['profile']['name'];
                                $result['logs']['contact'][] = 'Updated contact name to: ' . $contactInfo['profile']['name'];
                            }
                        } else if ($contactInfo && isset($contactInfo['profile']['name'])) {
                            $result['logs']['contact'][] = 'Creating new contact: ' . $contactInfo['profile']['name'];
                            // Insert new contact
                            $uid = Factory::getApplication()->input->get('uid', '', 'STRING');
                            
                            $insertQuery = $db->getQuery(true)
                                ->insert($db->quoteName('#__dt_whatsapp_tenants_contacts'))
                                ->columns($db->quoteName(['name', 'phone_number', 'created_by', 'last_updated']))
                                ->values(implode(',', [
                                    $db->quote($contactInfo['profile']['name']),
                                    $db->quote($from),
                                    $db->quote($uid),
                                    $db->quote(date('Y-m-d H:i:s'))
                                ]));
                            $db->setQuery($insertQuery)->execute();
                            
                            // Get the newly inserted contact ID
                            $contact = [
                                'id' => $db->insertid(),
                                'name' => $contactInfo['profile']['name'],
                                'phone_number' => $from,
                                'keywords_tags' => ''
                            ];
                            $result['logs']['contact'][] = 'Created new contact with ID: ' . $contact['id'];
                        } else {
                            $result['logs']['contact'][] = 'Contact not found and no contact info in payload';
                        }
                    } catch (\Exception $e) {
                        $result['errors'][] = 'Contact lookup/upsert error: ' . $e->getMessage();
                        $result['logs']['contact'][] = 'Error: ' . $e->getMessage();
                    }

                    if ($contact && isset($contact['name'])) {
                        $uid = Factory::getApplication()->input->get('uid', '', 'STRING');
                        $config = $this->getConfig($uid);
                        if (!$config) {
                            $result['errors'][] = 'No config found for uid: ' . $uid;
                            $result['logs']['api'][] = 'No config found for uid: ' . $uid;
                        } else {
                            $keywordsResult = $this->updateContactKeywordsIfMatched($contact, $uid, $text);
                            $result['logs']['keywords'] = $keywordsResult['logs'] ?? [];
                            
                            $secret = $config['dreamztrack_key'] ?? '';
                            $env = strtoupper($config['dreamztrack_endpoint'] ?? 'DEVELOPMENT');
                            $endpoint = ($env === 'PRODUCTION')
                                ? 'https://dreamztrack-api-prod.dreamztrack.com.my/open-api/v1/customer'
                                : 'https://dreamztrack-api-dev.dreamztrack.com.my/open-api/v1/customer';

                            $result['logs']['api'] = [];
                            $result['logs']['api'][] = 'Using endpoint: ' . $endpoint;

                            $apiPayload = [
                                "contact_no" => $from,
                                "name" => $contact['name'],
                                "branch_name" => $config['dreamztrack_branch'] ?? "HQ Branch",
                                "source_name" => "WhatsApp",
                                "type" => "WhatsApp",
                                "tags" => $keywordsResult['keywords'] ?? []
                            ];

                            if (empty($secret)) {
                                $result['logs']['api'][] = 'No DreamzTrack secret found, skipping API call';
                                $result['leads'] = [
                                    'code' => null,
                                    'body' => null
                                ];
                            } else {
                                $result['logs']['api'][] = 'Sending contact data to DreamzTrack';
                                try {
                                    $ch = curl_init($endpoint);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                        'x-dreamztrack-secret: ' . $secret,
                                        'Content-Type: application/json'
                                    ]);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiPayload));
                                    $apiResponseBody = curl_exec($ch);
                                    $apiResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                    curl_close($ch);
                                    $result['logs']['api'][] = 'API response code: ' . $apiResponseCode;
                                    $result['leads'] = [
                                        'code' => $apiResponseCode,
                                        'body' => $apiResponseBody
                                    ];
                                } catch (\Exception $e) {
                                    $result['logs']['api'][] = 'API error: ' . $e->getMessage();
                                    $result['leads'] = [
                                        'code' => null,
                                        'body' => null
                                    ];
                                    $result['errors'][] = 'Dreamztrack API error: ' . $e->getMessage();
                                }
                            }
                        }
                    } else {
                        $result['logs']['api'] = ['No contact found, skipping API call'];
                        $result['leads'] = [
                            'code' => null,
                            'body' => null
                        ];
                    }
                }
                break;

            default:
                $result = [
                    'status' => self::STATUS_IGNORED,
                    'type' => 'unknown',
                    'data' => [],
                    'errors' => [],
                    'message' => 'Field not handled'
                ];
                break;
        }

        // For messages events, forward the payload using the forward_url config.
        if ($field === 'messages') {
            $result['logs']['forwarding'] = [];
            $uid = Factory::getApplication()->input->get('uid', '', 'STRING');
            if (empty($uid)) {
                $result['status'] = self::STATUS_ERROR;
                $result['logs']['forwarding'][] = 'No uid provided';
            } else {
                $config = $this->getConfig($uid);
                if (!$config || empty($config['forward_url'])) {
                    $result['status'] = self::STATUS_ERROR;
                    $result['logs']['forwarding'][] = 'No forward URL found for user: ' . $uid;
                } else {
                    $result['logs']['forwarding'][] = 'Forwarding to URL: ' . $config['forward_url'];
                    try {
                        $ch = curl_init($config['forward_url']);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->originalPayload);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type: application/json'
                        ]);
                        $responseBody = curl_exec($ch);
                        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        $result['logs']['forwarding'][] = 'Response code: ' . $responseCode;
                        $result['forward'] = [
                            'response_code' => $responseCode,
                            'response_body' => $responseBody
                        ];
                    } catch (\Exception $e) {
                        $result['status'] = self::STATUS_ERROR;
                        $result['logs']['forwarding'][] = 'Error: ' . $e->getMessage();
                        $result['errors'][] = 'Forwarding error: ' . $e->getMessage();
                    }
                }
            }
        }

        // Log the webhook event.
        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $columns = ['field', 'value', 'status', 'detail'];
            $values = [
                $db->quote($field),
                $db->quote(json_encode($value)),
                $db->quote($result['status'] ?? ''),
                $db->quote(json_encode($result))
            ];
            $query->insert($db->quoteName('#__dt_whatsapp_tenants_webhook'))
                ->columns(implode(',', array_map([$db, 'quoteName'], $columns)))
                ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();
        } catch (\Exception $e) {
            $result['errors'][] = 'Webhook logging error: ' . $e->getMessage();
            Log::add('Webhook logging error: ' . $e->getMessage(), Log::ERROR, 'whatsapp_webhook_db');
        }

        return $result;
    }
}
