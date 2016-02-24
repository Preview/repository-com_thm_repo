<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     com_thm_repo.admin
 * @name        THM_RepoControllerExport_Edocman_Manager
 * @description THM_RepoControllerExport_Edocman_Manager is responsible for data migration for Edocman component
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


class THM_RepoControllerExport_TO_Edocman_Manager extends JControllerLegacy
{
    /**
     * constructor (registers additional tasks to methods)
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $categories;
    private $documents;
    private $documentCategory;
    private $links;
    private $edocmanDirectory = "../media/edocman/";

    /**
     * Method to get the categories
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Method to set the paramamter to $categories
     * @param $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Method to get documents
     * @return documents
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Method to set the parameter to $documents
     * @param $documents
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;
    }

    /**
     * Method to get the DocumentCategory
     * @return mixed
     */
    public function getDocumentCategory()
    {
        return $this->documentCategory;
    }

    /**
     * Method to set the parameter to $documentCategory
     * @param $documentCategory
     */
    public function setDocumentCategory($documentCategory)
    {
        $this->documentCategory = $documentCategory;
    }

    /**
     * Method to get links
     * @return links
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Method to set the parameter to $links
     * @param $links
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * Method to get edocmanDirectory
     * @return links
     */
    public function getEdocmanDirectory()
    {
        return $this->edocmanDirectory;
    }


    /**
     * Main function which is called from button Import Edocman Data
     */
    public function run()
    {
        $this->setCategories($this->compileCategories());
        $this->setDocuments($this->compileDocuments());
        $this->setDocumentCategory($this->compileDocumentCategory());
        $this->setLinks($this->compileLinks());

        $this->saveCategoriesToDb();
        $this->saveDocumentsToDb();
        $this->saveDocumentCategoryToDb();
        $this->saveLinksToDbDocuments();

        $this->importFoldersToCategories();
        $this->importEntitiesToDocuments();
    }

