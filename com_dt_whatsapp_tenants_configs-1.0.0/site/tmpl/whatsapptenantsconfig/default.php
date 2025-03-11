<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_configs
 * @author     dreamztech <support@dreamztech.com.my>
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;


?>

<div class="item_fields">
<?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
    <?php endif;?>
	<table class="table">
		

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_CALLBACK_URL'); ?></th>
			<td><?php echo $this->item->callback_url; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_FORWARD_URL'); ?></th>
			<td><?php echo $this->item->forward_url; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_APP_ID'); ?></th>
			<td><?php echo $this->item->app_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_PHONE_NUMBER_ID'); ?></th>
			<td><?php echo $this->item->phone_number_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_BUSINESS_ACCOUNT_ID'); ?></th>
			<td><?php echo $this->item->business_account_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_TOKEN'); ?></th>
			<td><?php echo $this->item->token; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_PHONE_NUMBER'); ?></th>
			<td><?php echo $this->item->phone_number; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_USER_ID'); ?></th>
			<td><?php echo $this->item->user_id_name; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_DREAMZTRACK_ENDPOINT'); ?></th>
			<td>
			<?php

			if (!empty($this->item->dreamztrack_endpoint) || $this->item->dreamztrack_endpoint === 0)
			{
				echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_DREAMZTRACK_ENDPOINT_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$this->item->dreamztrack_endpoint))));
			}
			?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_DREAMZTRACK_BRANCH'); ?></th>
			<td><?php echo $this->item->dreamztrack_branch; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_DREAMZTRACK_KEY'); ?></th>
			<td><?php echo $this->item->dreamztrack_key; ?></td>
		</tr>

	</table>

</div>

