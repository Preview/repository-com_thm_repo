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
// No direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&layout=edit&id='.(int) $this->item->id); ?>"
      method="post" name="adminForm" id="folder-form">
	<fieldset class="adminform">
      	<legend><?php echo JText::_( 'COM_THM_REPO_FOLDER_DETAILS' ); ?></legend>
      	<ul class="adminformlist">
      		<li><?php echo $this->form->getLabel('name'); ?>
      			<?php echo $this->form->getInput('name'); ?>
      		</li>
      		<li><?php echo $this->form->getLabel('description'); ?>
      			<?php echo $this->form->getInput('description'); ?>
      		</li>
      		<li><?php echo $this->form->getLabel('parent_id'); ?>
      			<?php echo $this->form->getInput('parent_id'); ?>
      		</li>
      		<li><?php echo $this->form->getLabel('viewlevel'); ?>
      			<?php echo $this->form->getInput('viewlevel'); ?>
      		</li>
		</ul>
	</fieldset>
		<fieldset class="adminform">
      	<legend><?php echo JText::_( 'COM_THM_REPO_FOLDER_INFOS' ); ?></legend>
      	<ul class="adminformlist">
      		<li><?php echo $this->form->getLabel('created_by'); ?>
      			<?php echo $this->form->getInput('created_by'); ?>
      		</li>
      		<li><?php echo $this->form->getLabel('created'); ?>
      			<?php echo $this->form->getInput('created'); ?>
      		</li>
      		<li><?php echo $this->form->getLabel('modified_by'); ?>
      			<?php echo $this->form->getInput('modified_by'); ?>
      		</li>
      		<li><?php echo $this->form->getLabel('modified'); ?>
      			<?php echo $this->form->getInput('modified'); ?>
      		</li>
		</ul>
	</fieldset>
	
	<div class="clr"></div>
	<div class="width-100 fltlft">
	<?php echo JHtml::_('sliders.panel', JText::_('COM_THM_REPO_ACCESS'), 'accesscontrol'); ?>
	<fieldset class="panelform">
		<ul class="adminformlist">
			<li>
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</li>
		</ul>
	</fieldset>
	<?php echo JHtml::_('sliders.end'); ?>
	</div>
	<div>
		<input type="hidden" name="task" value="folder.edit" />
        <?php echo JHtml::_('form.token'); ?>
	</div>
</form>