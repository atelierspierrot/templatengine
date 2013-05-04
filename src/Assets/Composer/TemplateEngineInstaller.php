<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace Assets\Composer;

use Composer\Composer,
    Composer\IO\IOInterface,
    Composer\Package\PackageInterface,
    Composer\Script\Event;

use Assets\Composer\TemplateEngineAutoloadGenerator;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class TemplateEngineInstaller
{

    /**
     */
    public static function postAutoloadDump(Event $event)
    {
        $_this = new TemplateEngineAutoloadGenerator($event->getComposer()->getPackage());
    }

}

// Endfile