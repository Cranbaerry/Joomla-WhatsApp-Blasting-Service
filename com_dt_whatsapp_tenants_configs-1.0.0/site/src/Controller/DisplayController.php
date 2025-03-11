<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_configs
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantsconfigs\Component\Dt_whatsapp_tenants_configs\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

/**
 * Display Component Controller
 *
 * @since  1.0.0
 */
class DisplayController extends \Joomla\CMS\MVC\Controller\BaseController
{
	/**
	 * Constructor.
	 *
	 * @param  array                $config   An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 * @param  MVCFactoryInterface  $factory  The factory.
	 * @param  CMSApplication       $app      The JApplication for the dispatcher
	 * @param  Input              $input    Input
	 *
	 * @since  1.0.0
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link InputFilter::clean()}.
	 *
	 * @return  \Joomla\CMS\MVC\Controller\BaseController  This object to support chaining.
	 *
	 * @since   1.0.0
	 */
	public function display($cachable = false, $urlparams = false)
	{

		$view = $this->input->getCmd('view', 'whatsapptenantsconfigs');
		$view = $view == "featured" ? 'whatsapptenantsconfigs' : $view;
		$this->input->set('view', $view);
		
		// Check if config exists
		$model = $this->getModel('whatsapptenantsconfig');
		$user  = Factory::getUser();
		$user_id = $user->get('id');
		$config = $model->getItemByUserId($user_id);
		$requested_config_id = (int) $this->app->getUserState('com_dt_whatsapp_tenants_configs.edit.whatsapptenantsconfig.id');
		if (!empty($config) && $requested_config_id > 0 && $config->id != $requested_config_id) {
			$requested_config_id = (int) $this->app->setUserState('com_dt_whatsapp_tenants_configs.edit.whatsapptenantsconfig.id', $config->id);
			throw new \Exception('Unauthorized access');
		}

		if (!empty($user_id)) {
			switch($view) {
				case 'whatsapptenantsconfigs':
					if (!empty($config)) {
						// Redirect to respective edit
						$this->setRedirect('whatsapp-configs?task=whatsapptenantsconfig.edit&id=' . $config->id);
					} else {
						$this->setRedirect('whatsapp-configs?task=whatsapptenantsconfigform.edit&id=0');
					}
					break;
				case 'whatsapptenantsconfig':
					if (empty($config)) {
						$this->setRedirect('whatsapp-configs?task=whatsapptenantsconfigform.edit&id=0');
					}
					break;
			}
		}

		parent::display($cachable, $urlparams);
		return $this;
	}
}