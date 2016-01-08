<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerDB_Data_Manager
 * @description THM_GroupsControllerDB_Data_Manager class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

// Import Joomla modelform library
jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.file');
jimport('thm_repo.core.All');


/**
 * THM_RepoControllerImport_Edocman_Manager is responsible for data migration for Edocman component
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoControllerImport_Edocman_Manager extends JControllerLegacy
{
    /**
     * constructor (registers additional tasks to methods)
     *
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function run()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from('#__edocman_categories');
        $db->setQuery($query);

        if ($db->loadResult() != null) {
            $query2 = $db->getQuery(true);
            $query2->select('*');
            $query2->from('#__thm_repo_folder');
            $db->setQuery($query2);
            $result = $db->loadAssocList();

            $maximum = 92;
            $titles = array();
            $j = 0;
            for ($i = 0; $i < $maximum; $i++) {
                if ((int)$result[$j]["id"] == $i) {
                    $titles[$i] = array(
                        (int)($result[$j]["id"]),
                        (int)($result[$j]["parent_id"]),
                        ($result[$j]["name"]));
                    $j++;
                } else {
                    $titles[$i] = null;
                }
            }
            // getLevels
            for ($i = 0; $i < sizeof($titles) - 1; $i++)
            {
                $level = 1;
                $id = $i;
                $pid = $id;

                if(!(empty($titles[$pid]))) {
                    do {
                        $pid = ($titles[$pid][1]);
                        if ($pid != 0) {
                            $pid--;
                            $level++;
                        }
                    } while ($pid != 0);
                    $titles[$id][3] = $level;
                }
            }
            var_dump($titles);

    } else
    {
        echo "Table edocman_categories is not empty!";
    }
    }
}
