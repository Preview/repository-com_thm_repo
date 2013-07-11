<?php

/**
 * @package    THM_Repo
 * @author     Stefan Schneider, <stefan.schneider@mni.thm.de>
 * @copyright  2013 TH Mittelhessen
 * @license    GNU GPL v.2
 * @link       www.mni.thm.de
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
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 */
	public function __construct($config = array())
	{
		$config['filter_fields'] = array(
				'a.id',
				'a.name',
				'a.parent_id',
				'b.title'
		);
		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.id', 'ASC');
	}
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
		
		$query->order($db->escape($this->getState('list.ordering', 'default_sort_column')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		return $query;
	}
	
	/**
	 * Gets the foldername 
	 * 
	 * @param   unknown  $id  The Current id
	 * 
	 * @return unknown Returns the Current Foldername
	 */
	public function getFoldername($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__thm_repo_folder');
		$query->where('id = ' . $id);
		$db->setQuery($query);
		$result = $db->loadResult();
	
		return $result;
	}
}