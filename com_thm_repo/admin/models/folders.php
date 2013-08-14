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
 * THM_RepoModelFolders class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoModelFolders extends JModelList
{
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
		parent::populateState('f.lft', 'ASC');
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
				'f.lft'
		);
		parent::__construct($config);
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
		$query->select('f.*, v.title, COUNT(*)-1 AS level');
		$query->from('#__thm_repo_folder AS f, #__thm_repo_folder AS p');
		$query->where('f.lft BETWEEN p.lft AND p.rgt');
		$query->join('INNER', '#__viewlevels AS v on p.viewlevel = v.id');
		$query->group('f.lft');
		$query->order($db->escape($this->getState('list.ordering', 'f.lft')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		
		return $query;
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
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$order = JRequest::getVar('order', array(), 'post', 'array');
		$err = 0;
		

		
		if (isset($direction))
		{
			// Load lft, rgt and parent_id from folder
			$query = $db->getQuery(true);
			$query->select('f.lft, f.rgt, f.parent_id');
			$query->from('#__thm_repo_folder AS f');
			$query->where('id = ' . (int) $cid[0]);
			$db->setQuery($query);
			$folderdata = $db->loadObject();				
			
			// Order up
			if ($direction == -1)
			{
				// Load all folders that need to be moved up
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__thm_repo_folder AS f');
				$query->where('lft >= ' . $folderdata->lft . ' AND rgt <= ' . $folderdata->rgt);
				$db->setQuery($query);
				$foldersUp = $db->loadObjectList();
					
				// Load folder thats left of ordering folder
				$query = $db->getQuery(true);
				$query->select('f.lft, f.rgt');
				$query->from('#__thm_repo_folder AS f');
				$query->where('parent_id = ' . $folderdata->parent_id . ' AND lft < ' . $folderdata->lft);
				$query->order('f.lft');
				$db->setQuery($query);
				$folderLeft = $db->loadObjectList();
				$folderLeft = (object) end($folderLeft);
					
				// Load all folders that need to be moved down
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__thm_repo_folder AS f');
				$query->where('lft >= ' . $folderLeft->lft . ' AND rgt <= ' . $folderLeft->rgt);
				$db->setQuery($query);
				$foldersDown = $db->loadObjectList();
					
				// Set width
				$widthDown = $folderdata->rgt - $folderdata->lft + 1;
				$widthUp = $folderLeft->rgt - $folderLeft->lft + 1;

				// Start Transaction	
				$db->transactionStart();
					
				// Move folders down
				foreach ($foldersDown AS $folderDown)
				{
					$query = $db->getQuery(true);
					$query->update('#__thm_repo_folder AS f');
					$query->set('f.lft = f.lft + ' . $widthDown . ', f.rgt = f.rgt + ' . $widthDown);
					$query->where('f.id = ' . (int) $folderDown->id);
					$db->setQuery($query);
					if (!$db->query())
					{
						return false;
					}
				}
					
				// Move folders up
				foreach ($foldersUp AS $folderUp)
				{
					$query = $db->getQuery(true);
					$query->update('#__thm_repo_folder AS f');
					$query->set('f.lft = f.lft - ' . $widthUp . ', f.rgt = f.rgt - ' . $widthUp);
					$query->where('f.id = ' . (int) $folderUp->id);
					$db->setQuery($query);
					if (!$db->query())
					{
						return false;
					}
				
				}
				// Commit Transaction
				$db->transactionCommit();		
			}
			elseif ($direction == 1)
			{
				// Load all folders that need to be moved down
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__thm_repo_folder AS f');
				$query->where('lft >= ' . $folderdata->lft . ' AND rgt <= ' . $folderdata->rgt);
				$db->setQuery($query);
				$foldersDown = $db->loadObjectList();
					
				// Load folder thats right of ordering folder
				$query = $db->getQuery(true);
				$query->select('f.lft, f.rgt');
				$query->from('#__thm_repo_folder AS f');
				$query->where('parent_id = ' . $folderdata->parent_id . ' AND rgt > ' . $folderdata->rgt);
				$query->order('f.lft');
				$db->setQuery($query);
				$folderRight = $db->loadObjectList();
				$folderRight = (object) reset($folderRight);
					
				// Load all folders that need to be Up down
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__thm_repo_folder AS f');
				$query->where('lft >= ' . $folderRight->lft . ' AND rgt <= ' . $folderRight->rgt);
				$db->setQuery($query);
				$foldersUp = $db->loadObjectList();
					
				// Set width
				$widthUp = $folderdata->rgt - $folderdata->lft + 1;
				$widthDown = $folderRight->rgt - $folderRight->lft + 1;
				
				$db->transactionStart();
					
				// Move folders down
				foreach ($foldersDown AS $folderDown)
				{
					$query = $db->getQuery(true);
					$query->update('#__thm_repo_folder AS f');
					$query->set('f.lft = f.lft + ' . $widthDown . ', f.rgt = f.rgt + ' . $widthDown);
					$query->where('f.id = ' . (int) $folderDown->id);
					$db->setQuery($query);
					if (!$db->query())
					{
						return false;
					}
				}
					
				// Move folders up
				foreach ($foldersUp AS $folderUp)
				{
					$query = $db->getQuery(true);
					$query->update('#__thm_repo_folder AS f');
					$query->set('f.lft = f.lft - ' . $widthUp . ', f.rgt = f.rgt - ' . $widthUp);
					$query->where('f.id = ' . (int) $folderUp->id);
					$db->setQuery($query);
					if (!$db->query())
					{
						return false;
					}
				
				}
				$db->transactionCommit();			
			}
			
		}
		return true;
	}
	
}