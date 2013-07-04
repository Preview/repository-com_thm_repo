<?php
/**
 * @package    THM_Repo
 * @author     Stefan Schneider, <stefan.schneider@mni.thm.de>
 * @copyright  2013 TH Mittelhessen
 * @license    GNU GPL v.2
 * @link       www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die();

// Import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * FilesList Model
*/
class THM_RepoModelFiles extends JModelList
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
				'b.path',
				'a.parent_id',
				'a.viewlevels'
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
		
		// Select some fields
		$query->select('a.*, b.*');
		
		// From the links table
		$query->from('#__thm_repo_entity AS a');
		$query->join('INNER', '#__thm_repo_file AS b ON a.id = b.id');
		
		$query->order($db->escape($this->getState('list.ordering', 'a.id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		
		return $query;
	}
	
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