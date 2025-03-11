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
use \Comdtwhatsapptenantstemplates\Component\Dt_whatsapp_tenants_templates\Site\Helper\Dt_whatsapp_tenants_templatesHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_dt_whatsapp_tenants_templates', JPATH_SITE);

$user    = Factory::getApplication()->getIdentity();
$canEdit = Dt_whatsapp_tenants_templatesHelper::canUserEdit($this->item, $user);


?>

<div class="whatsapptenantstemplate-edit front-end-edit">

<?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
    <?php endif;?>
	<?php if (!$canEdit) : ?>
		<h3>
		<?php throw new \Exception(Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo Text::sprintf('COM_DT_WHATSAPP_TENANTS_TEMPLATES_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-whatsapptenantstemplate"
			  action="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_templates&task=whatsapptenantstemplateform.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
				<?php echo $this->form->getInput('modified_by'); ?>
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'whatsapptenantstemplate')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'whatsapptenantstemplate', Text::_('COM_DT_WHATSAPP_TENANTS_TEMPLATES_TAB_WHATSAPPTENANTSTEMPLATE', true)); ?>
	<?php echo $this->form->renderField('name'); ?>

	<?php echo $this->form->renderField('language'); ?>

	<?php echo $this->form->renderField('category'); ?>

	<?php echo $this->form->renderField('header_type'); ?>

	<?php echo $this->form->renderField('header_text'); ?>

	<?php echo $this->form->renderField('header_media'); ?>

				<?php if (!empty($this->item->header_media)) : ?>
					<?php $header_mediaFiles = array(); ?>
					<?php foreach ((array)$this->item->header_media as $fileSingle) : ?>
						<?php if (!is_array($fileSingle)) : ?>
							<a href="<?php echo Route::_(Uri::root() . 'uploads' . DIRECTORY_SEPARATOR . Factory::getApplication()->getIdentity()->id . DIRECTORY_SEPARATOR . $fileSingle, false);?>"><?php echo $fileSingle; ?></a> | 
							<?php $header_mediaFiles[] = $fileSingle; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				<input type="hidden" name="jform[header_media_hidden]" id="jform_header_media_hidden" value="<?php echo implode(',', $header_mediaFiles); ?>" />
				<?php endif; ?>
	<?php echo $this->form->renderField('body'); ?>

	<?php echo $this->form->renderField('footer'); ?>

	<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<span class="fas fa-check" aria-hidden="true"></span>
							<?php echo Text::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn btn-danger"
					   href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_templates&task=whatsapptenantstemplateform.cancel'); ?>"
					   title="<?php echo Text::_('JCANCEL'); ?>">
					   <span class="fas fa-times" aria-hidden="true"></span>
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_dt_whatsapp_tenants_templates"/>
			<input type="hidden" name="task"
				   value="whatsapptenantstemplateform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
