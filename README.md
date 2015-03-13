Template Engine
===============

[![documentation](http://img.ateliers-pierrot-static.fr/read-the-doc.svg)](http://docs.ateliers-pierrot.fr/templatengine/)

A PHP package to build HTML5 views (based on [HTML5 Boilerplate](http://html5boilerplate.com/)
layouts and the [Composer Assets Plugin](https://github.com/atelierspierrot/assets-manager)).


What is this package ?
----------------------

This package defines a simple Template Engine to manage PHP view files, some HTML files including
PHP scripts to build views passing them parameters and objects and some tools to manage global
layouts to embed these views. As it is based on the [Composer Assets Plugin](https://github.com/atelierspierrot/assets-manager),
the Template Engine also facilitate assets files usage and URL.

**This package is not yet really documented. Please take a look at the code and the [PHP
Documentation](http://docs.ateliers-pierrot.fr/templatengine/) for more information. A
complete demonstration is available in the package itself (PHP server is required).**


Installation
------------

This package requires that you use [Composer](http://getcomposer.org/) to manage your dependencies.

To use the package, you need to add it to your requirements in your `composer.json` AND to
call its `TemplateEngineInstaller::postAutoloadDump` for the `post-autoload-dump` Composer's event:

    "require": {
        ...
        "atelierspierrot/templatengine": "1.*"
    },
    ...
    "scripts": {
        ...
        "post-autoload-dump": "Assets\\Composer\\TemplateEngineInstaller::postAutoloadDump"
    }

The `Assets` and `TemplateEngine` namespaces will be automatically added to the project
Composer autoloader.


Composer Extra settings
-----------------------

Using the `Template Engine`, you can define some extra features in your package's `composer.json`
to specify some paths and presets used by the engine.

### Example

Below is the example of the package default configuration values added to the default
[Composer Assets Plugin](https://github.com/atelierspierrot/assets-manager) configuration
values:

    "extra": {
        "views": [ "www", "www/html5boilerplate" ],
        "views-functions": "src/TemplateEngine/views_functions.php",
        "cache-dir": "tmp",
        "cache-assets-dir": "tmp_assets",
        "layouts": "www/html5boilerplate"
    }

### `views`: array|string

This defines one or more relative paths from your package root directory where to find your
view files. These directories must exist and defaults to `www/` (the default assets directory).

### `views-functions`: array|string

The view functions are loaded before any view rendering and may define some useful methods
to use in your view files. See the [Views Functions](#views-functions) section below for 
more infos.

This entry defines one or more relative file paths from your package root directory where 
to find your view functions. These files must exist.

### `cache-dir`: string

This defines the relative path from your `assets` directory to generate temporary files. If
it does not exist, the directory will be created. This setting defaults to `tmp/`.

### `cache-assets-dir`: string

This defines the relative path from your `assets` directory to generate temporary assets 
files such as merged or minifies CSS or JS. If it does not exist, the directory will be
created. This setting defaults to `tmp_assets/`.

### `layouts`: array|string

This defines one or more relative paths from your package root directory where to find your
layout files, the global templates to use as other partial views wrapper. These directories
must exist.


Views functions
---------------

Any package defining the extra `views-functions` setting can define a set of standalone
methods to use in view files ; all functions files of the declared `views-functions` are loaded
for all views, so all of these methods may be accessible in any view file.


Author & License
----------------

>    Template Engine

>    http://github.com/atelierspierrot/templatengine

>    Copyright (c) 2013-2015 Pierre Cassat and contributors

>    Licensed under the Apache 2 license.

>    http://www.apache.org/licenses/LICENSE-2.0

>    ----

>    Les Ateliers Pierrot - Paris, France

>    <http://www.ateliers-pierrot.fr/> - <contact@ateliers-pierrot.fr>
