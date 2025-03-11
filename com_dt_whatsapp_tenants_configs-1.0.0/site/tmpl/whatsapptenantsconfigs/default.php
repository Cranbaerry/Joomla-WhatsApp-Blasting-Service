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
$canCreate  = $user->authorise('core.create', 'com_dt_whatsapp_tenants_configs') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'whatsapptenantsconfigform.xml');
$canEdit    = $user->authorise('core.edit', 'com_dt_whatsapp_tenants_configs') && file_exists(JPATH_COMPONENT .  DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'whatsapptenantsconfigform.xml');
$canCheckin = $user->authorise('core.manage', 'com_dt_whatsapp_tenants_configs');
$canChange  = $user->authorise('core.edit.state', 'com_dt_whatsapp_tenants_configs');
$canDelete  = $user->authorise('core.delete', 'com_dt_whatsapp_tenants_configs');

// Import CSS
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_dt_whatsapp_tenants_configs.list');
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
		<table class="table table-striped" id="whatsapptenantsconfigList">
			<thead>
			<tr>
				
					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_CALLBACK_URL', 'a.callback_url', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_FORWARD_URL', 'a.forward_url', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_APP_ID', 'a.app_id', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_PHONE_NUMBER_ID', 'a.phone_number_id', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_BUSINESS_ACCOUNT_ID', 'a.business_account_id', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_TOKEN', 'a.token', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_PHONE_NUMBER', 'a.phone_number', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_USER_ID', 'a.user_id', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_DREAMZTRACK_KEY', 'a.dreamztrack_key', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_DREAMZTRACK_BRANCH', 'a.dreamztrack_branch', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_DREAMZTRACK_ENDPOINT', 'a.dreamztrack_endpoint', $listDirn, $listOrder); ?>
					</th>

						<?php if ($canEdit || $canDelete): ?>
					<th class="center">
						<?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_WHATSAPPTENANTSCONFIGS_ACTIONS'); ?>
					</th>
					<?php endif; ?>

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
				<?php $canEdit = $user->authorise('core.edit', 'com_dt_whatsapp_tenants_configs'); ?>
				
				<tr class="row<?php echo $i % 2; ?>">
					
					<td>
						<?php echo $item->id; ?>
					</td>
					<td>
						<?php echo $item->callback_url; ?>
					</td>
					<td>
						<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_dt_whatsapp_tenants_configs.' . $item->id) || $item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
						<?php if($canCheckin && $item->checked_out > 0) : ?>
							<a href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_configs&task=whatsapptenantsconfig.checkin&id=' . $item->id .'&'. Session::getFormToken() .'=1'); ?>">
							<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'whatsapptenantsconfig.', false); ?></a>
						<?php endif; ?>
						<a href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_configs&view=whatsapptenantsconfig&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->forward_url); ?></a>
					</td>
					<td>
						<?php echo $item->app_id; ?>
					</td>
					<td>
						<?php echo $item->phone_number_id; ?>
					</td>
					<td>
						<?php echo $item->business_account_id; ?>
					</td>
					<td>
						<?php echo $item->token; ?>
					</td>
					<td>
						<?php echo $item->phone_number; ?>
					</td>
					<td>
								<?php $container = \Joomla\CMS\Factory::getContainer();
								$userFactory = $container->get(UserFactoryInterface::class);?>
								<?php $user = $userFactory->loadUserById($item->user_id); ?>
								<?php echo $user->name; ?>
					</td>
					<td>
						<?php echo $item->dreamztrack_key; ?>
					</td>
					<td>
						<?php echo $item->dreamztrack_branch; ?>
					</td>
					<td>
						<?php echo $item->dreamztrack_endpoint; ?>
					</td>
					<?php if ($canEdit || $canDelete): ?>
						<td class="center">
						</td>
					<?php endif; ?>

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php if ($canCreate) : ?>
		<a href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_configs&task=whatsapptenantsconfigform.edit&id=0', false, 0); ?>"
		   class="btn btn-success btn-small"><i
				class="icon-plus"></i>
			<?php echo Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_ADD_ITEM'); ?></a>
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

				if (!confirm(\"" . Text::_('COM_DT_WHATSAPP_TENANTS_CONFIGS_DELETE_MESSAGE') . "\")) {
					return false;
				}
			}
		", [], [], ["jquery"]);
	}
?>