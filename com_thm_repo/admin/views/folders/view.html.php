<?php

/**
 * @package  	com_thm_repo
 * @author      Stefan Schneider	<stefan.schneider@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Folders View
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

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_THM_REPO_MANAGER_FOLDERS'));
		JToolBarHelper::deleteList('', 'folders.delete');
		JToolBarHelper::editList('folder.edit');
		JToolBarHelper::addNew('folder.add');
	}
}