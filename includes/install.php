<?php

/*
 * Runs on plugin activation
 *
 * Sets up initial plugin option contents.
 */
function nari100_activate($is_forced = null){
    $pages = nari100_listdir(NARI_ACCOUNTANT_DIR_PLUGIN.'includes/classes/db/');
    Nari100Settings::add( $pages );

    /*
     * Create DB Tables
     */
    global $wpdb;
    foreach(  $pages as $db ) {
        $class = str_replace('.php','',$db);
        $sTable = NARI_DB . $class;

        $sSQL = call_user_func('Nari100' . ucfirst($class) . '::CreateDB');
        $sSQL = "CREATE TABLE IF NOT EXISTS `{$sTable}` (" . $sSQL . "
            PRIMARY KEY (`" . $class . "_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $wpdb->query($sSQL);
    }


    if ($is_forced != 'forced activation'){
        flush_rewrite_rules();
    }
}


/*
 * Runs on plugin deactivation
 *
 * Nothing but flushing rewrite rules right now
 */
function nari100_deactivate(){
    flush_rewrite_rules();
}


/*
 * Runs on plugin UNINSTALL
 *
 * Remove our options from the database.
 */
function nari100_uninstall(){
    Nari100Settings::delete();

    /*
     * Delete DB Tables
     */
    global $wpdb;
    foreach( nari100_listdir(NARI_ACCOUNTANT_DIR_PLUGIN.'includes/classes/db/') as $db ) {
        $class = str_replace('.php', '', $db);
        $sTable = NARI_DB . $class;
        $sSQL = "DROP TABLE IF EXISTS `{$sTable}`;";
        $wpdb->query($sSQL);
    }
}