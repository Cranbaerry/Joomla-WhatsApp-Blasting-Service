<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_templates
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantstemplates\Component\Dt_whatsapp_tenants_templates\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Exception;

/**
 * Whatsapptenantstemplate class.
 *
 * @since  1.0.0
 */
class WhatsapptenantstemplateformController extends FormController
{
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function edit($key = NULL, $urlVar = NULL)
	{
		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $this->app->getUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.id');
		$editId     = $this->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$this->app->setUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.id', $editId);

		// Get the model.
		$model = $this->getModel('Whatsapptenantstemplateform', 'Site');

		// Check out the item
		if ($editId)
		{
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(Route::_('index.php?option=com_dt_whatsapp_tenants_templates&view=whatsapptenantstemplateform&layout=edit', false));
	}

	/**
	 * Method to save data.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   1.0.0
	 */
	public function save($key = NULL, $urlVar = NULL)
	{
		// Check for request forgeries.
		$this->checkToken();

		// Initialise variables.
		$model = $this->getModel('Whatsapptenantstemplateform', 'Site');

		// Get the user data.
		$data = $this->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			throw new \Exception($model->getError(), 500);
		}

		// Send an object which can be modified through the plugin event
		$objData = (object) $data;
		$this->app->triggerEvent(
			'onContentNormaliseRequestData',
			array($this->option . '.' . $this->context, $objData, $form)
		);

		$data = (array) $objData;

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof \Exception)
				{
					$this->app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$this->app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$jform = $this->input->get('jform', array(), 'ARRAY');

			// Save the data in the session.
			$this->app->setUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.data', $jform);

			// Redirect back to the edit screen.
			$id = (int) $this->app->getUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.id');
			$this->setRedirect(Route::_('index.php?option=com_dt_whatsapp_tenants_templates&view=whatsapptenantstemplateform&layout=edit&id=' . $id, false));

			$this->redirect();
		}

		$url = '';


		// CUSTOM CODE
		try {
			$config_model = Factory::getApplication()->bootComponent('com_dt_whatsapp_tenants_configs')->getMVCFactory()->createModel('whatsapptenantsconfig');
			$user  = Factory::getUser();
			$user_id = $user->get('id');
			$config = $config_model->getItemByUserId($user_id);
			$files = $this->app->input->files->get('jform');
			$file_handle = null;
			$template = null;
			
			if (empty($config)) {
				throw new Exception("No configuration found for user.");
			}

			if (!empty($data['id'])) {
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select('*')
					->from('#__dt_whatsapp_tenants_templates')
					->where('id = ' . $db->quote($data['id']));
				$db->setQuery($query);
				$db->execute();
				
				$template = $db->loadObject();
				$url_main = "https://graph.facebook.com/v22.0/{$template->template_id}";
				if ($data['name'] != $template->name) {
					throw new Exception("Template name cannot be changed from {$template->name}");
				}
			} else {
				//die('This is an insert operation.');
				$url_main = "https://graph.facebook.com/v22.0/$config->business_account_id/message_templates";
			}

			if (!empty($files['header_media']) && $data['header_type'] !== 'TEXT') {
				$fileData = $files['header_media'];
				$uploadSessionId = null;
				
				// Initialize file upload id
				if (empty($files['header_media']['name'])) {
					// No new file uploaded, so get the existing file from the database.
					$filePath = JPATH_ROOT . '/uploads/' . $user_id . '/' . $template->header_media;
					if (!file_exists($filePath)) {
						throw new Exception("Existing file not found");
					}
					$fileData = [
						'name'     => $template->header_media,
						'size'     => filesize($filePath),
						'type'     => mime_content_type($filePath),
						'tmp_name' => $filePath,
						'error'    => 0
					];
				}

				$url = "https://graph.facebook.com/v22.0/{$config->app_id}/uploads?" . http_build_query([
					'file_name'    => $fileData['name'],
					'file_length'  => $fileData['size'],
					'file_type'    => $fileData['type'],
					'access_token' => $config->token,
				]);

				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				if ($response === false) {
					throw new Exception("cURL error: " . curl_error($ch));
				}

				$jsonResponse = json_decode($response);
				if ($jsonResponse && isset($jsonResponse->error)) {
					$errorMsg  = $jsonResponse->error->message;
					$errorCode = $jsonResponse->error->code;
					$userErrorMsg = $jsonResponse->error->error_user_msg;
					throw new Exception("Facebook API error (code {$errorCode}): {$errorMsg} {$userErrorMsg}");
				}

				if ($jsonResponse && isset($jsonResponse->id)) {
					$uploadSessionId = $jsonResponse->id;
				}

				curl_close($ch);
	
				// Begin upload
				$fileBinary = file_get_contents($fileData['tmp_name']);
				$url = "https://graph.facebook.com/v22.0/{$uploadSessionId}";
				$ch = curl_init($url);
				$headers = [
					"Authorization: OAuth {$config->token}",
					"file_offset: 0"
				];
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fileBinary);
				$response = curl_exec($ch);
				if ($response === false) {
					throw new Exception("cURL error: " . curl_error($ch));
				}

				$jsonResponse = json_decode($response);
				if ($jsonResponse && isset($jsonResponse->debug_info->message)) {
					throw new Exception("Facebook API error (code {$jsonResponse->debug_info->type}): {$jsonResponse->debug_info->message}");
				}

				if ($jsonResponse && isset($jsonResponse->h)) {
					$file_handle = $jsonResponse->h;
				}

				curl_close($ch);
			}
	
