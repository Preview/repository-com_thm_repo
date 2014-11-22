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

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_thm_repo/css/start/start.css");

?>
<div class="description1">Main Menu</div>

<div id="gimenu1">

	<!-- Manage Entries -->
	<hr />
	<div class="menuitem">
		<div class="icon" onclick="location.href='index.php?option=com_thm_repo&view=folders';">
			<div class="picture2">
				<img src="components/com_thm_repo/img/icon-48-folders.png" alt="Folder Manager" />
			</div>
			<div class="description2"><?php echo JText::_('COM_THM_REPO_FOLDERMANAGER');?></div>
		</div>
	</div>

	<div class="menuitem">
		<div class="icon" onclick="location.href='index.php?option=com_thm_repo&view=files';">
			<div class="picture2">
				<img src="components/com_thm_repo/img/icon-48-files.png" alt="File Manager" />
			</div>
			<div class="description2"><?php echo JText::_('COM_THM_REPO_FILEMANAGER');?></div>
		</div>
	</div>

	<div class="menuitem">
		<div class="icon" onclick="location.href='index.php?option=com_thm_repo&view=links';">
			<div class="picture2">
				<img src="components/com_thm_repo/img/icon-48-links.png" alt="Link Manager" />
			</div>

			<div class="description2"><?php echo JText::_('COM_THM_REPO_LINKMANAGER');?></div>
		</div>
	</div>

    <div class="menuitem">
        <div class="icon" onclick="location.href='index.php?option=com_thm_repo&task=doExport';">
            <div class="picture2">
                <img src="components/com_thm_repo/img/download.png" width="60" height="40" align="center" alt="Export" />
            </div>

            <div class="description2"><?php echo JText::_('COM_THM_REPO_EXPORTMANAGER');?></div>
        </div>
    </div>

</div>
