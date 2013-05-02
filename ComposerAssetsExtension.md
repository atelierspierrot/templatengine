Template Engine - Composer Assets Extension
===========================================

This document describes the [Composer](https://getcomposer.org/) extension to manage assets
used by our [Template Engine](https://github.com/atelierspierrot/templatengine) package.

The plugin can be used separately as a stand-alone Composer extension.


## How it works?

The goal of this extension is to manage some packages of assets (*javascript libraries, CSS
frameworks or views*) just like Composer standardly manages PHP packages. Assets packages
are downloaded and stored in a specific `vendor` directory and an internal system allows
you to retrieve and load the assets packages files just as you do with PHP classes (*a kind
of assets autoloader*).

Just like any standard Composer feature, all names or configuration variables are configurable.

## Assets `vendor`

Let's say your project is constructed on the following structure, where `src/` contains
your PHP sources and `www/` is your web document root:

    | composer.json
    | src/
    | www/

By default, Composer will install your dependencies in a `vendor/` directory and build a
PHP autoloader:

    | composer.json
    | src/
    | vendor/
    | ------- autoload.php
    | www/

The Composer extension will copy the assets of your dependencies in a `www/vendor/` directory
and build a JSON map in the original `vendor/`:

    | composer.json
    | src/
    | vendor/
    | ------- autoload.php
    | ------- assets.json
    | www/
    | ---- vendor/

### How to inform the extension about your package assets

The extension will consider any package with an `extra` setting called `assets` as 