    /**
     * Method to save the categories in database edocman_categories
     *
     */
    public function saveCategoriesToDb()
    {
        $categories = $this->getCategories();
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

                $query
                    ->insert($db->quoteName('#__edocman_categories'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));

                $db->setQuery($query);
                $db->execute();
                $query = null;
                $columns = null;
                $values = null;
            }
            echo "Migrate edocman_categories successful! ";
        } else {
            echo "Table edocman_categories is not empty! ";
        }
    }

    /**
     * Method to compile the categories in the correct structure from thm_repo_foleder
     * calculate the category level and category path
     * @return mixed
     */
    public function compileCategories()
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
                        $path = mb_strtolower($levels[$id][2], 'UTF-8') . "/" . $path;
                        $id = $pid;
                        $level++;
                    }
                } while ($pid != 0);
                $levels[$i][3] = $level;
                $path = substr($path, 0, strlen($path) - 1);
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

    /**
     * Method to compile the documents in the correct structure from thm_repo_entity and thm_repo_version
     * compile the document path
     * @return array
     */
    public function compileDocuments()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__thm_repo_entity');
        $db->setQuery($query);
        $result = $db->loadAssocList();

        $categories = $this->getCategories();
        $documents = array();
        for ($i = 0; $i < sizeof($result); $i++) {
            $parent_id = (int)$result[$i]["parent_id"];
            $documents[$i]["id"] = $result[$i]["id"];
            $documents[$i]["asset_id"] = $result[$i]["asset_id"];
            $documents[$i]["created_user_id"] = $result[$i]["created_by"];
            $documents[$i]["created_time"] = $result[$i]["created"];
            $documents[$i]["ordering"] = $result[$i]["ordering"];
            $documents[$i]["published"] = $result[$i]["published"];
            $documents[$i]["access"] = $result[$i]["viewlevel"];
            if ($parent_id < sizeof($categories)) {
                for ($j = 0; $j < sizeof($categories); $j++) {
                    if ($parent_id == $categories[$j]["id"]) {
                        $documents[$i]["path"] = $categories[$j][1];
                    }
                }
            } else {
                $documents[$i]["path"] = "";
            }
        }

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__thm_repo_version');
        $db->setQuery($query);
        $result = $db->loadAssocList();

        $links = array();

        for ($i = 0; $i < sizeof($documents); $i++) {
            for ($j = 0; $j < sizeof($result); $j++) {
                if ($documents[$i]["id"] == $result[$j]["id"]) {
                    $documents[$i]["id"] == $result[$j]["id"];
                    $documents[$i]["title"] = $result[$j]["name"];
                    $documents[$i]["alias"] = strtolower($result[$j]["name"]);
                    $originalFilename = substr(strrchr($result[$j]["path"], "/"), 1);
                    $documents[$i]["original_filename_repo"] = $originalFilename;
                    $pos = strpos($originalFilename, '_');
                    $originalFilename = substr($originalFilename, $pos + 1);
                    $pos = strpos($originalFilename, '_');
                    $originalFilename = substr($originalFilename, $pos + 1);
                    $documents[$i]["original_filename"] = $originalFilename;
                    $documents[$i]["description"] = $result[$j]["description"];
                    $documents[$i]["modified_time"] = $result[$j]["modified"];
                    $documents[$i]["modified_user_id"] = $result[$j]["modified_by"];
                    $documents[$i]["filename"] = $documents[$i]["path"] . "/" . $originalFilename;
                }
            }

            if (!(array_key_exists('title', $documents[$i]))) {
                array_push($links, $documents[$i]);
            }
        }
        $this->setLinks($links);
        return $documents;
    }

    /**
     * Method so save the documents to the database edocman_documents
     * and push all documents without titles to $links
     */
    public function saveDocumentsToDb()
    {
        $documents = $this->getDocuments();
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
                        'indexed_content', 'params');

                    $values = array($db->quote($documents[$i]["id"]), $db->quote($documents[$i]["title"]),
                        $db->quote($documents[$i]["alias"]), $db->quote($documents[$i]["filename"]),
                        $db->quote($documents[$i]["original_filename"]), $db->quote($documents[$i]["description"]),
                        $db->quote($documents[$i]["modified_time"]), $db->quote($documents[$i]["modified_user_id"]),
                        $db->quote($documents[$i]["asset_id"]), $db->quote($documents[$i]["created_user_id"]),
                        $db->quote($documents[$i]["created_time"]), $db->quote($documents[$i]["ordering"]),
                        $db->quote($documents[$i]["published"]), $db->quote($documents[$i]["access"]), $db->quote(null),
                        $db->quote("0"), $db->quote("0.00"), $db->quote("0"), $db->quote("0"), $db->quote(null),
                        $db->quote("*"), $db->quote(null), $db->quote(null));

                    $query
                        ->insert($db->quoteName('#__edocman_documents'))
                        ->columns($db->quoteName($columns))
                        ->values(implode(',', $values));

                    $db->setQuery($query);
                    $db->execute();
                }
                $query = null;
                $columns = null;
                $values = null;
            }
            echo "Migrate edocman_documents successful! ";
        } else {
            echo "Table edocman_documents is not empty! ";
        }
    }

    /**
     * Method to compile the documentCategory to the correct structure from thm_repo_entity
     * @return array
     */
    public function compileDocumentCategory()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__thm_repo_entity');
        $db->setQuery($query);
        $result = $db->loadAssocList();
        $docCat = array();

        for ($i = 0; $i < sizeof($result); $i++) {
            $docCat[$i]["document_id"] = $result[$i]["id"];
            $docCat[$i]["category_id"] = $result[$i]["parent_id"];
        }

        return $docCat;
    }

    /**
     * Method to save the documentCategoriy to database edocman_documtn_category
     *
     */
    public function saveDocumentCategoryToDb()
    {
        $docCat = $this->getDocumentCategory();
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

                $query
                    ->insert($db->quoteName('#__edocman_document_category'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));

                $db->setQuery($query);
                $db->execute();
                $query = null;
                $columns = null;
                $values = null;
            }
            echo "Migrate edocman_document_category successful! ";
        } else {
            echo "Table edocman_document_category is not empty! ";
        }
    }

    /**
     * Method to compile the Links to the correct structure from thm_repo_entity
     * @return array
     */
    public function compileLinks()
    {
        $links = $this->getLinks();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__thm_repo_link');
        $db->setQuery($query);
        $result = $db->loadAssocList();

        for ($i = 0; $i < sizeof($links); $i++) {
            for ($j = 0; $j < sizeof($result); $j++) {
                if ($links[$i]["id"] == $result[$j]["id"]) {
                    $links[$i]["title"] = $result[$j]["name"];
                    $links[$i]["alias"] = strtolower($result[$j]["name"]);
                    $links[$i]["filename"] = $links[$i]["path"] . "/" . $links[$i]["title"];
                    $links[$i]["original_filename"] = "";
                    $links[$i]["description"] = $result[$j]["description"];
                    $links[$i]["modified_time"] = $result[$j]["modified"];
                    $links[$i]["modified_user_id"] = $result[$j]["modified_by"];
                    $links[$i]["document_url"] = $result[$j]["link"];
                }
            }
        }
        return $links;
    }

    /*
    public function createTableLinks()
    {
        $db = JFactory::getDBO();
        $query = "CREATE TABLE IF NOT EXISTS #__edocman_links ( `id` int(10) NOT NULL, `asset_id` int(10) NOT NULL, `created_user_id` int(10) NOT NULL, `created_time` varchar(40) NOT NULL, `ordering` int(10) NOT NULL, `published` int(10) NOT NULL, `access` int(10) NOT NULL, `path` varchar(40) NOT NULL, `name` varchar(100) NOT NULL, `description` varchar(400) NOT NULL, `modified_time` varchar(40) NOT NULL, `modified_user_id` int(10) NOT NULL, `link` varchar(100) NOT NULL, PRIMARY KEY (`id`) )";
        $db->setQuery($query);
        $db->query();
    }
    */

    /**
     * Method to save links to database edocman_documents
     */
    public function saveLinksToDbDocuments()
    {
        $links = $this->getLinks();
        $db = JFactory::getDBO();

        for ($i = 0; $i < sizeof($links); $i++) {
            $query = $db->getQuery(true);
            $columns = array('id', 'title', 'alias', 'filename', 'original_filename', 'document_url', 'description', 'modified_time',
                'modified_user_id', 'asset_id', 'created_user_id', 'created_time', 'ordering', 'published', 'access',
                'image', 'rating_count', 'rating_sum', 'hits', 'downloads', 'checked_out', 'language',
                'indexed_content', 'params');

            $values = array($db->quote($links[$i]["id"]), $db->quote($links[$i]["title"]),
                $db->quote($links[$i]["alias"]), $db->quote($links[$i]["filename"]),
                $db->quote($links[$i]["original_filename"]), $db->quote($links[$i]["document_url"]),$db->quote($links[$i]["description"]),
                $db->quote($links[$i]["modified_time"]), $db->quote($links[$i]["modified_user_id"]),
                $db->quote($links[$i]["asset_id"]), $db->quote($links[$i]["created_user_id"]),
                $db->quote($links[$i]["created_time"]), $db->quote($links[$i]["ordering"]),
                $db->quote($links[$i]["published"]), $db->quote($links[$i]["access"]), $db->quote(null),
                $db->quote("0"), $db->quote("0.00"), $db->quote("0"), $db->quote("0"), $db->quote(null),
                $db->quote("*"), $db->quote(null), $db->quote(null));

            $query
                ->insert($db->quoteName('#__edocman_documents'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));

            $db->setQuery($query);
            $db->execute();
            $query = null;
            $columns = null;
            $values = null;
        }
        echo "Migrate links to edocman_document successful! ";
    }

    public function importFoldersToCategories()
    {
        $categories = $this->getCategories();

        $edocmanFolder = $this->getEdocmanDirectory();
        for ($i = 1; $i < sizeof($categories); $i++) {
            $structure = $edocmanFolder . $categories[$i][1];
            if (!mkdir(utf8_decode($structure), 0777, true)) {
                die('Create Folder: ' . $structure . ' failed');
            }
        }
        echo "Copy folders to " . $this->getEdocmanDirectory() . " successful! ";
    }

    public function importEntitiesToDocuments()
    {
        $documents = $this->getDocuments();
        for ($i = 0; $i < sizeof($documents); $i++) {
            if (array_key_exists('original_filename_repo', $documents[$i])) {
                if ($documents[$i]['original_filename_repo'] != false) {
                    $file = "../media/com_thm_repo/" . $documents[$i]["original_filename_repo"];
                    $file = utf8_decode($file);
                    $newfile = $this->getEdocmanDirectory() . $documents[$i]["filename"];
                    $newfile = utf8_decode($newfile);

                    if (!copy($file, $newfile)) {
                        echo "copy from $file to $newfile failed\n";
                        var_dump($documents[$i]);
                    }
                }
            }
        }
        echo "Copy documents to " . $this->getEdocmanDirectory() . " successful! ";
    }
}
