<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_webhook
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantswebhook\Component\Dt_whatsapp_tenants_webhook\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

/**
 * Whatsapptenantswebhooks list controller class.
 *
 * @since  1.0.0
 */
class WhatsapptenantswebhooksController extends AdminController
{
	/**
	 * Method to clone existing Whatsapptenantswebhooks
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 */
	public function duplicate()
	{
		// Check for request forgeries
		$this->checkToken();

		// Get id(s)
		$pks = $this->input->post->get('cid', array(), 'array');

		try
		{
			if (empty($pks))
			{
				throw new \Exception(Text::_('COM_DT_WHATSAPP_TENANTS_WEBHOOK_NO_ELEMENT_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(Text::_('COM_DT_WHATSAPP_TENANTS_WEBHOOK_ITEMS_SUCCESS_DUPLICATED'));
		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_dt_whatsapp_tenants_webhook&view=whatsapptenantswebhooks');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since   1.0.0
	 */
	public function getModel($name = 'Whatsapptenantswebhook', $prefix = 'Administrator', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$pks   = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		Factory::getApplication()->close();
	}
}
