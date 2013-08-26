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
$model = JModel::getInstance('files', 'THM_RepoModel');
?>


<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&view=files'); ?>" method="post" name="adminForm" id="adminForm">
        <table class="adminlist">
        	<thead>
        		<tr>
        			<th></th>
        			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_ID', 'e.id', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_NAME', 've.name', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_FOLDER', 'fo.parent', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_PATH', 've.path', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_VIEWLEVEL', 'vi.title', $this->sortDirection, $this->sortColumn); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_DOWNLOAD'); ?></th>
        			<th><?php echo JText::_('COM_THM_REPO_VIEW_VERSIONS'); ?></th>
        		</tr>
        	</thead>
       		<tbody>
        		<?php 
        		foreach ($this->items as $i => $item)
        		{ ?>
        			<tr class="row<?php echo $i % 2; ?>">
        				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
        				<td><?php echo $item->id; ?></td>
        				<td><?php echo $item->name; ?></td>
         				<td><?php echo $item->parent; ?></td>
        				<td><a href="<?php echo JRoute::_('index.php?option=com_thm_repo&task=file.edit&id=' . (int) $item->id); ?>">
        					<?php echo $item->path; ?></a></td>
        				<td><?php echo $item->title; ?></td>
        				<td align="center">
        				<a href="<?php echo JRoute::_('index.php?option=com_thm_repo&view=files&downloadid=' . (int) $item->id); ?>">
        					<img src="components/com_thm_repo/img/download.png"></a>
 						<?php 
						if (isset($_GET['downloadid']))
						{
							$id = $_GET['downloadid'];
							$model->download($id);
						}
						?>       				
        				</td>
        				<td><input type=button 
        						onClick=
        							"location.href='<?php echo JRoute::_('index.php?option=com_thm_repo&view=versions&id=' . (int) $item->id); ?>'" 
        						value='<?php echo JText::_('COM_THM_REPO_VIEW_VERSIONS'); ?>'></td>
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
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>