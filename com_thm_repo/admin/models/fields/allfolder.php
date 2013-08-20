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
// No direct access to this file
defined('_JEXEC') or die;

// Import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Folder Form Field class for the THM Repo component
 * 
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
*/
class JFormFieldallFolder extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var         string
	 */
	protected $type = 'allfolder';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return      array           An array of JHtml options.
	 */
	protected function getOptions()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,parent_id,name');
		$query->from('#__thm_repo_folder');
		$db->setQuery((string) $query);
		$messages = $db->loadObjectList();
		$options = array();
		$messages = $this->parentChildSort_r('id', 'parent_id', $messages);
		if ($messages)
		{
			foreach ($messages as $message)
			{	
				$count = 0;
				$prefix = '';
				while ($count < $message->depth)
				{
					$prefix .= '-';	
					$count++;
				}
				
				// Create select list
				$options[] = JHtml::_('select.option', $message->id, $prefix . $message->name);
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}

	/**
	 * sorts an array after parent, child, grandchild,...
	 *
	 * @param   string  $idField      The item's ID identifier (required)
	 * @param   string  $parentField  The item's parent identifier (required)
	 * @param   array	$els          The array (required)
	 * @param   string  $parentID	  The parent ID for which to sort (internal)
	 * @param   array   &$result	  The result set (internal)
	 * @param   number  &$depth		  The depth (internal)
	 *
	 * @return array sorted array
	 */
	public function parentChildSort_r($idField, $parentField, $els, $parentID = null, &$result = array(), &$depth = 0)
	{
		foreach ($els as $key => $value):
		if ($value->$parentField == $parentID)
		{
			$value->depth = $depth;
			array_push($result, $value);
			unset($els[$key]);
			$oldParent = $parentID;
			$parentID = $value->$idField;
			$depth++;
			$this->parentChildSort_r($idField, $parentField, $els, $parentID, $result, $depth);
			$parentID = $oldParent;
			$depth--;
		}
		endforeach;
		return $result;
	}
}