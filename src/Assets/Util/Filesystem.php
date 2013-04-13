<?php

namespace Assets\Util;

use Composer\Util\Filesystem as OriginalFilesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 */
class Filesystem extends OriginalFilesystem
{

    /**
     * Copy is a non-atomic version of {@link rename}.
     *
     * Some systems can't rename and also don't have proc_open,
     * which requires this solution.
     *
     * @param string $source
     * @param string $target
     */
    public function copy($source, $target)
    {
        $it = new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::SELF_FIRST);

        if (!file_exists($target)) {
            mkdir($target, 0777, true);
        }

        foreach ($ri as $file) {
            $targetPath = $target . DIRECTORY_SEPARATOR . $ri->getSubPathName();
            if ($file->isDir()) {
                if (!file_exists($targetPath)) {
                    mkdir($targetPath);
                }
            } else {
                copy($file->getPathname(), $targetPath);
            }
        }
    }

}
