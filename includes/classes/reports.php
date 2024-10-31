<?php
/**
 * @category   class
 * @package    Nari100Reports
 * @author     Seyed Shahrokh Nabavi <info@nabavi.nl>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0
 */

class Nari100Reports {
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;
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
     * @return string
     */
    static public function Admin(){
        $title     = str_replace('Nari100', '', get_class());
        if ( isset($_POST['do']) ) {
            switch( $_POST['do'] ) {
                default:
                    return 'Who are you? huuuuuun';
                    break;
            }
            return array( 'error' => 'success', 'msg' => __('Options successfully updated.','nari100') );
        } else {
            include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/reports.php';
        }
    }

    /**
     *
     */
    static public function DoSort( $sort ){
        $result = '';

        $orderby = $_POST['by'] === 'asc' ? ' ASC' : ' DESC';

        switch ( $sort ) {
            case 'date':
                $result = '`t1`.`transactions_date`' . $orderby;
                break;
            case 'account':
                $result = '`t2`.`accounts_name`' . $orderby;
                break;
            case 'type':
                $result = '`t1`.`transactions_type`' . $orderby;
                break;
        }

        return $result;
    }

    /**
     *
     */
    static public function DoReport( $flt ){
        $result = '';

        if( isset($flt['by-account']) && intval($flt['by-account']) ) {
            $result .= ' AND `t2`.`accounts_id` = ' . intval($flt['by-account']);
        }

        if( isset($flt['by-type']) && in_array($flt['by-type'], Nari100Transactions::getInstance()->type) ){
            $result .= " AND `t1`.`transactions_type` = '" . $flt['by-type'] . "'";
        }

        if( isset($flt['by-payer']) && intval($flt['by-payer']) ) {
            $result .= " AND `t1`.`payer_id` = '" . $flt['by-payer'] . "'";
        }

        if( isset($flt['by-payee']) && intval($flt['by-payee']) ) {
            $result .= " AND `t1`.`payer_id` = '" . $flt['by-payee'] . "'";
        }

        if( isset($flt['by-method']) && intval($flt['by-method']) ) {
            $result .= " AND `t1`.`method_id` = '" . $flt['by-method'] . "'";
        }

        if( isset($_POST['search']) && strlen(trim($_POST['search'])) > 3 ) {
            global $wpdb;

            $search = $wpdb->esc_like( trim($_POST['search']) );
            $search = '%' . $search . '%';

            $result .= $wpdb->prepare( " AND `t1`.`transactions_description` LIKE %s", $search );
        }

        return $result;
    }
}