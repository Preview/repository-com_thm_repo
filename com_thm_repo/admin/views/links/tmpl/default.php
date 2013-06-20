<?php
/**
 * @package		com_thm_repo
 * @author      Stefan Schneider	<stefan.schneider@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;
// load tooltip behavior
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo'); ?>" method="post" name="adminForm" id="adminForm">
        <table class="adminlist">
       <table class="adminlist">
        	<thead>
        		<tr>
        			<th></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_ID', 'link_id', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_LINK', 'link', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_NAME', 'name', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_ID', 'id', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_PARENT_ID', 'parent_id', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_DESCRIPTION', 'description', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_CREATED', 'created', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_MODIFIED', 'modified', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_MODIFIED_BY', 'modified_by', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_CREATE_BY', 'create_by', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_( 'grid.sort', 'COM_THM_REPO_VIEW_VIEWLEVELS', 'viewlevels', $this->sortDirection, $this->sortColumn); ?></th>
        		</tr>
        	</thead>
       		<tbody>
        		<?php foreach ($this->items as $i => $item) : ?>
        			<tr class="row<?php echo $i % 2; ?>">
        				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
        				<td><?php echo $item->link_id; ?></td>
        				<td><?php echo $item->link; ?></td>
        				<td><?php echo $item->name; ?></td>
        				<td><?php echo $item->id; ?></td>
        				<td><?php echo $item->parent_id; ?></td>
        				<td><?php echo $item->description; ?></td>
        				<td><?php echo $item->created; ?></td>
        				<td><?php echo $item->modified; ?></td>
        				<td><?php echo $item->modified_by; ?></td>
        				<td><?php echo $item->create_by; ?></td>
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
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>