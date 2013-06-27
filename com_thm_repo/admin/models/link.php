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
	 * @param   string $pk
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
		$db = JFactory::getDBO();
		
		// Insert New Link
		if ($linkdata->id == 0)
		{
			
			if (!($db->insertObject('#__thm_repo_entity', $entitydata, 'id')))
			{
				return false;
			}
			
			// Insert created entity id to linkdata id 
			$linkdata->id = $db->insertID();
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
		
		
		// Delete Link record
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
		return true;
	}
}