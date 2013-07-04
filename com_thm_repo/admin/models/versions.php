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
 * VersionsList Model
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
		$query->select('*');
		
		// From the links table
		$query->from('#__thm_repo_version');
		if ($id != null)
		{
			$query->where('id = ' . $id);
		}
		
		$query->order($db->escape($this->getState('list.ordering', 'id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		
		return $query;
	}
	
	/**
	 * Order State of Versions View
	 * 
	 * @param   string $ordering 
	 * @param   string $direction
	 */
	protected function populateState($ordering = null, $direction = null) 
	{
		parent::populateState('id', 'ASC');
	}
	
	/**
	 * Filter Fields
	 * 
	 * @param   unknown $config
	 */
	public function __construct($config = array())
	{
		$config['filter_fields'] = array(
				'id',
				'versionnumber',
				'path',
				'name',
				'size',
				'mimetype',
				'modified'
		);
		parent::__construct($config);
	}
}