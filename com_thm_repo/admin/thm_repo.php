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

if (!defined('DS'))
{
    define('DS', DIRECTORY_SEPARATOR);
}

JError::$legacy = false;

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('THM_Repo');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();