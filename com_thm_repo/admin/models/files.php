<?php
/**
 * @package  	com_thm_repo
 * @author      Stefan Schneider	<stefan.schneider@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * FilesList Model
*/
class THM_RepoModelFiles extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 * @see        JController
	 */
	public function __construct($config = array())
	{
		$config['filter_fields'] = array(
				'id',
				'name',
				'path',
				'mimeType'
		);
		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('id', 'ASC');
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
		$query->select('id,name,path,size,mimeType');
		// From the files table
		$query->from('#__thm_repo_files');
		// Order query
		$query->order($db->escape($this->getState('list.ordering', 'id')).' '.
				$db->escape($this->getState('list.direction', 'ASC')));
		
		return $query;
	}
}