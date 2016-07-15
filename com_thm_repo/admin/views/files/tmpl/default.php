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
$model = JModelLegacy::getInstance('files', 'THM_RepoModel');

$user = JFactory::getUser();


if (isset($_GET['downloadid']))
{
	$id = $_GET['downloadid'];
	$model->download($id);
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&view=files'); ?>" method="post" name="adminForm"
	  id="adminForm">

	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<input type="text" name="filter_search" id="filter_search"
				   value="<?php echo $this->escape($this->searchterms); ?>"
				   title="<?php echo JText::_('COM_THM_REPO_SEARCH_NAME'); ?>"/>
			<button type="submit">
				<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
			</button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>
	</fieldset>
	<table class="table table-striped" id="itemList">
		<thead>
		<tr>
			<th></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_ID', 'e.id', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_NAME', 've.name', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JText::_('COM_THM_REPO_VIEW_PUBLISHED'); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_FOLDER', 'fo.parent', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_PATH', 've.path', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_VIEWLEVEL', 'vi.title', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JText::_('COM_THM_REPO_VIEW_VERSIONS'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($this->items as $i => $item)
		{ ?>
			<?php $canChange = $user->authorise('core.edit', 'com_content.entity.' . $item->id); ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
				<td><?php echo $item->id; ?></td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_thm_repo&task=file.edit&id=' . (int) $item->id); ?>">
						<?php echo $item->name; ?>
					</a>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'files.', $canChange); ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_thm_repo&task=folder.edit&id=' . (int) $item->parent_id); ?>">
						<?php echo $item->parent; ?>
					</a>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_thm_repo&view=files&downloadid=' . (int) $item->id); ?>">
						<?php echo basename($item->path); ?>
					</a>
				</td>
				<td><?php echo $item->title; ?></td>
				<td><input type=button
						   onClick=
						   "location.href='<?php echo JRoute::_('index.php?option=com_thm_repo&view=versions&id=' . (int) $item->id); ?>'"
						   value='<?php echo JText::_('COM_THM_REPO_VIEW_VERSIONS') . " (" . $model->countVersions($item->id) . ")"; ?>'>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
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