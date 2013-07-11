<?php

/**
 * @package    THM_REPO
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
 * EntitiesList Model
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
		$query2 = $db->getQuery(true);
		
		
		// Select some fields
		$query->select('entity.id AS id, entity.*, link.link, file.path, viewlevel.title');
		
		// From the entity table
		$query->from('#__thm_repo_entity AS entity');
		
		// Get File Infos
		$query->join('LEFT', '#__thm_repo_file AS file ON entity.id = file.id');
		$query->join('LEFT', '#__thm_repo_link AS link ON entity.id = link.id');
		$query->join('INNER', '#__viewlevels AS viewlevel on entity.viewlevels = viewlevel.id');
		if ($id != null)
		{
			$query->where('entity.parent_id = ' . $id);
		}
		
// 		// Select some fields
// 		$query2->select('entity.id');
		
// 		// From the entity table
// 		$query2->from('#__thm_repo_entity AS entity');

// 		// Get Link Infos
// 		$query2->join('INNER', '#__thm_repo_link AS link ON entity.id = link.id');
		
// 		if ($id != null)
// 		{
// 			$query2->where('entity.parent_id = ' . $id);
// 		}
// 		$query->union($query2);

		
		
		
		$query->order($db->escape($this->getState('list.ordering', 'entity.id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		
		return $query;
	}
	
	/**
	 * Order State of Entities View
	 * 
	 * @param   string $ordering 
	 * @param   string $direction
	 */
	protected function populateState($ordering = null, $direction = null) 
	{
		parent::populateState('entity.id', 'ASC');
	}
	
	/**
	 * Filter Fields
	 * 
	 * @param   unknown $config
	 */
	public function __construct($config = array())
	{
		$config['filter_fields'] = array(
				'entity.id',
				'name',
				'path',
				'viewlevel.title'
		);
		parent::__construct($config);
	}
}