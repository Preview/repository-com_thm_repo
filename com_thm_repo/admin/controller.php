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

// No direct access
defined('_JEXEC') or die;

jimport('thm_repo.core.All');

/**
 * THM_RepoController class for component com_thm_repo
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_repo.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_RepoController extends JControllerLegacy
{
	/**
	 * Method to display admincenter
	 * 
	 * @param   boolean  $cachable   cachable
	 * @param   boolean  $urlparams  url param
	 *
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Set default view if not set
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->getCmd('view', 'start'));

		// Submenu
        $vName = JFactory::getApplication()->input->getWord('view', 'thm_repo');

        // TODO: replace deprecated call
		JSubMenuHelper::addEntry(JText::_('COM_THM_REPO_START'), 'index.php?option=com_thm_repo&view=start', $vName == 'start');
		JSubMenuHelper::addEntry(JText::_('COM_THM_REPO_FOLDERMANAGER'), 'index.php?option=com_thm_repo&view=folders', $vName == 'folders');
		JSubMenuHelper::addEntry(JText::_('COM_THM_REPO_FILEMANAGER'),  'index.php?option=com_thm_repo&view=files', $vName == 'files');
		JSubMenuHelper::addEntry(JText::_('COM_THM_REPO_LINKMANAGER'), 'index.php?option=com_thm_repo&view=links', $vName == 'links');

		// Call parent behavior
		parent::display($cachable, $urlparams);
	}

    public function portOldRepositoryData()
    {
        jimport('thm_repo.core.All');
        jimport('joomla.filesystem.file');

        // TODO: Check is repository installed !?

        $folders = $this->createObjectTree();

        $this->importIntoRepo($folders);

        echo 'Done!';
    }

    private function getSuperUserId()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select('u.id')
            ->from('#__users u')
            ->innerJoin('#__user_usergroup_map m ON u.id = m.user_id')
            ->innerJoin('#__usergroups g ON g.id = m.group_id')
            ->where("g.title like 'Super Users'");
        $resultList = $db->setQuery($query)->loadObjectList();

        if (empty($resultList))
        {
            throw new RuntimeException("Unexpected State: No SuperUser found.");
        }

        return (int) $resultList[0]->id;
    }

    private function importIntoRepo($folders)
    {
        // Create Root Folder
        $_public = 1;
        $_published = true;

        $root = new THMFolder(
            null, 'Root', 'One Node to rule them all!', new THMUser($this->getSuperUserId()),
            $_public, $_published
        );
        THMFolder::persist($root);

        try
        {
            $this->importFolders($root, $folders);
        }
        catch (Exception $ex)
        {
            print_r($ex);
            THMFolder::remove($root, true);
        }
    }

    private function importFolders($repoFolder, $folders)
    {
        foreach ($folders as $folder)
        {
            $newRepoFolder = new THMFolder(
                $repoFolder, $folder->title, empty($folder->description) ? 'No Description' : $folder->description,
                $this->getValidUser($folder->created_by),
                (int) $folder->viewlevel, ($folder->published == 1)
            );
            THMFolder::persist($newRepoFolder);

            $this->importFolders($newRepoFolder, $folder->childFolders);
            $this->importEntities($newRepoFolder, $folder->childEntities);
        }
    }

    private function importEntities($repoFolder, $entities)
    {
        foreach ($entities as $entity)
        {
            if ($entity->type == 'file')
            {
                $this->importFile($repoFolder, $entity);
            }
            elseif ($entity->type == 'link')
            {
                $this->importLink($repoFolder, $entity);
            }
        }
    }

    private function importFile($repoFolder, $file)
    {
        $filePath = JPATH_ROOT . '/thm_repository/' . $file->path;
        if (JFile::exists($filePath))
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            $repoFile = new THMFile(
                $repoFolder, $file->title, empty($file->description) ? 'No Description' : $file->description,
                $this->getValidUser($file->created_by),
                (int) $file->viewlevel, $file->published == 1,
                array(
                    'tmp_name' => $filePath,
                    'name' => basename($filePath),
                    'size' => filesize($filePath),
                    'type' => $mimeType
                )
            );
            THMFile::persist($repoFile);
        }
    }

    private function importLink($repoFolder, $link)
    {
        $repoLink = new THMWebLink(
            $repoFolder, $link->title, empty($link->description) ? 'No Description' : $link->description,
            $this->getValidUser($link->created_by),
            $link->path, (int) $link->viewlevel, $link->published == 1
        );

        THMWebLink::persist($repoLink);
    }

    private function createObjectTree($parentId = 1)
    {
        // 1. Read all categories from repository
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select("id, parent_id, title, description, published, access as 'viewlevel'")
            ->from('#__categories')
            ->where("extension like 'com_thm_repository' AND parent_id = $parentId")
            ->order("lft");

        $folders = $db->setQuery($query)->loadObjectList();

        foreach ($folders as $folder)
        {
            $folder->childFolders = $this->createObjectTree((int) $folder->id);
            $folder->childEntities = $this->createEntityObject((int) $folder->id);
        }

        return $folders;
    }

    private function createEntityObject($parentId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select("DISTINCT e.id, e.catid, e.version, e.title, e.path, e.description, e.access as 'viewlevel'")
            ->select("e.created_by, e.state as 'published', t.name as 'type'")
            ->from("#__thm_repository_entity e")
            ->innerJoin("#__thm_repository_type t ON e.type = t.id")
            ->leftJoin("#__thm_repository_version v ON e.id = v.entityId")
            ->where("(active = 1 or active is null) AND e.catid = $parentId")
            ->order("e.id, e.version");

        return $db->setQuery($query)->loadObjectList();
    }

    private function getValidUser($id)
    {
        $resultList = null;

        if (!empty($id))
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('id')
                ->from('#__users')
                ->where("id = $id");
            $resultList = $db->setQuery($query)->loadObjectList();
        }

        return new THMUser(empty($resultList) ? $this->getSuperUserId() : (int) $id);
    }

    /*
     * This function will walk a TreeVisitor object through the file-tree.
     *
     * @param TreeVisitor $visitor Visitor.
     * @param THMFolder $folder Is the folder from where to start traversing the file tree or null to start from
     *                          the root folder.
     */
    private function walkTree($visitor, $folder = null)
    {
        if($folder == null)
        {
            $folder = THMFolder::getRoot();
        }

        $this->walkFolders($visitor, $folder);

        $visitor->done();
    }

    /*
     * Helper function for walkTree.
     */
    private function walkFolders($visitor, $folder)
    {
        $visitor->enteringFolder($folder);

        foreach ($folder->getFolders() as $f)
        {
            $this->walkFolders($visitor, $f);
        }

        foreach ($folder->getEntities() as $e)
        {
            $visitor->visitEntity($e);
        }

        $visitor->leavingFolder($folder);
    }
    
    
    

    /*
     * Import Zip File Action
     *
     * @copyright   
     * @author      adnan.oezsarigoel@mni.thm.de
     * 
     * @access      public
     * @param       
     * @return      
     */
    public function zipImportAction() {  
    
        jimport('joomla.filesystem.folder');
        
        $tmpDir = sys_get_temp_dir() . '/' . uniqid('import_thm_repo_');  // This folder will be deleted by JFolder::delete
        
        $message = '';
        
        $file = JRequest::getVar('import_thm_repo_form_file', null, 'files', 'array');
        
        if (isset($file)) {
            $filename = JFile::makeSafe($file['name']);
            
            if (strtolower(JFile::getExt($filename)) === 'zip') {      
                $zip = new ZipArchive();
                $zip->open($file['tmp_name']);
                $zip->extractTo($tmpDir);
                $zip->close();
                
                $message .= 'Temp-Ordner ' . $tmpDir . ' erstellt!<br /><br />'; // Only for debugging

                // $listFolderTree = JFolder::listFolderTree($tmpDir, $filter, $maxLevel = 3, $level = 0, $parent = 0);
                // $listFolderTree = JFolder::listFolderTree($tmpDir);
                $importObj = $this->dirToImportOject($tmpDir);
                $message .= '<strong>Archive Content</strong><br />' . print_r($importObj, TRUE) . '<br /><br />'; // Only for debugging
                
                if (file_exists($tmpDir . '/Metadaten.json')) {
                    $jsonStr = file_get_contents($tmpDir . '/Metadaten.json');
                    $metaInformations = json_decode($jsonStr, TRUE); 
                    $message .= '<strong>Meta Data</strong><br />'; // Only for debugging
                    $message .= $this->importMetaInformations($metaInformations) . '<br /><br />';
                }
                else {
                    $message .= 'Metadaten.json nicht gefunden!<br /><br />'; // Only for debugging
                }
                
                if (JFolder::delete($tmpDir)) {
                    $message .= 'Temp-Ordner ' . $tmpDir . ' erfolgreich entfernt!<br /><br />'; // Only for debugging
                }
                else {
                    $message .= 'Temp-Ordner ' . $tmpDir . ' löschen fehlgeschlagen!<br /><br />'; // Only for debugging
                }
            }
            else {
                $message .= 'Es sind nur Zip-Dateien erlaubt!<br /><br />'; // Only for debugging
            }
        }
            
        $this->setMessage($message);

        $this->setRedirect('index.php?option=com_thm_repo&view=start');        
    } // end of function zipImportAction
    
    
    

    /*
     * Directory to Import Object
     *
     * @copyright   
     * @author      adnan.oezsarigoel@mni.thm.de
     * 
     * @access      private
     * @param       String          Path to unpacked Zip-Directory
     * @return      array           Array with Directory Informations
     * @TODO        Check "Do something" in function
     * @TODO        $obj is still not finished
     */
    private function dirToImportOject($dir) {
        $obj = array();
        if ($handle = opendir($dir)) {
            while ($file = readdir($handle)) {
                if ($file === "." || $file === "..") continue;
                else if (is_dir($dir . "/" . $file)) {
                    // Do something here with directories
                    $obj[$file] = $this->dirToImportOject($dir . "/" . $file);
                    // array_push($obj, $this->dirToImportOject($dir . "/" . $file));
                }
                else {
                    $pathInfo = pathinfo($dir . "/" . $file);
                    if ($pathInfo['extension'] === 'url') {
                        // Do something here with urls
                        $url = file_get_contents($dir . "/" . $file);
                        $obj[$file] = $url;
                        // array_push($obj, $url);
                    }
                    else {
                        // Do something here with other files
                        $obj[$file] = $pathInfo['filename'];
                        // array_push($obj, $pathInfo['filename']);
                    }
                }
            }
            closedir($handle);
        }
        return $obj;
    } // end of function dirToImportOject
    
    
    

    /*
     * Import Meta Informations
     *
     * @copyright   
     * @author      adnan.oezsarigoel@mni.thm.de
     * 
     * @access      private
     * @param       array           Meta Informations Array
     * @param       String          Only for debugging
     * @return      String          Only for debugging
     * @TODO        Check "Do something" in function
     * @TODO        Delete or uncomment echos in function
     */
    private function importMetaInformations($metaInformations, $prefix = ' | ') {
        $message = '';
        foreach ($metaInformations AS $obj) {
            if (empty($obj) || !isset($obj['type'])) continue;
            else if ($obj['type'] === 'folder') {
                // Do something here with directories
                $message .= $prefix . $obj['name'] . '<br />';
                $message .= $this->importMetaInformations($obj['children'], $prefix . ' - ');
            }
            else if ($obj['type'] === 'link') {
                // Do something here with urls
                $message .= $prefix . $obj['name'] . ' -&gt; ' . $obj['uri'] . '<br />';
            }
            else {
                // Do something here with other files
                $message .= $prefix . $obj['name'] . '<br />';
            }
        }
        return $message;
    } // end of function importMetaInformations
    
    
    

    /**
     * Export-Task called by click event from export button.
     *
     * @return nothing
     */
    public function doExport()
    {
        $rootFolder = THMFolder::getRoot();
        $jsonMetaInfo = json_encode($this->getMetaInfoFolder($rootFolder), JSON_PRETTY_PRINT);

        $zipper = new ZipVisitor($jsonMetaInfo);

        $this->walkTree($zipper);

        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=thm-repo-export-" . date("Y-m-d-H-i", time()) . ".zip");
        readfile($zipper->file);
    }

    /**
     * Collect meta information recursively from folder.
     *
     * @param   THMFolder  $folder  folder to collect information from
     *
     * @return stdClass  object containing json meta information
     */
    private function getMetaInfoFolder($folder)
    {
        $jsonObject = new stdClass;

        $jsonObject->name = $folder->getName();
        $jsonObject->type = true;
        $jsonObject->created_by = $folder->getCreatedBy()->getDisplayName();
        $jsonObject->created_on = $folder->getCreated();
        $jsonObject->modified_by = $folder->getModifiedBy()->getDisplayName();
        $jsonObject->modified_on = $folder->getModified();
        $jsonObject->description = $folder->getDescription();
        $jsonObject->viewlevel = $folder->getViewLevel();
        $jsonObject->enabled = $folder->isPublished();

        $children = array();

        foreach ($folder->getFolders() as $f)
        {
            $children[] = $this->getMetaInfoFolder($f);
        }

        foreach ($folder->getEntities() as $a)
        {
            $children[] = $this->getMetaInfoEntity($a);
        }

        $jsonObject->children = $children;

        return $jsonObject;
    }

    /**
     * Collect meta information recursively from file.
     *
     * @param   THMEntity  $entity  entity to collect information from
     *
     * @return stdClass  object containing json meta information
     */
    private function getMetaInfoEntity($entity)
    {
        $jsonObject = new stdClass;

        $jsonObject->name = $entity->getName();

        if ($entity instanceof THMWebLink)
        {
            $jsonObject->type = 'link';
            $jsonObject->uri = $entity->getLink();
        }
        else
        {
            $jsonObject->type = 'file';
        }

        $jsonObject->created_by = $entity->getCreatedBy()->getDisplayName();
        $jsonObject->created_on = $entity->getCreated();
        $jsonObject->modified_by = $entity->getModifiedBy()->getDisplayName();
        $jsonObject->modified_on = $entity->getModified();
        $jsonObject->description = $entity->getDescription();
        $jsonObject->viewlevel = $entity->getViewLevel();
        $jsonObject->enabled = $entity->isPublished();

        return $jsonObject;
    }
}

