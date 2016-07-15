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

jimport('joomla.user.authentication');
jimport('joomla.filesystem.file');
jimport('thm_core.log.THMChangelogColoriser');

/**
 * Script file of THM_Repo component
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 */
class COM_THM_RepoInstallerScript
{
	const UPDATE = 'Update';
	const INSTALL = 'Install';
	const UNINSTALL = 'Uninstall';

	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance $adapter The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $adapter)
	{
	}

	/**
	 * Called before any type of action
	 *
	 * @param   string           $route   Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, JAdapterInstance $adapter)
	{
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string           $route   Which action is happening (Install|Uninstall|discover_install|Update)
	 * @param   JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter)
	{
		if (ucfirst($route) == self::UPDATE || ucfirst($route) == self::INSTALL)
		{
			$uri = JURI::root(true) . '/libraries/thm_core/log/THMChangelogColoriser.css';
			echo "<link rel='stylesheet' type='text/css' href='{$uri}' />";
			echo THMChangelogColoriser::colorise(dirname(__FILE__) . '/CHANGELOG.php');
		}
	}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter)
	{
		$uploadPath   = JPATH_ROOT . '/media/com_thm_repo';
		$htAccessPath = $uploadPath . '/.htaccess';

		JFolder::create($uploadPath);

		$htContent = 'deny from all';

		JFile::write($htAccessPath, $htContent, true);

		jimport('thm_repo.core.All');

		$uid  = JFactory::getUser()->id;
		$user = new THMUser((int) $uid);

		$publicViewlevel = 1;
		$published       = true;

		$folder = new THMFolder(
			null,
			'root',
			'The main folder to organize files and folders.',
			$user,
			$publicViewlevel,
			$published
		);

		THMFolder::persist($folder);
	}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function uninstall(JAdapterInstance $adapter)
	{
		jimport('thm_repo.core.All');

		try
		{
			$root = THMFolder::getRoot(true);
			THMFolder::remove($root, true);
		}
		catch (Exception $e)
		{
			/* Nothing to delete if no folder was found! */
		}

		$uploadPath = JPATH_ROOT . '/media/com_thm_repo';
		JFolder::delete($uploadPath);
	}
}
