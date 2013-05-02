Template Engine - Composer Extra settings
=========================================

Using the `Template Engine`, you can define some extra features in your package's `composer.json`
to specify some paths and presets used by the engine.

## Example

Below is the example of the package itself:

    "extra": {
        // these entries are used by the classic Composer extension
        "assets": "www",
        "assets_vendor": "vendor",
        "document_root": "www",
        "assets_presets": {
            "jquery.tablesorter": {
                "css": "vendor_assets/blue/style.css",
                "jsfiles_footer": [ "vendor_assets/jquery.metadata.js", "min:vendor/jquery.tablesorter.min.js" ]
            },
            "jquery.highlight": {
                "css": "vendor_assets/jquery.highlight.css",
                "jsfiles_footer": "vendor_assets/jquery.highlight.js"
            }
        },
        // these entries are used by the Template Engine
        "views": [ "www", "www/html5boilerplate" ],
        "views_functions": "src/TemplateEngine/views_functions.php",
        "cache-dir": "tmp",
        "cache-assets-dir": "tmp_assets",
        "layouts": "www/html5boilerplate"
    }

## Configuration entries

All the paths are relative to the package `vendor` installation directory or its `assets`
installation directory.

### `assets`: string

This defines the relative path of your assets in the package. This directory must exist
and must be unique (*its value must be a string*).

### `assets_vendor`: string

This defines the relative path of your packages'assets in the `assets` directory above.
This directory will be created if it doesn't exist and must be unique (*its value must be a string*).

### `document_root`: string - only for **root** package

This defines the relative path used to build the URLs to include your package's assets ; 
this must be the base directory of your HTTP root.
This directory must exist and is unique (*its value must be a string*). It is only considered
for the root package.

## `views`: array|string

This defines one or more relative paths from your package root directory where to find your
view files. These directories must exist.

## `views_functions`: array|string

The view functions are loaded before any view rendering and may define some useful methods
to use in your view files. See the [Views Functions](#views_fcts) section below for more infos.

This defines one or more relative paths from your package root directory where to find your
view functions. These directories must exist.

## `assets_presets`: array of arrays

An assets preset is a predefined set of CSS or Javascript files to include to use a specific
tool (*such as a jQuery plugin for instance*). Each preset can be used in a view file writing:

    _use( preset name );

A preset is defined as a `key => array` pair where the `key` is the preset name (*the name
you will call using the `_use()` method*) and the corresponding array defines the required
assets files to be included in the whole template.

### `css`: string|array

The CSS entry of a preset is a list of one or more CSS files to include. This must be a list
of existing files and file paths must be relative to the package `assets` directory.

### `jsfiles_header` and `jsfiles_footer`: string|array

These Javascript entries defines respectively some scripts to be included in the page header
or footer. This must be a list of existing files and file paths must be relative to the
package `assets` directory.

### Specific rules

As the template engine embeds a `Minifier` for assets, you may inform it if one of your
preset files is already minified or packed. To do so, you can prefix the file path with
`min:` or `pack:`. For instance:

    "jsfiles_footer": [ "vendor/jquery.metadata.js", "min:vendor/jquery.tablesorter.min.js" ]

This way, your file will not be minified if you use this feature.
