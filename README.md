Template Engine
===============

A PHP package to build HTML5 views (based on HTML5 Boilerplate layouts).


## Assets manager

The `TemplateEngine\Assets` namespace defines some classes to manage a set of assets, views
and assets presets to use in your template engine views.

The global `Loader` class is based on three paths:

- `base_dir`: the package root directory (must be the directory containing the `composer.json` file)
- `assets_dir`: the package asssets directory related to `base_dir`
- `document_root`: the path in the filesystem of the web assets root directory ; this is used
to build all related assets paths to use in HTTP.

For these three paths, their defaults values are defined on a default package structure:

    package_name/
    |----------- src/
    |----------- www/

    $loader->base_dir = realpath(package_name)
    $loader->assets_dir = www
    $loader->document_root = www or the server DOCUMENT_ROOT

NOTE - These paths are stored in the object without the trailing slash.

## Author & License

>    Patterns

>    https://github.com/atelierspierrot/patterns

>    Copyleft 2013, Pierre Cassat and contributors

>    Licensed under the GPL Version 3 license.

>    http://opensource.org/licenses/GPL-3.0

>    ----

>    Les Ateliers Pierrot - Paris, France

>    <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
