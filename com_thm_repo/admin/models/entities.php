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
		$query->select('e.id AS id, ve.name as vename, l.link, l.name as lname, ve.path, vi.title, e.ordering');
		
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
				'vi.title',
				'e.ordering'
		);
		parent::__construct($config);
	}
	
	/**
	 * Method to reorder
	 *
	 * @param   String  $direction  null
	 *
	 * @return	Bool true on sucess
	 */
	public function reorder($direction = null)
	{
		$db =& JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$order = JRequest::getVar('order', array(), 'post', 'array');
		$err = 0;
	
		if (isset($direction))
		{
			/*
				$query = "SELECT e.order FROM #__thm_repo_entities as e WHERE `id` = " . $cid[0] . ";
			*/
			$query = $db->getQuery(true);
			$query->select('e.ordering');
			$query->from('#__thm_repo_entity AS e');
			$query->where('id = ' . (int) $cid[0]);
			$db->setQuery($query);
			$itemOrder = $db->loadObject();
	
			if ($direction == -1)
			{
				/*
					$query = "UPDATE #__thm_groups_structure as a SET"
				. " a.order=" . $itemOrder->order
				. " WHERE a.order=" . ($itemOrder->order - 1);
				*/
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_entity AS e');
				$query->set('e.ordering = ' . $itemOrder->ordering);
				$query->where('e.ordering = ' . ($itemOrder->ordering - 1));
	
				$db->setQuery($query);
				if (!$db->query())
				{
					$err = 1;
				}
				/*
					$query = "UPDATE #__thm_groups_structure as a SET"
				. " a.order=" . ($itemOrder->order - 1)
				. " WHERE a.id=" . $cid[0];
				*/
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_entity AS e');
				$query->set('e.ordering = ' . ($itemOrder->ordering - 1));
				$query->where('e.id = ' . $cid[0]);
	
				$db->setQuery($query);
				if (!$db->query())
				{
					$err = 1;
				}
			}
			elseif ($direction == 1)
			{
				/*
					$query = "UPDATE #__thm_groups_structure as a SET"
				. " a.order=" . $itemOrder->order
				. " WHERE a.order=" . ($itemOrder->order + 1);
				*/
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_entity AS e');
				$query->set('e.ordering = ' . $itemOrder->ordering);
				$query->where('e.ordering = ' . ($itemOrder->ordering + 1));
	
				$db->setQuery($query);
				if (!$db->query())
				{
					$err = 1;
				}
				/*
					$query = "UPDATE #__thm_groups_structure as a SET"
				. " a.order=" . ($itemOrder->order + 1)
				. " WHERE a.id=" . $cid[0];
				*/
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_entity AS e');
				$query->set('e.ordering = ' . ($itemOrder->ordering + 1));
				$query->where('e.id = ' . $cid[0]);
				$db->setQuery($query);
				if (!$db->query())
				{
					$err = 1;
				}
			}
		}
		else
		{
			$i = 0;
			foreach ($order as $itemOrder)
			{
				/*
					$query = "UPDATE #__thm_groups_structure as a SET"
				. " a.order=" . ($itemOrder)
				. " WHERE a.id=" . $cid[$i];
				*/
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_entity AS e');
				$query->set('e.ordering = ' . ($itemOrder));
				$query->where('e.id = ' . $cid[$i]);
	
				$db->setQuery($query);
				if (!$db->query())
				{
					$err = 1;
				}
				$i++;
			}
		}
		if (!$err)
		{
			return true;
		}
		else
		{
			return false;
		}
	}	
}