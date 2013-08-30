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
defined('_JEXEC') or die();


 
/**
 * Script file of THM_Repo component
 * 
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 */
class Com_THM_RepoInstallerScript
{
	/**
	 * method to install the component
	 * 
	 * @param   object  $parent  class calling this method
     *
     * @return void
     */
	public function install($parent)
	{
		$uploadPath = JPATH_ROOT . DS . 'media' . DS . 'com_thm_repo';

		JFolder::create($uploadPath);
		JFactory::getLanguage()->load('com_thm_repo');
		$parent->getParent()->setRedirectURL('index.php?option=com_thm_repo&task=folder.edit');
    		JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_REPO_INSTALL_NOTICE'), 'warning');
	}        
	
	/**
	 * Method to uninstall the component
	 * 
	 * @param   object  $parent  class calling this method
	 * 
	 * @return void
	 */
	public function uninstall($parent)
	{
		$uploadPath = JPATH_ROOT . DS . 'media' . DS . 'com_thm_repo';
		JFolder::delete($uploadPath);
	}
}
