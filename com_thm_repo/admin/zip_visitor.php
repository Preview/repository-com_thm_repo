<?php
include( JPATH_COMPONENT_ADMINISTRATOR . '/visitor.php');

/**
 * Class ZipVisitor zips all Folders and Entities it
 * encounters in zip that is created in the temp directory.
 *
 * The path of the zip is saved in $file.
 */
class ZipVisitor implements TreeVisitor
{
    /**
     * @var array stack to save current directory.
     */
    private $path = [];
    private $zip;
    public $file;

    public function __construct($json)
    {
        $this->file = tempnam(sys_get_temp_dir(), 'Tux');
        $this->zip = new ZipArchive();

        $this->zip->open($this->file);

        $this->zip->addFromString("Metadata.json", $json);
    }

    /**
     * Adds the current folder to the stack of folders we are currently in
     * and creates a empty folder in the zip.
     *
     * @param $folder current Folder
     */
    public function enteringFolder($folder)
    {
        $this->path[] = $folder->getName();
        $this->zip->addEmptyDir($this->path());
    }

    /**
     * Removes the last folder from the stack.
     *
     * @param $folder current Folder
     */
    public function leavingFolder($folder)
    {
        array_pop($this->path);
    }

    /**
     * Adds the entity to the zip.
     *
     * @param $entity
     */
    public function visitEntity($entity)
    {
        if ($entity instanceof THMWebLink)
        {
            $content = "[InternetShortcut]\nURL=" . $entity->getLink();
            $this->zip->addFromString($this->path() . "/" . $entity->getName() . ".url", $content);
        }
        else if ($entity instanceof THMFile)
        {
            $this->zip->addFile(JPATH_ROOT . $entity->getPath(), $this->path() . "/" . $entity->getName() . "." . pathinfo(JPATH_ROOT . $entity->getPath(), PATHINFO_EXTENSION));
        }
    }

    /**
     * Closes the Zip.
     */
    public function done()
    {
        $this->zip->close();
    }

    /**
     * Builds our current path from the $path stack.
     *
     * @return string current path.
     */
    private function path()
    {
        return join("/", $this->path);
    }
}
