<?php

/**
 * @category    Joomla_Component
 * @package     THM_Repo
 * @subpackage  Com_Thm_Repo.admin
 * @author      Stefan Schneider, <stefan.schneider@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access
defined('_JEXEC') or die;

jimport('thm_repo.core.All');
include( JPATH_COMPONENT_ADMINISTRATOR . '/zip_visitor.php');

/**
 * THM_RepoController class for component com_thm_repo
 *
 * @category Joomla.Component.Admin
 * @package  Com_Thm_Repo.admin
 * @link     www.mni.thm.de
 * @since    Class available since Release 2.0
 */
class THM_RepoController extends JControllerLegacy
{
    /**
     * Method to display admincenter
     *
     * @param boolean $cachable  cachable
     * @param boolean $urlparams url param
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
        JSubMenuHelper::addEntry(
            JText::_('COM_THM_REPO_START'),
            'index.php?option=com_thm_repo&view=start',
            $vName == 'start'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_THM_REPO_FOLDERMANAGER'),
            'index.php?option=com_thm_repo&view=folders',
            $vName == 'folders'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_THM_REPO_FILEMANAGER'),
            'index.php?option=com_thm_repo&view=files',
            $vName == 'files'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_THM_REPO_LINKMANAGER'),
            'index.php?option=com_thm_repo&view=links',
            $vName == 'links'
        );

        // Call parent behavior
        parent::display($cachable, $urlparams);
    }

    /**
     *
     */
    public function portOldRepositoryData()
    {
        jimport('thm_repo.core.All');
        jimport('joomla.filesystem.file');

        // TODO: Check is repository installed !?

        $folders = $this->_createObjectTree();

        $this->_importIntoRepo($folders);

        echo 'Done!';
    }

    /**
     * Gets the id of a super user
     * @return int The id of a super user
     */
    private function _getSuperUserId()
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

        if (empty($resultList)) {
            throw new RuntimeException("Unexpected State: No SuperUser found.");
        }

