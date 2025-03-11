<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_blastings
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantsblastings\Component\Dt_whatsapp_tenants_blastings\Site\Controller;

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

/**
 * Whatsapptenantsblasting class.
 *
 * @since  1.0.0
 */
class WhatsapptenantsblastingformController extends FormController
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
		$previousId = (int) $this->app->getUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.id');
		$editId     = $this->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$this->app->setUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.id', $editId);

		// Get the model.
		$model = $this->getModel('Whatsapptenantsblastingform', 'Site');

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
		$this->setRedirect(Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&view=whatsapptenantsblastingform&layout=edit', false));
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
		$model = $this->getModel('Whatsapptenantsblastingform', 'Site');

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
			$this->app->setUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.data', $jform);

			// Redirect back to the edit screen.
			$id = (int) $this->app->getUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.id');
			$this->setRedirect(Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&view=whatsapptenantsblastingform&layout=edit&id=' . $id, false));

			$this->redirect();
		}

		$contactsRaw = $this->input->post->get('contacts', [], 'array');
		$contactsData = json_decode($contactsRaw[0], true);
		$user = Factory::getUser();
		$currentUserId = $user->id;
		$db = Factory::getDbo();

		// Insert to contacts
		foreach ($contactsData as $contact) {
			$phone = $contact['phone'];
			$name  = $contact['name'];

			$query = $db->getQuery(true);
			$query->select($db->qn('id'))
				->from($db->qn('#__dt_whatsapp_tenants_contacts'))
				->where($db->qn('phone_number') . ' = ' . $db->q($phone))
				->where($db->qn('created_by') . ' = ' . (int) $currentUserId);
			$db->setQuery($query);
			$existingId = $db->loadResult();
			if ($existingId) {
				$columns = [
					$db->qn('name') . ' = ' . $db->q($name),
					$db->qn('last_updated') . ' = ' . $db->q(date('Y-m-d H:i:s'))
				];
				$query = $db->getQuery(true)
					->update($db->qn('#__dt_whatsapp_tenants_contacts'))
					->set(implode(', ', $columns))
					->where($db->qn('id') . ' = ' . (int) $existingId)
					->where($db->qn('created_by') . ' = ' . (int) $currentUserId);
				$db->setQuery($query);
				$result = $db->execute();
			} else {
				$columns = ['phone_number', 'name', 'created_by', 'last_updated'];
				$query = $db->getQuery(true)
					->insert($db->qn('#__dt_whatsapp_tenants_contacts'))
					->columns(implode(', ', array_map([$db, 'qn'], $columns)))
					->values(implode(', ', [
						$db->q($phone),
						$db->q($name),
						$db->q($currentUserId),
						$db->q(date('Y-m-d H:i:s'))
					]));
				$db->setQuery($query);
				$result = $db->execute();
			}
		}
		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$this->app->setUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $this->app->getUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.id');
			$this->setMessage(Text::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&view=whatsapptenantsblastingform&layout=edit&id=' . $id, false));
			$this->redirect();
		}

		// Insert to scheduled messages
		foreach ($contactsData as $contact) {
			$targetPhone = $contact['phone'];
			$query = $db->getQuery(true);
			$columns = [
				'state'             => $db->q(1),
				'ordering'          => $db->q(0),
				'checked_out'       => $db->q(0),
				'checked_out_time'  => $db->q('0000-00-00 00:00:00'),
				'created_by'        => $db->q($currentUserId),
				'modified_by'       => $db->q($currentUserId),
				'target_phone_number'=> $db->q($targetPhone),
				'template_id'       => $db->q($data['template_id']),
				'status'            => $db->q('QUEUED'),
				'raw_response'      => $db->q(''),
				'blasting_id'       => $db->q($return),
				'type'              => $db->q('TEMPLATE'),
				'scheduled_time'    => $db->q($data['scheduled_time'])
			];
			$cols = array_map([$db, 'qn'], array_keys($columns));
			$vals = array_values($columns);
			$query->insert($db->qn('#__dt_whatsapp_tenants_scheduled_messages'))
				->columns(implode(', ', $cols))
				->values(implode(', ', $vals));
			$db->setQuery($query);
			$db->execute();
		}
				
		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
		}

		// Clear the profile id from the session.
		$this->app->setUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.id', null);

		// Redirect to the list screen.
		if (!empty($return))
		{
			$this->setMessage(Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_ITEM_SAVED_SUCCESSFULLY'));
		}
		
		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		//$url  = (empty($item->link) ? 'index.php?option=com_dt_whatsapp_tenants_blastings&view=whatsapptenantsblastings' : $item->link);
		$url  = (empty($item->link) ? 'dashboard?view=whatsapptenantsblastings' : $item->link);
		$this->setRedirect(Route::_($url, false));

		// Flush the data from the session.
		$this->app->setUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.data', null);

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
		$editId = (int) $this->app->getUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.id');

		// Get the model.
		$model = $this->getModel('Whatsapptenantsblastingform', 'Site');

		// Check in the item
		if ($editId)
		{
			$model->checkin($editId);
		}

		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_dt_whatsapp_tenants_blastings&view=whatsapptenantsblastings' : $item->link);
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
		$model = $this->getModel('Whatsapptenantsblastingform', 'Site');
		$pk    = $this->input->getInt('id');

		// Attempt to save the data
		try
		{
			// Check in before delete
			$return = $model->checkin($return);
			// Clear id from the session.
			$this->app->setUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.id', null);

			$menu = $this->app->getMenu();
			$item = $menu->getActive();
			$url = (empty($item->link) ? 'index.php?option=com_dt_whatsapp_tenants_blastings&view=whatsapptenantsblastings' : $item->link);

			if($return)
			{
				$model->delete($pk);
				$this->setMessage(Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_ITEM_DELETED_SUCCESSFULLY'));
			}
			else
			{
				$this->setMessage(Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_ITEM_DELETED_UNSUCCESSFULLY'), 'warning');
			}
			

			$this->setRedirect(Route::_($url, false));
			// Flush the data from the session.
			$this->app->setUserState('com_dt_whatsapp_tenants_blastings.edit.whatsapptenantsblasting.data', null);
		}
		catch (\Exception $e)
		{
			$errorType = ($e->getCode() == '404') ? 'error' : 'warning';
			$this->setMessage($e->getMessage(), $errorType);
			$this->setRedirect('index.php?option=com_dt_whatsapp_tenants_blastings&view=whatsapptenantsblastings');
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