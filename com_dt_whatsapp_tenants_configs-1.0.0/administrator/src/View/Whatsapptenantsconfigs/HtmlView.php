<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_configs
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantsconfigs\Component\Dt_whatsapp_tenants_configs\Administrator\View\Whatsapptenantsconfigs;
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Comdtwhatsapptenantsconfigs\Component\Dt_whatsapp_tenants_configs\Administrator\Helper\Dt_whatsapp_tenants_configsHelper;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\Component\Content\Administrator\Extension\ContentComponent;
use \Joomla\CMS\Form\Form;
use \Joomla\CMS\HTML\Helpers\Sidebar;
/**
 * View class for a list of Whatsapptenantsconfigs.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		$this->sidebar = Sidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = Dt_whatsapp_tenants_configsHelper::getActions();

		ToolbarHelper::title(Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_TITLE_WHATSAPPTENANTSCONFIGS'), "generic");

		$toolbar = Toolbar::getInstance('toolbar');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/src/View/Whatsapptenantsconfigs';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				$toolbar->addNew('whatsapptenantsconfig.add');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fas fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if (isset($this->items[0]->state))
			{
				$childBar->publish('whatsapptenantsconfigs.publish')->listCheck(true);
				$childBar->unpublish('whatsapptenantsconfigs.unpublish')->listCheck(true);
				$childBar->archive('whatsapptenantsconfigs.archive')->listCheck(true);
			}

			$childBar->standardButton('duplicate')
				->text('JTOOLBAR_DUPLICATE')
				->icon('fas fa-copy')
				->task('whatsapptenantsconfigs.duplicate')
				->listCheck(true);

			if (isset($this->items[0]->checked_out))
			{
				$childBar->checkin('whatsapptenantsconfigs.checkin')->listCheck(true);
			}

			if (isset($this->items[0]->state))
			{
				$childBar->trash('whatsapptenantsconfigs.trash')->listCheck(true);
			}
		}

		

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{

			if ($this->state->get('filter.state') == ContentComponent::CONDITION_TRASHED && $canDo->get('core.delete'))
			{
				$toolbar->delete('whatsapptenantsconfigs.delete')
					->text('JTOOLBAR_EMPTY_TRASH')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}
		}

		if ($canDo->get('core.admin'))
		{
			$toolbar->preferences('com_dt_whatsapp_tenants_configs');
		}

		// Set sidebar action
		Sidebar::setAction('index.php?option=com_dt_whatsapp_tenants_configs&view=whatsapptenantsconfigs');
	}
	
	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => Text::_('JGRID_HEADING_ID'),
			'a.`ordering`' => Text::_('JGRID_HEADING_ORDERING'),
			'a.`callback_url`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_CALLBACK_URL'),
			'a.`forward_url`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_FORWARD_URL'),
			'a.`app_id`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_APP_ID'),
			'a.`phone_number_id`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_PHONE_NUMBER_ID'),
			'a.`business_account_id`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_BUSINESS_ACCOUNT_ID'),
			'a.`token`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_TOKEN'),
			'a.`phone_number`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_PHONE_NUMBER'),
			'a.`user_id`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_USER_ID'),
			'a.`dreamztrack_endpoint`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_DREAMZTRACK_ENDPOINT'),
			'a.`dreamztrack_branch`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_DREAMZTRACK_BRANCH'),
			'a.`dreamztrack_key`' => Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_DREAMZTRACK_KEY'),
		);
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return bool
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}
}