        return (int) $resultList[0]->id;
    }

    /**
     *
     */
    private function _importIntoRepo($folders)
    {
        // Create Root Folder
        $_public = 1;
        $_published = true;

        $root = new THMFolder(
            null, 'Root', 'One Node to rule them all!',
            new THMUser($this->_getSuperUserId()),
            $_public, $_published
        );
        THMFolder::persist($root);

        try
        {
            $this->_importFolders($root, $folders);
        }
        catch (Exception $ex)
        {
            print_r($ex);
            THMFolder::remove($root, true);
        }
    }

    /**
     * @param $repoFolder
     * @param $folders
     * @return
     */
    private function _importFolders($repoFolder, $folders)
    {
        foreach ($folders as $folder) {
            $this->_importFolder($repoFolder, $folder);

            $this->_importEntities($newRepoFolder, $folder->children);
        }
    }

    /**
     * Imports a folder into the repo and initiates importing of its children
     * 
     * @param THMFolder $repoFolder     The parent folder
     * @param array     $folder         Array with folder details (name,
     * description, created_by, viewlevel, enabled, children)
     * @param array     $pathComponents Array of path components that
     * point to the parent folder
     */
    private function _importFolder($repoFolder, $folder, $pathComponents)
    {
        // Setting default values - Default User is set by function _getValidUser
        if (!isset($folder['viewlevel'])) $folder['viewlevel'] = 1;
        if (!isset($folder['enabled'])) $folder['enabled'] = true;
    
        $newRepoFolder = new THMFolder(
            $repoFolder,
            $folder['name'],
            $folder['description'],
            $this->_getValidUser($folder['created_by']),
            (int) $folder['viewlevel'],
            (bool) $folder['enabled']
        );
        THMFolder::persist($newRepoFolder);

        $pathComponents[] = $folder['name'];
		if (array_key_exists('children', $folder)) {
			$this->_importEntities($newRepoFolder, $folder['children'], $pathComponents);
		}
	}

    /**
     * Imports multiple entities into the repo
     * 
     * @param THMFolder $repoFolder     The parent folder
     * @param array     $entities       Array of entities that will be imported
     * @param array     $pathComponents The path components that point to the
     * containing folder in the file system
     */
    private function _importEntities($repoFolder, $entities, $pathComponents = array())
    {
        foreach ($entities as $entity) {
            $this->_importEntity($repoFolder, $entity, $pathComponents);
        }
    }

    /**
     * Imports one entity into the repo
     * 
     * @param THMFolder $repoFolder     The parent folder
     * @param array     $entity         The entity details
     * @param array     $pathComponents The oath components that point
     * to the containing folder in the file system
     * 
     * @throws RuntimeException If the entity type is unknown
     */
    private function _importEntity($repoFolder, $entity, $pathComponents = array())
    {
        if ($entity['type'] == 'file') {
            $this->_importFile($repoFolder, $entity, $pathComponents);
        } elseif ($entity['type'] == 'link') {
            $this->_importLink($repoFolder, $entity, $pathComponents);
        } elseif ($entity['type'] == 'folder') {
            $this->_importFolder($repoFolder, $entity, $pathComponents);
        } else {
            throw new RuntimeException('Unknown entity type: ' . $entity['type']);
        }
    }

    /**
     * Imports a file into the repo and initiates importing of its children
     * 
     * @param THMFolder $repoFolder     The parent folder
     * @param array     $file           Array with file details (name, description,
     * created_by, viewlevel, enabled, children)
     * @param array     $pathComponents The path components that will point
     * to the containing folder
     */
    private function _importFile($repoFolder, $file, $pathComponents)
    {            
        // Setting default values - Default User is set by function _getValidUser
        if (!isset($file['viewlevel'])) $file['viewlevel'] = 1;
        if (!isset($file['enabled'])) $file['enabled'] = true;
        
        $pathComponents[] = $file['name'];
        $filePath = implode(DS, $pathComponents);

        if (JFile::exists($filePath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            $repoFile = new THMFile(
                $repoFolder,
                JFile::stripExt($file['name']),
                $file['description'],
                $this->_getValidUser($file['created_by']),
                (int) $file['viewlevel'],
                (bool) $file['enabled'],
                array(
                    'tmp_name' => $filePath,
                    'name' => $file['name'],
                    'size' => filesize($filePath),
                    'type' => $mimeType
                )
            );
            THMFile::persist($repoFile);
        }
    }

    /**
     * Imports a link into the repo and initiates importing of its children
     * 
     * @param THMFolder $repoFolder     The parent folder
     * @param array     $link           Array of link details (name, description,
     * created_by, uri, viewlevel, enabled, children)
     * @param array     $pathComponents The path components that point to the
     * containing folder
     */
    private function _importLink($repoFolder, $link, $pathComponents)
    {            
        // Setting default values - Default User is set by function _getValidUser
        if (!isset($link['viewlevel'])) $link['viewlevel'] = 1;
        if (!isset($link['enabled'])) $link['enabled'] = true;
            
        $repoLink = new THMWebLink(
            $repoFolder,
            $link['name'],
            $link['description'],
            $this->_getValidUser($link['created_by']),
            $link['uri'],
            (int) $link['viewlevel'],
            (bool) $link['enabled']
        );

        THMWebLink::persist($repoLink);
    }

    /**
     * @param int $parentId
     * @return mixed
     */
    private function _createObjectTree($parentId = 1)
    {
        // 1. Read all categories from repository
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(
                "id, parent_id, title, description, published, access as 'viewlevel'"
            )
            ->from('#__categories')
            ->where("extension like 'com_thm_repository' AND parent_id = $parentId")
            ->order("lft");

        $folders = $db->setQuery($query)->loadObjectList();

        foreach ($folders as $folder) {
            $folder->childFolders = $this->_createObjectTree((int) $folder->id);
            $folder->childEntities = $this->_createEntityObject((int) $folder->id);
        }

        return $folders;
    }

    /**
     * @param $parentId
     * @return mixed
     */
    private function _createEntityObject($parentId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(
                "DISTINCT e.id, e.catid, e.version, e.title,
                e.path, e.description, e.access as 'viewlevel'"
            )
            ->select("e.created_by, e.state as 'published', t.name as 'type'")
            ->from("#__thm_repository_entity e")
            ->innerJoin("#__thm_repository_type t ON e.type = t.id")
            ->leftJoin("#__thm_repository_version v ON e.id = v.entityId")
            ->where("(active = 1 or active is null) AND e.catid = $parentId")
            ->order("e.id, e.version");

        return $db->setQuery($query)->loadObjectList();
    }

    /**
     * Gets a valid user in the system, if a user with
     * the given name can't be found a super user will be selected
     * 
     * @param string $name The name of the user
     * 
     * @return THMUser A valid user
     */
    private function _getValidUser($name)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $name = $db->quote($id);
        $query
            ->select('*')
            ->from('#__users')
            ->where("name = $name");
        $result = $db->setQuery($query)->loadAssoc();

        return new THMUser(
            empty($result['id']) ? (int) JFactory::getUser()->get('id') : (int) $result['id']
        );
    }

    /**
     * Import Zip File Action
     *
     * @access public
     * @return NULL
     *
     * @copyright   
     * @author    adnan.oezsarigoel@mni.thm.de
     */
    public function zipImportAction()
    {
        jimport('joomla.filesystem.folder');
        
		// This folder will be deleted by JFolder::delete
        $tmpDir = sys_get_temp_dir() . DS . uniqid('import_thm_repo_');
        
        $message = '';
        
        $file = JRequest::getVar(
            'import_thm_repo_form_file', null, 'files', 'array'
        );
        
        if (isset($file)) {
            $filename = JFile::makeSafe($file['name']);
            
            if (strtolower(JFile::getExt($filename)) === 'zip') {      
                $zip = new ZipArchive;
                $result = $zip->open($file['tmp_name']);
				if ($result === true) {
					$zip->extractTo($tmpDir);
					$zip->close();
					
					$metaFileName = 'Metadata.json';
					if (file_exists($tmpDir . DS . $metaFileName)) {
						$jsonStr = file_get_contents($tmpDir . DS . $metaFileName);
						$metaData = json_decode($jsonStr, true);
						if ($metaData !== null) {							
							$message .= $this->_getMetaInformations($metaData);

							$this->_importEntities(
								THMFolder::getRoot(false), $metaData, array($tmpDir)
							);
							
							JFactory::getApplication()->enqueueMessage($message);
						} else {
							JFactory::getApplication()->enqueueMessage('Fehler in der Metadaten-Struktur: ' . json_last_error_msg(), 'error');
						}
					} else {
						JFactory::getApplication()->enqueueMessage($metaFileName . ' nicht gefunden!', 'error');
					}
						
					if (!JFolder::delete($tmpDir)) {
						JFactory::getApplication()->enqueueMessage('Temp-Ordner ' . $tmpDir . ' löschen fehlgeschlagen!', 'warning');
					}
				} else {
					JFactory::getApplication()->enqueueMessage('Die hochgeladene Datei ist kein gültiges ZIP-Archiv.', 'error');
				}
            } else {
                JFactory::getApplication()->enqueueMessage('Es sind nur Zip-Dateien erlaubt!', 'error');
			}
		}

		$this->setRedirect('index.php?option=com_thm_repo&view=start');
    }
    
    
    

    /**
     * Get Meta Informations
     *
     * @param array  $metaInformations Meta Informations Array
     * @param String $prefix           Only for debugging
     *
     * @return String
     *
     * @access private
     *
     * @copyright
     * @author    adnan.oezsarigoel@mni.thm.de
     */
    private function _getMetaInformations($metaInformations, $prefix = ' | ')
    {
        $message = '';
        foreach ($metaInformations AS $obj) {
            if (empty($obj) || !isset($obj['type'])) {
                continue;
            } else if ($obj['type'] === 'folder') {
                $message .= $prefix . $obj['name'] . '<br />';
                $message .= $this->_getMetaInformations(
                    $obj['children'], $prefix . ' - '
                );
            } else if ($obj['type'] === 'link') {
                $message .= $prefix . $obj['name'] .
                    ' -&gt; ' . $obj['uri'] . '<br />';
            } else {
                $message .= $prefix . $obj['name'] . '<br />';
            }
        }
        return $message;
    } // end of function _getMetaInformations
    
    
    

    /**
     * Export-Task called by click event from export button.
     *
     * @return nothing
     */
    public function doExport()
    {

        $jsonMetaInfo = json_encode($this->getMetaInfoFolder(), JSON_PRETTY_PRINT);

        $zipper = new ZipVisitor($jsonMetaInfo);

        $this->walkTree($zipper);

        header("Content-Type: application/zip");
        header(
            "Content-Disposition: attachment; filename=thm-repo-export-" .
            date("Y-m-d-H-i", time()) . ".zip"
        );
        readfile($zipper->file);

        exit();
    }

    /**
     * Collect meta information recursively from folder.
     *
     * @param THMFolder $folder folder to collect information from
     *
     * @return stdClass  object containing json meta information
     */
    private function getMetaInfoFolder($folder = null)
    {
        if ($folder == null) {
            $folder = THMFolder::getRoot();
        } else {
            $jsonObject = new stdClass;
            $jsonObject->name = $folder->getName();
            $jsonObject->type = 'folder';
            $jsonObject->created_by = $folder->getCreatedBy()->getDisplayName();
            $jsonObject->created_on = $folder->getCreated();
            $jsonObject->modified_by = $folder->getModifiedBy()->getDisplayName();
            $jsonObject->modified_on = $folder->getModified();
            $jsonObject->description = $folder->getDescription();
            $jsonObject->viewlevel = $folder->getViewLevel();
            $jsonObject->enabled = $folder->isPublished() ? 1 :0;
        }

        $children = array();

        foreach ($folder->getFolders() as $f)
        {
            $children[] = $this->getMetaInfoFolder($f);
        }

        foreach ($folder->getEntities() as $a)
        {
            $children[] = $this->getMetaInfoEntity($a);
        }

        if ($jsonObject == null) {
            return $children;
        } else {
            $jsonObject->children = $children;
            return $jsonObject;
        }
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
        $jsonObject->enabled = $entity->isPublished() ? 1 : 0;

        return $jsonObject;
    }

    /*
     * This function will walk a TreeVisitor object through the file-tree.
     *
     * @param TreeVisitor $visitor Visitor.
     * @param THMFolder $folder Is the folder from where to start traversing the file tree or null to start from
     *                          the root folder.
     */
    private function walkTree($visitor)
    {
        $this->walkFolders($visitor);

        $visitor->done();
    }

    /*
     * Helper function for walkTree.
     */
    private function walkFolders($visitor, $folder = null)
    {
        if($folder == null)
        {
            $folder = THMFolder::getRoot();
        } else {
            $visitor->enteringFolder($folder);
        }

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

}
