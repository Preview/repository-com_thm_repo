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
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * Links Controller
 */
class THM_RepoControllerLinks extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since       2.5
	 */
	public function getModel($name = 'Link', $prefix = 'THM_RepoModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
}