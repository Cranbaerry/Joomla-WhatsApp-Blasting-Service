<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_blastings
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
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSSCHEDULEDMESSAGE_TARGET_PHONE_NUMBER'); ?></th>
			<td><?php echo $this->item->target_phone_number; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSSCHEDULEDMESSAGE_TYPE'); ?></th>
			<td>
			<?php

			if (!empty($this->item->type) || $this->item->type === 0)
			{
				echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_TYPE_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$this->item->type))));
			}
			?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSSCHEDULEDMESSAGE_TEMPLATE_ID'); ?></th>
			<td><?php echo $this->item->template_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSSCHEDULEDMESSAGE_KEYWORD_ID'); ?></th>
			<td><?php echo $this->item->keyword_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSSCHEDULEDMESSAGE_KEYWORD_MESSAGE'); ?></th>
			<td><?php echo nl2br($this->item->keyword_message); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSSCHEDULEDMESSAGE_BLASTING_ID'); ?></th>
			<td><?php echo $this->item->blasting_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSSCHEDULEDMESSAGE_STATUS'); ?></th>
			<td>
			<?php

			if (!empty($this->item->status) || $this->item->status === 0)
			{
				echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_STATUS_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$this->item->status))));
			}
			?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSSCHEDULEDMESSAGE_RAW_RESPONSE'); ?></th>
			<td><?php echo nl2br($this->item->raw_response); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSSCHEDULEDMESSAGE_SCHEDULED_TIME'); ?></th>
			<td><?php echo $this->item->scheduled_time; ?></td>
		</tr>

	</table>

</div>

