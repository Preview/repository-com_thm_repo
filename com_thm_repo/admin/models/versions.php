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
 * THM_RepoModelVersions class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoModelVersions extends JModelList
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
		$query->select('v.path, v.name, v.size, v.mimetype, v.modified, v.version AS id, f.current_version');
		
		// From the links table
		$query->from('#__thm_repo_version as v');
		if ($id != null)
		{
			$query->where('v.id = ' . $id);
		}
		$query->join('LEFT', '#__thm_repo_file AS f on v.version = f.current_version and v.id = f.id');
		
		
		$query->order($db->escape($this->getState('list.ordering', 'id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		
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
		parent::populateState('id', 'ASC');
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
				'id',
				'v.version',
				'v.path',
				'v.name',
				'v.size',
				'v.mimetype',
				'v.modified'
		);
		parent::__construct($config);
	}
	
	/**
	 * Updates the current Version on file table
	 * 
	 * @return boolean true or false
	 */
	public function setversion()
	{
		$version = JRequest::getVar('cid', array(), 'post', 'array');
		$version = $version[0];
		$id = JRequest::getVar('id');
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->update('#__thm_repo_file AS f');
		$query->set('f.current_version = ' . $version);
		$query->where('f.id = ' . $id);
		$db->setQuery($query);
		if (!$db->query())
		{
			return false;
		}
		return true;
	}
}