<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


$app = JFactory::getApplication();


if ($app->isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework', true);

// Get Model Functions
$model = JModelLegacy::getInstance('entities', 'THM_RepoModel');

// Get ID from URL
$id = JFactory::getApplication()->input->getInt('id', 0);
$listOrder	= $this->sortColumn;
$listDirn	= $this->sortDirection;
$saveOrder	= $listOrder == 'e.ordering';
$user = JFactory::getUser();

$function  = $app->input->getCmd('function', 'jSelectRepoEntity');
?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&view=entities&id=' . (int) $id . '&layout=modal&tmpl=component&function=' . $function .'&' . JSession::getFormToken()) ?>"
      method="post" name="adminForm" id="adminForm" class="form-inline">
	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
				<label for="filter_search">
					<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
				</label>
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom">
					<span class="icon-search"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.id('filter_search').value='';this.form.submit();">
					<span class="icon-remove"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="clearfix"></div>
		</div>
		<hr class="hr-condensed" />
	</fieldset>

	<table class="table table-striped table-condensed">
        <thead>
        <tr>
            <th></th>
            <th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_ID', 'e.id', $this->sortDirection, $this->sortColumn); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_NAME', 'name', $this->sortDirection, $this->sortColumn); ?></th>
            <th><?php echo JText::_('COM_THM_REPO_VIEW_PUBLISHED'); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_TYPE', 'path', $this->sortDirection, $this->sortColumn); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_ENTITIES', 'path', $this->sortDirection, $this->sortColumn); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'COM_THM_REPO_VIEW_VIEWLEVEL', 'v.title', $this->sortDirection, $this->sortColumn); ?></th>
        </tr>
        </thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
        <?php
        foreach ($this->items as $i => $item)
        { ?>

            <tr class="row<?php echo $i % 2; ?>">
                <td><button name="" type="button" value="Select" onclick="<?php echo "$function({$item->id})"; ?>"></td>
                <td><?php echo $item->id; ?></td>
                <td> <?php echo $item->lname; ?>
                     <?php echo $item->vename; ?>
                </td>
                <td align="center">
                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'entities.', false); ?>
                </td>
                <td align="center">
                    <?php
                    if ($item->path)
                    {
                        ?>
                        <img src="components/com_thm_repo/img/file.png" >
                    <?php
                    }
                    else
                    {
                        ?>
                        <img src="components/com_thm_repo/img/link.png" >
                    <?php
                    }
                    ?>
                </td>
                <td>
                    <?php echo $item->path; ?>
                    <?php echo $item->link; ?>
                </td>
                <td><?php echo $item->title; ?></td>
            </tr>
        <?php
        }
        ?>
		</tbody>
	</table>
</form>
