<?php
/**
 * @package    THM_Repo
 * @author     Stefan Schneider, <stefan.schneider@mni.thm.de>
 * @copyright  2013 TH Mittelhessen
 * @license    GNU GPL v.2
 * @link       www.mni.thm.de
 */



// No direct access to this file
defined('_JEXEC') or die();

//jimport('joomla.application.component.controller');

 
/**
 * Script file of THM_Repo component
 */
class COM_THM_RepoInstallerScript
{
	/**
	 * method to install the component
     *
     * @return void
     */
	public function install($parent) 
    {
    	// $parent is the class calling this method
    	// $parent->getParent()->setRedirect('index.php?option=com_thm_repo&task=folder.edit', JText::_('COM_THM_REPO_VIEW_MODIFIED_BY'));
       $parent->getParent()->setRedirectURL('index.php?option=com_thm_repo&task=folder.edit');
       echo '<p>' . JText::_('COM_THM_REPO_VIEW_MODIFIED_BY') . '</p>';
    }        
}