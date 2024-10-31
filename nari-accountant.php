<?php
/*
Plugin Name: Nari Accountant
Plugin URI: http://accountant.nabavi.nl/
Description: A Bookkeeper and an accountant inside of your Wordpress to Managing your bank accounts, Adding expenses and deposit, Get a wide variety of financial reports, and a lot more.
Version: 1.0.12
Author: Seyed Shahrokh Nabavi
Author URI: http://nabavi.nl/
Text Domain: nari100
Domain Path: /languages
License: GPL2
PHP: 5.3.0
*/

global $wpdb;

// Definitions
define('NARI_ACCOUNTANT_VERSION', '1.0.11');
define('NARI_APP_CODE', '100');
define('NARI_DB', $wpdb->prefix . 'nari' . NARI_APP_CODE . '_');
define('NARI_ACCOUNTANT_URL_PLUGIN', plugin_dir_url(__FILE__));
define('NARI_ACCOUNTANT_DIR_PLUGIN', dirname( __FILE__ ) . DIRECTORY_SEPARATOR);


// Includes
include_once NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/helper.php';
include_once NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/install.php';
include_once NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/upgrade.php';
include_once NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/functions.php';

// Language
load_plugin_textdomain('nari100', false, dirname( plugin_basename(__FILE__) ) . '/languages');

// Register Activation/Deactivation Hooks
register_activation_hook( __FILE__, 'nari100_activate' );
register_deactivation_hook( __FILE__, 'nari100_deactivate' );
register_uninstall_hook( __FILE__, 'nari100_uninstall' );

// Add SubPage for Ordering function
add_action( 'admin_menu', 'nari100_register_menu' );

// Ajax registration
add_action( 'wp_ajax_nari100', 'nari100_ajax' );

// Load script for all admin panel
add_action( 'admin_enqueue_scripts', 'nari100_admin_scripts' );

// Check if there is notifications
add_action( 'admin_notices', 'nari100_notification');


// Dynamic Text Of Language Package
__('Accounts', 'nari100');
__('Definitions', 'nari100');
__('Transactions', 'nari100');
__('Settings', 'nari100');
__('Reports', 'nari100');
__('Expense Categories', 'nari100');
__('Income Categories', 'nari100');
__('Expense', 'nari100');
__('Income', 'nari100');


function on_paid( $ID, $post ) {
//function on_paid( $ID ) {
    $rec = get_post_meta ( $ID );

    $myfile = fopen("d:/newfile.txt", "w") or die("Unable to open file!");

    $txt = "[System - Inserted by WP-Invoice] Record ID = " . $ID . "\r\n";
    $txt .= "invoice_id = " . $rec['invoice_id'][0] . "\r\n";
    if( $rec['total_payments'][0] === '0' ){
        $payment = $rec['subtotal'][0] + round( $rec['total_tax'][0], 2) - $rec['total_discount'][0];
    } else if ( $_POST['event_type'] === 'add_payment' ) {
        $payment = $rec['total_payments'][0] + floatval($_POST['event_amount']);
    }
    $txt .= "Payment = " . $payment . "\r\n";

    fwrite($myfile, $txt);
    fclose($myfile);
}
//add_action(  'paid_wpi_object',  'on_paid', 10, 2 );