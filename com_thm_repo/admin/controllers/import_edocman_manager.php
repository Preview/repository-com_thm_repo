<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     com_thm_repo.admin
 * @name        THM_RepoControllerImport_Edocman_Manager
 * @description THM_RepoControllerImport_Edocman_Manager is responsible for data migration for Edocman component
 * @author      Markus Gerlach, <markus.gerlach@mni.thm.de>
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
        $this->setCategories($this->getCategories());
        $this->setDocuments($this->getDocuments());
        $this->setDocumentCategory($this->getDocumentCategory());
    }

    public function setCategories($categories)
    {

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from('#__edocman_categories');
        $db->setQuery($query);

        if ($db->loadResult() == 0) {
            for ($i = 0; $i < sizeof($categories); $i++) {
                $query = $db->getQuery(true);
                $columns = array('id', 'parent_id', 'title', 'description', 'access', 'asset_id', 'created_user_id',
                    'created_time', 'modified_user_id', 'modified_time', 'published', 'category_layout', 'alias',
                    'level', 'checked_out', 'checked_out_time', 'language', 'path');

                $values = array($db->quote($categories[$i]["id"]), $db->quote($categories[$i]["parent_id"]),
                    $db->quote($categories[$i]["name"]), $db->quote($categories[$i]["description"]), $db->quote($categories[$i]["viewlevel"]),
                    $db->quote($categories[$i]["asset_id"]), $db->quote($categories[$i]["created_by"]),
                    $db->quote($categories[$i]["created"]), $db->quote($categories[$i]["modified_by"]),
                    $db->quote($categories[$i]["modified"]), $db->quote($categories[$i]["published"]),
                    $db->quote("default"), $db->quote(strtolower($categories[$i]["name"])), $db->quote($categories[$i][0]),
                    $db->quote("0"), $db->quote("null"), $db->quote("*"), $db->quote($categories[$i][1]));

                // Prepare the insert query.
                $query
                    ->insert($db->quoteName('#__edocman_categories'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));

                // Set the query using our newly populated query object and execute it.
                $db->setQuery($query);
                $db->execute();
                $query = null;
                $columns = null;
                $values = null;
            }
            echo "Migrate edocman_categories successful!";
        } else {
            echo "Table edocman_categories is not empty!";
        }
    }

    public function getCategories()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__thm_repo_folder');
        $db->setQuery($query);
        $categories = $db->loadAssocList();

        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__thm_repo_folder');
        $query->order('id DESC');
        $db->setQuery($query);
        $maximum = (int)$db->loadResult();

        $levels = array();
        $j = 0;
        for ($i = 0; $i <= $maximum; $i++) {
            if ((int)$categories[$j]["id"] == $i) {
                $levels[$i] = array(
                    (int)($categories[$j]["id"]),
                    (int)($categories[$j]["parent_id"]),
                    ($categories[$j]["name"]));
                    //strtolower($categories[$j]["name"]));
                $j++;
            } else {
                $levels[$i] = null;
            }
        }

        // getLevels and path
        for ($i = 0; $i < sizeof($levels); $i++) {
            $level = 1;
            $id = $i;
            $path = "";
            if (!(empty($levels[$id]))) {
                do {
                    $pid = ($levels[$id][1]);
                    if ($pid != 0) {
                        $path = strtolower($levels[$id][2]) . "/" . $path;
                        $id = $pid;
                        $level++;
                    }
                } while ($pid != 0);
                $levels[$i][3] = $level;
                $path = substr($path, 0, strlen($path)-1);
                $levels[$i][4] = $path;
            }
        }

        //setLevels and path
        for ($i = 0; $i < sizeof($categories); $i++) {
            for ($j = 0; $j < sizeof($levels); $j++) {
                if ((int)$categories[$i]["id"] == $levels[$j][0]) {
                    array_push($categories[$i], $levels[$j][3]);
                    array_push($categories[$i], $levels[$j][4]);
                }
            }
        }

        //set parent_id root = 0
        $categories[0]["parent_id"] = '0';
        return $categories;
    }

    public function getDocuments()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__thm_repo_entity');
        $db->setQuery($query);
        $result = $db->loadAssocList();

        $documents = array();
        for ($i = 0; $i < sizeof($result); $i++) {
            $documents[$i]["id"] = $result[$i]["id"];
            $documents[$i]["asset_id"] = $result[$i]["asset_id"];
            $documents[$i]["created_user_id"] = $result[$i]["created_by"];
            $documents[$i]["created_time"] = $result[$i]["created"];
            $documents[$i]["ordering"] = $result[$i]["ordering"];
            $documents[$i]["published"] = $result[$i]["published"];
            $documents[$i]["access"] = $result[$i]["viewlevel"];
        }

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__thm_repo_version');
        $db->setQuery($query);
        $result = $db->loadAssocList();

        for ($i = 0; $i < sizeof($documents); $i++) {
            for ($j = 0; $j < sizeof($result); $j++) {
                if ($documents[$i]["id"] == $result[$j]["id"]) {
                    $documents[$i]["id"] == $result[$j]["id"];
                    $documents[$i]["title"] = $result[$j]["name"];
                    $documents[$i]["alias"] = strtolower($result[$j]["name"]);
                    $documents[$i]["filename"] = substr(strrchr ($result[$j]["path"], "/"), 1);
                    $documents[$i]["original_filename"] = substr(strrchr ($result[$j]["path"], "/"), 1);
                    $documents[$i]["description"] = $result[$j]["description"];
                    $documents[$i]["modified_time"] = $result[$j]["modified"];
                    $documents[$i]["modified_user_id"] = $result[$j]["modified_by"];
                }
            }
        }

        return $documents;
    }

    public function setDocuments($documents)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__edocman_documents');
        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result == 0) {
            for ($i = 0; $i < sizeof($documents); $i++) {
                if (array_key_exists('title', $documents[$i])) {
                    $query = $db->getQuery(true);
                    $columns = array('id', 'title', 'alias', 'filename', 'original_filename', 'description', 'modified_time',
                        'modified_user_id', 'asset_id', 'created_user_id', 'created_time', 'ordering', 'published', 'access',
                        'image', 'rating_count', 'rating_sum', 'hits', 'downloads', 'checked_out', 'language',
                        'indexed_content','params');

                    $values = array($db->quote($documents[$i]["id"]), $db->quote($documents[$i]["title"]),
                        $db->quote($documents[$i]["alias"]), $db->quote($documents[$i]["filename"]),
                        $db->quote($documents[$i]["original_filename"]), $db->quote($documents[$i]["description"]),
                        $db->quote($documents[$i]["modified_time"]), $db->quote($documents[$i]["modified_user_id"]),
                        $db->quote($documents[$i]["asset_id"]), $db->quote($documents[$i]["created_user_id"]),
                        $db->quote($documents[$i]["created_time"]), $db->quote($documents[$i]["ordering"]),
                        $db->quote($documents[$i]["published"]), $db->quote($documents[$i]["access"]), $db->quote(null),
                        $db->quote("0"), $db->quote("0.00"),  $db->quote("0"), $db->quote("0"), $db->quote(null),
                        $db->quote("*"), $db->quote(null), $db->quote(null));

                    // Prepare the insert query.
                    $query
                        ->insert($db->quoteName('#__edocman_documents'))
                        ->columns($db->quoteName($columns))
                        ->values(implode(',', $values));

                    // Set the query using our newly populated query object and execute it.
                    $db->setQuery($query);
                    $db->execute();
                } else {
                    $query = $db->getQuery(true);
                    $columns = array('id', 'asset_id', 'created_user_id', 'created_time', 'ordering', 'published', 'access');

                    $values = array($db->quote($documents[$i]["id"]), $db->quote($documents[$i]["asset_id"]),
                        $db->quote($documents[$i]["created_user_id"]), $db->quote($documents[$i]["created_time"]),
                        $db->quote($documents[$i]["ordering"]), $db->quote($documents[$i]["published"]),
                        $db->quote($documents[$i]["access"]));

                    // Prepare the insert query.
                    $query
                        ->insert($db->quoteName('#__edocman_documents'))
                        ->columns($db->quoteName($columns))
                        ->values(implode(',', $values));

                    // Set the query using our newly populated query object and execute it.
                    $db->setQuery($query);
                    $db->execute();
                }
                $query = null;
                $columns = null;
                $values = null;

                echo "Migrate edocman_documents successful!";
            }
        } else {
            echo "Table edocman_documents is not empty!";
        }
    }

    public function getDocumentCategory(){
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__thm_repo_entity');
        $db->setQuery($query);
        $result = $db->loadAssocList();
        $docCat = array();

        //var_dump($result);

        for($i=0; $i<sizeof($result); $i++){
            $docCat[$i]["document_id"] = $result[$i]["id"];
            $docCat[$i]["category_id"] = $result[$i]["parent_id"];
        }
        return $docCat;
    }

    public function setDocumentCategory($docCat){
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__edocman_document_category');
        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result == 0) {
            for ($i = 0; $i < sizeof($docCat); $i++) {

                $query = $db->getQuery(true);
                $columns = array('id', 'document_id', 'category_id', 'is_main_category');

                $values = array($db->quote($docCat[$i]["document_id"]), $db->quote($docCat[$i]["document_id"]),
                    $db->quote($docCat[$i]["category_id"]), $db->quote("1"));

                // Prepare the insert query.
                $query
                    ->insert($db->quoteName('#__edocman_document_category'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));

                // Set the query using our newly populated query object and execute it.
                $db->setQuery($query);
                $db->execute();
                $query = null;
                $columns = null;
                $values = null;
            }
            echo "Migrate edocman_document_category successful!";
        }
        else {
            echo "Table edocman_document_category is not empty!";
        }
    }
}