//TODO Where would you put an interface in Joomla?
/*
 *
 */
interface TreeVisitor
{
    /*
     * Will be called when entering a Folder.
     *
     * @param THMFolder $folder The Folder we are entering.
     */
    public function enteringFolder($folder);

    /*
     * Will be called when leaving a Folder.
     *
     * @param THMFolder $folder The Folder we leave.
     */
    public function leavingFolder($folder);

    /*
     * Will be called when an entity is found.
     *
     * @param THMEntity $entity The entity found.
     */
    public function visitEntity($entity);

    public function done();
}

class ZipVisitor implements TreeVisitor
{
    private $path = [];
    private $zip;
    public $file;
    public $report = "";

    public function __construct($json)
    {
        $this->file = tempnam(sys_get_temp_dir(), 'Tux');
        $this->zip = new ZipArchive();

        $this->zip->open($this->file);

        $this->zip->addFromString("METAINFO.json", $json);
    }

    public function enteringFolder($folder)
    {
        $this->path[] = $folder->getName();
        $this->zip->addEmptyDir($this->path());
        $this->report .= "entering: " . $folder->getName() . "\n";
    }

    public function leavingFolder($folder)
    {
        array_pop($this->path);
        $this->report .= "leaving: " . $folder->getName() . "\n";
    }

    public function visitEntity($entity)
    {
        if ($entity instanceof THMWebLink)
        {
            $this->zip->addFromString($this->path() . "/" . $entity->getName() . ".url", $entity->getLink());
        }
        else if ($entity instanceof THMFile)
        {
            $this->zip->addFile(JPATH_ROOT . $entity->getPath(), $this->path() . "/" . $entity->getName() . "." . pathinfo(JPATH_ROOT . $entity->getPath(), PATHINFO_EXTENSION));
        }
    }

    public function done()
    {
        $this->zip->close();
    }

    private function path()
    {
        return join("/", $this->path);
    }
}