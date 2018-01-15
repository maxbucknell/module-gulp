<?php
namespace MaxBucknell\Prefab\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class Filesystem
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var FlysystemFactory
     */
    private $flysystemFactory;

    /**
     * @var \League\Flysystem\Filesystem
     */
    private $filesystem;

    public function __construct(
        DirectoryList $directoryList,
        FlysystemFactory $flysystemFactory
    ) {
        $this->directoryList = $directoryList;
        $this->flysystemFactory = $flysystemFactory;
    }

    public function getAbsoluteLocation()
    {
        $root = $this->getRootDirectory();
        $dir = $this->getLocation();

        return "{$root}/{$dir}";
    }

    public function getLocation()
    {
        $dir= $this->directoryList->getPath(DirectoryList::STATIC_VIEW);
        $path = $this->getRelativeDirectory($dir);

        return $path;
    }

    /**
     * @return string
     */
    public function getRootDirectory()
    {
        return $this->directoryList->getPath(DirectoryList::ROOT);
    }

    public function getFilesystem()
    {
        if (!$this->filesystem) {
            $root = $this->getRootDirectory();
            $this->filesystem = $this->flysystemFactory->create($root);
        }

        return $this->filesystem;
    }

    public function getRelativeDirectory($directory)
    {
        $root = $this->getRootDirectory();

        if (strpos($directory, $root) === 0) {
            return str_replace($root, '', $directory);
        } else {
            return $directory;
        }
    }
}