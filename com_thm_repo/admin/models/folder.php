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
 * THM_RepoModelFolder class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoModelFolder extends JModelAdmin
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
	public function getTable($type = 'Folder', $prefix = 'THM_RepoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * 
	 * @return  mixed    $form      A JForm object on success, false on failure
	 * 
	 * @since       2.5
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_thm_repo.folder', 'folder', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed   $data  The data for the form.
	 * 
	 * @since       2.5
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_thm_repo.edit.folder.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
	}

	public function validate($form, $data, $group = null)
	{
		$folderData = (object) $data;

		if ($folderData->parent_id == null && $folderData->id == null)
		{
			$db = JFactory::getDbo(true);

			/* Check is root folder exists already. */
			$rootQuery = $db->getQuery(true);
			$rootQuery
				->select('f.id')
				->from('#__thm_repo_folder f')
				->where('f.lft = 1');
			$rootResult = $db->setQuery($rootQuery)->loadAssoc();

			if (!empty($rootResult))
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_REPO_SELECT_PARENT'));
				return false;
			}
		}

		return parent::validate($form, $data, $group);
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
		$folderdata = (object) $data;

		$table = JTable::getInstance('Folder', 'THM_RepoTable');

		if (!$table->save($data))
		{
			return false;
		}
		
		// GetDBO
		$db = JFactory::getDbo();
		
		// Start transaction
		$db->transactionStart();
		
		// Root create
		if ($folderdata->parent_id == null && $folderdata->id == null)
		{
			$folderdata->id = $table->id;
			$folderdata->lft = 1;
			$folderdata->rgt = 2;
			if (!($db->updateObject('#__thm_repo_folder', $folderdata, 'id')))
			{
				return false;
			}
		}
		else 
		{
			// Check if new file or updated
			if ($data['id'] == 0)
			{
				// Get lft and rgt from Parent
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__thm_repo_folder');
				$query->where('id = ' . $folderdata->parent_id);
				$db->setQuery($query);
				$parent = $db->loadObject();
					
				/* 			
				 * UPDATE tree SET rgt=rgt+2 WHERE rgt >= $RGT;
				 * UPDATE tree SET lft=lft+2 WHERE lft > $RGT;
				 * INSERT INTO tree (name,lft,rgt) VALUES ('Nagetiere', $RGT, $RGT+1);
				 */
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_folder');
				$query->set('rgt = rgt + 2');
				$query->where("rgt >= " . (int) $parent->rgt);
				$db->setQuery($query);
				$db->query();
					
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_folder');
				$query->set('lft = lft + 2');
				$query->where("lft > " . (int) $parent->rgt);
				$db->setQuery($query);
				$db->query();
					
				$folderdata->lft = (int) $parent->rgt;
				$folderdata->rgt = (int) $parent->rgt + 1;
				$folderdata->id = (int) $table->id;
				$folderdata->asset_id = $table->asset_id;
				
	
				if (!($db->updateObject('#__thm_repo_folder', $folderdata, 'id')))
				{
					return false;
				}
			}
			else
			{
				// Update Folder
				if (!($db->updateObject('#__thm_repo_folder', $folderdata, 'id')))
				{
					return false;
				}
				
				// Reload Folder
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__thm_repo_folder');
				$query->where('id = ' . (int) $folderdata->id);
				$db->setQuery($query);
				$folderdata = $db->loadObject();
				
				$range = (int) $folderdata->rgt - (int) $folderdata->lft + 1;
				
				// Negate subtree that needs to be moved
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_folder');
				$query->set('lft = 0 - lft, rgt = 0 - rgt');
				$query->where('lft >= ' . (int) $folderdata->lft . ' AND rgt <= ' . (int) $folderdata->rgt);			
				$db->setQuery($query);
				$db->query();
				
				// Degrade everything thats on top and right on subtree
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_folder');
				$query->set('lft = lft - ' . $range);
				$query->where('lft > ' . (int) $folderdata->rgt);
				$db->setQuery($query);
				$db->query();
				
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_folder');
				$query->set('rgt = rgt - ' . $range);
				$query->where('rgt > ' . (int) $folderdata->rgt);
				$db->setQuery($query);
				$db->query();
				
				// Get new Parent folder
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__thm_repo_folder');
				$query->where('id = ' . (int) $folderdata->parent_id);
				$db->setQuery($query);
				$parent = $db->loadObject();
				
				// Increase everything right from Parent folder
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_folder');
				$query->set('lft = lft + ' . $range);
				$query->where('lft > ' . (int) $parent->lft);
				$db->setQuery($query);
				$db->query();
				
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_folder');
				$query->set('rgt = rgt + ' . $range);
				$query->where('rgt > ' . (int) $parent->lft);
				$db->setQuery($query);
				$db->query();
				
				// Bring subtree back into the tree
				
				
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_folder');
				$query->set('lft = (-(lft)) - ' . (int) $folderdata->lft . ' + ' . (int) $parent->lft . '+ 1');
				$query->where('lft < 0');
				$db->setQuery($query);
				$db->query();
				
				$query = $db->getQuery(true);
				$query->update('#__thm_repo_folder');
				$query->set('rgt = (-(rgt)) - ' . (int) $folderdata->lft . ' + ' . (int) $parent->lft . '+ 1');
				$query->where('rgt < 0');
				$db->setQuery($query);
				$db->query();
				
							
			}
		
		}
		// Transaction commit
		$db->transactionCommit();
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

		// Check have child
		$entitiesQuery = $db->getQuery(true);
		$entitiesQuery
			->select('e.id')
			->from('#__thm_repo_entity e')
			->where("e.parent_id = $id");
		$entityResult = $db->setQuery($entitiesQuery)->loadAssoc();

		if (!empty($entityResult))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_REPO_FOLDER_CONTAINS_ENTITIES'));
			return false;
		}

		// Get Data
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__thm_repo_folder');
		$query->where('id = ' . $id);
		$db->setQuery($query);
		$folderdata = $db->loadObject();
		
 		$table = JTable::getInstance('Folder', 'THM_RepoTable');
 		if (!$table->delete($id))
 		{
 			return false;
 		}
		
		
		// Delete asset entry
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where('id = ' . (int) $folderdata->asset_id);
		$db->setQuery($query);
		if (!($db->query()))
		{
			return false;
		}	
	
		
		
		$query = $db->getQuery(true);
		$query->update('#__thm_repo_folder');
		$query->set('lft = lft - 2');
		$query->where("lft > " . (int) $folderdata->lft);
		$db->setQuery($query);
		$db->query();
		
		$query = $db->getQuery(true);
		$query->update('#__thm_repo_folder');
		$query->set('rgt = rgt - 2');
		$query->where("rgt > " . (int) $folderdata->rgt);
		$db->setQuery($query);
		$db->query();	
		
		return true;
	}
}