			$payload = [
				"name"      => $data["name"],
				"language"  => $data["language"],
				"category"  => $data["category"],
				"components" => [
					[
						"type"    => "HEADER",
						"format"  => $data["header_type"],
						"text"    => trim($data["header_text"])
					],
					[
						"type"    => "BODY",
						"text"    => trim($data["body"])
					],
					[
						"type"    => "BUTTONS",
						"buttons" => [
							[
								"type" => "QUICK_REPLY",
								"text" => "Unsubscribe from Promos"
							]
						]
					]
				]
			];

			if (!empty($data["footer"])) {
				$payload["components"][] = [
					"type"    => "FOOTER",
					"text"    => $data["footer"]
				];
			}

			if (!empty($files['header_media']) && $data['header_type'] !== 'TEXT') {
				$payload['components'][0]['example']['header_handle'] = [$file_handle];
				unset($payload['components'][0]['text']);
			}

			$jsonData = json_encode($payload);
	
			// Initialize the cURL session
			$ch = curl_init($url_main);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Content-Type: application/json",
				"Authorization: Bearer $config->token"
			]);
	
			// Execute the cURL request
			$response = curl_exec($ch);
			if ($response === false) {
				throw new Exception("cURL error: " . curl_error($ch));
			}

			$jsonResponse = json_decode($response);
			if ($jsonResponse && isset($jsonResponse->error)) {
				$errorMsg  = $jsonResponse->error->message;
				$errorCode = $jsonResponse->error->code;
				$userErrorMsg = $jsonResponse->error->error_user_msg;
				throw new Exception("Facebook API error (code {$errorCode}): {$errorMsg} {$userErrorMsg}");
			}

			if ($jsonResponse && isset($jsonResponse->status)) {
				$data['status'] = $jsonResponse->status;
				$data['category'] = $jsonResponse->category;
				$data['template_id'] = $jsonResponse->id;
			}
		
			// Close the cURL session
			curl_close($ch);
		} catch (Exception $e) {
			// Save the data in the session.
			$this->app->setUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.data', $data);
			
			// Redirect back to the edit screen.
			$id = (int) $this->app->getUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.id');
			$this->setMessage(Text::sprintf($e->getMessage()), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_dt_whatsapp_tenants_templates&view=whatsapptenantstemplateform&layout=edit&id=' . $id, false));
			$this->redirect();
		}
		// END OF CUSTOM CODE

		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$this->app->setUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $this->app->getUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.id');
			$this->setMessage(Text::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_dt_whatsapp_tenants_templates&view=whatsapptenantstemplateform&layout=edit&id=' . $id, false));
			$this->redirect();
		}

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
		}

		// Clear the profile id from the session.
		$this->app->setUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.id', null);

		// Redirect to the list screen.
		if (!empty($return))
		{
			$this->setMessage(Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_ITEM_SAVED_SUCCESSFULLY'));
		}
		
		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_dt_whatsapp_tenants_templates&view=whatsapptenantstemplates' : $item->link);
		$this->setRedirect(Route::_($url, false));

		// Flush the data from the session.
		$this->app->setUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.data', null);

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $data);

	}

	/**
	 * Method to abort current operation
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function cancel($key = NULL)
	{

		// Get the current edit id.
		$editId = (int) $this->app->getUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.id');

		// Get the model.
		$model = $this->getModel('Whatsapptenantstemplateform', 'Site');

		// Check in the item
		if ($editId)
		{
			$model->checkin($editId);
		}

		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_dt_whatsapp_tenants_templates&view=whatsapptenantstemplates' : $item->link);
		$this->setRedirect(Route::_($url, false));
	}

	/**
	 * Method to remove data
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	public function remove()
	{
		$model = $this->getModel('Whatsapptenantstemplateform', 'Site');
		$pk    = $this->input->getInt('id');

		// Attempt to save the data
		try
		{
			// Check in before delete
			$return = $model->checkin($return);
			// Clear id from the session.
			$this->app->setUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.id', null);

			$menu = $this->app->getMenu();
			$item = $menu->getActive();
			$url = (empty($item->link) ? 'index.php?option=com_dt_whatsapp_tenants_templates&view=whatsapptenantstemplates' : $item->link);

			if($return)
			{
				// GRAB CONFIG DATA
				$config_model = Factory::getApplication()
					->bootComponent('com_dt_whatsapp_tenants_configs')
					->getMVCFactory()
					->createModel('whatsapptenantsconfig');
				$user    = Factory::getUser();
				$user_id = $user->get('id');
				$config  = $config_model->getItemByUserId($user_id);

				// Grab the item data based on the primary key
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select('name')
					->from('#__dt_whatsapp_tenants_templates')
					->where('id = ' . $db->quote($pk));
				$db->setQuery($query);
				$db->execute();
				$template = $db->loadResult();

				if (empty($template)) {
					throw new Exception("Item not found.");
				}

				if (empty($config)) {
					throw new Exception("No configuration found for user.");
				}

				// Initialize the cURL session
				$ch = curl_init("https://graph.facebook.com/v22.0/{$config->business_account_id}/message_templates?name={$template}");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					"Content-Type: application/json",
					"Authorization: Bearer {$config->token}"
				]);

				// Execute the cURL request
				$response = curl_exec($ch);
				if ($response === false) {
					throw new Exception("cURL error: " . curl_error($ch));
				}

				$jsonResponse = json_decode($response);
				if ($jsonResponse && isset($jsonResponse->error)) {
					$errorMsg    = $jsonResponse->error->message;
					$errorCode   = $jsonResponse->error->code;
					$userErrorMsg = isset($jsonResponse->error->error_user_msg) ? $jsonResponse->error->error_user_msg : "";
					throw new Exception("Facebook API error (code {$errorCode}): {$errorMsg} {$userErrorMsg}");
				} 

				// Close the cURL session
				curl_close($ch);
				// END OF CUSTOM CODE

				$model->delete($pk);
				$this->setMessage(Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_ITEM_DELETED_SUCCESSFULLY'));
			}
			else
			{
				$this->setMessage(Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_ITEM_DELETED_UNSUCCESSFULLY'), 'warning');
			}
			

			$this->setRedirect(Route::_($url, false));
			// Flush the data from the session.
			$this->app->setUserState('com_dt_whatsapp_tenants_templates.edit.whatsapptenantstemplate.data', null);
		}
		catch (\Exception $e)
		{
			$errorType = ($e->getCode() == '404') ? 'error' : 'warning';
			$this->setMessage($e->getMessage(), $errorType);
			$this->setRedirect('index.php?option=com_dt_whatsapp_tenants_templates&view=whatsapptenantstemplates');
		}
	}

	/**
     * Function that allows child controller access to model data
     * after the data has been saved.
     *
     * @param   BaseDatabaseModel  $model      The data model object.
     * @param   array              $validData  The validated data.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function postSaveHook(BaseDatabaseModel $model, $validData = array())
    {
    }
}
