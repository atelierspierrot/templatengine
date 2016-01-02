<?php
/**
 * This file is part of the TemplateEngine package.
 *
 * Copyright (c) 2013-2016 Pierre Cassat <me@e-piwi.fr> and contributors
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * The source code of this package is available online at 
 * <http://github.com/atelierspierrot/templatengine>.
 */

namespace Assets\Composer;

use \Composer\Composer;
use \Composer\IO\IOInterface;
use \Composer\Package\PackageInterface;
use \Composer\Script\Event;
use \Assets\Composer\TemplateEngineAutoloadGenerator;

/**
 * @author  piwi <me@e-piwi.fr>
 */
class TemplateEngineInstaller
{

    /**
     */
    public static function postAutoloadDump(Event $event)
    {
        $_this = new TemplateEngineAutoloadGenerator(
            $event->getComposer()->getPackage(),
            $event->getComposer()
        );
    }
}
