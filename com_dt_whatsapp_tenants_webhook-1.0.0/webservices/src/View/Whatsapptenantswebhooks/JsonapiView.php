<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_webhook
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantswebhook\Component\Dt_whatsapp_tenants_webhook\Api\View\Whatsapptenantswebhooks;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\JsonApiView as BaseApiView;

/**
 * The Whatsapptenantswebhooks view
 *
 * @since  1.0.0
 */
class JsonApiView extends BaseApiView
{
	/**
	 * The fields to render item in the documents
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $fieldsToRenderItem = [
		'id', 
		'field', 
		'value', 
		'status', 
	];

	/**
	 * The fields to render items in the documents
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $fieldsToRenderList = [
		'id', 
		'field', 
		'value', 
		'status', 
	];
}