<?php
/**
 * @package    THM_Repo
 * @author     Stefan Schneider, <stefan.schneider@mni.thm.de>
 * @copyright  2013 TH Mittelhessen
 * @license    GNU GPL v.2
 * @link       www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

// Load tooltip behavior
JHtml::_('behavior.tooltip');

// Get Model Functions
$model = JModel::getInstance('links', 'THM_RepoModel');

?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&view=links'); ?>" method="post" name="adminForm" id="adminForm">
       <table class="adminlist">
        	<thead>
        		<tr>
        			<th></th>
        			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_ID', 'a.id', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_NAME', 'a.name', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_FOLDER', 'a.parent_id', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_LINK', 'b.link', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_VIEWLEVELS', 'a.viewlevels', $this->sortDirection, $this->sortColumn); ?></th>
        		</tr>
        	</thead>
       		<tbody>
        		<?php foreach ($this->items as $i => $item) : ?>
        			<tr class="row<?php echo $i % 2; ?>">
        				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
        				<td><?php echo $item->id; ?></td>
           				<td><?php echo $item->name; ?></td>
            			<td><?php echo $model->getFoldername($item->parent_id); ?></td>
        				<td><a href="<?php echo JRoute::_('index.php?option=com_thm_repo&task=link.edit&id=' . (int) $item->id); ?>">
        				<?php echo $item->link; ?></a></td>
        				<td><?php echo $item->viewlevels; ?></td>
        			</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
        		<tr>
        			<td colspan="3"><?php echo $this->pagination->getListFooter(); ?></td>
        		</tr>
        	</tfoot>
        </table>
        <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
        		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>