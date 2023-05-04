<?php
/**
 * Plugin Name: Contact Form 7 - ChatGPT Integration
 * Plugin URI: https://github.com/yourname/cf7-chatgpt-integration
 * Description: This plugin integrates Contact Form 7 with ChatGPT for AI-powered auto-replies.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: cf7-chatgpt-integration
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Activation hook.
 */
function cf7_chatgpt_activation() {
	// Activation logic here.
}
register_activation_hook( __FILE__, 'cf7_chatgpt_activation' );

/**
 * Deactivation hook.
 */
function cf7_chatgpt_deactivation() {
	// Deactivation logic here.
}
register_deactivation_hook( __FILE__, 'cf7_chatgpt_deactivation' );

// Load required files.
require_once plugin_dir_path(__FILE__) . 'includes/class-cf7-chatgpt-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cf7-chatgpt-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cf7-chatgpt-templates.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cf7-chatgpt-conditional-logic.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cf7-chatgpt-integration.php'; // Add this line.
require_once plugin_dir_path(__FILE__) . 'includes/class-cf7-chatgpt-analytics.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cf7-chatgpt-reports.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cf7-chatgpt-security.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cf7-chatgpt-admin.php';

// Instantiate classes.
$cf7_chatgpt_security = new CF7_ChatGPT_Security();
$cf7_chatgpt_settings = new CF7_ChatGPT_Settings();
$cf7_chatgpt_api = new CF7_ChatGPT_API($api_key, $api_endpoint, $cf7_chatgpt_security);
$cf7_chatgpt_templates = new CF7_ChatGPT_Templates();
$cf7_chatgpt_conditional_logic = new CF7_ChatGPT_Conditional_Logic();
$cf7_chatgpt_analytics = new CF7_ChatGPT_Analytics();
$cf7_chatgpt_reports = new CF7_ChatGPT_Reports();
$cf7_chatgpt_admin = new CF7_ChatGPT_Admin();

