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
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_NAME'); ?></th>
        		</tr>
        	</thead>
       		<tbody>
        		<?php foreach ($this->items as $i => $item) : ?>
        			<tr class="row<?php echo $i % 2; ?>">
        				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
        				<td><?php echo $item->id; ?></td>
        				<td><?php echo $item->name; ?></td>
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