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

$user = JFactory::getUser();
?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&view=links'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <input type="text" name="filter_search" id="filter_search"
                   value="<?php echo $this->escape($this->searchterms); ?>"
                   title="<?php echo JText::_('COM_THM_REPO_SEARCH_NAME'); ?>" />
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
            <th width="1%" rowspan="2">
                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>
            <th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_ID', 'e.id', $this->sortDirection, $this->sortColumn); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_NAME', 'l.name', $this->sortDirection, $this->sortColumn); ?></th>
            <th><?php echo JText::_('COM_THM_REPO_VIEW_PUBLISHED'); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_FOLDER', 'f.parent', $this->sortDirection, $this->sortColumn); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_LINK', 'l.link', $this->sortDirection, $this->sortColumn); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_VIEWLEVEL', 'v.title', $this->sortDirection, $this->sortColumn); ?></th>
            <th width="5%"><?php echo JText::_('COM_THM_REPO_VIEW_OPENLINK');?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($this->items as $i => $item)
        {
            ?>
            <?php $canChange = $user->authorise('core.edit', 'com_content.entity.' . $item->id);?>
            <tr class="row<?php echo $i % 2; ?>">
                <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                <td><?php echo $item->id; ?></td>
                <td><?php echo $item->name; ?></td>
                <td align="center">
                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'links.', $canChange); ?>
                </td>
                <td><?php echo $item->parent; ?></td>
                <td><a href="<?php echo JRoute::_('index.php?option=com_thm_repo&task=link.edit&id=' . (int) $item->id); ?>">
                        <?php echo $item->link; ?></a></td>
                <td><?php echo $item->title; ?></td>
                <td align="center"><a href="<?php echo $item->link;?>" target="_blank"><img src="components/com_thm_repo/img/link.png" /></a>
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