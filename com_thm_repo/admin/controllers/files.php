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
 
// Import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * Files Controller
 * 
 * @category  Joomla.Component.Admin
 * @package   thm_repo
 * 
 */
class THM_RepoControllerFiles extends JControllerAdmin
{

	/**
	 * Returns the Model (proxy)
	 *
	 * @param   string  $name    Model name
	 * @param   string  $prefix  Model prefix
	 *
	 * @return  JModel
	 */
	public function getModel($name = 'File', $prefix = 'THM_RepoModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

}