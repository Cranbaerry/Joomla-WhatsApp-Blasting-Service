<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_webhook
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Comdtwhatsapptenantswebhook\Component\Dt_whatsapp_tenants_webhook\Api\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\ApiController;

/**
 * The Whatsapptenantswebhooks controller
 *
 * @since  1.0.0
 */
class WhatsapptenantswebhooksController extends ApiController 
{
	/**
	 * The content type of the item.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $contentType = 'whatsapptenantswebhooks';

	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $default_view = 'whatsapptenantswebhooks';
}