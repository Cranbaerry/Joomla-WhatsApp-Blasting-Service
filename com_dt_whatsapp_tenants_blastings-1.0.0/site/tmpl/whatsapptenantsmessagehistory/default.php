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
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSMESSAGEHISTORY_FROM'); ?></th>
			<td><?php echo $this->item->from; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSMESSAGEHISTORY_PHONE_NUMBER_ID'); ?></th>
			<td><?php echo $this->item->phone_number_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSMESSAGEHISTORY_TIMESTAMP'); ?></th>
			<td><?php echo $this->item->timestamp; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSMESSAGEHISTORY_TEXT'); ?></th>
			<td><?php echo nl2br($this->item->text); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSMESSAGEHISTORY_TYPE'); ?></th>
			<td><?php echo $this->item->type; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSMESSAGEHISTORY_MEDIA_CAPTION'); ?></th>
			<td><?php echo $this->item->media_caption; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSMESSAGEHISTORY_ERRORS'); ?></th>
			<td><?php echo nl2br($this->item->errors); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FORM_LBL_WHATSAPPTENANTSMESSAGEHISTORY_RAW_RESPONSE'); ?></th>
			<td><?php echo nl2br($this->item->raw_response); ?></td>
		</tr>

	</table>

</div>

