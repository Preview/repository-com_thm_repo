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
        	<thead>
        		<tr>
        			<th></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_ID'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_PARENT_ID'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_NAME'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_DESCRIPTION')?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_CREATED'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_MODIFIED'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_MODIFIED_BY'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_CREATE_BY'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_VIEWLEVELS'); ?></th>
        		</tr>
        	</thead>
       		<tbody>
        		<?php foreach ($this->items as $i => $item) : ?>
        			<tr class="row<?php echo $i % 2; ?>">
        				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
        				<td><?php echo $item->id; ?></td>
        				<td><?php echo $item->parent_id; ?></td>
        				<td><?php echo $item->name; ?></td>
        				<td><?php echo $item->description; ?></td>
        				<td><?php echo $item->created; ?></td>
        				<td><?php echo $item->modified; ?></td>
        				<td><?php echo $item->modified_by; ?></td>
        				<td><?php echo $item->create_by; ?></td>
        				<td><?php echo $item->viewlevels; ?></td>
        				<td></td>
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