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
$model = JModel::getInstance('entities', 'THM_RepoModel');

// Get ID from URL
$id        = JRequest::getVar('id');
$listOrder = $this->sortColumn;
$listDirn  = $this->sortDirection;
$saveOrder = $listOrder == 'e.ordering';
$user      = JFactory::getUser();

?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&view=entities&id=' . (int) $id); ?>" method="post"
	  name="adminForm" id="adminForm">
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
	<table class="adminlist">
		<thead>
		<tr>
			<th></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_ID', 'e.id', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_NAME', 'name', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JText::_('COM_THM_REPO_VIEW_PUBLISHED'); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_TYPE', 'path', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_ENTITIES', 'path', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_VIEWLEVEL', 'v.title', $this->sortDirection, $this->sortColumn); ?></th>
			<th width="10%">
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'e.ordering', $this->sortDirection, $this->sortColumn); ?>
				<?php
				if ($saveOrder)
				{
					echo JHtml::_('grid.order', $this->items, 'filesave.png', 'entities.saveorder');
				}
				?>
			</th>
			<th><?php echo JText::_('COM_THM_REPO_VIEW_DOWNLOAD'); ?></th>

		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($this->items as $i => $item)
		{
			?>
			<?php $canChange = $user->authorise('core.edit', 'com_content.entity.' . $item->id); ?>
			<?php $ordering = $listOrder == 'e.ordering'; ?>

			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
				<td><?php echo $item->id; ?></td>
				<td><a href="<?php echo $item->path ?
						JRoute::_('index.php?option=com_thm_repo&task=file.edit&id=' . (int) $item->id)
						: JRoute::_('index.php?option=com_thm_repo&task=link.edit&id=' . (int) $item->id); ?>">
						<?php echo $item->lname; ?>
						<?php echo $item->vename; ?></a></td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'entities.', $canChange); ?>
				</td>
				<td align="center">
					<?php
					if ($item->path)
					{
						?>
						<img src="components/com_thm_repo/img/file.png">
						<?php
					}
					else
					{
						?>
						<img src="components/com_thm_repo/img/link.png">
						<?php
					}
					?>
				</td>
				<td>
					<?php echo $item->path; ?>
					<?php echo $item->link; ?>
				</td>
				<td><?php echo $item->title; ?></td>
				<td class="order">
					<?php
					if ($saveOrder)
					{
						?>
						<?php
						if ($listDirn == 'asc')
						{
							?>
							<span>
                                        <?php echo $this->pagination->orderUpIcon($i, 1, 'entities.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?>
                                    </span>
							<span>
                                        <?php echo $this->pagination->orderDownIcon(
											$i, $this->pagination->total, 1, 'entities.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering
										); ?>
                                    </span>
							<?php
						}
						elseif ($listDirn == 'desc')
						{
							?>
							<span>
                                        <?php echo $this->pagination->orderUpIcon(
											$i, 1, 'entities.orderdown', 'JLIB_HTML_MOVE_UP', $ordering
										); ?>
                                    </span>
							<span>
                                        <?php echo $this->pagination->orderDownIcon(
											$i, $this->pagination->total, 1, 'entities.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering
										); ?>
                                    </span>
							<?php
						}
						?>
						<?php
					}
					?>
					<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5"
						   value="<?php echo $item->ordering; ?>"
						<?php echo $disabled; ?> class="text-area-order"/>
				</td>
				<td align="center">
					<?php
					if ($item->path)
					{
						?>
						<a href="<?php echo JRoute::_(
							'index.php?option=com_thm_repo&view=entities&id=' . $id . '&downloadid=' . $item->id
						); ?>">
							<img src="components/com_thm_repo/img/download.png"></a>
						<?php
						if (isset($_GET['downloadid']))
						{
							$id = $_GET['downloadid'];
							$model->download($id);
						}
						?>
						<?php
					}
					else
					{
						?>
						<a href="<?php echo $item->link; ?>" target="_blank"><img
								src="components/com_thm_repo/img/link.png"/></a>
						<?php
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