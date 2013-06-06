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
<?php foreach($this->form->getFieldset() as $field): ?>
                        <li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
                </ul>
        </fieldset>
        <div>
                <input type="hidden" name="task" value="link.edit" />
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>