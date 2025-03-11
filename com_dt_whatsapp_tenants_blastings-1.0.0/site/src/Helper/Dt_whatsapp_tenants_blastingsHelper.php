<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_blastings
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Comdtwhatsapptenantsblastings\Component\Dt_whatsapp_tenants_blastings\Site\Helper;

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Class Dt_whatsapp_tenants_blastingsFrontendHelper
 *
 * @since  1.0.0
 */
class Dt_whatsapp_tenants_blastingsHelper
{
	

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets the edit permission for an user
	 *
	 * @param   mixed  $item  The item
	 *
	 * @return  bool
	 */
	public static function canUserEdit($item)
	{
		$permission = false;
		$user       = Factory::getApplication()->getIdentity();

		if ($user->authorise('core.edit', 'com_dt_whatsapp_tenants_blastings') || (isset($item->created_by) && $user->authorise('core.edit.own', 'com_dt_whatsapp_tenants_blastings') && $item->created_by == $user->id) || $user->authorise('core.create', 'com_dt_whatsapp_tenants_blastings'))
		{
			$permission = true;
		}

		return $permission;
	}
}
