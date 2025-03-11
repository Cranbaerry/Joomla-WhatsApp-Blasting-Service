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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Session\Session;
use \Joomla\CMS\User\UserFactoryInterface;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user       = Factory::getApplication()->getIdentity();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_dt_whatsapp_tenants_blastings') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'whatsapptenantsscheduledmessageform.xml');
$canEdit    = $user->authorise('core.edit', 'com_dt_whatsapp_tenants_blastings') && file_exists(JPATH_COMPONENT .  DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'whatsapptenantsscheduledmessageform.xml');
$canCheckin = $user->authorise('core.manage', 'com_dt_whatsapp_tenants_blastings');
$canChange  = $user->authorise('core.edit.state', 'com_dt_whatsapp_tenants_blastings');
$canDelete  = $user->authorise('core.delete', 'com_dt_whatsapp_tenants_blastings');

// Import CSS
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_dt_whatsapp_tenants_blastings.list');
?>

<?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
    </div>
<?php endif;?>
<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if(!empty($this->filterForm)) { echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); } ?>
	<div class="table-responsive">
		<table class="table table-striped" id="whatsapptenantsscheduledmessageList">
			<thead>
			<tr>
				
					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_TARGET_PHONE_NUMBER', 'a.target_phone_number', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_TYPE', 'a.type', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_TEMPLATE_ID', 'a.template_id', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_KEYWORD_ID', 'a.keyword_id', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_KEYWORD_MESSAGE', 'a.keyword_message', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_STATUS', 'a.status', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSSCHEDULEDMESSAGES_SCHEDULED_TIME', 'a.scheduled_time', $listDirn, $listOrder); ?>
					</th>

			

			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
					<div class="pagination">
						<?php echo $this->pagination->getPagesLinks(); ?>
					</div>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php $canEdit = $user->authorise('core.edit', 'com_dt_whatsapp_tenants_blastings'); ?>
				
				<tr class="row<?php echo $i % 2; ?>">
					
					<td>
						<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_dt_whatsapp_tenants_blastings.' . $item->id) || $item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
						<?php if($canCheckin && $item->checked_out > 0) : ?>
							<a href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&task=whatsapptenantsscheduledmessage.checkin&id=' . $item->id .'&'. Session::getFormToken() .'=1'); ?>">
							<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'whatsapptenantsscheduledmessage.', false); ?></a>
						<?php endif; ?>
						<a href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&view=whatsapptenantsscheduledmessage&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->target_phone_number); ?></a>
					</td>
					<td>
						<?php echo $item->type; ?>
					</td>
					<td>
						<?php echo $item->template_id; ?>
					</td>
					<td>
						<?php echo $item->keyword_id; ?>
					</td>
					<td>
						<?php echo $item->keyword_message; ?>
					</td>
					<td>
						<?php echo $item->status; ?>
					</td>
					<td>
						<?php echo $item->scheduled_time; ?>
					</td>
		

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php if ($canCreate) : ?>
		<a href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&task=whatsapptenantsscheduledmessageform.edit&id=0', false, 0); ?>"
		   class="btn btn-success btn-small"><i
				class="icon-plus"></i>
			<?php echo Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_ADD_ITEM'); ?></a>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value=""/>
	<input type="hidden" name="filter_order_Dir" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php
	if($canDelete) {
		$wa->addInlineScript("
			jQuery(document).ready(function () {
				jQuery('.delete-button').click(deleteItem);
			});

			function deleteItem() {

				if (!confirm(\"" . Text::_('COM_DT_WHATSAPP_TENANTS_BLASTINGS_DELETE_MESSAGE') . "\")) {
					return false;
				}
			}
		", [], [], ["jquery"]);
	}
?>