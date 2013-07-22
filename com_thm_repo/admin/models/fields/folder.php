<?php
/**
 * @package    THM_Repo
 * @author     Stefan Schneider, <stefan.schneider@mni.thm.de>
 * @copyright  2013 TH Mittelhessen
 * @license    GNU GPL v.2
 * @link       www.mni.thm.de
 */
// No direct access to this file
defined('_JEXEC') or die;

// Import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Folder Form Field class for the THM Repo component
*/
class JFormFieldFolder extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var         string
	 */
	protected $type = 'folder';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return      array           An array of JHtml options.
	 */
	protected function getOptions()
	{
		
		// Get current id
		$id = JFactory::getApplication()->input->getInt('id');
		
		// Gets an entry with the current id
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,parent_id,name');
		$query->from('#__thm_repo_folder');
		$query->where('id = ' . $id);
		$db->setQuery((string) $query);
		$result = $db->loadObjectList();
		
		// $allData => All Folders
		$allData = $this->getAll($id);
		
		// $childData => Child Folders of the needed id
		$childData = $this->getChilds($result);
		
		// Remove the childs that are in $childData from $allData
 		if ($allData && $childData)
 		{
	 		foreach ($allData as $key => $allDat)
	 		{
	 			foreach ($childData as $childDat) 
	 			{
	 				if ($childDat->id == $allDat->id)
	 				{
	 					unset($allData[$key]);
	 				}
	 			}
	 		}
 		}

		$messages = $this->parentChildSort_r('id', 'parent_id', $allData);
		


		if ($messages)
		{
			foreach ($messages as $message)
			{	
				// Create select list without current id
				if ($message->id != $id) 
				{
					$count = 0;
					$prefix = '';
					while ($count < $message->depth)
					{
						$prefix .= '-';
						$count++;
					}
					
					$options[] = JHtml::_('select.option', $message->id, $prefix . $message->name);
				}
			}
			$options = array_merge(parent::getOptions(), $options);
		}
		
		return $options;
	}
	
	/**
	 * Creates a list with all childs, grandchilds, grandgrand..
	 * 
	 * @param   unknown  $parents  Childs of an id
	 * 
	 * @return multitype:|NULL
	 */
	public function getChilds($parents)
	{
		$results = array();

		if ($parents)
		{
			foreach ($parents as $parent)
			{
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->select('id,parent_id,name');
				$query->from('#__thm_repo_folder');
				$query->where('parent_id = ' . $parent->id);
				$db->setQuery((string) $query);
				$childs = $db->loadObjectList();
				$result = $this->getChilds($childs);
				$results = array_merge((array) $results, (array) $childs, (array) $result);
			}
			return $results;
		}
		return null;
	}	
	
	/**
	 * Creates a List with all Folders
	 * 
	 * @param   unknown  $id  The Current id
	 * 
	 * @return Returns an Array with all Folder elements
	 */
	protected function getAll($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,parent_id,name');
		$query->from('#__thm_repo_folder');
		$db->setQuery((string) $query);
		return $result = $db->loadObjectList();
		
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