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
defined('_JEXEC') or die();

// Import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * THM_RepoModelFiles class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoModelFiles extends JModelList
{
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
				've.name',
				've.path',
				'fo.parent',
				'vi.title'
		);
		parent::__construct($config);
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
		// List state information.
		parent::populateState('e.id', 'ASC');
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
		$query->select('e.*, vi.title, fo.name AS parent, ve.*, fi.*');
		
		// From the links table
		$query->from('#__thm_repo_entity AS e');
		$query->join('INNER', '#__thm_repo_version AS ve ON e.id = ve.id');
		$query->join('INNER', '#__thm_repo_file AS fi ON ve.id = fi.id AND ve.version = fi.current_version');
		$query->join('INNER', '#__viewlevels AS vi on e.viewlevel = vi.id');
		$query->join('LEFT', '#__thm_repo_folder AS fo on e.parent_id = fo.id');
		
		
		$query->order($db->escape($this->getState('list.ordering', 'e.id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		
		return $query;
	}
	
	/**
	 * Function to download a file
	 * 
	 * @param   int  $id  ID of the file
	 * 
	 * @return  void
	 */
	public function download($id)
	{	
		// GetDBO
		$db = JFactory::getDBO();
		
		// Get current Versionnumber of file
		$query = $db->getQuery(true);
		$query->select('current_version');
		$query->from('#__thm_repo_file');
		$query->where('id = ' . $id);
		$db->setQuery($query);
		$version = $db->loadResult();
				
		// Get Data from the Version
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__thm_repo_version');
		$query->where('id = ' . $id . ' AND version = ' . $version);
		$db->setQuery($query);
		$versiondata = $db->loadObject();
		
		// Clean the output buffer
		ob_end_clean();
		
		/* create the header */
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false); 
		
		// Required for certain browsers
		header("Content-Type: " . filetype($versiondata->path));
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=\"" . $versiondata->name . "\";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . $versiondata->size);
		
		/* download file */
// 		flush();
		readfile($versiondata->path);
	}
	
}