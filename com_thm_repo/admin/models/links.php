<?php

/**
 * @category    Joomla component
 * @package     THM_Repo
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
 * THM_RepoModelLinks class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoModelLinks extends JModelList
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
		
		// Select some fields
		$query->select('e.*, l.id, l.*, v.title, f.name AS parent');
		
		// From the links table
		$query->from('#__thm_repo_entity AS e');
		$query->join('INNER', '#__thm_repo_link AS l ON e.id = l.id');
		$query->join('INNER', '#__viewlevels AS v on e.viewlevel = v.id');
		$query->join('LEFT', '#__thm_repo_folder AS f on e.parent_id = f.id');
		
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
	protected function populateState($ordering = 'e.id', $direction = 'ASC') 
	{
		parent::populateState($ordering, $direction);
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
				'l.link',
				'l.name',
				'f.parent',
				'v.title'
		);
		parent::__construct($config);
	}
}