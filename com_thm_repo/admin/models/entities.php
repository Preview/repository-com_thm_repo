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
 * THM_RepoModelEntities class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoModelEntities extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		$id = JRequest::getVar('id');
		
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		
		// Select some fields
		$query->select('e.id AS id, ve.name as vename, l.link, l.name as lname, ve.path, vi.title');
		
		// From the entity table
		$query->from('#__thm_repo_entity AS e');
		
		// Get File Infos
		$query->join('LEFT', '#__thm_repo_file AS f ON e.id = f.id');
		$query->join('LEFT', '#__thm_repo_version AS ve ON ve.id = e.id AND f.current_version = ve.version');
		$query->join('LEFT', '#__thm_repo_link AS l ON e.id = l.id');
		$query->join('INNER', '#__viewlevels AS vi on e.viewlevel = vi.id');
		if ($id != null)
		{
			$query->where('e.parent_id = ' . $id);
		}

		
		$query->order($db->escape($this->getState('list.ordering', 'e.id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		
		return $query;
	}
	
	/**
	 * Method to populate
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 * 
	 * @access  protected
	 * @return	populatestate
	 */
	protected function populateState($ordering = null, $direction = null) 
	{
		parent::populateState('e.id', 'ASC');
	}
	
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
				'e.id',
				'name',
				'path',
				'vi.title'
		);
		parent::__construct($config);
	}
}