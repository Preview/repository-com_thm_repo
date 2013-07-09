<?php
/**
 * @package    THM_Repo
 * @author     Stefan Schneider, <stefan.schneider@mni.thm.de>
 * @copyright  2013 TH Mittelhessen
 * @license    GNU GPL v.2
 * @link       www.mni.thm.de
 */
// No direct access to this file
defined('_JEXEC') or die;
 
// Import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * Entities Controller
 */
class THM_RepoControllerEntities extends JControllerAdmin
{
/**
 * Returns the Model (proxy)
 *
 * @param   string  $name    Model name
 * @param   string  $prefix  Model prefix
 *
 * @return  JModel
 */
	public function getModel($name = 'Entity', $prefix = 'THM_RepoModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
}