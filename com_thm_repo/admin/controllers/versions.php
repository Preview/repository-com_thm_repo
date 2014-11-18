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
 
// Import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * Versions Controller
 * 
 * @category  Joomla.Component.Admin
 * @package   thm_repo
 * 
 */
class THM_RepoControllerVersions extends JControllerAdmin
{
	
	/**
	 * Set current version
	 *
	 * @return void
	 */
	public function publish()
	{
		
 		$model = $this->getModel('versions');
 		$id = JRequest::getVar('id'); 		
 			
		if ($model->setversion())
		{
			$msg = JText::_('COM_THM_REPO_SET_VERSION_SUCCESSFUL');
		}
		else
		{
			$msg = JText::_('COM_THM_REPO_SET_VERSION_ERROR');
		}
		$this->setRedirect('index.php?option=com_thm_repo&view=versions&id=' . (int) $id, $msg);
	}
	
}