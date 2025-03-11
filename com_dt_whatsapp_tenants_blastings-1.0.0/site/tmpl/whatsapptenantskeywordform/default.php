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
use \Comdtwhatsapptenantsblastings\Component\Dt_whatsapp_tenants_blastings\Site\Helper\Dt_whatsapp_tenants_blastingsHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_dt_whatsapp_tenants_blastings', JPATH_SITE);

$user = Factory::getApplication()->getIdentity();
$canEdit = Dt_whatsapp_tenants_blastingsHelper::canUserEdit($this->item, $user);


if ($this->item->state == 1) {
	$state_string = 'Publish';
	$state_value = 1;
} else {
	$state_string = 'Unpublish';
	$state_value = 0;
}
$canState = Factory::getApplication()->getIdentity()->authorise('core.edit.state', 'com_dt_whatsapp_tenants_blastings');
?>

<div class="whatsapptenantskeyword-edit front-end-edit">

	<?php if ($this->params->get('show_page_heading')): ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>
	<?php if (!$canEdit): ?>
		<h3>
			<?php throw new \Exception(Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else: ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo Text::sprintf('COM_DT_WHATSAPP_TENANTS_BLASTINGS_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-whatsapptenantskeyword"
			action="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&task=whatsapptenantskeywordform.save'); ?>"
			method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

			<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />

			<?php echo $this->form->getInput('created_by'); ?>
			<?php echo $this->form->getInput('modified_by'); ?>
			<?php echo $this->form->getInput('created_date'); ?>
			<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'whatsapptenantskeyword')); ?>
			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'whatsapptenantskeyword', Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_TAB_WHATSAPPTENANTSKEYWORD', true)); ?>
			<?php echo $this->form->renderField('name'); ?>

			<div class="control-group">
				<?php if (!$canState): ?>
					<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
					<div class="controls"><?php echo $state_string; ?></div>
					<input type="hidden" name="jform[state]" value="<?php echo $state_value; ?>" />
				<?php else: ?>
					<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
				<?php endif; ?>
			</div>

			<div class="control-group">
				<label id="jform_scheduled_message_json-lbl" class="form-label">Schedule Data</label>
				<!-- Hidden textarea that will hold the JSON -->
				<textarea name="jform[scheduled_message_json]" id="jform_scheduled_message_json" class="form-control"
					placeholder="Schedule Data" autocomplete="off" style="display:none;">
							<?php echo $this->form->getValue('scheduled_message_json'); ?>
						</textarea>

				<!-- Container for dynamic schedule items -->
				<div id="schedule_items_container"></div>

				<!-- Button to add a new schedule item -->
				<button type="button" id="add_schedule_item" class="btn btn-secondary">Add Schedule Item</button>
			</div>

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
						href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&task=whatsapptenantskeywordform.cancel'); ?>"
						title="<?php echo Text::_('JCANCEL'); ?>">
						<span class="fas fa-times" aria-hidden="true"></span>
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_dt_whatsapp_tenants_blastings" />
			<input type="hidden" name="task" value="whatsapptenantskeywordform.save" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
<script>
	jQuery(document).ready(function ($) {
		var scheduleIndex = 0;

		// Update the hidden textarea with JSON data for all schedule items.
		function updateScheduleJSON() {
			var scheduleData = [];
			$('#schedule_items_container .schedule-item').each(function () {
				var message = $(this).find('.schedule-message').val().trim();
				var intervalValue = parseInt($(this).find('.schedule-interval').val(), 10);
				var unit = $(this).find('.schedule-unit').val();

				// Only add items with non-empty message and valid interval
				if (message !== "" && !isNaN(intervalValue) && intervalValue > 0 && unit) {
					scheduleData.push({
						message: message,
						interval: intervalValue,
						unit: unit
					});
				}
			});
			$('#jform_scheduled_message_json').val(JSON.stringify(scheduleData));
		}

		// Load existing schedule items from the hidden textarea (if any)
		function loadExistingScheduleItems() {
			var existingValue = $('#jform_scheduled_message_json').val().trim();
			if (existingValue) {
				try {
					var scheduleItems = JSON.parse(existingValue);
					scheduleItems.forEach(function (item) {
						scheduleIndex++;
						var scheduleItemHTML = `
					<div class="schedule-item" data-index="${scheduleIndex}" style="margin-bottom: 10px; border: 1px solid #ccc; padding: 10px;">
						<div class="form-group">
							<label>Message:</label>
							<textarea class="form-control schedule-message" placeholder="Enter message" required>${item.message}</textarea>
						</div>
						<div class="form-group">
							<label>Time Interval:</label>
							<div class="input-group">
								<input type="number" class="form-control schedule-interval" placeholder="Value" min="1" required value="${item.interval}" />
								<select class="form-control schedule-unit">
									<option value="seconds" ${item.unit === 'seconds' ? 'selected' : ''}>Seconds</option>
									<option value="minutes" ${item.unit === 'minutes' ? 'selected' : ''}>Minutes</option>
									<option value="hours" ${item.unit === 'hours' ? 'selected' : ''}>Hours</option>
									<option value="days" ${item.unit === 'days' ? 'selected' : ''}>Days</option>
								</select>
							</div>
							<small class="form-text text-muted">Send message after the specified interval from now</small>
						</div>
						<button type="button" class="btn btn-danger remove_schedule_item">Remove</button>
					</div>`;
						$('#schedule_items_container').append(scheduleItemHTML);
					});
				} catch (e) {
					console.error('Invalid JSON in scheduled_message_json:', e);
				}
			}
		}

		// Append a new schedule item block.
		$('#add_schedule_item').click(function () {
			scheduleIndex++;
			var scheduleItemHTML = `
		<div class="schedule-item" data-index="${scheduleIndex}" style="margin-bottom: 10px; border: 1px solid #ccc; padding: 10px;">
			<div class="form-group">
				<label>Message:</label>
				<textarea class="form-control schedule-message" placeholder="Enter message" required></textarea>
			</div>
			<div class="form-group">
				<label>Time Interval:</label>
				<div class="input-group">
					<input type="number" class="form-control schedule-interval" placeholder="Value" min="1" required />
					<select class="form-control schedule-unit">
						<option value="seconds">Seconds</option>
						<option value="minutes" selected>Minutes</option>
						<option value="hours">Hours</option>
						<option value="days">Days</option>
					</select>
				</div>
				<small class="form-text text-muted">Send message after the specified interval from now</small>
			</div>
			<button type="button" class="btn btn-danger remove_schedule_item">Remove</button>
		</div>`;
			$('#schedule_items_container').append(scheduleItemHTML);
		});

		// Remove a schedule item.
		$('#schedule_items_container').on('click', '.remove_schedule_item', function () {
			$(this).closest('.schedule-item').remove();
			updateScheduleJSON();
		});

		// Update JSON when any input changes.
		$('#schedule_items_container').on('input change', '.schedule-message, .schedule-interval, .schedule-unit', function () {
			updateScheduleJSON();
		});

		// Validate fields on form submission.
		$('form').on('submit', function (e) {
			var isValid = true;
			$('#schedule_items_container .schedule-item').each(function () {
				var message = $(this).find('.schedule-message').val().trim();
				var interval = $(this).find('.schedule-interval').val();
				if (message === "" || interval === "") {
					isValid = false;
					alert("Please fill in all required fields for schedule items (Message and Time Interval).");
					return false; // Break out of the loop.
				}
			});
			if (!isValid) {
				e.preventDefault();
			}
		});

		// Load existing schedule items (if any) when the page loads.
		loadExistingScheduleItems();
		// Update the JSON initially.
		updateScheduleJSON();
	});
</script>