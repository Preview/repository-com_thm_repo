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

    <div class="menuitem">
        <form action="index.php?option=com_thm_repo&task=zipImportAction" method="post" enctype="multipart/form-data" id="import_thm_repo_form" name="import_thm_repo_form">
            <input name="import_thm_repo_form_file" id="import_thm_repo_form_file" type="file" size="5000" maxlength="100000" accept="application/zip" class="hide" />
            <span id="import_thm_repo_form_button" onClick="document.getElementById('import_thm_repo_form_file').click();"><?php echo JText::_('COM_THM_REPO_IMPORTMANAGER');?></span>
        </form>
    </div>
    
</div>
    
<style>
    form#import_thm_repo_form{margin:3px 10px 3px 3px;}
    span#import_thm_repo_form_button{width:106px;height:89px;line-height:89px;border:1px solid #3364a3;color:#000000;background:#719ece;font-size:22px;text-align:center;cursor:pointer;display:block;}
    span#import_thm_repo_form_button:hover{background:#9bc8f8;}
</style>
<script type="text/javascript">
    document.getElementById('import_thm_repo_form_file').addEventListener('change', function(e) {
        if (this.value) {
            if (confirm("<?php echo JText::_('COM_THM_REPO_IMPORTMANAGER_CONFIRM_MESSAGE'); ?>".replace('%filename%', this.value))) {
                this.form.submit();
            }
            else {
                this.value = '';
            }
        }
    });
</script>