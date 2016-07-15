<?php
/**
 * @category    Joomla component
 * @package     THM_Repo
 * @subpackage  com_thm_repo.admin
 * @author      Stefan Schneider, <stefan.schneider@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

// Get Model Functions
$model = JModelLegacy::getInstance('folders', 'THM_RepoModel');

// Span layout
$span = "<span style='color: #D7D7D7; font-weight: bold; margin-right: 5px;'>|&mdash;</span>";

$listOrder = $this->sortColumn;
$listDirn  = $this->sortDirection;
$ordering  = ($listOrder == 'f.lft');
$user      = JFactory::getUser();

// Load tooltip behavior
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&view=folders'); ?>" method="post" name="adminForm"
	  id="adminForm">
	<table class="table table-striped" id="itemList">
		<thead>
		<tr>
			<th width="1%" class="hidden-phone">
				<?php echo JHtml::_('searchtools.sort', '', 'lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
			</th>
			<th width="1%" class="hidden-phone">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th><?php echo 'ID' ?></th>
			<th><?php echo JHTML::_('grid.sort', JText::_('COM_THM_REPO_VIEW_NAME'), 'name', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JText::_('COM_THM_REPO_VIEW_PUBLISHED'); ?></th>
			<th><?php echo JText::_('COM_THM_REPO_VIEW_VIEWLEVEL'); ?></th>
			<th><?php echo JText::_('COM_THM_REPO_VIEW_ENTITIES'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php $originalOrders = array(); ?>
		<?php
		foreach ($this->items as $i => $item)
		{
			?>
			<?php $canChange = $user->authorise('core.edit', 'com_content.folder.' . $item->id); ?>
			<?php $orderkey = array_search($item->id, $this->ordering[$item->parent_id]); ?>
			<?php $count = 0; ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="order nowrap center hidden-phone">
                        <span class="sortable-handler">
                                <i class="icon-menu"></i>
                            </span>
					<input type="text" style="display:none" name="order[]" size="5"
						   value="<?php echo $orderkey + 1; ?>"/>
				</td>
				<td class="center hidden-phone">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td><?php echo $item->id; ?></td>
				<td><?php
					while ($count < $item->level)
					{
						?>
						<?php echo $span; ?>
						<?php $count++; ?>
						<?php
					}
					?>
					<a href="<?php echo JRoute::_('index.php?option=com_thm_repo&task=folder.edit&id=' . (int) $item->id); ?>">
						<?php echo $item->name; ?></a></td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'folders.', $canChange); ?>
				</td>
				<td><?php echo $item->title; ?></td>
				<td align="center"><input type=button
										  onClick="location.href='<?php echo JRoute::_('index.php?option=com_thm_repo&view=entities&id=' . (int) $item->id); ?>'"
										  value='<?php echo JText::_('COM_THM_REPO_VIEW_ENTITIES') . " (" . $model->countEntities($item->id) . ")"; ?>'>

				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
		</tfoot>
	</table>
	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>