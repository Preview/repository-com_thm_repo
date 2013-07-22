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
defined('_JEXEC') or die('Restricted access');

// Import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * FoldersList Model
*/
class THM_RepoModelFolders extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select all fields from folder table
		$query->select('a.*, b.title');
		$query->from('#__thm_repo_folder AS a');
		$query->join('INNER', '#__viewlevels AS b on a.viewlevels = b.id');
		
		return $query;
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
	protected function parentChildSort_r($idField, $parentField, $els, $parentID = null, &$result = array(), &$depth = 0)
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