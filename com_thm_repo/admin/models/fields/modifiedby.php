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
 * ModiefiedBy Form Field class for the THM Repo component
*/
class JFormFieldModifiedby extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'modifiedby';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
        
        
		//Load user
		$user = JFactory::getUser();
			
		$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$user->id.'" />';
		$html[] = "<div>".$user->name." (".$user->username.")</div>";
        
		return implode($html);
	}
}