<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_blastings
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantsblastings\Component\Dt_whatsapp_tenants_blastings\Administrator\View\Whatsapptenantsscheduledmessages;
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Comdtwhatsapptenantsblastings\Component\Dt_whatsapp_tenants_blastings\Administrator\Helper\Dt_whatsapp_tenants_blastingsHelper;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\Component\Content\Administrator\Extension\ContentComponent;
use \Joomla\CMS\Form\Form;
use \Joomla\CMS\HTML\Helpers\Sidebar;
/**
 * View class for a list of Whatsapptenantsscheduledmessages.
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
		$canDo = Dt_whatsapp_tenants_blastingsHelper::getActions();

		ToolbarHelper::title(Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_TITLE_WHATSAPPTENANTSSCHEDULEDMESSAGES'), "generic");

		$toolbar = Toolbar::getInstance('toolbar');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/src/View/Whatsapptenantsscheduledmessages';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				$toolbar->addNew('whatsapptenantsscheduledmessage.add');
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
				$childBar->publish('whatsapptenantsscheduledmessages.publish')->listCheck(true);
				$childBar->unpublish('whatsapptenantsscheduledmessages.unpublish')->listCheck(true);
				$childBar->archive('whatsapptenantsscheduledmessages.archive')->listCheck(true);
			}

			$childBar->standardButton('duplicate')
				->text('JTOOLBAR_DUPLICATE')
				->icon('fas fa-copy')
				->task('whatsapptenantsscheduledmessages.duplicate')
				->listCheck(true);

			if (isset($this->items[0]->checked_out))
			{
				$childBar->checkin('whatsapptenantsscheduledmessages.checkin')->listCheck(true);
			}

			if (isset($this->items[0]->state))
			{
				$childBar->trash('whatsapptenantsscheduledmessages.trash')->listCheck(true);
			}
		}

		

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{

			if ($this->state->get('filter.state') == ContentComponent::CONDITION_TRASHED && $canDo->get('core.delete'))
			{
				$toolbar->delete('whatsapptenantsscheduledmessages.delete')
					->text('JTOOLBAR_EMPTY_TRASH')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}
		}

		if ($canDo->get('core.admin'))
		{
			$toolbar->preferences('com_dt_whatsapp_tenants_blastings');
		}

		// Set sidebar action
		Sidebar::setAction('index.php?option=com_dt_whatsapp_tenants_blastings&view=whatsapptenantsscheduledmessages');
	}
	
	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`ordering`' => Text::_('JGRID_HEADING_ORDERING'),
			'a.`target_phone_number`' => Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_TARGET_PHONE_NUMBER'),
			'a.`type`' => Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_TYPE'),
			'a.`template_id`' => Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_TEMPLATE_ID'),
			'a.`keyword_id`' => Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_KEYWORD_ID'),
			'a.`keyword_message`' => Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_KEYWORD_MESSAGE'),
			'a.`status`' => Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_STATUS'),
			'a.`scheduled_time`' => Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_SCHEDULED_TIME'),
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
