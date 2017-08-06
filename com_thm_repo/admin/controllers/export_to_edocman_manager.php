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

require JPATH_ROOT . '/components/com_edocman/helper/helper.php';


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

	private $categoriesOffset;

	private $documentsOffset;

	private $documentCategoryOffset;

	private $linksOffset;

	private $edocmanDirectory;

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
	 *
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
	 *
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
	 *
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
	 *
	 * @param $links
	 */
	public function setLinks($links)
	{
		$this->links = $links;
	}

	/**
	 * Method to get categoriesOffset
	 * @return categoriesOffset
	 */
	public function getCategoriesOffset()
	{
		return $this->categoriesOffset;
	}

	/**
	 * Method to set the parameter to $categoriesOffset
	 *
	 * @param $categoriesOffset
	 */
	public function setCategoriesOffset($categoriesOffset)
	{
		$this->categoriesOffset = $categoriesOffset;
	}

	/**
	 * Method to get documentsOffset
	 * @return documentsOffset
	 */
	public function getDocumentsOffset()
	{
		return $this->documentsOffset;
	}

	/**
	 * Method to set the parameter to $documentsOffset
	 *
	 * @param $documentsOffset
	 */
	public function setDocumentsOffset($documentsOffset)
	{
		$this->documentsOffset = $documentsOffset;
	}

	/**
	 * Method to get documentCategoryOffset
	 * @return documentCategoryOffset
	 */
	public function getDocumentCategoryOffset()
	{
		return $this->documentCategoryOffset;
	}

	/**
	 * Method to set the parameter to $documentCategoryOffset
	 *
	 * @param $documentCategoryOffset
	 */
	public function setDocumentCategoryOffset($documentCategoryOffset)
	{
		$this->documentCategoryOffset = $documentCategoryOffset;
	}

	/**
	 * Method to get linksOffset
	 * @return linksOffset
	 */
	public function getLinksOffset()
	{
		return $this->linksOffset;
	}

	/**
	 * Method to set the parameter to $linksOffset
	 *
	 * @param $linksOffset
	 */
	public function setLinksOffset($linksOffset)
	{
		$this->linksOffset = $linksOffset;
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
	 * @param mixed $edocmanDirectory
	 */
	public function setEdocmanDirectory($edocmanDirectory)
	{
		$this->edocmanDirectory = $edocmanDirectory;
	}


	/**
	 * Main function which is called from button Import Edocman Data
	 */
	public function run()
	{
		$this->setEdocmanDirectory(EdocmanHelper::getConfig()->documents_path . DIRECTORY_SEPARATOR);

		$this->setCategoriesOffset($this->calcCategoriesOffset());
		$this->setDocumentsOffset($this->calcDocumentsOffset());
		$this->setDocumentCategoryOffset($this->calcDocumentCategoryOffset());
		$this->setLinksOffset($this->calcLinksOffset());

		$this->setCategories($this->compileCategories());
		$this->setDocuments($this->compileDocuments());
		$this->setDocumentCategory($this->compileDocumentCategory());
		$this->setLinks($this->compileLinks());

		$this->saveCategoriesToDb();
		$this->saveDocumentsToDb();
		$this->saveDocumentCategoryToDb();
		$this->saveLinksToDbDocuments();
		$this->updateTHMTreeModules();
		$this->updateJoomlaContent($this->getDocumentsOffset());
		$this->updateJoomlaAssets();

		$this->importFoldersToCategories();
		$this->importEntitiesToDocuments();
	}

	/**
	 * Method to calculate the offset for the id of the categories
	 * @return categoriesOffset
	 */
	public function calcCategoriesOffset()
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('max(id)');
		$query->from('#__edocman_categories');
		$db->setQuery($query);
		return (int) $db->loadResult();
	}

	/**
	 * Method to calculate the offset for the id of the documents
	 * @return documentsOffset
	 */
	public function calcDocumentsOffset()
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('max(id)');
		$query->from('#__edocman_documents');
		$db->setQuery($query);
		return (int) $db->loadResult();
	}

	/**
	 * Method to calculate the offset for the id of the document category
	 * @return documentCategoryOffset
	 */
	public function calcDocumentCategoryOffset()
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('max(id)');
		$query->from('#__edocman_document_category');
		$db->setQuery($query);
		return (int) $db->loadResult();
	}

	/**
	 * Method to calculate the offset for the id of the links
	 * @return linksOffset
	 */
	public function calcLinksOffset()
	{
		return $this->getDocumentsOffset();
	}

	/**
	 * Method to save the categories in database edocman_categories
	 *
	 */
	public function saveCategoriesToDb()
	{
		$categories = $this->getCategories();
		$db         = JFactory::getDBO();

		for ($i = 0; $i < sizeof($categories); $i++)
		{
			$query   = $db->getQuery(true);
			$columns = array('id', 'parent_id', 'title', 'description', 'access', 'created_user_id',
				'created_time', 'modified_user_id', 'modified_time', 'published', 'category_layout', 'alias',
				'level', 'checked_out', 'checked_out_time', 'language', 'path');

			$values = array($db->quote($categories[$i]["id"] + $this->getCategoriesOffset()), $db->quote($categories[$i]["parent_id"] + $this->getCategoriesOffset()),
				$db->quote($categories[$i]["name"]), $db->quote($categories[$i]["description"]), $db->quote($categories[$i]["viewlevel"]),
				$db->quote($categories[$i]["created_by"]),
				$db->quote($categories[$i]["created"]), $db->quote($categories[$i]["modified_by"]),
				$db->quote($categories[$i]["modified"]), $db->quote($categories[$i]["published"]),
				$db->quote("default"), $db->quote(strtolower($this->removeSpecialChars($categories[$i]["name"]))), $db->quote($categories[$i][0]),
				$db->quote("0"), $db->quote(null), $db->quote("*"), $db->quote($categories[$i][1]));

			$query
				->insert($db->quoteName('#__edocman_categories'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));

			$db->setQuery($query);
			$db->execute();
			$query   = null;
			$columns = null;
			$values  = null;
		}

		echo "Migrate edocman_categories with id-offset " . $this->getCategoriesOffset() . " successful!<br>";
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
		$maximum = (int) $db->loadResult();

		$levels = array();
		$j      = 0;
		for ($i = 0; $i <= $maximum; $i++)
		{
			if ((int) $categories[$j]["id"] == $i)
			{
				$levels[$i] = array(
					(int) ($categories[$j]["id"]),
					(int) ($categories[$j]["parent_id"]),
					($categories[$j]["name"]));
				$j++;
			}
			else
			{
				$levels[$i] = null;
			}
		}

		// GetLevels and path
		for ($i = 0; $i < sizeof($levels); $i++)
		{
			$level = 1;
			$id    = $i;
			$path  = "";
			if (!(empty($levels[$id])))
			{
				do
				{
					$pid = ($levels[$id][1]);
					if ($pid != 0)
					{
						$path = strtolower($this->removeSpecialChars($levels[$id][2])) . DIRECTORY_SEPARATOR . $path;
						$id   = $pid;
						$level++;
					}
				} while ($pid != 0);

				$levels[$i][3] = $level;
				$path          = substr($path, 0, strlen($path) - 1);
				$levels[$i][4] = $path;
			}
		}

		// SetLevels and path
		for ($i = 0; $i < sizeof($categories); $i++)
		{
			for ($j = 0; $j < sizeof($levels); $j++)
			{
				if ((int) $categories[$i]["id"] == $levels[$j][0])
				{
					array_push($categories[$i], $levels[$j][3]);
					array_push($categories[$i], $levels[$j][4]);
				}
			}
		}

		// Set parent_id root = 0
		$categories[0]["parent_id"] = 0-$this->getCategoriesOffset();

		return $categories;
	}

	/**
	 * Method to compile the documents in the correct structure from thm_repo_entity and thm_repo_version
	 * compile the document path
	 * @return array
	 */
	public function compileDocuments()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__thm_repo_entity');
		$db->setQuery($query);
		$result = $db->loadAssocList();

		$categories = $this->getCategories();
		$documents  = array();
		for ($i = 0; $i < sizeof($result); $i++)
		{
			$parent_id                        = (int) $result[$i]["parent_id"];
			$documents[$i]["id"]              = $result[$i]["id"];
			$documents[$i]["asset_id"]        = $result[$i]["asset_id"];
			$documents[$i]["created_user_id"] = $result[$i]["created_by"];
			$documents[$i]["created_time"]    = $result[$i]["created"];
			$documents[$i]["ordering"]        = $result[$i]["ordering"];
			$documents[$i]["published"]       = $result[$i]["published"];
			$documents[$i]["access"]          = $result[$i]["viewlevel"];
			if ($parent_id < sizeof($categories))
			{
				for ($j = 0; $j < sizeof($categories); $j++)
				{
					if ($parent_id == $categories[$j]["id"])
					{
						$documents[$i]["path"] = $categories[$j][1];
					}
				}
			}
			else
			{
				$documents[$i]["path"] = "";
			}
		}

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__thm_repo_version');
		$db->setQuery($query);
		$result = $db->loadAssocList();

		$links = array();

		for ($i = 0; $i < sizeof($documents); $i++)
		{
			for ($j = 0; $j < sizeof($result); $j++)
			{
				if ($documents[$i]["id"] == $result[$j]["id"])
				{
					$documents[$i]["id"] == $result[$j]["id"];
					$documents[$i]["title"]                  = $result[$j]["name"];
					$documents[$i]["alias"]                  = strtolower($this->removeSpecialChars($result[$j]["name"]));
					$originalFilename                        = substr(strrchr($result[$j]["path"], "/"), 1);
					if ($originalFilename === FALSE)
					{
						$originalFilename = substr(strrchr($result[$j]["path"], "\\"), 1);
					}
					$documents[$i]["original_filename_repo"] = $originalFilename;
					$documents[$i]["original_filename"]      = $result[$j]["name"] . "." . pathinfo($originalFilename, PATHINFO_EXTENSION);
					$documents[$i]["description"]            = $result[$j]["description"];
					$documents[$i]["modified_time"]          = $result[$j]["modified"];
					$documents[$i]["modified_user_id"]       = $result[$j]["modified_by"];
					$documents[$i]["filename"]               = $documents[$i]["path"] . DIRECTORY_SEPARATOR . $documents[$i]["original_filename_repo"];
				}
			}

			if (!(array_key_exists('title', $documents[$i])))
			{
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
		$db        = JFactory::getDBO();

		for ($i = 0; $i < sizeof($documents); $i++)
		{
			if (array_key_exists('title', $documents[$i]))
			{
				$query   = $db->getQuery(true);
				$columns = array('id', 'title', 'alias', 'filename', 'original_filename', 'description', 'modified_time',
					'modified_user_id', 'created_user_id', 'created_time', 'ordering', 'published', 'access',
					'image', 'rating_count', 'rating_sum', 'hits', 'downloads', 'checked_out', 'language',
					'indexed_content', 'params');

				$values = array($db->quote($documents[$i]["id"] + $this->getDocumentsOffset()), $db->quote($documents[$i]["title"]),
					$db->quote($documents[$i]["alias"]), $db->quote($documents[$i]["filename"]),
					$db->quote($documents[$i]["original_filename"]), $db->quote($documents[$i]["description"]),
					$db->quote($documents[$i]["modified_time"]), $db->quote($documents[$i]["modified_user_id"]),
					$db->quote($documents[$i]["created_user_id"]),
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

			$query   = null;
			$columns = null;
			$values  = null;
		}

		echo "Migrate edocman_documents with id-offset " . $this->getDocumentsOffset() . " successful!<br>";
	}

	/**
	 * Method to compile the documentCategory to the correct structure from thm_repo_entity
	 * @return array
	 */
	public function compileDocumentCategory()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__thm_repo_entity');
		$db->setQuery($query);
		$result = $db->loadAssocList();
		$docCat = array();

		$documents = $this->getDocuments();
		$links = $this->getLinks();

		for ($i = 0; $i < sizeof($result); $i++)
		{
			for ($j = 0; $j < sizeof($documents); $j++)
			{
				if ($documents[$i]["id"] == $result[$i]["id"]) {
					$docCat[$i]["document_id"] = $result[$i]["id"];
					$docCat[$i]["category_id"] = $result[$i]["parent_id"];
					break;
				}
			}
			for ($j = 0; !isset($docCat[$i]["document_id"]) && $j < sizeof($links); $j++)
			{
				if ($links[$i]["id"] == $result[$i]["id"]) {
					$docCat[$i]["document_id"] = $result[$i]["id"];
					$docCat[$i]["category_id"] = $result[$i]["parent_id"];
					break;
				}
			}
		}

		return $docCat;
	}

	/**
	 * Method to save the documentCategory to database edocman_documtn_category
	 *
	 */
	public function saveDocumentCategoryToDb()
	{
		$docCat = $this->getDocumentCategory();
		$db     = JFactory::getDBO();

		for ($i = 0; $i < sizeof($docCat); $i++)
		{
			$query   = $db->getQuery(true);
			$columns = array('id', 'document_id', 'category_id', 'is_main_category');

			$values = array($db->quote($docCat[$i]["document_id"] + $this->getDocumentCategoryOffset()), $db->quote($docCat[$i]["document_id"] + $this->getDocumentsOffset()),
				$db->quote($docCat[$i]["category_id"] + $this->getCategoriesOffset()), $db->quote("1"));

			$query
				->insert($db->quoteName('#__edocman_document_category'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));

			$db->setQuery($query);
			$db->execute();
			$query   = null;
			$columns = null;
			$values  = null;
		}

		echo "Migrate edocman_document_category with id-offset " . $this->getDocumentCategoryOffset() . " successful!<br>";
	}

	/**
	 * Method to compile the Links to the correct structure from thm_repo_entity
	 * @return array
	 */
	public function compileLinks()
	{
		$links = $this->getLinks();
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__thm_repo_link');
		$db->setQuery($query);
		$result = $db->loadAssocList();

		for ($i = 0; $i < sizeof($links); $i++)
		{
			for ($j = 0; $j < sizeof($result); $j++)
			{
				if ($links[$i]["id"] == $result[$j]["id"])
				{
					$links[$i]["title"]             = $result[$j]["name"];
					$links[$i]["alias"]             = strtolower($this->removeSpecialChars($result[$j]["name"]));
					$links[$i]["filename"]          = $links[$i]["path"] . DIRECTORY_SEPARATOR . $links[$i]["title"];
					$links[$i]["original_filename"] = "";
					$links[$i]["description"]       = $result[$j]["description"];
					$links[$i]["modified_time"]     = $result[$j]["modified"];
					$links[$i]["modified_user_id"]  = $result[$j]["modified_by"];
					$links[$i]["document_url"]      = $result[$j]["link"];
					break;
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
		$db    = JFactory::getDBO();

		for ($i = 0; $i < sizeof($links); $i++)
		{
			if(!empty($links[$i]["document_url"]))
			{
				$query   = $db->getQuery(true);
				$columns = array('id', 'title', 'alias', 'filename', 'original_filename', 'document_url', 'description', 'modified_time',
					'modified_user_id', 'created_user_id', 'created_time', 'ordering', 'published', 'access',
					'image', 'rating_count', 'rating_sum', 'hits', 'downloads', 'checked_out', 'language',
					'indexed_content', 'params');

				$values = array($db->quote($links[$i]["id"] + $this->getLinksOffset()), $db->quote($links[$i]["title"]),
					$db->quote($links[$i]["alias"]), $db->quote($links[$i]["filename"]),
					$db->quote($links[$i]["original_filename"]), $db->quote($links[$i]["document_url"]), $db->quote($links[$i]["description"]),
					$db->quote($links[$i]["modified_time"]), $db->quote($links[$i]["modified_user_id"]),
					$db->quote($links[$i]["created_user_id"]),
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
				$query   = null;
				$columns = null;
				$values  = null;
			}

		}
		echo "Migrate links to edocman_document successful!<br>";
	}

	public function importFoldersToCategories()
	{
		$categories = $this->getCategories();

		$edocmanFolder = $this->getEdocmanDirectory();
		for ($i = 1; $i < sizeof($categories); $i++)
		{
			$structure = $edocmanFolder . $categories[$i][1];
			if (!is_dir(utf8_decode($structure)) && !mkdir(utf8_decode($structure), 0755, true))
			{
				die('Create Folder: ' . $structure . ' failed');
			}
		}
		echo "Copy folders to " . $this->getEdocmanDirectory() . " successful!<br>";
	}

	public function importEntitiesToDocuments()
	{
		$documents = $this->getDocuments();
		for ($i = 0; $i < sizeof($documents); $i++)
		{
			if (array_key_exists('original_filename_repo', $documents[$i]))
			{
				$file    = "../media/com_thm_repo/" . $documents[$i]["original_filename_repo"];
				$file    = utf8_decode($file);
				$newfile = $this->getEdocmanDirectory() . $documents[$i]["filename"];
				$newfile = utf8_decode($newfile);
				if (empty($documents[$i]["original_filename_repo"]) || !file_exists($file) || !copy($file, $newfile))
				{
					$this->deleteDocumentFromDb($documents[$i]["id"], $file);
				}
			}
		}
		echo "Copy documents to " . $this->getEdocmanDirectory() . " successful!<br>";
	}

	public function deleteDocumentFromDb($id, $file)
	{
		$db    = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__edocman_documents'));
		$query->where($db->quoteName('id').'='.$db->quote($id + $this->getDocumentsOffset()));
		$db->setQuery($query);
		$db->execute();

		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__edocman_document_category'));
		$query->where($db->quoteName('document_id').'='.$db->quote($id + $this->getDocumentsOffset()));
		$db->setQuery($query);
		$db->execute();

		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($db->quoteName('name').'='.$db->quote('com_edocman.document.'.$id + $this->getDocumentsOffset()));
		$db->setQuery($query);
		$assets = $db->loadAssocList();

		echo "Failed to import document $id $file!<br>";
	}

	public function updateTHMTreeModules()
	{
		$db    = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'params')));
		$query->from($db->quoteName('#__modules'));
		$query->where($db->quoteName('module').'='.$db->quote('mod_thm_trees'));
		$db->setQuery($query);
		$modules = $db->loadAssocList();

		for ($i = 0; $i < sizeof($modules); $i++)
		{
			$params = json_decode($modules[$i]['params']);
			if ($params->library == 'thm_repo_treesimpl')
			{
				$params->library = 'edocman_treesimpl';
				$params->enable_links = '1';
				$params->category = strval(intval($params->category) + $this->getCategoriesOffset());

				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__modules'));
				$query->set($db->quoteName('params').'='.$db->quote(json_encode($params)));
				$query->where($db->quoteName('id').'='.$db->quote($modules[$i]['id']));
				$db->setQuery($query);
				$db->execute();
			}
		}

		echo "Update of THMTreeModules successful!<br>";
	}

	public function updateJoomlaContent($docOffset = 0)
	{
		$db    = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'content')));
		$query->from($db->quoteName('#__modules'));
		$query->where($db->quoteName('content').'LIKE'.$db->quote('%{\"repo\":%}%'));
		$db->setQuery($query);
		$modules = $db->loadAssocList();
		for ($i = 0; $i < sizeof($modules); $i++)
		{
			if (preg_match_all('/{\"repo\":(\d+)}/', $modules[$i]['content'], $matches, PREG_OFFSET_CAPTURE))
			{
				for ($j = 0; $j < sizeof($matches[0]); $j++)
				{
					$id =  intval($matches[1][$j][0]) + $this->getDocumentsOffset();
					$query = $db->getQuery(true);
					$query->select($db->quoteName('title'));
					$query->from($db->quoteName('#__edocman_documents'));
					$query->where($db->quoteName('id') . '=' . $db->quote($id));
					$db->setQuery($query);
					$title = $db->loadResult();
					$modules[$i]['content'] = str_replace($matches[0][$j][0], '<a href="index.php?option=com_edocman&amp;view=document&amp;id=' . $id . '">' . $title . '</a>', $modules[$i]['content']);
				}
			}

			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__modules'));
			$query->set($db->quoteName('content').'='.$db->quote($modules[$i]['content']));
			$query->where($db->quoteName('id').'='.$db->quote($modules[$i]['id']));
			$db->setQuery($query);
			$db->execute();
		}



		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'description')));
		$query->from($db->quoteName('#__categories'));
		$query->where($db->quoteName('description').'LIKE'.$db->quote('%{\"repo\":%}%'));
		$db->setQuery($query);
		$table = $db->loadAssocList();
		for ($i = 0; $i < sizeof($table); $i++)
		{
			if (preg_match_all('/{\"repo\":(\d+)}/', $table[$i]['description'], $matches, PREG_OFFSET_CAPTURE))
			{
				for ($j = 0; $j < sizeof($matches[0]); $j++)
				{
					$id =  intval($matches[1][$j][0]) + $this->getDocumentsOffset();
					$query = $db->getQuery(true);
					$query->select($db->quoteName('title'));
					$query->from($db->quoteName('#__edocman_documents'));
					$query->where($db->quoteName('id') . '=' . $db->quote($id));
					$db->setQuery($query);
					$title = $db->loadResult();
					$table[$i]['description'] = str_replace($matches[0][$j][0], '<a href="index.php?option=com_edocman&amp;view=document&amp;id=' . $id . '">' . $title . '</a>', $table[$i]['description']);
				}
			}

			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__categories'));
			$query->set($db->quoteName('description').'='.$db->quote($table[$i]['description']));
			$query->where($db->quoteName('id').'='.$db->quote($table[$i]['id']));
			$db->setQuery($query);
			$db->execute();
		}



		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('core_content_id', 'core_body')));
		$query->from($db->quoteName('#__ucm_content'));
		$query->where($db->quoteName('core_body').'LIKE'.$db->quote('%{\"repo\":%}%'));
		$db->setQuery($query);
		$table = $db->loadAssocList();
		for ($i = 0; $i < sizeof($table); $i++)
		{
			if (preg_match_all('/{\"repo\":(\d+)}/', $table[$i]['core_body'], $matches, PREG_OFFSET_CAPTURE))
			{
				for ($j = 0; $j < sizeof($matches[0]); $j++)
				{
					$id =  intval($matches[1][$j][0]) + $this->getDocumentsOffset();
					$query = $db->getQuery(true);
					$query->select($db->quoteName('title'));
					$query->from($db->quoteName('#__edocman_documents'));
					$query->where($db->quoteName('id') . '=' . $db->quote($id));
					$db->setQuery($query);
					$title = $db->loadResult();
					$table[$i]['core_body'] = str_replace($matches[0][$j][0], '<a href="index.php?option=com_edocman&amp;view=document&amp;id=' . $id . '">' . $title . '</a>', $table[$i]['core_body']);
				}
			}

			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__ucm_content'));
			$query->set($db->quoteName('core_body').'='.$db->quote($table[$i]['core_body']));
			$query->where($db->quoteName('core_content_id').'='.$db->quote($table[$i]['core_content_id']));
			$db->setQuery($query);
			$db->execute();
		}



		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'introtext', 'fulltext')));
		$query->from($db->quoteName('#__content'));
		$query->where($db->quoteName('introtext') . 'LIKE' . $db->quote('%{\"repo\":%}%'), 'OR');
		$query->where($db->quoteName('fulltext') . 'LIKE' . $db->quote('%{\"repo\":%}%'));
		$db->setQuery($query);
		$content = $db->loadAssocList();
		for ($i = 0; $i < sizeof($content); $i++)
		{
			if (preg_match_all('/{\"repo\":(\d+)}/', $content[$i]['introtext'], $matches, PREG_OFFSET_CAPTURE))
			{
				for ($j = 0; $j < sizeof($matches[0]); $j++)
				{
					$id =  intval($matches[1][$j][0]) + $this->getDocumentsOffset();
					$query = $db->getQuery(true);
					$query->select($db->quoteName('title'));
					$query->from($db->quoteName('#__edocman_documents'));
					$query->where($db->quoteName('id') . '=' . $db->quote($id));
					$db->setQuery($query);
					$title = $db->loadResult();
					$content[$i]['introtext'] = str_replace($matches[0][$j][0], '<a href="index.php?option=com_edocman&amp;view=document&amp;id=' . $id . '">' . $title . '</a>', $content[$i]['introtext']);
				}
			}

			if (preg_match_all('/{\"repo\":(\d+)}/', $content[$i]['fulltext'], $matches, PREG_OFFSET_CAPTURE))
			{
				for ($j = 0; $j < sizeof($matches[0]); $j++)
				{
					$id =  intval($matches[1][$j][0]) + $this->getDocumentsOffset();
					$query = $db->getQuery(true);
					$query->select($db->quoteName('title'));
					$query->from($db->quoteName('#__edocman_documents'));
					$query->where($db->quoteName('id') . '=' . $db->quote($id));
					$db->setQuery($query);
					$title = $db->loadResult();
					$content[$i]['fulltext'] = str_replace($matches[0][$j][0], '<a href="index.php?option=com_edocman&amp;view=document&amp;id=' . $id . '">' . $title . '</a>', $content[$i]['fulltext']);
				}
			}

			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__content'));
			$query->set($db->quoteName('introtext').'='.$db->quote($content[$i]['introtext']));
			$query->set($db->quoteName('fulltext').'='.$db->quote($content[$i]['fulltext']));
			$query->where($db->quoteName('id').'='.$db->quote($content[$i]['id']));
			$db->setQuery($query);
			$db->execute();
		}

		echo "Update of JoomlaContent successful!<br>";
	}

	public function updateJoomlaAssets()
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('max(id)');
		$query->from('#__assets');
		$db->setQuery($query);
		$offset = $db->loadResult() + 1;

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('name').'LIKE'.$db->quote('com_thm_repo.%'));
		$db->setQuery($query);
		$assets = $db->loadAssocList();

		for ($i = 0; $i < sizeof($assets); $i++)
		{
			$id = intval(substr(strrchr($assets[$i]['name'], '.'), 1));
			if (strpos($assets[$i]['name'], 'com_thm_repo.folder.') !== FALSE)
			{
				$assets[$i]['id'] = $offset + $i;
				$assets[$i]['name'] = 'com_edocman.category.'.($id + $this->getCategoriesOffset());

				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__edocman_categories'));
				$query->set($db->quoteName('asset_id').'='.$db->quote($assets[$i]['id']));
				$query->where($db->quoteName('id').'='.$db->quote($id + $this->getCategoriesOffset()));
				$db->setQuery($query);
				$db->execute();
			}
			else if (strpos($assets[$i]['name'], 'com_thm_repo.entity.') !== FALSE)
			{
				$assets[$i]['id'] = $offset + $i;
				$assets[$i]['name'] = 'com_edocman.document.'.($id + $this->getDocumentsOffset());

				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__edocman_documents'));
				$query->set($db->quoteName('asset_id').'='.$db->quote($assets[$i]['id']));
				$query->where($db->quoteName('id').'='.$db->quote($id + $this->getDocumentsOffset()));
				$db->setQuery($query);
				$db->execute();
			}
			$rules = json_decode($assets[$i]['rules'], true);
			foreach($rules as $key => $value){
				foreach($value as $k => $v){
					if($v == 0)
					{
						unset($rules[$key][$k]);
					}
				}
			}
			$assets[$i]['rules'] = json_encode($rules);
		}

		for ($i = 0; $i < sizeof($assets); $i++)
		{
			$id = intval(substr(strrchr($assets[$i]['name'], '.'), 1));
			if (strpos($assets[$i]['name'], 'com_edocman.category.') !== FALSE)
			{
				$query = $db->getQuery(true);
				$query->select(array($db->quoteName('parent_id'), $db->quoteName('title')));
				$query->from($db->quoteName('#__edocman_categories'));
				$query->where($db->quoteName('id').'='.$db->quote($id));
				$db->setQuery($query);
				$result = $db->loadAssoc();
				$assets[$i]['title'] = $result['title'];

				if ($assets[$i]['title'] == 'root')
				{
					$query = $db->getQuery(true);
					$query->select($db->quoteName('id'));
					$query->from($db->quoteName('#__assets'));
					$query->where($db->quoteName('name') . '=' . $db->quote('com_edocman'));
					$db->setQuery($query);
					$edocman_asset_id = $db->loadResult();
					$assets[$i]['parent_id'] = $edocman_asset_id;
				}

				else
				{
					$query = $db->getQuery(true);
					$query->select($db->quoteName('id'));
					$query->from($db->quoteName('#__assets'));
					$query->where($db->quoteName('name').'='.$db->quote('com_edocman.category.' . $result['parent_id']));
					$db->setQuery($query);
					$assets[$i]['parent_id'] = $db->loadResult();
				}
			}
			else if (strpos($assets[$i]['name'], 'com_edocman.document.') !== FALSE) {
				$query = $db->getQuery(true);
				$query->select($db->quoteName('title'));
				$query->from($db->quoteName('#__edocman_documents'));
				$query->where($db->quoteName('id') . '=' . $db->quote($id));
				$db->setQuery($query);
				$result = $db->loadResult();
				$assets[$i]['title'] = $result;

				$query = $db->getQuery(true);
				$query->select($db->quoteName('category_id'));
				$query->from($db->quoteName('#__edocman_document_category'));
				$query->where($db->quoteName('document_id') . '=' . $db->quote($id), 'AND');
				$query->where($db->quoteName('is_main_category') . '=' . $db->quote(1));
				$db->setQuery($query);
				$result = $db->loadResult();

				$query = $db->getQuery(true);
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__assets'));
				$query->where($db->quoteName('name') . '=' . $db->quote('com_edocman.document.' . $result));
				$db->setQuery($query);
				$assets[$i]['parent_id'] = $db->loadResult();
			}

			if ($assets[$i]['parent_id'] != 0)
			{
				$query   = $db->getQuery(true);
				$columns = array('id', 'parent_id', 'name', 'title', 'rules');

				$values = array($db->quote($assets[$i]['id']), $db->quote($assets[$i]['parent_id']),
					$db->quote($assets[$i]['name']), $db->quote($assets[$i]['title']),
					$db->quote($assets[$i]['rules']));

				$query
					->insert($db->quoteName('#__assets'))
					->columns($db->quoteName($columns))
					->values(implode(',', $values));

				$db->setQuery($query);
				$db->execute();
			}
		}

		if (JTable::getInstance('asset')->rebuild($edocman_asset_id))
		{
			echo "Update of JoomlaAssets successful!<br>";
		}
		else
		{
			echo "Update of JoomlaAssets unsuccessful!<br>";
		}
	}

	/**
	 * Method to remove special Characters from String
	 * @param $str
	 * @return $str
	 */
	public function removeSpecialChars($str) {
		$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß");
		$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss");
		$str = str_replace($search, $replace, $str);
		$str = preg_replace('/[^A-Za-z0-9-]/', '-', $str);
		$str = preg_replace('/^-+|-+$/', '', $str);
		return preg_replace('/-+/', '-', $str);
	}
}
