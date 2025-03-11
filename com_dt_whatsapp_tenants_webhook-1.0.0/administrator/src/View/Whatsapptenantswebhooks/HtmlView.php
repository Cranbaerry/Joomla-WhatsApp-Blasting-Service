<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_webhook
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantswebhook\Component\Dt_whatsapp_tenants_webhook\Administrator\View\Whatsapptenantswebhooks;
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Comdtwhatsapptenantswebhook\Component\Dt_whatsapp_tenants_webhook\Administrator\Helper\Dt_whatsapp_tenants_webhookHelper;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\Component\Content\Administrator\Extension\ContentComponent;
use \Joomla\CMS\Form\Form;
use \Joomla\CMS\HTML\Helpers\Sidebar;
/**
 * View class for a list of Whatsapptenantswebhooks.
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
		$canDo = Dt_whatsapp_tenants_webhookHelper::getActions();

		ToolbarHelper::title(Text::_('COM_DT_WHATSAPP_TENANTS_WEBHOOK_TITLE_WHATSAPPTENANTSWEBHOOKS'), "generic");

		$toolbar = Toolbar::getInstance('toolbar');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/src/View/Whatsapptenantswebhooks';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				$toolbar->addNew('whatsapptenantswebhook.add');
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
				$childBar->publish('whatsapptenantswebhooks.publish')->listCheck(true);
				$childBar->unpublish('whatsapptenantswebhooks.unpublish')->listCheck(true);
				$childBar->archive('whatsapptenantswebhooks.archive')->listCheck(true);
			}

			$childBar->standardButton('duplicate')
				->text('JTOOLBAR_DUPLICATE')
				->icon('fas fa-copy')
				->task('whatsapptenantswebhooks.duplicate')
				->listCheck(true);

			if (isset($this->items[0]->checked_out))
			{
				$childBar->checkin('whatsapptenantswebhooks.checkin')->listCheck(true);
			}

			if (isset($this->items[0]->state))
			{
				$childBar->trash('whatsapptenantswebhooks.trash')->listCheck(true);
			}
		}

		

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{

			if ($this->state->get('filter.state') == ContentComponent::CONDITION_TRASHED && $canDo->get('core.delete'))
			{
				$toolbar->delete('whatsapptenantswebhooks.delete')
					->text('JTOOLBAR_EMPTY_TRASH')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}
		}

		if ($canDo->get('core.admin'))
		{
			$toolbar->preferences('com_dt_whatsapp_tenants_webhook');
		}

		// Set sidebar action
		Sidebar::setAction('index.php?option=com_dt_whatsapp_tenants_webhook&view=whatsapptenantswebhooks');
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
			'a.`field`' => Text::_('COM_DT_WHATSAPP_TENANTS_WEBHOOK_WHATSAPPTENANTSWEBHOOKS_FIELD'),
			'a.`value`' => Text::_('COM_DT_WHATSAPP_TENANTS_WEBHOOK_WHATSAPPTENANTSWEBHOOKS_VALUE'),
			'a.`status`' => Text::_('COM_DT_WHATSAPP_TENANTS_WEBHOOK_WHATSAPPTENANTSWEBHOOKS_STATUS'),
			'a.`detail`' => Text::_('COM_DT_WHATSAPP_TENANTS_WEBHOOK_WHATSAPPTENANTSWEBHOOKS_DETAIL'),
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
