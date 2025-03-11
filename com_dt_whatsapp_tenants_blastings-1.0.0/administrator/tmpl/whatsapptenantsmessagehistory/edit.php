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

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>

<form
	action="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="whatsapptenantsmessagehistory-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'whatsapptenantsmessagehistory')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'whatsapptenantsmessagehistory', Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_TAB_WHATSAPPTENANTSMESSAGEHISTORY', true)); ?>
	<div class="row-fluid">
		<div class="col-md-12 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_FIELDSET_WHATSAPPTENANTSMESSAGEHISTORY'); ?></legend>
				<?php echo $this->form->renderField('from'); ?>
				<?php echo $this->form->renderField('phone_number_id'); ?>
				<?php echo $this->form->renderField('timestamp'); ?>
				<?php echo $this->form->renderField('text'); ?>
				<?php echo $this->form->renderField('type'); ?>
				<?php echo $this->form->renderField('media_caption'); ?>
				<?php echo $this->form->renderField('errors'); ?>
				<?php echo $this->form->renderField('raw_response'); ?>
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
