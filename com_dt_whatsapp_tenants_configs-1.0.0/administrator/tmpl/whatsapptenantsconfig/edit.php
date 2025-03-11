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

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>

<form
	action="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_configs&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="whatsapptenantsconfig-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'whatsapptenantsconfig')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'whatsapptenantsconfig', Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_TAB_WHATSAPPTENANTSCONFIG', true)); ?>
	<div class="row-fluid">
		<div class="col-md-12 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FIELDSET_WHATSAPPTENANTSCONFIG'); ?></legend>
				<?php echo $this->form->renderField('callback_url'); ?>
				<?php echo $this->form->renderField('forward_url'); ?>
				<?php echo $this->form->renderField('app_id'); ?>
				<?php echo $this->form->renderField('phone_number_id'); ?>
				<?php echo $this->form->renderField('business_account_id'); ?>
				<?php echo $this->form->renderField('token'); ?>
				<?php echo $this->form->renderField('phone_number'); ?>
				<?php echo $this->form->renderField('user_id'); ?>
				<?php echo $this->form->renderField('dreamztrack_endpoint'); ?>
				<?php echo $this->form->renderField('dreamztrack_branch'); ?>
				<?php echo $this->form->renderField('dreamztrack_key'); ?>
			</fieldset>
		</div>
	</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />

	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>

	
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
