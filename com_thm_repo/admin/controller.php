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

// No direct access
defined('_JEXEC') or die;

/**
 * THM_RepoController class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoController extends JControllerLegacy
{
	/**
	 * Method to display admincenter
	 * 
	 * @param   boolean  $cachable   cachable
	 * @param   boolean  $urlparams  url param
	 *
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Set default view if not set
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->getCmd('view', 'start'));
		
		
		// Submenu
        $vName = JFactory::getApplication()->input->getWord('view', 'thm_repo');

        // TODO: replace deprecated call
		JSubMenuHelper::addEntry(JText::_('COM_THM_REPO_START'), 'index.php?option=com_thm_repo&view=start', $vName == 'start');
		JSubMenuHelper::addEntry(JText::_('COM_THM_REPO_FOLDERMANAGER'), 'index.php?option=com_thm_repo&view=folders', $vName == 'folders');
		JSubMenuHelper::addEntry(JText::_('COM_THM_REPO_FILEMANAGER'),  'index.php?option=com_thm_repo&view=files', $vName == 'files');
		JSubMenuHelper::addEntry(JText::_('COM_THM_REPO_LINKMANAGER'), 'index.php?option=com_thm_repo&view=links', $vName == 'links');
	
		// Call parent behavior
		parent::display($cachable, $urlparams);
	}
}

