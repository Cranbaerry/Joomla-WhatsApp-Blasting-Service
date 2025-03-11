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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

// Import CSS
$wa =  $this->document->getWebAssetManager();
$wa->useStyle('com_dt_whatsapp_tenants_blastings.admin')
    ->useScript('com_dt_whatsapp_tenants_blastings.admin');

$user      = Factory::getApplication()->getIdentity();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_dt_whatsapp_tenants_blastings');



if (!empty($saveOrder))
{
	$saveOrderingUrl = 'index.php?option=com_dt_whatsapp_tenants_blastings&task=whatsapptenantskeywords.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}

?>

<form action="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&view=whatsapptenantskeywords'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

				<div class="clearfix"></div>
				<table class="table table-striped" id="whatsapptenantskeywordList">
					<thead>
					<tr>
						<th class="w-1 text-center">
							<input type="checkbox" autocomplete="off" class="form-check-input" name="checkall-toggle" value=""
								   title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>
						
						
					<th  scope="col" class="w-1 text-center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
						
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSKEYWORDS_NAME', 'a.name', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSKEYWORDS_CREATED_DATE', 'a.created_date', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_DT_WHATSAPP_TENANTS_BLASTINGS_WHATSAPPTENANTSKEYWORDS_SCHEDULED_MESSAGE_JSON', 'a.scheduled_message_json', $listDirn, $listOrder); ?>
						</th>
						
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
					<tbody <?php if (!empty($saveOrder)) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" <?php endif; ?>>
					<?php foreach ($this->items as $i => $item) :
						$ordering   = ($listOrder == 'a.ordering');
						$canCreate  = $user->authorise('core.create', 'com_dt_whatsapp_tenants_blastings');
						$canEdit    = $user->authorise('core.edit', 'com_dt_whatsapp_tenants_blastings');
						$canCheckin = $user->authorise('core.manage', 'com_dt_whatsapp_tenants_blastings');
						$canChange  = $user->authorise('core.edit.state', 'com_dt_whatsapp_tenants_blastings');
						?>
						<tr class="row<?php echo $i % 2; ?>" data-draggable-group='1' data-transition>
							<td class="text-center">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							
							
							<td class="text-center">
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'whatsapptenantskeywords.', $canChange, 'cb'); ?>
							</td>
							
							<td>
								<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'whatsapptenantskeywords.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit) : ?>
									<a href="<?php echo Route::_('index.php?option=com_dt_whatsapp_tenants_blastings&task=whatsapptenantskeyword.edit&id='.(int) $item->id); ?>">
									<?php echo $this->escape($item->name); ?>
									</a>
								<?php else : ?>
												<?php echo $this->escape($item->name); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php echo $item->created_date; ?>
							</td>
							<td>
								<?php echo $item->scheduled_message_json; ?>
							</td>
							

						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
				<input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>