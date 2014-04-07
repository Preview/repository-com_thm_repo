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
      method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <div class="row-fluid">
                <legend><?php echo JText::_('COM_THM_REPO_FILE_DETAILS'); ?></legend>
                <div class="control-group">
                    <label for="file"><?php echo JText::_('COM_THM_REPO_FILE_UPLOAD'); ?></label>
                    <input type="file" name="file"/>
                </div>
                <div class="control-group">
                    <?php echo $this->form->getLabel('name'); ?>
                    <?php echo $this->form->getInput('name'); ?>
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
                <div class="control-group">
                    <?php echo $this->form->getLabel('current_version'); ?>
                    <?php echo $this->form->getInput('current_version'); ?>
                </div>
            </div>
        </fieldset>
            <fieldset class="adminform">
                <div class="row-fluid">
                <legend><?php echo JText::_('COM_THM_REPO_FILE_INFOS'); ?></legend>
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
        </fieldset>
        <div class="clr"></div>
        <div class="width-100 fltlft">
        <?php echo JHtml::_('sliders.panel', JText::_('COM_THM_REPO_ACCESS'), 'accesscontrol'); ?>
        <fieldset class="panelform">
            <div class="row-fluid">
                <div class="control-group">
                    <?php echo $this->form->getLabel('rules'); ?>
                    <?php echo $this->form->getInput('rules'); ?>
                </div>
            </div>
        </fieldset>
        <?php echo JHtml::_('sliders.end'); ?>
        </div>
            <div>
                    <input type="hidden" name="task" value="file.edit" />
                    <?php echo JHtml::_('form.token'); ?>
            </div>
    </div>
</form>