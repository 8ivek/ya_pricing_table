<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://bivek.ca
 * @since             1.0.0
 * @package           Yapt
 *
 * @wordpress-plugin
 * Plugin Name:       YA Pricing Table
 * Plugin URI:        https://bivek.ca/yapt
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            bvk
 * Author URI:        https://bivek.ca
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       yapt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('YAPT_VERSION', '1.0.0');

$url = plugin_dir_url(__FILE__);
define('YAPT_PLUGIN_URL', $url);

$dir_path = plugin_dir_path(__FILE__);
define('YAPT_PLUGIN_DIR_PATH', $dir_path);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-yapt-activator.php
 */
function activate_yapt()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-yapt-activator.php';
    Yapt_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-yapt-deactivator.php
 */
function deactivate_yapt()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-yapt-deactivator.php';
    Yapt_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_yapt');
register_deactivation_hook(__FILE__, 'deactivate_yapt');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-yapt.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_yapt()
{
    $plugin = new Yapt();
    $plugin->run();
}

run_yapt();
