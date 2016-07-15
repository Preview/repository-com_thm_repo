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

// Load tooltip behavior
JHtml::_('behavior.tooltip');

// Get Model Functions
$model = JModel::getInstance('versions', 'THM_RepoModel');

// Get ID from URL
$id = JRequest::getVar('id');

?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&view=versions&id=' . (int) $id); ?>" method="post"
	  name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
		<tr>
			<th></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_VERSIONNUMBER', 'id', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_NAME', 'v.name', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JTEXT::_('COM_THM_REPO_VIEW_CURRENT_VERSION') ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_PATH', 'v.path', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_SIZE', 'v.size', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_MIMETYPE', 'v.mimetype', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_MODIFIED', 'v.modified', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JText::_('COM_THM_REPO_VIEW_DOWNLOAD'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($this->items as $i => $item)
		{
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
				<td><?php echo $item->id; ?></td>
				<td><?php echo $item->name; ?></td>
				<td align="center"><?php echo $item->current_version ?
						JHtml::_('jgrid.published', 1, 'versions.', 1) :
						JHtml::_('jgrid.published', $item->current_version, $i, 'versions.', 1) ?>
				</td>
				<td><?php echo $item->path; ?></td>
				<td><?php echo $item->size; ?></td>
				<td><?php echo $item->mimetype; ?></td>
				<td><?php echo $item->modified; ?></td>
				<td align="center">
					<a href="<?php echo JRoute::_('index.php?option=com_thm_repo&view=versions&id=' . $id . '&downloadid=' . $item->id); ?>">
						<img src="components/com_thm_repo/img/download.png"></a>
					<?php
					if (isset($_GET['downloadid']))
					{
						$id = $_GET['downloadid'];
						$model->download($id);
					}
					?>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="3"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
		</tfoot>
	</table>
	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>