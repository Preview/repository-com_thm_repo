<?php
/**
 * @category    Joomla component
 * @package	    THM_Repo
 * @subpackage  com_thm_repo.admin
 * @author      Stefan Schneider, <stefan.schneider@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;	

// Span layout
$span = "<span style='color: #D7D7D7; font-weight: bold; margin-right: 5px;'>|&mdash;</span>";

// Load tooltip behavior
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo'); ?>" method="post" name="adminForm" id="adminForm">
        <table class="adminlist">
        	<thead>
        		<tr>
        			<th></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_ID'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_NAME'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_VIEWLEVEL'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_ENTITIES'); ?></th>  
        		</tr>
        	</thead>
       		<tbody>
	        	<?php foreach ($this->items as $i => $item) : ?>
	        		<?php $count = 0; ?>
	        		<tr class="row<?php echo $i % 2; ?>">
		        		<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
			        	<td><?php echo $item->id; ?></td>
			        	<td><?php while ($count < $item->level) :?>
			        			<?php echo $span; ?>
			        			<?php $count++;?>
			        		<?php endwhile; ?>
			        		<a href="<?php echo JRoute::_('index.php?option=com_thm_repo&task=folder.edit&id=' . (int) $item->id); ?>">
			        		<?php echo $item->name; ?></a></td>
			        	<td><?php echo $item->title; ?></td>
			        	<td><input type=button onClick="location.href='<?php echo JRoute::_('index.php?option=com_thm_repo&view=entities&id=' . (int) $item->id); ?>'" value='<?php echo JText::_('COM_THM_REPO_VIEW_ENTITIES'); ?>'></td>		        				
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