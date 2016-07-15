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
 * Entities Controller
 *
 * @category  Joomla.Component.Admin
 * @package   thm_repo
 *
 */
class THM_RepoControllerEntities extends JControllerAdmin
{
	/**
	 * Returns the Model (proxy)
	 *
	 * @param   string $name   Model name
	 * @param   string $prefix Model prefix
	 *
	 * @return  JModel
	 */
	public function getModel($name = 'Entity', $prefix = 'THM_RepoModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Save order
	 *
	 * @return void
	 */
	public function saveorder()
	{
		$model = $this->getModel('entities');
		$id    = JRequest::getVar('id');

		if ($model->reorder())
		{
			$msg = JText::_('COM_THM_REPO_ORDER_SUCCESSFUL');
		}
		else
		{
			$msg = JText::_('COM_THM_REPO_ORDER_ERROR');
		}

		$this->setRedirect('index.php?option=com_thm_repo&view=entities&id=' . (int) $id, $msg);
	}

	/**
	 * Order up
	 *
	 * @return void
	 */
	public function orderup()
	{
		$model = $this->getModel('entities');
		$id    = JRequest::getVar('id');

		if ($model->reorder(-1))
		{
			$msg = JText::_('COM_THM_REPO_ORDER_SUCCESSFUL');
		}
		else
		{
			$msg = JText::_('COM_THM_REPO_ORDER_ERROR');
		}

		$this->setRedirect('index.php?option=com_thm_repo&view=entities&id=' . (int) $id, $msg);
	}

	/**
	 * Order down
	 *
	 * @return void
	 */
	public function orderdown()
	{
		$model = $this->getModel('entities');
		$id    = JRequest::getVar('id');

		if ($model->reorder(1))
		{
			$msg = JText::_('COM_THM_REPO_ORDER_SUCCESSFUL');
		}
		else
		{
			$msg = JText::_('COM_THM_REPO_ORDER_ERROR');
		}

		$this->setRedirect('index.php?option=com_thm_repo&view=entities&id=' . (int) $id, $msg);
	}
}
