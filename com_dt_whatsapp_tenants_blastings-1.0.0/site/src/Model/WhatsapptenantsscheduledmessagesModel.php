<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_blastings
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantsblastings\Component\Dt_whatsapp_tenants_blastings\Site\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Layout\FileLayout;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use \Comdtwhatsapptenantsblastings\Component\Dt_whatsapp_tenants_blastings\Site\Helper\Dt_whatsapp_tenants_blastingsHelper;


/**
 * Methods supporting a list of Dt_whatsapp_tenants_blastings records.
 *
 * @since  1.0.0
 */
class WhatsapptenantsscheduledmessagesModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see    JController
	 * @since  1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'target_phone_number', 'a.target_phone_number',
				'type', 'a.type',
				'template_id', 'a.template_id',
				'keyword_id', 'a.keyword_id',
				'keyword_message', 'a.keyword_message',
				'blasting_id', 'a.blasting_id',
				'status', 'a.status',
				'raw_response', 'a.raw_response',
				'scheduled_time', 'a.scheduled_time',
			);
		}

		parent::__construct($config);
	}

	
       /**
        * Checks whether or not a user is manager or super user
        *
        * @return bool
        */
        public function isAdminOrSuperUser()
        {
            try{
                $user = Factory::getApplication()->getIdentity();
                return in_array("8", $user->groups) || in_array("7", $user->groups);
            }catch(\Exception $exc){
                return false;
            }
        }

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.id', 'DESC');

		$app = Factory::getApplication();
		$list = $app->getUserState($this->context . '.list');

		$value = $app->getUserState($this->context . '.list.limit', $app->get('list_limit', 25));
		$list['limit'] = $value;
		
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$ordering  = $this->getUserStateFromRequest($this->context .'.filter_order', 'filter_order', 'a.id');
		$direction = strtoupper($this->getUserStateFromRequest($this->context .'.filter_order_Dir', 'filter_order_Dir', 'DESC'));
		
		if(!empty($ordering) || !empty($direction))
		{
			$list['fullordering'] = $ordering . ' ' . $direction;
		}

		$app->setUserState($this->context . '.list', $list);

		

		$context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $context);

		// Split context into component and optional section
		if (!empty($context))
		{
			$parts = FieldsHelper::extract($context);

			if ($parts)
			{
				$this->setState('filter.component', $parts[0]);
				$this->setState('filter.section', $parts[1]);
			}
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
			// Create a new query object.
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			// Select the required fields from the table.
			$query->select(
						$this->getState(
								'list.select', 'DISTINCT a.*'
						)
				);

			$query->from('`#__dt_whatsapp_tenants_scheduled_messages` AS a');
			
		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
		// Join over the foreign key 'template_id'
		$query->select('`#__dt_whatsapp_tenants_templates_4168298`.`name` AS #__dt_whatsapp_tenants_templates_fk_value_4168298');
		$query->join('LEFT', '#__dt_whatsapp_tenants_templates AS #__dt_whatsapp_tenants_templates_4168298 ON #__dt_whatsapp_tenants_templates_4168298.`id` = a.`template_id`');
		// Join over the foreign key 'keyword_id'
		$query->select('`#__dt_whatsapp_tenants_keywords_4169744`.`name` AS whatsapptenantskeywords_fk_value_4169744');
		$query->join('LEFT', '#__dt_whatsapp_tenants_keywords AS #__dt_whatsapp_tenants_keywords_4169744 ON #__dt_whatsapp_tenants_keywords_4169744.`id` = a.`keyword_id`');
		// Join over the foreign key 'blasting_id'
		$query->select('`#__dt_whatsapp_tenants_blastings_4168833`.`id` AS whatsapptenantsblastings_fk_value_4168833');
		$query->join('LEFT', '#__dt_whatsapp_tenants_blastings AS #__dt_whatsapp_tenants_blastings_4168833 ON #__dt_whatsapp_tenants_blastings_4168833.`id` = a.`blasting_id`');
		if(!$this->isAdminOrSuperUser()){
			$query->where("a.created_by = " . Factory::getApplication()->getIdentity()->get("id"));
		}
			
		if (!Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_dt_whatsapp_tenants_blastings'))
		{
			$query->where('a.state = 1');
		}
		else
		{
			$query->where('(a.state IN (0, 1))');
		}

			// Filter by search in title
			$search = $this->getState('filter.search');

			if (!empty($search))
			{
				if (stripos($search, 'id:') === 0)
				{
					$query->where('a.id = ' . (int) substr($search, 3));
				}
				else
				{
					$search = $db->Quote('%' . $db->escape($search, true) . '%');
					$query->where('( a.target_phone_number LIKE ' . $search . ' )');
				}
			}
			

		// Filtering type
		$filter_type = $this->state->get("filter.type");
		if ($filter_type != '') {
			$query->where("a.`type` = '".$db->escape($filter_type)."'");
		}

		// Filtering status
		$filter_status = $this->state->get("filter.status");
		if ($filter_status != '') {
			$query->where("a.`status` = '".$db->escape($filter_status)."'");
		}

			
			
			// Add the list ordering clause.
			$orderCol  = $this->state->get('list.ordering', 'a.id');
			$orderDirn = $this->state->get('list.direction', 'DESC');

			if ($orderCol && $orderDirn)
			{
				$query->order($db->escape($orderCol . ' ' . $orderDirn));
			}

			return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $item)
		{

				if (!empty($item->type))
					{
						$item->type = Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_TYPE_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$item->type))));
					}

			if (isset($item->template_id))
			{

				$values    = explode(',', $item->template_id);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = $this->getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__dt_whatsapp_tenants_templates_4168298`.`name`')
						->from($db->quoteName('#__dt_whatsapp_tenants_templates', '#__dt_whatsapp_tenants_templates_4168298'))
						->where($db->quoteName('#__dt_whatsapp_tenants_templates_4168298.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->name;
					}
				}

				$item->template_id = !empty($textValue) ? implode(', ', $textValue) : $item->template_id;
			}


			if (isset($item->keyword_id))
			{

				$values    = explode(',', $item->keyword_id);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = $this->getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__dt_whatsapp_tenants_keywords_4169744`.`name`')
						->from($db->quoteName('#__dt_whatsapp_tenants_keywords', '#__dt_whatsapp_tenants_keywords_4169744'))
						->where($db->quoteName('#__dt_whatsapp_tenants_keywords_4169744.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->name;
					}
				}

				$item->keyword_id = !empty($textValue) ? implode(', ', $textValue) : $item->keyword_id;
			}


			if (isset($item->blasting_id))
			{

				$values    = explode(',', $item->blasting_id);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = $this->getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__dt_whatsapp_tenants_blastings_4168833`.`id`')
						->from($db->quoteName('#__dt_whatsapp_tenants_blastings', '#__dt_whatsapp_tenants_blastings_4168833'))
						->where($db->quoteName('#__dt_whatsapp_tenants_blastings_4168833.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->id;
					}
				}

				$item->blasting_id = !empty($textValue) ? implode(', ', $textValue) : $item->blasting_id;
			}


				if (!empty($item->status))
					{
						$item->status = Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_STATUS_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$item->status))));
					}
		}

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = Factory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(Text::_("COM_DT_WHATSAPP_TENANTS_BLASTINGS_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
	}
}
