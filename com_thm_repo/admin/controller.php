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
}



