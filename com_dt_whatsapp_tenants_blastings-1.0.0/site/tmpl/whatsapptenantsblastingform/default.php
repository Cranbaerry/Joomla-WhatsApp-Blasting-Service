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
?>

<style>
	/* Your existing styles remain unchanged */
	#excelDataTable_info {
		color: black !important;
	}

	.selected-item {
		display: inline-block;
		color: black;
		background-color: #e0e0e0;
		border: 1px solid #ccc;
		border-radius: 5px;
		padding: 5px 10px;
		margin: 5px;
	}

	#select-group {
		height: 200px;
		width: 500px;
	}

	.close-button {
		margin-left: 5px;
		border: none;
		background: transparent;
		cursor: pointer;
		font-weight: bold;
		color: #f44336;
	}

	.close-button:hover {
		color: #d32f2f;
	}

	.select2-container--default .select2-results__option,
	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		color: black !important;
	}

	#tableContainer {
		max-height: 400px;
		overflow-y: auto;
		/* border: 1px solid #ddd; */
		display: none;
		padding: 5px;
	}

	#tableContainer::-webkit-scrollbar {
		width: 10px;
	}

	table {
		width: 100%;
		border-collapse: collapse;
	}

	th,
	td {
		padding: 8px;
		text-align: left;
		border: 1px solid #ddd;
	}

	th {
		background-color: gray;
	}

	.btn-upload {
		padding: 2px 6px;
		background-color: #007bff;
		color: white;
		border: 2px solid #007bff;
		border-right: none;
		border-radius: 4px 0 0 4px;
		cursor: pointer;
		margin-bottom: 10px;
	}

	.file-name {
		display: inline-block;
		padding: 2px 6px;
		border: 2px solid #ddd;
		border-left: none;
		border-radius: 0 4px 4px 0;
		font-size: 14px;
		color: white;
		vertical-align: top;
	}

	.dropdown-item {
		cursor: pointer;
		align-items: center;
		text-align: left;
	}

	.dropdown-item:hover {
		background-color: #f0f0f0;
	}

	.caption-item {
		background-color: white !important;
		width: 300px !important;
		height: 35px !important;
	}

	label {
		display: block;
		margin-bottom: 0.5rem;
		color: white;
	}

	select {
		height: 1%;
	}

	input,
	select {
		display: block;
		width: 100%;
		padding: 0.75rem;
		border: 1px solid #ccc;
		border-radius: 0.25rem;
	}

	.width-50 {
		width: 50%;
	}

	.ml-auto {
		margin-left: auto;
	}

	.text-center {
		text-align: center;
	}

	/* Progressbar */
	.progressbar {
		position: relative;
		display: flex;
		justify-content: space-between;
		counter-reset: step;
		margin: 2rem 0 4rem;
	}

	.progressbar::before,
	.progress {
		content: "";
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
		height: 4px;
		width: 100%;
		background-color: #dcdcdc;
		z-index: 0;
	}

	.progress {
		background-color: #4c4cc3;
		width: 0%;
		transition: 0.3s;
	}

	.progress-step {
		z-index: 1;
		width: 2.1875rem;
		height: 2.1875rem;
		background-color: #dcdcdc;
		border-radius: 50%;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.progress-step::before {
		counter-increment: step;
		content: counter(step);
	}

	.progress-step::after {
		content: attr(data-title);
		position: absolute;
		top: calc(100% + 0.5rem);
		font-size: 0.85rem;
		color: #666;
	}

	.progress-step-active {
		background-color: #4c4cc3;
		color: #f3f3f3;
	}

	/* Form */
	.form {
		width: 75%;
		margin: 0 auto;
		border: 1px solid #ccc;
		border-radius: 0.35rem;
		padding: 1.5rem;
		margin-top: 5%;
	}

	.form-step {
		display: none;
		transform-origin: top;
		animation: animate 0.5s;
	}

	.form-step-active {
		display: block;
	}

	.input-group {
		margin: 2rem 0;
	}

	@keyframes animate {
		from {
			transform: scale(1, 0);
			opacity: 0;
		}

		to {
			transform: scale(1, 1);
			opacity: 1;
		}
	}

	/* Button */
	.btns-group {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 1.5rem;
	}

	.btn-step {
		padding: 0.75rem;
		display: block;
		text-decoration: none;
		background-color: #4c4cc3;
		color: #f3f3f3;
		text-align: center;
		border-radius: 0.25rem;
		cursor: pointer;
		transition: 0.3s;
	}

	.btn-step:hover {
		box-shadow: 0 0 0 2px #fff, 0 0 0 3px #4c4cc3;
	}

	#excelDataTable tbody {
		color: black !important;
	}
