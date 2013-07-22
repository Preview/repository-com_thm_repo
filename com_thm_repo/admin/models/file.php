<?php
/**
 * @package    THM_Repo
 * @author     Stefan Schneider, <stefan.schneider@mni.thm.de>
 * @copyright  2013 TH Mittelhessen
 * @license    GNU GPL v.2
 * @link       www.mni.thm.de
 */
// No direct access to this file
defined('_JEXEC') or die;

// Import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * File Model
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
	public function getTable($type = 'File', $prefix = 'THM_RepoTable', $config = array())
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
	 * @param   string $pk
	 *
	 * @return unknown
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
	
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		if ($pk > 0)
		{
				
			// Get Data from #__thm_repo_file table and assign it to $item
			$data = $this->getData($item->id);
			$item->path = $data->path;
			$item->file_id = $data->id;
			$item->size = $data->size;
			$item->mimetype = $data->mimetype;
		}
		else
		{
			// Set Data NULL for creating new file
			$item->path = null;
			$item->file_id = null;
			$item->size = null;
			$item->mimetype = null;
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
		$query->from('#__thm_repo_file');
		$query->where('id = ' . $id);
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result;
	
	}
	
	/**
	 *
	 * @param   unknown $data
	 *
	 * @return boolean
	 */
	public function save($data)
	{
		// Retrieve file details from uploaded file, sent from adminForm form
		$file = JRequest::getVar('file', null, 'files', 'array');
		
		// Clean up filename to get rid of strange characters like spaces etc
		$filename = JFile::makeSafe($file['name']);
		

		// Assign file_data
		$filedata  = (object) $data;
	
		// Remove not needed data for file table
		unset($filedata->name);
		unset($filedata->parent_id);
		unset($filedata->description);
		unset($filedata->created);
		unset($filedata->modified);
		unset($filedata->modified_by);
		unset($filedata->create_by);
		unset($filedata->viewlevels);
		
		// Add Size and MIME-Type to $filedata
		$filedata->size = $file['size'];
		$filedata->mimetype = $file['type'];
			
		// Assign entity data
		$entitydata = (object) $data;
	
		// Remove file data from entitydata
		unset($entitydata->path);
		unset($entitydata->size);
		unset($entitydata->mimetype);
	
		// GetDBO
		$db = JFactory::getDBO();
	
	
		// Insert New File
		if ($entitydata->id == 0)
		{
				
			if (!($db->insertObject('#__thm_repo_entity', $entitydata, 'id')))
			{
				return false;
			}
				
			// Insert created entity id to filedata id
			$filedata->id = $db->insertID();
						
			// Add Path to Filedata
			$filedata->path = JPATH_ROOT . DS . "media" . DS . "com_thm_repo" . DS . $filedata->id . "_" . $filename;
			if (!($db->insertObject('#__thm_repo_file', $filedata, 'id')))
			{
				return false;
			}
		}
		else
		{
			// Save Version Data to Table
			$this->saveVersion($entitydata->id, $filename);
			
			// Update #__thm_repo_entity table
			if (!($db->updateObject('#__thm_repo_entity', $entitydata, 'id')))
			{
				return false;
			}
			
			// Check If New File is Uploaded
			if ($filename)
			{			
				// Build Path
				$filedata->path = JPATH_ROOT . DS . "media" . DS . "com_thm_repo" . DS . $filedata->id . "_" . $filename;
			
				
				// Update #__thm_repo_file table
				if (!($db->updateObject('#__thm_repo_file', $filedata, 'id')))
				{
					return false;
				}
			}
		}
		
		
		
		// Set up the source and destination of the file
		if ($filename)
		{
			$src = $file['tmp_name'];
			$dest = JPATH_ROOT . DS . "media" . DS . "com_thm_repo" . DS . $filedata->id . "_" . $filename;
			
			if (!JFile::upload($src, $dest))
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 *
	 * @param   unknown $data
	 *
	 * @return boolean
	 */
	public function delete($data)
	{
		$id = $data[0];
	
		// GetDBO
		$db = JFactory::getDBO();
		
		// Delete File
		$query = $db->getQuery(true);
		$query->select('path');
		$query->from('#__thm_repo_file');
		$query->where('id = ' . $id);
		$db->setQuery($query);
		$path = $db->loadObject();
		JFile::delete($path->path);
		
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
				JFile::delete($version->path);
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
		return true;
	}
	
	public function getVersionData($id)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id, a.name, a.modified, a.description, b.path, b.size, b.mimetype');
		$query->from('#__thm_repo_entity AS a');
		$query->where('a.id = ' . $id);
		$query->join('INNER', '#__thm_repo_file AS b ON a.id = b.id');		
		$db->setQuery($query);
		$result = $db->loadObject();
	
		return $result;
	
	}
	
	public function saveVersion($id, $filename)
	{
		// Get needed Data for Version Update
		$versiondata = $this->getVersionData($id);
			
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('versionnumber');
		$query->from('#__thm_repo_version');
		$query->where('id = ' . $id);
		$db->setQuery($query);
		$version = $db->loadResultArray();
				
		// Increment Version Number and add to Versiondata
		$versiondata->versionnumber = max($version) + 1;
			
		// Check if File is changed
		if ($filename)
		{			
			// Create a Version of File
			$versionsrc = $versiondata->path;
			$versiondest = $versiondata->path . "_" . $versiondata->versionnumber;
		
			if (!JFile::move($versionsrc, $versiondest))
			{
				return false;
			}
			
			// Add Versionnumber to Path
			$versiondata->path = $versiondest;
			
			// Update Path on Version with same File
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__thm_repo_version'));
			$query->set('path = ' . $db->quote($versiondest));
			$query->where('path = ' . $db->quote($versionsrc));
			$db->setQuery($query);
			$db->query();

		}
		
		// Insert into Version table
		if (!($db->insertObject('#__thm_repo_version', $versiondata, 'id')))
		{
			return false;
		}
		
		return true;

	}
}