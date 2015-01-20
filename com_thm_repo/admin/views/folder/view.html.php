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

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * THM_RepoViewFolder class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
*/
class THM_RepoViewFolder extends JViewLegacy
{
	/**
	 * Files view display method
	 * 
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	public function display($tpl = null)
	{
		// Get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
            JFactory::getApplication()->enqueueMessage( implode('<br />', $errors), 'error');
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;

		// Set the toolbar
		$this->addToolBar();

        if (version_compare(JVERSION, '3', '<'))
        {
            $tpl = 'j25';
        }

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 * 
	 * @return void
	 */
	protected function addToolBar()
	{
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);
		JToolBarHelper::title($isNew ? JText::_('COM_THM_REPO_MANAGER_FOLDER_NEW') : JText::_('COM_THM_REPO_MANAGER_FOLDER_EDIT'));
		JToolBarHelper::save('folder.save');
		JToolBarHelper::cancel('folder.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
}