<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Task.WhatsAppBlast
 *
 * This plugin processes WhatsApp blast tasks.
 */

namespace Joomla\Plugin\Task\WhatsAppBlast\Extension;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\SubscriberInterface;

defined('_JEXEC') or die;

class WhatsAppBlast extends CMSPlugin implements SubscriberInterface
{
    use TaskPluginTrait;

    protected $autoloadLanguage = true;

    // Blasting statuses.
    const BLASTING_STATUS_QUEUED      = 'QUEUED';
    const BLASTING_STATUS_PROCESSING  = 'PROCESSING';
    const BLASTING_STATUS_FAILED      = 'FAILED';
    const BLASTING_STATUS_FINISHED    = 'FINISHED';

    // Scheduled messages statuses.
    const SCHEDULED_STATUS_QUEUED     = 'QUEUED';
    const SCHEDULED_STATUS_PROCESSING = 'PROCESSING';
    const SCHEDULED_STATUS_DELIVERED  = 'DELIVERED';
    const SCHEDULED_STATUS_FAILED     = 'FAILED';
    const SCHEDULED_STATUS_CANCELED   = 'CANCELED';

    protected const TASKS_MAP = [
        'plg_task_whatsappblast' => [
            'langConstPrefix' => 'PLG_TASK_WHATSAPPBLAST_TASK',
            'method'          => 'doWhatsAppBlast',
            'form'            => 'whatsappservice_form',
        ],
    ];

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onTaskOptionsList'    => 'advertiseRoutines',
            'onExecuteTask'        => 'standardRoutineHandler',
            'onContentPrepareForm' => 'enhanceTaskItemForm',
        ];
    }

    /**
     * Constructor.
     *
     * @param DispatcherInterface $dispatcher The dispatcher.
     * @param array               $config     Configuration settings.
     */
    public function __construct(DispatcherInterface $dispatcher, array $config)
    {
        parent::__construct($dispatcher, $config);
        $this->logTask('WhatsAppBlast plugin constructed.', 'info');
    }

    /**
     * Executes the WhatsApp blast task.
     *
     * Processes messages from the queue, updates statuses accordingly, and ensures that any blasting record
     * is set to FINISHED only when there are no pending scheduled messages.
     *
     * @param ExecuteTaskEvent $event The task event object.
     * @return int Status::OK on success, Status::KNOCKOUT on error.
     */
    private function doWhatsAppBlast(ExecuteTaskEvent $event): int
    {
        $maxMPS = (int) $this->params->get('max_mps', 80);
        $count  = ['success' => 0, 'failed' => 0];
        $this->logTask('WhatsApp blast task started.', 'info');

        try {
            while ($messages = $this->getMessagesFromQueue($maxMPS)) {
                $this->logTask('Fetched ' . count($messages) . ' messages from DB.', 'info');
                $batchStart = microtime(true);

                // Gather blasting record IDs (if any) and scheduled message IDs.
                $blastingIds = [];
                $messageIds  = [];

                foreach ($messages as $message) {
                    // Scheduled message ID remains in $message->id.
                    $messageIds[] = (int) $message->id;
                    // If blasting data is available, store its ID.
                    if (!empty($message->blasting) && isset($message->blasting->id)) {
                        $blastingIds[] = (int) $message->blasting->id;
                    }
                }

                $db = Factory::getDbo();
                $db->transactionStart();

                // Update blasting records to PROCESSING (if available).
                if (!empty($blastingIds)) {
                    $query = $db->getQuery(true)
                        ->update($db->quoteName('#__dt_whatsapp_tenants_blastings'))
                        ->set($db->quoteName('status') . ' = ' . $db->quote(self::BLASTING_STATUS_PROCESSING))
                        ->where($db->quoteName('id') . ' IN (' . implode(',', $blastingIds) . ')');
                    $db->setQuery($query);
                    $db->execute();
                    $this->logTask('Blasting records status set to PROCESSING.', 'info');
                }

                // Update scheduled messages status to PROCESSING.
                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__dt_whatsapp_tenants_scheduled_messages'))
                    ->set($db->quoteName('status') . ' = ' . $db->quote(self::SCHEDULED_STATUS_PROCESSING))
                    ->where($db->quoteName('id') . ' IN (' . implode(',', $messageIds) . ')');
                $db->setQuery($query);
                $db->execute();
                $this->logTask('Scheduled messages status set to PROCESSING.', 'info');

                $db->transactionCommit();

                // Process each message.
                foreach ($messages as $message) {
                    $this->logTask('Sending scheduled message ID: ' . $message->id, 'info');
                    $status = $this->sendMessageViaCloudAPI($message);
                    if ($status) {
                        $count['success']++;
                    } else {
                        $count['failed']++;
                    }
                }

                $elapsed = microtime(true) - $batchStart;
                if ($elapsed < 1) {
                    usleep((1 - $elapsed) * 1000000);
                }
                $this->logTask('Batch processed in ' . $elapsed . ' seconds.', 'info');
            }

            // Update blasting records to FINISHED when no pending scheduled messages exist.
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $subQuery = 'SELECT 1 FROM ' . $db->quoteName('#__dt_whatsapp_tenants_scheduled_messages') . ' m '
                . 'WHERE m.' . $db->quoteName('blasting_id') . ' = ' . $db->quoteName('#__dt_whatsapp_tenants_blastings.id')
                . ' AND m.' . $db->quoteName('status') . ' IN ('
                . $db->quote(self::SCHEDULED_STATUS_QUEUED) . ', '
                . $db->quote(self::SCHEDULED_STATUS_PROCESSING) . ')';

            $query->update($db->quoteName('#__dt_whatsapp_tenants_blastings'))
                ->set($db->quoteName('status') . ' = ' . $db->quote(self::BLASTING_STATUS_FINISHED))
                ->where($db->quoteName('status') . ' = ' . $db->quote(self::BLASTING_STATUS_PROCESSING))
                ->where('NOT EXISTS (' . $subQuery . ')');
            $db->setQuery($query);
            $db->execute();
            $this->logTask('Blasting records updated to FINISHED where applicable.', 'info');
        } catch (Exception $e) {
            $db = Factory::getDbo();

            // Update blasting records to FAILED.
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__dt_whatsapp_tenants_blastings'))
                ->set($db->quoteName('status') . ' = ' . $db->quote(self::BLASTING_STATUS_FAILED));
            $db->setQuery($query);
            $db->execute();
            $this->logTask('Blasting records updated to FAILED.', 'error');

            // Update scheduled messages to CANCELED.
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__dt_whatsapp_tenants_scheduled_messages'))
                ->set($db->quoteName('status') . ' = ' . $db->quote(self::SCHEDULED_STATUS_CANCELED))
                ->where($db->quoteName('status') . ' = ' . $db->quote(self::SCHEDULED_STATUS_QUEUED));
            $db->setQuery($query);
            $db->execute();
            $this->logTask('Scheduled messages in QUEUE updated to CANCELED. Exception: ' . $e->getMessage(), 'error');

            return Status::KNOCKOUT;
        } finally {
            $this->logTask(
                'Batch complete. Total messages processed: ' . ($count['success'] + $count['failed']) .
                '. Success: ' . $count['success'] . ', Failed: ' . $count['failed'] . '.',
                'info'
            );
        }

        $this->logTask('WhatsApp blast task completed successfully.', 'info');
        return Status::OK;
    }

    /**
     * Retrieves messages from the queue.
     *
     * This method pulls all records from the scheduled messages table where the status is QUEUED
     * and the scheduled time has passed. For non-KEYWORD messages (which require blasting data), it then
     * looks up the corresponding blasting record. The scheduled message’s primary key remains in `$message->id`
     * and any associated blasting data is attached as an object in `$message->blasting`.
     *
     * @param int $limit Maximum number of messages to fetch.
     * @return array An array of message objects.
     */
    protected function getMessagesFromQueue(int $limit): array
    {
        $db = Factory::getDbo();

        // Fetch all scheduled messages that are queued and due.
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__dt_whatsapp_tenants_scheduled_messages'))
            ->where($db->quoteName('status') . ' = ' . $db->quote(self::SCHEDULED_STATUS_QUEUED))
            ->where($db->quoteName('scheduled_time') . ' <= NOW()');
        $db->setQuery($query);
        $messages = $db->loadObjectList();
        $this->logTask('Fetched ' . count($messages) . ' messages from the queue.', 'info');

        // Process each message.
        foreach ($messages as $message) {
            // For non-KEYWORD messages, attempt to load the associated blasting record.
            if (strtoupper($message->type) !== 'KEYWORD' && !empty($message->blasting_id)) {
                $queryBlast = $db->getQuery(true)
                    ->select('*')
                    ->from($db->quoteName('#__dt_whatsapp_tenants_blastings'))
                    ->where($db->quoteName('id') . ' = ' . (int) $message->blasting_id)
                    ->where($db->quoteName('status') . ' = ' . $db->quote(self::BLASTING_STATUS_QUEUED))
                    ->where($db->quoteName('scheduled_time') . ' <= NOW()');
                $db->setQuery($queryBlast);
                $blasting = $db->loadObject();

                if ($blasting) {
                    // Attach the blasting record as an object.
                    $message->blasting = $blasting;
                } else {
                    $message->blasting = null;
                }
            } else {
                // For KEYWORD messages or when no blasting data is needed.
                $message->blasting = null;
            }
        }

        // Sort messages by scheduled_time (and ordering if available).
        usort($messages, function ($a, $b) {
            $timeA = strtotime($a->scheduled_time);
            $timeB = strtotime($b->scheduled_time);
            if ($timeA === $timeB) {
                $orderA = isset($a->ordering) ? (int) $a->ordering : 0;
                $orderB = isset($b->ordering) ? (int) $b->ordering : 0;
                return $orderA - $orderB;
            }
            return $timeA - $timeB;
        });

        // Limit the results.
        $messages = array_slice($messages, 0, $limit);

        // Preload configuration for each unique created_by.
        $createdByIds = [];
        foreach ($messages as $message) {
            if (isset($message->created_by)) {
                $createdByIds[] = $message->created_by;
            }
        }
        $createdByIds = array_unique($createdByIds);
        $configs = [];
        if (!empty($createdByIds)) {
            $quotedIds = array_map(function ($id) use ($db) {
                return $db->quote($id);
            }, $createdByIds);
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__dt_whatsapp_tenants_configs'))
                ->where($db->quoteName('created_by') . ' IN (' . implode(',', $quotedIds) . ')');
            $db->setQuery($query);
            $configList = $db->loadObjectList('created_by');
            foreach ($createdByIds as $userId) {
                $configs[$userId] = $configList[$userId] ?? null;
                $this->logTask("Loaded config for user ID: $userId", 'info');
            }
        }

        foreach ($messages as $message) {
            $message->config = $configs[$message->created_by] ?? null;
        }

        // Preload template languages.
        $templateIds = [];
        foreach ($messages as $message) {
            if (!empty($message->template_id)) {
                $templateIds[] = $message->template_id;
            }
        }
        $templateIds = array_unique($templateIds);
        if (!empty($templateIds)) {
            $quotedTemplateIds = array_map(function ($id) use ($db) {
                return $db->quote($id);
            }, $templateIds);
            $query = $db->getQuery(true)
                ->select([$db->quoteName('id'), $db->quoteName('language'), $db->quoteName('name')])
                ->from($db->quoteName('#__dt_whatsapp_tenants_templates'))
                ->where($db->quoteName('id') . ' IN (' . implode(',', $quotedTemplateIds) . ')');
            $db->setQuery($query);
            $templateRows = $db->loadObjectList('id');
            foreach ($messages as $message) {
                $message->language = isset($templateRows[$message->template_id])
                    ? $templateRows[$message->template_id]->language
                    : 'en_US';
                $message->template_name = isset($templateRows[$message->template_id])
                    ? $templateRows[$message->template_id]->name
                    : 'UNDEFINED_NAME';
            }
            $this->logTask('Loaded template languages for templates: ' . implode(',', $templateIds), 'info');
        } else {
            foreach ($messages as $message) {
                $message->language = 'en_US';
            }
            $this->logTask('No template IDs found; defaulting language to en_US.', 'info');
        }

        return $messages;
    }

    /**
     * Sends a single message using the WhatsApp Cloud API.
     *
     * For messages of type KEYWORD it sends a text payload using the v22.0 API; otherwise, it sends a template
     * message using the v13.0 API.
     *
     * @param object $message Message data.
     * @return bool True if the message was delivered successfully, false otherwise.
     */
    protected function sendMessageViaCloudAPI($message): bool
    {
        try {
            if (empty($message->config)) {
                throw new Exception('No configuration found for user ID ' . $message->created_by);
            }

            // Choose the API endpoint and payload based on message type.
            if (isset($message->type) && strtoupper($message->type) === 'KEYWORD') {
                // KEYWORD message: use text payload with v22.0 API.
                $this->logTask('Sending without template: ' . $message->keyword_message, 'info');
                $url = 'https://graph.facebook.com/v22.0/' . $message->config->phone_number_id . '/messages';
                $data = [
                    "messaging_product" => "whatsapp",
                    "recipient_type"    => "individual",
                    "to"                => $message->target_phone_number,
                    "type"              => "text",
                    "text"              => [
                        "preview_url" => true,
                        "body"        => $message->keyword_message
                    ]
                ];
            } else {
                // Template message: use v13.0 API.
                $this->logTask('Sending with template: ' . $message->template_name, 'info');
                $url = 'https://graph.facebook.com/v13.0/' . $message->config->phone_number_id . '/messages';
                $data = [
                    "messaging_product" => "whatsapp",
                    "recipient_type"    => "individual",
                    "to"                => $message->target_phone_number,
                    "type"              => "template",
                    "template"          => [
                        "name"     => $message->template_name,
                        "language" => [
                            "code" => $message->language ?? 'en_US'
                        ]
                    ]
                ];
            }

            $jsonData = json_encode($data);
            $this->logTask('Payload for scheduled message ID ' . $message->id . ': ' . $jsonData, 'info');

            $ch = curl_init();
            if ($ch === false) {
                throw new Exception("Failed to initialize cURL");
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer " . $message->config->token,
                "Content-Type: application/json"
            ]);

            $response = curl_exec($ch);
            if ($response === false) {
                $curlError = curl_error($ch);
                curl_close($ch);
                throw new Exception("cURL error: " . $curlError);
            }
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $this->logTask('HTTP Code: ' . $httpCode . ' for scheduled message ID ' . $message->id, 'info');

            if ($httpCode >= 200 && $httpCode < 300) {
                $this->logTask('Message sent via Cloud API for scheduled message ID ' . $message->id . '. Response: ' . $response, 'info');
                $db = Factory::getDbo();
                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__dt_whatsapp_tenants_scheduled_messages'))
                    ->set($db->quoteName('raw_response') . ' = ' . $db->quote($response))
                    ->set($db->quoteName('status') . ' = ' . $db->quote(self::SCHEDULED_STATUS_DELIVERED))
                    ->where($db->quoteName('id') . ' = ' . (int) $message->id);
                $db->setQuery($query);
                $db->execute();
                return true;
            } else {
                throw new Exception("HTTP Error Code: $httpCode, Response: $response");
            }
        } catch (Exception $e) {
            $this->logTask('Cloud API Exception for scheduled message ID ' . $message->id . ': ' . $e->getMessage(), 'error');
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__dt_whatsapp_tenants_scheduled_messages'))
                ->set($db->quoteName('raw_response') . ' = ' . $db->quote($response))
                ->set($db->quoteName('status') . ' = ' . $db->quote(self::SCHEDULED_STATUS_FAILED))
                ->where($db->quoteName('id') . ' = ' . (int) $message->id);
            $db->setQuery($query);
            $db->execute();
            return false;
        }
    }
}
