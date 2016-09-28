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
// No direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&layout=edit&id=' . (int) $this->item->id); ?>"
	  method="post" name="adminForm" id="adminForm">
	<div class="form-horizontal">
		<fieldset class="adminform">
			<div class="row-fluid">
				<legend><?php echo JText::_('COM_THM_REPO_FOLDER_DETAILS'); ?></legend>
				<div class="control-group">
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</div>
				<div class="control-group">
					<?php echo $this->form->getLabel('description'); ?>
					<?php echo $this->form->getInput('description'); ?>
				</div>
				<div class="control-group">
					<?php echo $this->form->getLabel('parent_id'); ?>
					<?php echo $this->form->getInput('parent_id'); ?>
				</div>
				<div class="control-group">
					<?php echo $this->form->getLabel('published'); ?>
					<?php echo $this->form->getInput('published'); ?>
				</div>
				<div class="control-group">
					<?php echo $this->form->getLabel('viewlevel'); ?>
					<?php echo $this->form->getInput('viewlevel'); ?>
				</div>
			</div>
		</fieldset>
		<fieldset class="adminform">
			<div class="row-fluid">
				<legend><?php echo JText::_('COM_THM_REPO_FOLDER_INFOS'); ?></legend>
				<div class="control-group">
					<?php echo $this->form->getLabel('created_by'); ?>
					<?php echo $this->form->getInput('created_by'); ?>
				</div>
				<div class="control-group">
					<?php echo $this->form->getLabel('created'); ?>
					<?php echo $this->form->getInput('created'); ?>
				</div>
				<div class="control-group">
					<?php echo $this->form->getLabel('modified_by'); ?>
					<?php echo $this->form->getInput('modified_by'); ?>
				</div>
				<div class="control-group">
					<?php echo $this->form->getLabel('modified'); ?>
					<?php echo $this->form->getInput('modified'); ?>
				</div>
			</div>
		</fieldset>
		<div>
			<input type="hidden" name="task" value="folder.edit"/>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>
<div class="form-horizontal">
	<h3><?php echo JText::_('COM_THM_REPO_ACCESS')?></h3>
	<fieldset class="panelform">
		<div class="row-fluid">
			<div class="control-group">
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</div>
		</div>
	</fieldset>
</div>
