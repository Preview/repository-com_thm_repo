<?php
/**
 * @package		com_thm_repo
 * @author      Stefan Schneider	<stefan.schneider@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
// No direct access
defined('_JEXEC') or die;

// import Joomla table library
jimport('joomla.database.table');

/**
 * Folder Table class
*/
class THM_RepoTableFolder extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__thm_repo_folders', 'id', $db);
	}
}