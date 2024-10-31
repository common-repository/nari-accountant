<?php

/*
 * Class auto loader
 */
spl_autoload_register( 'nari100_autoloader' );
function nari100_autoloader( $class_name ) {
    if ( false !== strpos( $class_name, 'Nari' . NARI_APP_CODE ) ) {
        $classes_dir = NARI_ACCOUNTANT_DIR_PLUGIN . 'includes' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
        $class_file = strtolower(str_replace( 'Nari' . NARI_APP_CODE, '', $class_name ) . '.php');
        $class = $classes_dir . $class_file;

        if( file_exists($class) )
            require_once $class;
        else {
            $class = $classes_dir . 'db' . DIRECTORY_SEPARATOR . $class_file;
            if (file_exists($class))
                require_once $class;
        }
    }
}

/*
 * List of files and directory
 */
function nari100_listdir($dir='.') {
    if (!is_dir($dir)) {
        return false;
    }

    $files = array();
    if ($handle = opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $files[] = $entry;
            }
        }
        closedir($handle);
    }
    return $files;
}

/*
 *
 */
function nari100_price( $price, $currency = true ){
    $dec = get_option( 'nari100_dec_point');
    $thousands = get_option( 'nari100_thousands_sep');
    $currency = $currency ? get_option( 'nari100_currency') . ' ' : '';

    return $currency . number_format(abs($price), 2, $dec, $thousands);
}

/*
 *
 */
function nari100_pages( $no_extension = false){
    $pages = unserialize( get_option('nari100_pages') );
    if( $no_extension  )
        foreach( $pages as $key=>$page )
            $pages[$key] = str_replace('.php', '', $page);

    return $pages;
}

/*
 *
 */
function nari100_strlen($string, $min=0, $max=null){
    $length = strlen($string);
    if( !is_numeric($min) || !is_numeric($max) ) return false;
    if( $length >= $min && $length <= $max) return true;
    return false;
}

/*
 *
 */
function nari100_is_date($date){
    return (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/",$date));
}

/*
 *
 */
function nari100_check_balance($current_balance, $balance_limit){
    if( $current_balance < 0 )
        $color = 'danger';
    else if( $current_balance < $balance_limit )
        $color = 'warning';
    else
        $color = 'success';

    return $color;
}