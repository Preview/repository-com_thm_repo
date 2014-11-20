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
 * Folder Form Field class for the THM Repo component
 * 
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
*/
class JFormFieldallFolder extends JFormFieldList
{
    /**
     * The field type.
     *
     * @var         string
     */
    protected $type = 'allfolder';

    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions()
    {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Select all folders from folder table
        $query->select('f.id, f.parent_id, f.name, COUNT(*)-1 AS level');
        $query->from('#__thm_repo_folder AS f, #__thm_repo_folder AS p');
        $query->where('f.lft BETWEEN p.lft AND p.rgt');
        $query->group('f.lft');
        $query->order('f.lft', 'ASC');
        $db->setQuery((string) $query);
        $messages = $db->loadObjectList();
        $options = array();
        if ($messages)
        {
            foreach ($messages as $message)
            {
                $count = 0;
                $prefix = '';
                while ($count < $message->level)
                {
                    $prefix .= '-';
                    $count++;
                }

                // Create select list
                $options[] = JHtml::_('select.option', $message->id, $prefix . $message->name);
            }
        }
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}