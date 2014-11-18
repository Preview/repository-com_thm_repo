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

// Import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * 
 * THM_RepoModelfolder class for component com_thm_repo
 * 
 * @category  Joomla.Component.Admin
 * @package   thm_repo
 * 
 * Folder Form Field class for the THM Repo component
*/
class JFormFieldFolder extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var         string
	 */
	protected $type = 'folder';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return      array           An array of JHtml options.
	 */
	protected function getOptions()
	{
		$options = null;
		
		// Get current id
		$id = JFactory::getApplication()->input->getInt('id');
		$db = JFactory::getDBO();

		if ($id != null)
		{
			// Get lft and rgt from current id
			$query = $db->getQuery(true);
			$query->select('lft, rgt');
			$query->from('#__thm_repo_folder');
			$query->where('id = ' . $id);
			$db->setQuery((string) $query);
			$currentfolder = $db->loadObject();
		}
		else
		{
			$currentfolder = null;
		}

		
		// Select all folders from folder table
		$query = $db->getQuery(true);
		$query->select('f.id, f.parent_id, f.name, COUNT(*)-1 AS level');
		$query->from('#__thm_repo_folder AS f, #__thm_repo_folder AS p');
		$query->where('f.lft BETWEEN p.lft AND p.rgt');
		if ($currentfolder != null)
		{
			$query->where('f.lft NOT BETWEEN ' . $currentfolder->lft . ' AND ' . $currentfolder->rgt);
		}
		$query->group('f.lft');
		$query->order('f.lft', 'ASC');
		$db->setQuery((string) $query);
		$messages = $db->loadObjectList();

		if ($messages)
		{
			foreach ($messages as $message)
			{	
				// Create select list without current id
				if ($message->id != $id) 
				{
					$count = 0;
					$prefix = '';
					while ($count < $message->level)
					{
						$prefix .= '-';
						$count++;
					}
					
					$options[] = JHtml::_('select.option', $message->id, $prefix . $message->name);
				}
			}
			$options = array_merge(parent::getOptions(), $options);
		}
		
		return $options;
	}
}