<?php

/**
 * @category    Joomla component
 * @package     THM_Repo
 * @subpackage  com_thm_repo.site
 * @author      Andrej Sajenko <Andrej.Sajenko@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
//defined('_JEXEC') or die;

if (!defined('DS'))
{
    define('DS', DIRECTORY_SEPARATOR);
}

jimport('thm_repo.core.All');


$uId = (int) JFactory::getUser()->id;

$user = empty($uId) ? null : new THMUser($uId);
$filter = new THMAccessFilter($user);
$jInput = JFactory::getApplication()->input;

try
{
    $emptyDownloadId = -1;
    $downloadId = $jInput->getInt('downloadId', $emptyDownloadId);

    if ($downloadId === $emptyDownloadId)
        throw new Exception("Missing download id!", 400 /* Bad request */);

    $file = THMFile::get($downloadId);

    if (!$filter->accept($file))
    {
        throw new Exception("You have no permission to download this file!", 401 /* Unauthorized */);
    }

    $file->download();
}
catch (Exception $ex)
{
    http_response_code($ex->getCode());
    echo $ex->getMessage();
}