<?php
/**
 * @package    Com_Dt_whatsapp_tenants_webhook
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\ApiRouter;

/**
 * Web Services adapter for dt_whatsapp_tenants_webhook.
 *
 * @since  1.0.0
 */
class PlgWebservicesDt_whatsapp_tenants_webhook extends CMSPlugin
{
	public function onBeforeApiRoute(&$router)
	{
		
		$router->createCRUDRoutes('v1/dt_whatsapp_tenants_webhook/whatsapptenantswebhooks', 'whatsapptenantswebhooks', ['component' => 'com_dt_whatsapp_tenants_webhook']);
	}
}
