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
 * LinksList Model
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
		$query->select('a.*, b.id AS link_id, b.*, c.title, d.name AS parent');
		
		// From the links table
		$query->from('#__thm_repo_entity AS a');
		$query->join('INNER', '#__thm_repo_link AS b ON a.id = b.id');
		$query->join('INNER', '#__viewlevels AS c on a.viewlevels = c.id');
		$query->join('LEFT', '#__thm_repo_folder AS d on a.parent_id = d.id');
		
		
		
		$query->order($db->escape($this->getState('list.ordering', 'a.id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		
		return $query;
	}
	
	/**
	 * Order State of Links View
	 * 
	 * @param   string $ordering
	 * @param   string $direction
	 */
	protected function populateState($ordering = null, $direction = null) 
	{
		parent::populateState('a.id', 'ASC');
	}
	
	/**
	 * Filter Fields
	 * 
	 * @param   unknown $config
	 */
	public function __construct($config = array())
	{
		$config['filter_fields'] = array(
				'a.id',
				'b.link',
				'a.name',
				'd.parent',
				'c.title'
		);
		parent::__construct($config);
	}
}