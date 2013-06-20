<?php
/**
 * @package  	com_thm_repo
 * @author      Stefan Schneider	<stefan.schneider@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
// No direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_thm_repo&layout=edit&id='.(int) $this->item->id); ?>"
      method="post" name="adminForm" id="link-form">
	<fieldset class="adminform">
      	<legend><?php echo JText::_( 'COM_THM_REPO_LINK_DETAILS' ); ?></legend>
      	<ul class="adminformlist">
      		<li><?php echo $this->form->getLabel('name'); ?>
      			<?php echo $this->form->getInput('name'); ?>
      		</li>
      		<li><?php echo $this->form->getLabel('link'); ?>
      			<?php echo $this->form->getInput('link'); ?>
      		</li>
      		<li><?php echo $this->form->getLabel('description'); ?>
      			<?php echo $this->form->getInput('description'); ?>
      		</li>
      		<li><?php echo $this->form->getLabel('parent_id'); ?>
      			<?php echo $this->form->getInput('parent_id'); ?>
      		</li>
      		<li><?php echo $this->form->getLabel('viewlevels'); ?>
      			<?php echo $this->form->getInput('viewlevels'); ?>
      		</li>
		</ul>
	</fieldset>
		<fieldset class="adminform">
      	<legend><?php echo JText::_( 'COM_THM_REPO_LINK_INFOS' ); ?></legend>
      	<ul class="adminformlist">
      		<li><?php echo $this->form->getLabel('create_by'); ?>
      			<?php echo $this->form->getInput('create_by'); ?>
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
        <div>
                <input type="hidden" name="task" value="link.edit" />
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>