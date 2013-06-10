<?php
/**
 * @package  	com_thm_repo
 * @author      Stefan Schneider	<stefan.schneider@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
// No direct access to this file
defined('_JEXEC') or die;

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Modiefied Form Field class for the THM Repo component
*/
class JFormFieldModified extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'modified';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
        
        
		$old_time_updated = $this->value;
        if (!$old_time_updated) {
            $html[] = '-';
        } else {
            $jdate = new JDate($old_time_updated);
            $pretty_date = $jdate->format(JText::_('DATE_FORMAT_LC2'));
            $html[] = "<div>".$pretty_date."</div>";
        }
        $time_updated = date("Y-m-d H:i:s");
        $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$time_updated.'" />';
        
		return implode($html);
	}
}