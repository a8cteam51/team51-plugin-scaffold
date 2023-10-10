# Team 51 Plugin Scaffold

Welcome to the Team 51 Plugin Scaffold, a standardized starting point for creating new WordPress plugins for Team 51. This repository contains the necessary files and structure to ensure a consistent approach when developing new plugins.

## Getting Started

To begin, run the command `team51 create-repository --repo-type=plugin`

If you don't want to create a repository for your plugin, another option is to clone or download this repository. Rename the folder and the main PHP file with your desired plugin name. Be sure to follow the naming convention: plugin-name for the folder and plugin-name.php for the main PHP file.

## Configuration

You'll need to update the following fields in the main PHP file's header:

- Plugin Name: The name of your plugin
- Plugin URI: The URL of the plugin's repository
- Description: A brief description of the plugin's functionality.

## Folder Structure

This scaffold has the following folder structure:

```
plugin-name/
├── assets/
│   ├── css/
│   │   ├── build/
│   │   └── src/
│   ├── js/
│   │   ├── build/
│   │   └── src/
│   └── images/
├── blocks/
│   ├── build/
│   └── src/
├── includes/
├── languages/
├── models/
├── src/
│   ├ ...
│   └── Integrations/
├── templates/
│   ├ ...
│   └── admin/
└── plugin-name.php
```

- assets: A folder to store all static assets such as styles, scripts, and images.
- blocks: A folder for storing Gutenberg block files, if the plugin uses custom blocks.
- includes: Contains any PHP files with additional functionality for the plugin. Mostly useful for helper functions.
- languages: Contains the translation files for your plugin.
- models: Contains PHP classes or data models that represent the plugin's data structures. As an example, think of WooCommerce's `WC_Order` class.
- src: A folder for organizing the plugin's main PHP classes or code components, such as integrations with other plugins or services. These classes should be organized into subfolders following the [PSR-4](https://www.php-fig.org/psr/psr-4/) convention. `Composer` will handle the autoloading for these classes.
- templates: Contains any PHP template files used for rendering HTML output. Admin templates should generally be in their own folder separated from front-end templates.
- plugin-name.php: The main PHP file containing the plugin header and bootstraping functionality.

## Development

Develop your plugin by adding the necessary functionality by creating new files within the includes folder. Remember to enqueue your styles and scripts within the assets folder.

Follow the WordPress Coding Standards for PHP, CSS, and JavaScript when writing your code. You can read more about linting and formatting your code in the [Team51 Project Scaffold](https://github.com/a8cteam51/team51-project-scaffold#code-style--quality).

## PHP Dependencies Scoping

If you're using 3rd-party libraries, you should scope them to the plugin's namespace to avoid conflicts with other plugins.

This scaffold provides a small framework+example for doing that, but you'll need to create a configuration file for each library you want to use/scope. Follow these steps:

* Add your library to Composer in its `require-dev` block.
* Create a configuration file for your library in the `.php-scoper` folder (see the examples for `psr/log` and `psr/container` provided in the scaffold).
* Create a script in the `composer.json` file that will run the PHP Scoper tool. See the `composer.json` file in the scaffold for an example for the two PSR libraries mentioned above.
* Include your script inside the `scope-php-dependencies` script in the `composer.json` file.
* Whenever you use the library, make sure you reference the scoped version of the library. For example, if you're using the `psr/log` library, you'll need to reference it as `\WPCOMSpecialProjects\PluginName\Psr\Log` instead of `\Psr\Log`.

## Documentation

As you develop your plugin, update the README.md file with detailed information about your plugin's features, usage, installation, and any other pertinent information.

## Testing

If your plugin is WooCommerce specific, it should be tested with the Storefront theme and latest default theme. If it's a general plugin, it should be tested with the latest default theme as well as Twenty Twenty-One (a non-FSE theme).
