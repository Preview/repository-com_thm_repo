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
 * THM_RepoViewStart class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 */
class THM_RepoViewStart extends JViewLegacy
{
	/**
	 * Links view display method
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	public function display($tpl = null)
	{
		// Set the toolbar
		$this->addToolBar();

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
		$document = JFactory::getDocument();
		$document->addStyleSheet("components/com_thm_repo/css/icon/icon.css");

		JToolBarHelper::title(JText::_('COM_THM_REPO_MANAGER_START'), 'repo.png', JPATH_COMPONENT . DS . 'img' . DS . 'icon-48-repo.png');
		JToolBarHelper::custom('export_to_edocman_manager.run', 'iconname.png', 'iconname.png', 'Export data to Edocman', false, false);
	}
}
