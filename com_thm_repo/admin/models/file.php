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
defined('_JEXEC') or die;

// Import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * THM_RepoModelFile class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoModelFile extends JModelAdmin
{
	/**
	 * @var array messages
	 */
	protected $messages;
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 * 
	 * @return  JTable  A database object
	 * 
	 * @since   2.5
	 */
	public function getTable($type = 'Entity', $prefix = 'THM_RepoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * 
	 * @return  mixed    A JForm object on success, false on failure
	 * 
	 * @since   2.5
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_thm_repo.file', 'file', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return      mixed   The data for the form.
	 * 
	 * @since       2.5
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_thm_repo.edit.file.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
	}
	  

  	/**
  	 * Method to get a single record.
  	 * 
  	 * @param   integer  $pk  The id of the primary key.
  	 * 
  	 * @return  mixed    Object on success, false on failure.
  	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		if ($pk > 0)
		{	
			// Get Data from #__thm_repo_version table and assign it to $item
			$data = $this->getData($item->id);
			$item->name = $data->name;
			$item->description = $data->description;
			$item->modified = $data->modified;
			$item->modified_by = $data->modified_by;
			$item->path = $data->path;
			$item->file_id = $data->id;
			$item->size = $data->size;
			$item->mimetype = $data->mimetype;
			$item->current_version = $data->current_version;
		}
		else
		{
			// Set Data NULL for creating new file
			$item->path = null;
			$item->file_id = null;
			$item->size = null;
			$item->mimetype = null;
			$item->current_version = null;
		}
		
		return $item;
	}
	
	/**
	 * Method to get the needed data from entity table
	 *
	 * @param   unknown  $id  ID from creating/editing entry
	 *
	 * @return mixed   The data from #__thm_repo_entity table.
	 */
	public function getData($id)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__thm_repo_version AS v');
		$query->join('INNER', '#__thm_repo_file AS f ON v.id = f.id AND v.version = f.current_version');
		$query->where('v.id = ' . $id);
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result;
	
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return	boolean	True on success.
	 */
	public function save($data)
	{
		$table = JTable::getInstance('Entity', 'THM_RepoTable');
		$table->save($data);
		
		// Retrieve file details from uploaded file, sent from adminForm form
		$file = JRequest::getVar('file', null, 'files', 'array');
		
		// Clean up filename to get rid of strange characters like spaces etc
		$filename = JFile::makeSafe($file['name']);

		
		// GetDBO
		$db = JFactory::getDBO();
		
		// Get Maximum Versionsnumber
		$query = $db->getQuery(true);
		$query->select('version');
		$query->from('#__thm_repo_version');
		$query->where('id = ' . $data['id']);
		$db->setQuery($query);
		$version = $db->loadResultArray();
		$version = max($version);
		

		// Assign filedata
		$filedata->id = $data['id'];
		$filedata->current_version = $version + 1;
			
		// Assign entity data
		$entitydata->id = $data['id'];
		$entitydata->parent_id = $data['parent_id'];
		$entitydata->viewlevel = $data['viewlevel'];
		$entitydata->created = $data['created'];
		$entitydata->created_by = $data['created_by'];
		
		// Assign version data
		$versiondata->id = $data['id'];
		$versiondata->version = $version + 1;
		$versiondata->name = $data['name'];
		$versiondata->description = $data['description'];
		$versiondata->modified = $data['modified'];
		$versiondata->modified_by = $data['modified_by'];
		$versiondata->path = $data['path'];
		$versiondata->size = $file['size'];
		$versiondata->mimetype = $file['type'];

	
		// New File is uploaded
		if ($entitydata->id == 0)
		{
			// Get Ordering count
			$query = $db->getQuery(true);
			$query->select('ordering');
			$query->from('#__thm_repo_entity');
			$query->where('parent_id = ' . $entitydata->parent_id);
			$db->setQuery($query);
			$ordering = $db->loadResultArray();
			
			// Increment Order Number and add to entitydata
			$entitydata->ordering = max($ordering) + 1;
			$entitydata->id = $table->id;
				
			if (!($db->updateObject('#__thm_repo_entity', $entitydata, 'id')))
			{
				return false;
			}
				
			// Insert created entity id to version dataid and filedata id
			$versiondata->id = $table->id;
			$filedata->id = $table->id;
			
			if (!($db->insertObject('#__thm_repo_file', $filedata, 'id')))
			{
				return false;
			}
						
			// Add Path to Versiondata
			$versiondata->path = DS . "media" . DS . "com_thm_repo" . DS .
				$versiondata->id . "_" . $versiondata->version . "_" . $filename;
			if (!($db->insertObject('#__thm_repo_version', $versiondata, 'id')))
			{
				return false;
			}
		}
		// Old File is updated
		else
		{	
			// Get Ordering and add to entitydata
			$query = $db->getQuery(true);
			$query->select('ordering');
			$query->from('#__thm_repo_entity');
			$query->where('id = ' . $entitydata->id);
			$db->setQuery($query);
			$ordering = $db->loadResultArray();
			$entitydata->ordering = ($ordering);
				
			// Increment Order Number and add to entitydata

			if (!($db->updateObject('#__thm_repo_entity', $entitydata, 'id')))
			{
				return false;
			}
			
			if (!($db->updateObject('#__thm_repo_file', $filedata, 'id')))
			{
				return false;
			}

			// A New File is uploaded
			if ($filename)
			{				
				// Add Path to Versiondata
				$versiondata->path = DS . "media" . DS . "com_thm_repo" . DS .
					$versiondata->id . "_" . $versiondata->version . "_" . $filename;
				if (!($db->insertObject('#__thm_repo_version', $versiondata, 'id')))
				{
					return false;
				}		
			}
			// No New File is uploaded
			else 
			{
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->select('path, size, mimetype');
				$query->from('#__thm_repo_version');
				$query->where('id = ' . (int) $data['id'] . ' AND version=' . (int) $data['current_version']);
				$db->setQuery($query);
				$result = $db->loadObject();

				$versiondata->path = $result->path;
				$versiondata->size = $result->size;
				$versiondata->mimetype = $result->mimetype;

				// Add Version without new file
				if (!($db->insertObject('#__thm_repo_version', $versiondata, 'id')))
				{
					return false;
				}
			}
		}						
		// Set up the source and destination of the file
		if ($filename)
		{
			$src = $file['tmp_name'];
			$dest = JPATH_ROOT . DS . "media" . DS . "com_thm_repo" . DS . $versiondata->id . "_" . $versiondata->version . "_" . $filename;
			
			if (!JFile::upload($src, $dest))
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function delete(&$pks)
	{
		$id = $pks[0];
	
		// GetDBO
		$db = JFactory::getDBO();
		
		// Delete Version files
		$query = $db->getQuery(true);
		$query->select('path');
		$query->from('#__thm_repo_version');
		$query->where('id = ' . $id);
		$db->setQuery((string) $query);
		$versions = $db->loadObjectList();
	
		if ($versions)
		{
			foreach ($versions as $version)
			{	
				// Delete every Version File from deleted File
				JFile::delete(JPATH_ROOT . $version->path);
			}
		}
		
		// Delete Version record
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__thm_repo_version'));
		$query->where('id = ' . $id);
		$db->setQuery($query);
		if (!($db->query()))
		{
			return false;
		}
	
		// Delete File record
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__thm_repo_file'));
		$query->where('id = ' . $id);
		$db->setQuery($query);
		if (!($db->query()))
		{
			return false;
		}
		
		// Delete Entity record
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__thm_repo_entity'));
		$query->where('id = ' . $id);
		$db->setQuery($query);
		if (!($db->query()))
		{
			return false;
		}
		
		// Delete asset entry
		$table = JTable::getInstance('Entity', 'THM_RepoTable');
		if (!$table->delete($id))
		{
			return false;
		}
		return true;
	}
}