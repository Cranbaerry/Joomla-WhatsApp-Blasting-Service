<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_blastings
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantsblastings\Component\Dt_whatsapp_tenants_blastings\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use Comdtwhatsapptenantsblastings\Component\Dt_whatsapp_tenants_blastings\Administrator\Helper\Dt_whatsapp_tenants_blastingsHelper;

/**
 * Methods supporting a list of Whatsapptenantsscheduledmessages records.
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
	* @see        JController
	* @since      1.6
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
            }catch(Exception $exc){
                return false;
            }
        }

	
        /**
         * This method revises if the $id of the item belongs to the current user
         * @param   integer     $id     The id of the item
         * @return  boolean             true if the user is the owner of the row, false if not.
         *
         */
        public function userIDItem($id){
            try{
                $user = Factory::getApplication()->getIdentity();                
                $db    = $this->getDbo();

                $query = $db->getQuery(true);
                $query->select("id")
                      ->from($db->quoteName('#__dt_whatsapp_tenants_scheduled_messages'))
                      ->where("id = " . $db->escape($id))
                      ->where("created_by = " . $user->id);

                $db->setQuery($query);

                $results = $db->loadObject();
                if ($results){
                    return true;
                }else{
                    return false;
                }
            }catch(\Exception $exc){
                return false;
            }
        }

	
        /**
         * This method to check if there are items created by the current user.
         * @return  boolean             true if the user created one of the item, false if not.
         *
         */
        public function isUserCreatedItem(){
            try{
                $user = Factory::getApplication()->getIdentity();                
                $db    = $this->getDbo();                
                $query = $db->getQuery(true);
                $query->select("id")
                      ->from($db->quoteName('#__dt_whatsapp_tenants_scheduled_messages'))
                      ->where("created_by = " . $user->id);

                $db->setQuery($query);

                $results = $db->loadObject();
                if ($results){
                    return true;
                }else{
                    return false;
                }
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
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('id', 'DESC');

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
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		if(!$id || $this->userIDItem($id) || $this->isAdminOrSuperUser() || $this->isUserCreatedItem() || Factory::getUser()->authorise('core.manage', 'com_dt_whatsapp_tenants_blastings')){
		return parent::getStoreId($id);
		}else{
                               throw new \Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
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
		
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");
		$query->select("a.created_by AS created_by_user");
		if(!$this->isAdminOrSuperUser()){
			$query->where("a.created_by = " . Factory::getUser()->get("id"));
		}

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		// Join over the foreign key 'template_id'
		$query->select('`#__dt_whatsapp_tenants_templates_4168298`.`name` AS #__dt_whatsapp_tenants_templates_fk_value_4168298');
		$query->join('LEFT', '#__dt_whatsapp_tenants_templates AS #__dt_whatsapp_tenants_templates_4168298 ON #__dt_whatsapp_tenants_templates_4168298.`id` = a.`template_id`');
		// Join over the foreign key 'keyword_id'
		$query->select('`#__dt_whatsapp_tenants_keywords_4169744`.`name` AS whatsapptenantskeywords_fk_value_4169744');
		$query->join('LEFT', '#__dt_whatsapp_tenants_keywords AS #__dt_whatsapp_tenants_keywords_4169744 ON #__dt_whatsapp_tenants_keywords_4169744.`id` = a.`keyword_id`');
		// Join over the foreign key 'blasting_id'
		$query->select('`#__dt_whatsapp_tenants_blastings_4168833`.`id` AS whatsapptenantsblastings_fk_value_4168833');
		$query->join('LEFT', '#__dt_whatsapp_tenants_blastings AS #__dt_whatsapp_tenants_blastings_4168833 ON #__dt_whatsapp_tenants_blastings_4168833.`id` = a.`blasting_id`');
		

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif (empty($published))
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
				$query->where('( a.target_phone_number LIKE ' . $search . '  OR #__dt_whatsapp_tenants_templates_4168298.name LIKE ' . $search . '  OR  a.keyword_message LIKE ' . $search . ' )');
			}
		}
		

		// Filtering type
		$filter_type = $this->state->get("filter.type");

		if ($filter_type !== null && (is_numeric($filter_type) || !empty($filter_type)))
		{
			$query->where("a.`type` = '".$db->escape($filter_type)."'");
		}

		// Filtering template_id
		$filter_template_id = $this->state->get("filter.template_id");

		if ($filter_template_id !== null && !empty($filter_template_id))
		{
			$query->where("a.`template_id` = '".$db->escape($filter_template_id)."'");
		}

		// Filtering blasting_id
		$filter_blasting_id = $this->state->get("filter.blasting_id");

		if ($filter_blasting_id !== null && !empty($filter_blasting_id))
		{
			$query->where("a.`blasting_id` = '".$db->escape($filter_blasting_id)."'");
		}

		// Filtering status
		$filter_status = $this->state->get("filter.status");

		if ($filter_status !== null && (is_numeric($filter_status) || !empty($filter_status)))
		{
			$query->where("a.`status` = '".$db->escape($filter_status)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $oneItem)
		{
					$oneItem->type = !empty($oneItem->type) ? Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_TYPE_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$oneItem->type)))) : '';
					$oneItem->status = !empty($oneItem->status) ? Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_STATUS_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$oneItem->status)))) : '';
		}

		return $items;
	}
}
