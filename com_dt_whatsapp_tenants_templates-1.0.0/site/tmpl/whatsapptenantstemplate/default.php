<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_templates
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

$canEdit = Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_dt_whatsapp_tenants_templates');

if (!$canEdit && Factory::getApplication()->getIdentity()->authorise('core.edit.own', 'com_dt_whatsapp_tenants_templates'))
{
	$canEdit = Factory::getApplication()->getIdentity()->id == $this->item->created_by;
}
?>

<div class="item_fields">
<?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
    <?php endif;?>
	<table class="table">
		

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_FORM_LBL_WHATSAPPTENANTSTEMPLATE_NAME'); ?></th>
			<td><?php echo $this->item->name; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_FORM_LBL_WHATSAPPTENANTSTEMPLATE_LANGUAGE'); ?></th>
			<td>
			<?php

			if (!empty($this->item->language) || $this->item->language === 0)
			{
				echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_WHATSAPPTENANTSTEMPLATES_LANGUAGE_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$this->item->language))));
			}
			?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_FORM_LBL_WHATSAPPTENANTSTEMPLATE_CATEGORY'); ?></th>
			<td>
			<?php

			if (!empty($this->item->category) || $this->item->category === 0)
			{
				echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_WHATSAPPTENANTSTEMPLATES_CATEGORY_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$this->item->category))));
			}
			?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_FORM_LBL_WHATSAPPTENANTSTEMPLATE_HEADER_TYPE'); ?></th>
			<td>
			<?php

			if (!empty($this->item->header_type) || $this->item->header_type === 0)
			{
				echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_WHATSAPPTENANTSTEMPLATES_HEADER_TYPE_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$this->item->header_type))));
			}
			?></td>
		</tr>
		<?php if ($this->item->header_type == "TEXT"): ?>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_FORM_LBL_WHATSAPPTENANTSTEMPLATE_HEADER_TEXT'); ?></th>
			<td><?php echo $this->item->header_text; ?>
			</td>
		</tr>

		<?php endif; ?>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_FORM_LBL_WHATSAPPTENANTSTEMPLATE_HEADER_MEDIA'); ?></th>
			<td>
			<?php
			foreach ((array) $this->item->header_media as $singleFile) : 
				if (!is_array($singleFile)) : 
					$uploadPath = 'uploads' . DIRECTORY_SEPARATOR . $this->item->created_by . DIRECTORY_SEPARATOR . $singleFile;
					 echo '<a href="' . Route::_(Uri::root() . $uploadPath, false) . '" target="_blank">' . $singleFile . '</a> ';
				endif;
			endforeach;
		?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_FORM_LBL_WHATSAPPTENANTSTEMPLATE_BODY'); ?></th>
			<td><?php echo nl2br($this->item->body); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_FORM_LBL_WHATSAPPTENANTSTEMPLATE_FOOTER'); ?></th>
			<td><?php echo $this->item->footer; ?></td>
		</tr>

	</table>

</div>

<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_dt_whatsapp_tenants_templates.' . $this->item->id) || $this->item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
	<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_templates&task=whatsapptenantstemplate.edit&id='.$this->item->id); ?>"><?php echo Text::_("COM_DT_WHATSAPP_TENANTS_TEMPLATES_EDIT_ITEM"); ?></a>
	<?php elseif($canCheckin && $this->item->checked_out > 0) : ?>
	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_templates&task=whatsapptenantstemplate.checkin&id=' . $this->item->id .'&'. Session::getFormToken() .'=1'); ?>"><?php echo Text::_("JLIB_HTML_CHECKIN"); ?></a>

<?php endif; ?>

<?php if (Factory::getApplication()->getIdentity()->authorise('core.delete','com_dt_whatsapp_tenants_templates.whatsapptenantstemplate.'.$this->item->id)) : ?>

	<a class="btn btn-danger" rel="noopener noreferrer" href="#deleteModal" role="button" data-bs-toggle="modal">
		<?php echo Text::_("COM_DT_WHATSAPP_TENANTS_TEMPLATES_DELETE_ITEM"); ?>
	</a>

	<?php echo HTMLHelper::_(
                                    'bootstrap.renderModal',
                                    'deleteModal',
                                    array(
                                        'title'  => Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_DELETE_ITEM'),
                                        'height' => '50%',
                                        'width'  => '20%',
                                        
                                        'modalWidth'  => '50',
                                        'bodyHeight'  => '100',
                                        'footer' => '<button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button><a href="' . Route::_('index.php?option=com_dt_whatsapp_tenants_templates&task=whatsapptenantstemplate.remove&id=' . $this->item->id, false, 2) .'" class="btn btn-danger">' . Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_DELETE_ITEM') .'</a>'
                                    ),
                                    Text::sprintf('COM_DT_WHATSAPP_TENANTS_TEMPLATES_DELETE_CONFIRM', $this->item->id)
                                ); ?>

<?php endif; ?>