<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Dt_whatsapp_tenants_configs
 * @author     dreamztech
 * @copyright  2025 dreamztech
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Comdtwhatsapptenantsconfigs\Component\Dt_whatsapp_tenants_configs\Site\Helper\Dt_whatsapp_tenants_configsHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_dt_whatsapp_tenants_configs', JPATH_SITE);

$user    = Factory::getApplication()->getIdentity();
$canEdit = Dt_whatsapp_tenants_configsHelper::canUserEdit($this->item, $user);
?>

<div class="whatsapptenantsconfig-edit front-end-edit">

    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        </div>
    <?php endif; ?>

    <?php if (!$canEdit) : ?>
        <h3>
            <?php throw new \Exception(Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
        </h3>
    <?php else : ?>
        <?php if (!empty($this->item->id)): ?>
            <h1>Edit configurations</h1>
        <?php else: ?>
            <h1>Add configurations</h1>
        <?php endif; ?>

        <form id="form-whatsapptenantsconfig"
              action="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_configs&task=whatsapptenantsconfigform.save'); ?>"
              method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
            
            <input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />
            <input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />

            <?php echo $this->form->getInput('created_by'); ?>
            <?php echo $this->form->getInput('modified_by'); ?>

            <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'whatsapptenantsconfig')); ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'whatsapptenantsconfig', Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_TAB_WHATSAPPTENANTSCONFIG', true)); ?>

            <?php 
                // Build the callback URL using Joomla's root URL, the ajax API path, and the current user id.
                $callbackUrl = Uri::root() . 'index.php?option=com_ajax&plugin=whatsappwebhook&format=raw&method=processWebhook&uid=' . $user->id;
            ?>
            <div class="control-group has-success">
                <label id="jform_callback_url-lbl" for="jform_callback_url" class="required form-label">
                    <?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_FORM_LBL_WHATSAPPTENANTSCONFIG_CALLBACK_URL', true); ?>
                    <span class="star" aria-hidden="true">&nbsp;*</span>
                </label>
                <div class="input-group">
                    <input type="text" name="jform[callback_url]" id="jform_callback_url" value="<?php echo $callbackUrl; ?>" class="form-control required valid form-control-success" placeholder="Callback URL" required="" aria-required="true" autocomplete="off" aria-invalid="false" readonly style="background-color: #e9ecef;">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-secondary" id="copyCallbackUrl" title="<?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_COPY_CALLBACK_URL', true); ?>">
                            <span class="fas fa-copy" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>

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
                       href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_configs&task=whatsapptenantsconfigform.cancel'); ?>"
                       title="<?php echo Text::_('JCANCEL'); ?>">
                        <span class="fas fa-times" aria-hidden="true"></span>
                        <?php echo Text::_('JCANCEL'); ?>
                    </a>
                </div>
            </div>

            <input type="hidden" name="option" value="com_dt_whatsapp_tenants_configs"/>
            <input type="hidden" name="task" value="whatsapptenantsconfigform.save"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>

        <script>
        // Copy callback URL to clipboard when the button is clicked
        document.getElementById('copyCallbackUrl').addEventListener('click', function() {
            var copyText = document.getElementById('jform_callback_url');
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");
            // Optionally, add user feedback here (e.g., a tooltip or alert)
        });

        // Increase maxlength of jform_token
        document.getElementById('jform_token').setAttribute('maxlength', '1000');
        </script>
    <?php endif; ?>
</div>
