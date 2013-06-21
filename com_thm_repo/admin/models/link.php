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

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * Link Model
*/
class THM_RepoModelLink extends JModelAdmin
{
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    The table type to instantiate
	 * @param   string  A prefix for the table class name. Optional.
	 * @param   array   Configuration array for model. Optional.
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
	 * @param    string $pk
	 * 
	 * @return unknown
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');
		if ($pk > 0) 
		{
			
			// Get Data from #__thm_repo_entity table and assign it to $item
			$data = $this->getData($item->id);
		/*	$item->entity_id = $data->id;
			$item->parent_id = $data->parent_id;
			$item->name = $data->name;
			$item->description = $data->description;
			$item->created = $data->created;
			$item->modified = $data->modified;
			$item->modified_by = $data->modified_by;
			$item->create_by = $data->create_by;
			$item->viewlevels = $data->viewlevels; */
			$item->link = $data->link;
			$item->link_id = $data->id;
		} 
		else 
		{
			// Set link and link_id NULL for creating new links
			$item->link = null;
			$item->link_id = null;
		}
		return $item;
	}
	
	/**
	 * Method to get the needed data from entity table
	 * 
	 * @param   unknown $id
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
	 * 
	 * @param   unknown $data
	 * 
	 * @return boolean
	 */
	public function save($data)
	{
		// Assign link_data
		$linkdata  = (object) $data;
		
		// Remove not needed data for link table
		unset($linkdata->name);
		unset($linkdata->parent_id);
		unset($linkdata->description);
		unset($linkdata->created);
		unset($linkdata->modified);
		unset($linkdata->modified_by);
		unset($linkdata->create_by);
		unset($linkdata->viewlevels);
			
		// Assign entity data
		$entitydata = (object) $data;
		
		// Remove link from entitydata
		unset($entitydata->link);
		
		// GetDBO
		$db1 = JFactory::getDBO();
		$db2 = JFactory::getDBO();

		if ($linkdata->id == 0)
		{
			$db1->insertObject('#__thm_repo_entity', $entitydata, 'id');
			
			// Insert created entity id to linkdata id 
			$linkdata->id = $db1->insertID();
			$db2->insertObject('#__thm_repo_link', $linkdata, 'id');
			
		} 
		else
		{
			// Update #__thm_repo_entity table
			$db1->updateObject('#__thm_repo_entity', $entitydata, 'id');
			
			// Update #__thm_repo_link table
			$db2->updateObject('#__thm_repo_link', $linkdata, 'id');
		}		
		
		// TO DO: return statement
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
		
		$query1 = $db->getQuery(true);
		$query2 = $db->getQuery(true);
		
		// Delete Entity record
		$query1->delete($db->quoteName('#__thm_repo_link'));
		$query1->where('id = ' . $id);
		$db->setQuery($query1);
		$db->query();

		// Delete Link record
		$query2->delete($db->quoteName('#__thm_repo_entity'));
		$query2->where('id = ' . $id);
		$db->setQuery($query2);
		$db->query();
		
		// TO DO: return statement
		return true;
	}
}