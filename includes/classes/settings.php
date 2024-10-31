<?php
/**
 * @category   class
 * @package    Nari100Settings
 * @author     Seyed Shahrokh Nabavi <info@nabavi.nl>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0
 */

class Nari100Settings {
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;

    private static $default = array(
        'nari100_pages' => '',
        'nari100_version' => NARI_ACCOUNTANT_VERSION,
        'nari100_currency' => '',
        'nari100_dec_point' => '.',
        'nari100_thousands_sep' => ',',
        'nari100_backups' => array(),
        'nari100_bal_limit' => '100'
    );

    /**
     * @param $id
     */
    public function __construct(){
    }


    /**
     * Returns instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            $class = get_class();
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * Add options
     *
     * @return array
     */
    static public function add( $pages = null ) {
        // Extera page: This is not DB class
        if( $pages ) {
            $pages[] = 'reports.php';
            $pages[] = 'settings.php';
        }

        foreach( self::$default as $key=>$option ) {
            if( $key === 'nari100_pages' )
                add_option($key, serialize($pages));
            else
                add_option($key, $option);
        }
    }

    /**
     * Delete options
     *
     * @return array
     */
    static public function delete() {
        foreach( self::$default as $key=>$option ) {
            delete_option( $key );
        }
    }

    /**
     * Add or Edit account record into Database
     *
     * @return array
     */
    static public function save(){
        update_option( 'nari100_currency',  $_POST['currency']);
        update_option( 'nari100_dec_point', $_POST['dec_point']);
        update_option( 'nari100_bal_limit', intval($_POST['bal_limit']));
        update_option( 'nari100_thousands_sep', $_POST['thousands_sep']);
        update_option( 'nari100_payee_users',   (isset($_POST['payee_users']) ? 1 : 0) );
        update_option( 'nari100_payer_users',   (isset($_POST['payer_users']) ? 1 : 0) );

        return array( 'error' => 'success', 'msg' => __('Options successfully updated.', 'nari100') );
    }

    /**
     * @return string
     */
    static public function Admin(){
        $title     = str_replace('Nari100', '', get_class());
        $sub_menu = array(
            'general' => array(
                'selected' => 'active',
                'name'     => __('General', 'nari100'),
                'sub_menu' => array()
            ),
            'backupfrm' => array(
                'selected' => '',
                'name'     => __('Backup & Restore', 'nari100'),
                'sub_menu' => array()
            )
        );

        $do = isset($_POST['do']) ? $_POST['do'] : 'general';

        switch( $do ) {
            case 'general':
                $dec       = get_option( 'nari100_dec_point');
                $thousands = get_option( 'nari100_thousands_sep');
                $currency  = get_option( 'nari100_currency');
                $userPayee = get_option( 'nari100_payee_users');
                $userPayer = get_option( 'nari100_payer_users');
                $balance_limit  = get_option( 'nari100_bal_limit');
                $file = 'settings/general.php';
                break;
            case 'saveopt':
                return self::save();
                break;
            case 'backupfrm':
                $file = Nari100Backup::getInstance()->admin();
                break;
            case 'getbackup':
                return Nari100Backup::getInstance()->create_backup();
                break;
            case 'backupdel':
                return Nari100Backup::getInstance()->delete_backup();
                break;
            case 'backuprst':
                return Nari100Backup::getInstance()->restore_backup();
                break;
            default:
                return 'Who are you? huuuuuun';
                break;
        }

        if ( isset($_POST['do']) ) {
            ob_start();
            include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/' . $file;
            return ob_get_clean();
        } else {
            include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/pages.php';
        }
    }
}