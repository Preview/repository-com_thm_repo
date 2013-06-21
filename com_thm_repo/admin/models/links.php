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
		$query->select('a.*, b.id AS link_id, b.*');
		
		// From the links table
		$query->from('#__thm_repo_entity AS a');
		$query->join('INNER', '#__thm_repo_link AS b ON a.id = b.id');
		return $query;
	}
}