</style>

<!-- Include necessary libraries -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<h4 style="color:#141e3e;">Current Login: <?php echo $dreamztrack_phone; ?></h4>

<div class="whatsapp-div">
	<div id="contacts" style="margin-top: 20px;">
		<div class="contact">
			<button class="btn btn-success" id="viewBlastings">Blasting List <span id="second-loader"></span></button>
		</div>
	</div>

	<div id="blasting-message" style="margin-top: 20px;">
		<form id="form-whatsapptenantsblasting"
			action="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&task=whatsapptenantsblastingform.save'); ?>"
			method="post" class="form">
			<?php echo HTMLHelper::_('form.token'); ?>
			<input type="hidden" name="contacts" id="contactsField">
			<input type="hidden" name="option" value="com_dt_whatsapp_tenants_blastings" />
			<input type="hidden" name="jform[status]"
				value="<?php echo isset($this->item->status) ? $this->item->status : ''; ?>" id="jform_status" />
			<input type="hidden" name="task" value="whatsapptenantsblastingform.save" />
			<input type="hidden" name="jform[scheduled_time]" value="" id="scheduled_time" />
			<input type="hidden" name="jform[id]"
				value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />
			<input type="hidden" name="jform[state]"
				value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />
			<?php echo $this->form->getInput('created_by'); ?>
			<?php echo $this->form->getInput('modified_by'); ?>
			

			<!-- Hidden file input for Excel file -->
			<input type="file" id="excelFile" style="display:none" accept=".xlsx,.xls">
			<h3>Blasting Message</h3>
			<!-- Progress bar -->
			<div class="progressbar">
				<div class="progress" id="progress"></div>
				<div class="progress-step progress-step-active" data-title="Select a Template"></div>
				<div class="progress-step" data-title="Select Recipients"></div>
				<div class="progress-step" data-title="Scheduling Options"></div>
				<div class="progress-step" data-title="Confirmation"></div>
			</div>

			<!-- Step 1: Template Selection -->
			<div class="form-step step1 form-step-active">
				<div class="input-group">
					<?php echo $this->form->renderField('template_id'); ?>
				</div>
				<div>
					<a href="#" class="btn-step btn-next width-50 ml-auto">Next</a>
				</div>
			</div>

			<!-- Step 2: Excel Import -->
			<div class="form-step step2">
				<div class="row">
					<div class="col-md-5"></div>
					<div class="col-md-2"><!-- or --></div>
					<div class="col-md-5" style="margin-bottom: 10px;">
						<a href="#" class="btn-step" id="btn-get-contact">Import Excel <span
								id="btn-get-contact-text"></span></a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div id="tableContainer" style="margin-top: 20px; display:none;">
							<table id="excelDataTable" class="display" style="width:100%"></table>
						</div>
					</div>
				</div>
				<div class="btns-group">
					<a href="#" class="btn-step btn-prev">Previous</a>
					<a href="#" class="btn-step btn-next">Next</a>
				</div>
			</div>

			<!-- Step 3: Scheduling Options -->
			<div class="form-step step3">
				<div class="input-group">
					<?php echo $this->form->renderField('mode'); ?>

				</div>
				<div id="schedule_picker_group" class="input-group" style="display:none">
					<label for="schedule_time_picker">Date and time:</label>
					<input type="datetime-local" id="schedule_time_picker" name="schedule_time_picker">
				</div>
				<div class="btns-group">
					<a href="#" class="btn-step btn-prev">Previous</a>
					<a href="#" class="btn-step btn-next">Next</a>
				</div>
			</div>

			<!-- Step 4: Confirmation and Initiate Blasting -->
			<div class="form-step step4">
				<!-- Summary Section -->
				<div id="summary">
					<p><strong>Template:</strong> <span id="summary-template"></span></p>
					<p><strong>Blasting Mode:</strong> <span id="summary-mode"></span></p>
					<p><strong>Scheduled Time:</strong> <span id="summary-scheduled-time"></span></p>
					<p><strong>Contacts Selected:</strong> <span id="summary-contacts"></span></p>
				</div>
				<div class="btns-group" style="margin-top: 15px;">
					<a href="#" class="btn-step btn-prev">Previous</a>
					<button class="btn btn-success" type="button" id="start-blasting"
						style="margin-top: 0px !important;">
						Start Blasting <span id="fifth-loader"></span>
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	function getLocalFormattedDateTime() {
		const now = new Date();
		const year = now.getFullYear();
		const month = ('0' + (now.getMonth() + 1)).slice(-2);
		const day = ('0' + now.getDate()).slice(-2);
		const hours = ('0' + now.getHours()).slice(-2);
		const minutes = ('0' + now.getMinutes()).slice(-2);
		const seconds = ('0' + now.getSeconds()).slice(-2);
		return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
	}

	jQuery(document).ready(function ($) {

		$('#jform_mode').change(function () {
			if ($(this).val() === 'SCHEDULED') {
				// show the schedule date time
				$('#schedule_picker_group').show();
			} else {
				// hide the schedule date time
				$('#schedule_picker_group').hide();
			}
		});

		// on schedule-date time value change
		$('#schedule_time_picker').change(function () {
			var dateTimeVal = $(this).val(); // e.g. "2025-03-07T08:00"
			console.log(dateTimeVal);
			if (dateTimeVal) {
				// Replace 'T' with a space and append ":00" to get "YYYY-MM-DD HH:MM:SS"
				var formattedDateTime = dateTimeVal.replace('T', ' ') + ':00';
				$('#scheduled_time').val(formattedDateTime);
				console.log(formattedDateTime);
			}
		});

		$('#viewBlastings').click(function () {
			window.location.href = 'dashboard?view=whatsapptenantsblastings';
		});

		// Global array to hold contact objects: { phone: string, name: string }
		var contacts = [];
		var selectedContacts = [];

		// Multi-step form navigation
		var currentStep = 0;
		var steps = $('.form-step');
		showStep(currentStep);

		function showStep(n) {
			steps.removeClass('form-step-active');
			$(steps[n]).addClass('form-step-active');
		}

		$('.btn-next').click(function (e) {
			e.preventDefault();
			switch (currentStep) {
				case 0:
					if ($('#jform_template_id').val() === '') {
						alert('Please select a template.');
						return;
					}
					break;
				case 1:
					$.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
					$('#excelDataTable input.select-phone:checked').each(function () {
						var phone = $(this).val();
						// Find corresponding contact from the contacts array
						var contact = contacts.find(function (c) {
							return c.phone === phone;
						});
						if (contact) {
							selectedContacts.push(contact);
						}
					});
					if (contacts.length === 0) {
						alert('Please import contacts from an Excel file.');
						return;
					}
					if (selectedContacts.length === 0) {
						alert('Please select at least one contact.');
						return;
					}
					break;
				case 2:
					if ($('#jform_mode').val() === '') {
						alert('Please select a blasting mode.');
						return;
					}
					if ($('#schedule_time_picker').val() === '') {
						if ($('#jform_mode').val() !== 'INSTANT') {
							alert('Please select a scheduled time.');
							return;
						}
					}
					break;
				default:
					break;
			}
			if (currentStep < steps.length - 1) {
				currentStep++;
				// When reaching the final step, update the summary information
				if (currentStep === steps.length - 1) {
					var template = $('#jform_template_id option:selected').text();
					var mode = $('#jform_mode').val();
					var scheduledTime = $('#schedule_time_picker').val();
					var contactCount = contacts.length;
					var formattedTime = new Date(scheduledTime).toLocaleString('en-US', {
						month: 'long',
						day: 'numeric',
						year: 'numeric',
						hour: 'numeric',
						minute: 'numeric'
					});

					$('#summary-template').text(template);
					$('#summary-mode').text(mode);
					$('#summary-scheduled-time').text(mode === 'INSTANT' ? 'Immediate' : formattedTime);
					$('#summary-contacts').text(contactCount + ' contact(s) selected');
				}
				showStep(currentStep);
				$('.progress-step').eq(currentStep).addClass('progress-step-active');
				var progressPercent = (currentStep / (steps.length - 1)) * 100;
				$('#progress').css('width', progressPercent + '%');
			}
		});

		$('.btn-prev').click(function (e) {
			e.preventDefault();
			if (currentStep > 0) {
				$('.progress-step').eq(currentStep).removeClass('progress-step-active');
				currentStep--;
				showStep(currentStep);
				var progressPercent = (currentStep / (steps.length - 1)) * 100;
				$('#progress').css('width', progressPercent + '%');
			}
		});

		// Excel Import and DataTable population
		$('#btn-get-contact').click(function (e) {
			e.preventDefault();
			$('#excelFile').click();
		});

		$('#excelFile').change(function (e) {
			var file = e.target.files[0];
			if (file) {
				var reader = new FileReader();
				reader.onload = function (e) {
					var data = new Uint8Array(e.target.result);
					var workbook = XLSX.read(data, { type: 'array' });
					var firstSheetName = workbook.SheetNames[0];
					var worksheet = workbook.Sheets[firstSheetName];
					var jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: "" });

					if (jsonData.length < 2) {
						alert("No data found in the Excel file.");
						return;
					}

					contacts = [];
					// Skip header (index 0) and process data rows
					for (var i = 1; i < jsonData.length; i++) {
						var row = jsonData[i];
						var phone = row[0];
						var name = row[1];
						if (phone) {
							contacts.push({
								phone: String(phone),
								name: String(name)
							});
						}
					}

					// Build DataTable data from contacts array
					var tableData = contacts.map(function (contact) {
						return [
							'<input type="checkbox" class="select-phone" value="' + contact.phone + '">',
							contact.phone,
							contact.name
						];
					});

					// If DataTable exists, destroy and reinitialize
					if ($.fn.DataTable.isDataTable('#excelDataTable')) {
						$('#excelDataTable').DataTable().clear().destroy();
					}

					var table = $('#excelDataTable').DataTable({
						data: tableData,
						columns: [
							{ title: '<input type="checkbox" id="select-all">', orderable: false },
							{ title: "Phone Numbers" },
							{ title: "Name" }
						],
						searching: true,
						paging: true,
						ordering: true
					});

					// Show table container and adjust columns
					$('#tableContainer').show();
					table.columns.adjust().draw();

					// "Select all" functionality in header
					$('#select-all').on('click', function () {
						var rows = table.rows({ 'search': 'applied' }).nodes();
						$('input[type="checkbox"].select-phone', rows).prop('checked', this.checked);
					});
				};
				reader.readAsArrayBuffer(file);
			}
		});

		$('#start-blasting').on('click', function (e) {
			e.preventDefault();

			var contactsJson = JSON.stringify(selectedContacts);

			$('#contactsField').val(contactsJson);
			$('#jform_status').val('QUEUED');
			if ($('#jform_mode').val() === 'INSTANT') {
				$('#scheduled_time').val(getLocalFormattedDateTime());
			}

			var $btn = $(this);
			// Disable the button
			$btn.prop('disabled', true);
			// Add spinner inside the button (appended to the current text)
			$btn.find('#fifth-loader').html(
				'<div class="sk-chase sk-small">' +
				'<div class="sk-chase-dot"></div>' +
				'<div class="sk-chase-dot"></div>' +
				'<div class="sk-chase-dot"></div>' +
				'<div class="sk-chase-dot"></div>' +
				'<div class="sk-chase-dot"></div>' +
				'<div class="sk-chase-dot"></div>' +
				'</div>'
			);
			// Submit form
			$('#form-whatsapptenantsblasting').submit();
		});
	});

</script>