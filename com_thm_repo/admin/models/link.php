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
 * THM_RepoModelLink class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoModelLink extends JModelAdmin
{
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
	 * @return   mixed   A JForm object on success, false on failure
	 * 
	 * @since    2.5
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_thm_repo.link', 'link', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_thm_repo.edit.link.data', array());
		if (empty($data))
		{;
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
			
			// Get Data from #__thm_repo_entity table and assign it to $item
			$data = $this->getData($item->id);
			$item->link = $data->link;
			$item->link_id = $data->id;
			$item->name = $data->name;
			$item->description = $data->description;
			$item->modified = $data->modified;
			$item->modified_by = $data->modified_by;

		} 
		else 
		{
			// Set link and link_id NULL for creating new links
			$item->link = null;
			$item->link_id = null;
			$item->name = null;
			$item->description = null;
			$item->modified = null;
			$item->modified_by = null;		
		}
		return $item;
	}
	
	/**
	 * Method to get the needed data from entity table
	 * 
	 * @param   number  $id  ID
	 * 
	 * @return mixed   The data from #__thm_repo_entity table.
	 */
	public function getData($id)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__thm_repo_link');
		$query->where('id = ' . $id);
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
		
		// Assign linkdata
		$linkdata->id  = $data['id'];
		$linkdata->name = $data['name'];
		$linkdata->description = $data['description'];
		$linkdata->modified = $data['modified'];
		$linkdata->modified_by = $data['modified_by'];
		$linkdata->link = $data['link'];
			
		// Assign entitydata
		$entitydata->id = $data['id'];
		$entitydata->parent_id = $data['parent_id'];
		$entitydata->viewlevel = $data['viewlevel'];
		$entitydata->created = $data['created'];
		$entitydata->created_by = $data['created_by'];
			
		// GetDBO
		$db = JFactory::getDBO();
		
		// Get Ordering count
		$query = $db->getQuery(true);
		$query->select('ordering');
		$query->from('#__thm_repo_entity');
		$query->where('parent_id = ' . $entitydata->parent_id);
		$db->setQuery($query);
		$ordering = $db->loadResultArray();
		
		// Increment Version Number and add to Versiondata
		$entitydata->ordering = max($ordering) + 1;
		
		
		// Insert New Link
		if ($linkdata->id == 0)
		{		
			$entitydata->id = $table->id;
			if (!($db->updateObject('#__thm_repo_entity', $entitydata, 'id')))
			{
				return false;
			}
			
			// Insert created entity id to linkdata id 
			$linkdata->id = $table->id;
			if (!($db->insertObject('#__thm_repo_link', $linkdata, 'id'))) 
			{
				return false;
			}		
		} 
		else
		{
			// Update #__thm_repo_entity table
			if (!($db->updateObject('#__thm_repo_entity', $entitydata, 'id')))
			{
				return false;
			}
			
			// Update #__thm_repo_link table
			if (!($db->updateObject('#__thm_repo_link', $linkdata, 'id')))
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
		
		// Delete link record
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__thm_repo_link'));
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