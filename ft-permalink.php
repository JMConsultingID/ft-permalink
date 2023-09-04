<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://fundedtrading.com
 * @since             1.0.0
 * @package           Ft_Permalink
 *
 * @wordpress-plugin
 * Plugin Name:       FT Propfirm - Permalink
 * Plugin URI:        https://fundedtrading.com
 * Description:       This Plugin for Regenerate Permalink Custom Post Type
 * Version:           1.0.0
 * Author:            Ardika JM Consulting
 * Author URI:        https://fundedtrading.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ft-permalink
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FT_PERMALINK_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ft-permalink-activator.php
 */
function activate_ft_permalink() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ft-permalink-activator.php';
	Ft_Permalink_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ft-permalink-deactivator.php
 */
function deactivate_ft_permalink() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ft-permalink-deactivator.php';
	Ft_Permalink_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ft_permalink' );
register_deactivation_hook( __FILE__, 'deactivate_ft_permalink' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ft-permalink.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-ft-permalink-functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ft_permalink() {

	$plugin = new Ft_Permalink();
	$plugin->run();

}
run_ft_permalink();
