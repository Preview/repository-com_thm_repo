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
defined('_JEXEC') or die;

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * THM_ReposViewFolders class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 */
class THM_RepoViewFolders extends JView
{
	/**
	 * Folders view display method
	 * 
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
		$state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;
		$this->sortDirection = $state->get('list.direction');
		$this->sortColumn = $state->get('list.ordering');
		
		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) 
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}
		

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 * 
	 * @return nothing
	 */
	protected function addToolBar()
	{
		$user = JFactory::getUser();

		JToolBarHelper::title(JText::_('COM_THM_REPO_MANAGER_FOLDERS'));
		if ($user->authorise('core.delete', 'com_thm_repo'))
		{
			JToolBarHelper::deleteList('', 'folders.delete');
		}
		if ($user->authorise('core.edit', 'com_thm_repo'))
		{
			JToolBarHelper::editList('folder.edit');
		}
		if ($user->authorise('core.create', 'com_thm_repo'))
		{
			JToolBarHelper::addNew('folder.add');
		}		
		JToolBarHelper::preferences('com_thm_repo');
	}
}