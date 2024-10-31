<?php
/**
 * @category   class
 * @package    Nari100Transactions
 * @author     Seyed Shahrokh Nabavi <info@nabavi.nl>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0
 */

class Nari100Transactions {
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $table;

    /**
     * @var array
     */
    public $dbFields = array();

    /**
     * @var array
     */
    public $type = array('Income','Expense','Transfer');

    /**
     * @var array
     */
    public $status = array('Cleared','Uncleared','RepeatAll','RepeatLimit');

    /**
     * @param $id
     */
    public function __construct( $id = null ){
        $types  = $this->type;
        $status = $this->status;
        $this->dbFields = array(
            'transactions_id' => array('value' => '', 'sql' => "bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT"),
            'accounts_id' => array('value' => '', 'sql' => "bigint(20) unsigned NOT NULL"),
            'category_id' => array('value' => '', 'sql' => "bigint(20) unsigned NOT NULL"),
            'payer_id'    => array('value' => '', 'sql' => "bigint(20) unsigned NOT NULL"),
            'method_id'   => array('value' => '', 'sql' => "bigint(20) unsigned NOT NULL"),
            'transactions_tag'  => array('value' => '', 'sql' => "text COLLATE utf8_unicode_ci NOT NULL"),
            'transactions_tax'  => array('value' => '', 'sql' => "decimal(18,2) NOT NULL DEFAULT '0.00'"),
            'transactions_type' => array('value' => '', 'sql' => "enum('" . implode("', '", $types) . "') COLLATE utf8_unicode_ci NOT NULL"),
            'transactions_date' => array('value' => '', 'sql' => "datetime NOT NULL"),
            'transactions_amount' => array('value' => '', 'sql' => "decimal(18,2) NOT NULL"),
            'transactions_ref'    => array('value' => '', 'sql' => "varchar(200) COLLATE utf8_unicode_ci NOT NULL"),
            'transactions_status' => array('value' => '', 'sql' => "enum('" . implode("', '", $status) . "') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Cleared'"),
            'transactions_description' => array('value' => '', 'sql' => "text COLLATE utf8_unicode_ci NOT NULL")
        );
        $this->table = NARI_DB . "transactions";
        $this->key   = "transactions_id";

        $this->get_by_id( $id );
        $this->repeat_limit_exceeded();
    }

    /**
     * Load obj by id if exist
     *
     * @param $id
     */
    public function get_by_id( $id ){
        global $wpdb;

        $id = intval($id);

        $row  = $id ? $wpdb->get_row("SELECT * FROM `$this->table` WHERE `transactions_id` = {$id}") : 0;

        $this->dbFields['transactions_id']['value'] = $row ? $row->transactions_id : 0;
        $this->dbFields['accounts_id']['value'] = $row ? $row->accounts_id : 0;
        $this->dbFields['transactions_type']['value'] = $row ? $row->transactions_type : 'Expense';
        $this->dbFields['category_id']['value'] = $row ? $row->category_id : 0;
        $this->dbFields['transactions_amount']['value'] = $row ? $row->transactions_amount : '0.00';
        $this->dbFields['payer_id']['value'] = $row ? $row->payer_id : 0;
        $this->dbFields['method_id']['value'] = $row ? $row->method_id : 0;
        $this->dbFields['transactions_ref']['value'] = $row ? $row->transactions_ref : '';
        $this->dbFields['transactions_status']['value'] = $row ? $row->transactions_status : 'Cleared';
        $this->dbFields['transactions_description']['value'] = $row ? $row->transactions_description : '';
        $this->dbFields['transactions_tag']['value'] = $row ? $row->transactions_tag : '';
        $this->dbFields['transactions_tax']['value'] = $row ? $row->transactions_tax : '0.00';
        $this->dbFields['transactions_date']['value'] = $row ? $row->transactions_date : current_time('Y-m-d H:i:s');
    }

    /**
     * Returns the *Singleton* instance of this class.
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
     * Install DB
     *
     * @return string
     */
    static public function CreateDB(){
        $sql = '';
        foreach( self::getInstance()->dbFields as $key=>$item )
            $sql .= '`' . $key . '` ' . $item['sql'] . ',';
        return $sql;
    }

    /**
     * Delete transaction
     *
     * @return array
     */
    public function delete() {
        global $wpdb;
        $id = $this->dbFields['transactions_id']['value'];

        // Delete Account
        $wpdb->delete( $this->table, array('transactions_id' => $id), array('%d') );
        return array( 'error' => 'success', 'id' => $id );
    }

    /**
     * Change transaction status
     *
     * @return array
     */
    public function change_satus() {
        global $wpdb;
        switch ( $_POST['status'] ) {
            case 'remove':
                $status = 'Uncleared';
                break;
            case 'restore':
                $status = 'Cleared';
                break;
            default:
                return array('error' => 'danger', 'msg' => __('Problem in changing status', 'nari100'));
                break;
        }

        $wpdb->update(
            $this->table,
            array(
                'transactions_status' => $status
            ),
            array(
                'transactions_id' => $this->dbFields['transactions_id']['value']
            )
        );
        return array('error' => 'success', 'msg' => __('Status of this transaction changed.', 'nari100'));
    }

    public function save( $is_system = false ){
        global $wpdb;

        $id = $this->dbFields['transactions_id']['value'];

        $transactions_type   = isset($_POST['transactions_type']) ? $_POST['transactions_type'] : $this->dbFields['transactions_type']['value'];
        $payer_id            = isset($_POST['payer_id']) ? intval($_POST['payer_id'])   : $this->dbFields['payer_id']['value'];
        $method_id           = isset($_POST['method_id']) ? intval($_POST['method_id']) : $this->dbFields['method_id']['value'];
        $transactions_ref    = isset($_POST['transactions_ref']) ? $_POST['transactions_ref']   : $this->dbFields['transactions_ref']['value'];
        $transactions_date   = isset($_POST['transactions_date']) ? $_POST['transactions_date'] : $this->dbFields['transactions_date']['value'];
        $category_id         = isset($_POST['category_id']) ? intval($_POST['category_id']) : $this->dbFields['category_id']['value'];
        $transactions_tag    = isset($_POST['transactions_tag']) ? $_POST['transactions_tag'] : $this->dbFields['transactions_tag']['value'];
        $transactions_tax    = isset($_POST['transactions_tax']) ? $_POST['transactions_tax'] : $this->dbFields['transactions_tax']['value'];
        $transactions_description = isset($_POST['transactions_description']) ? $_POST['transactions_description'] : $this->dbFields['transactions_description']['value'];
        $transactions_status = isset($_POST['transactions_status']) ? $_POST['transactions_status'] : $this->dbFields['transactions_status']['value'];

        $accounts_id         = !$is_system && isset($_POST['accounts_id']) && $_POST['accounts_id'] ? intval($_POST['accounts_id']) : $this->dbFields['accounts_id']['value'];
        $transactions_amount = !$is_system && isset($_POST['transactions_amount']) ? $_POST['transactions_amount'] : $this->dbFields['transactions_amount']['value'];

        $repeat = isset($_POST['repeat']) ? intval($_POST['repeat']) : 0;
        $repeat_number = isset($_POST['repeat']) ? intval($_POST['repeat_number']) : 0;
        $msg = '';


        if ( $transactions_type == 'Expense' )
            $transactions_amount *= -1;


        if ($transactions_description == '') {
            $msg .= __('Please enter text for description.', 'nari100') . '<br>';
        }

        if( !$is_system )
            if ( !$accounts_id || !$payer_id || !$category_id || !$method_id || !in_array($transactions_type, $this->type) ) {
                $msg .= __('Data wrong.', 'nari100') . '<br>';
            }

        if (is_numeric($transactions_amount) == false) {
            $msg .= __('The format of amount is not correct.', 'nari100') . '<br>';
        }

        if (nari100_is_date($transactions_date) == false) {
            $msg .= __('The format of date is not correct.', 'nari100') . '<br>';
        }

        if ( $repeat ) {
            if ( $transactions_date <= current_time('Y-m-d H:i:s') ) {
                $msg .= __('Please enter the first occurrence date in future.', 'nari100') . '<br>';
            } else if ( $repeat_number === 0 ) {
                $msg .= __('Please enter number of repetitions.', 'nari100') . '<br>';
            } else {
                $transactions_status = 'RepeatLimit';
                switch ( $repeat ){
                    case 1:
                        $step = 1;
                        $transactions_date = ' day ' . $transactions_date;
                        break;
                    case 2:
                        $step = 1;
                        $transactions_date = ' week ' . $transactions_date;
                        break;
                    case 3:
                        $step = 2;
                        $transactions_date = ' week ' . $transactions_date;
                        break;
                    case 4:
                        $step = 1;
                        $transactions_date = ' month ' . $transactions_date;
                        break;
                    case 5:
                        $step = 2;
                        $transactions_date = ' month ' . $transactions_date;
                        break;
                    case 6:
                        $step = 3;
                        $transactions_date = ' month ' . $transactions_date;
                        break;
                    case 7:
                        $step = 6;
                        $transactions_date = ' month ' . $transactions_date;
                        break;
                    case 8:
                        $step = 1;
                        $transactions_date = ' year ' . $transactions_date;
                        break;
                    default:
                        $days = 0;
                        break;
                }
            }
        } else {
            $step = 1;
            $repeat_number = 1;
            $transactions_date = ' day ' . $transactions_date;
        }

        if($msg == ''){
            if ( $id ){
                // Edit Account
                $wpdb->update(
                    $this->table,
                    array(
                        'accounts_id' => $accounts_id,
                        'payer_id' => $payer_id,
                        'category_id' => $category_id,
                        'method_id' => $method_id,
                        'transactions_description' => $transactions_description,
                        'transactions_amount' => $transactions_amount,
                        'transactions_date' => date("Y-m-d H:i:s", strtotime( '0' . $transactions_date)),
                        'transactions_ref' => $transactions_ref
                    ),
                    array(
                        'transactions_id' => $id
                    )
                );
                $dr = $cr = $payer = $payee = '';
                if( $transactions_type == 'Expense' ){
                    $dr = $transactions_amount;
                    $payee = Nari100Definitions::get_name_by_id($payer_id);
                } else {
                    $cr = $transactions_amount;
                    $payer = Nari100Definitions::get_name_by_id($payer_id);
                }

                return array(
                    'error' => 'success',
                    'msg' => __('Transaction edited successfully', 'nari100'),
                    'row' => array(
                        str_replace(' day ', '', $transactions_date), Nari100Accounts::get_name_by_id($accounts_id),
                        $transactions_type, Nari100Definitions::get_name_by_id($category_id),
                        nari100_price($transactions_amount), $payer, $payee, Nari100Definitions::get_name_by_id($method_id),
                        $transactions_ref, $transactions_description, nari100_price($dr), nari100_price($cr)
                    )
                );
            } else {
                for(  $index = 0; $index < $step * $repeat_number; $index = $index + $step) {
                    // Add Account
                    $wpdb->insert(
                        $this->table,
                        array(
                            'accounts_id' => $accounts_id,
                            'payer_id' => $payer_id,
                            'category_id' => $category_id,
                            'method_id' => $method_id,
                            'transactions_description' => $transactions_description,
                            'transactions_amount' => $transactions_amount,
                            'transactions_type' => $transactions_type,
                            'transactions_status' => $transactions_status,
                            'transactions_tag' => $transactions_tag,
                            'transactions_tax' => $transactions_tax,
                            'transactions_date' => date("Y-m-d H:i:s", strtotime( ($index*$step) . $transactions_date)),
                            'transactions_ref' => $transactions_ref
                        ),
                        array(
                            '%d',
                            '%d',
                            '%d',
                            '%d',
                            '%s',
                            '%f',
                            '%s',
                            '%s',
                            '%s',
                            '%f',
                            '%s',
                            '%s'
                        )
                    );
                }
                return array('error' => 'success', 'msg' => __('Transaction created successfully', 'nari100'));
            }
        }
        else{
            return array( 'error' => 'danger', 'msg' => __('<strong>Error</strong><br/>', 'nari100') . $msg );
        }
    }

    /**
     * @return string
     */
    static public function AddSystem( $data ){
        $obj = new Nari100Transactions();
        foreach( $data as $key=>$value )
            $obj->dbFields[$key]['value'] = $value;
        $obj->save( true );
    }

    /**
     * @return string
     */
    static public function Admin(){
        $page_icon = "fa fa-exchange";
        $title    = str_replace('Nari100', '', get_class());
        $sub_menu = array(
            'Expense' => array(
                'selected' => 'active',
                'name'     => __('Add Expense', 'nari100'),
                'sub_menu' => array()
            ),
            'Income' => array(
                'selected' => '',
                'name'     => __('Add Deposit', 'nari100'),
                'sub_menu' => array()
            ),
            'Transfer' => array(
                'selected' => '',
                'name'     => __('Transfer', 'nari100'),
                'sub_menu' => array()
            ),
            'managelst' => array(
                'selected' => '',
                'name'     => __('View Transactions', 'nari100'),
                'sub_menu' => array()
            ),
            'repeatlst' => array(
                'selected' => '',
                'name'     => __('Repeated Transactions', 'nari100'),
                'sub_menu' => array()
            ),
            'deletedlst' => array(
                'selected' => '',
                'name'     => __('Deleted Transactions', 'nari100'),
                'sub_menu' => array()
            )
        );

        $do = isset($_POST['do']) ? $_POST['do'] : 'Expense';


        switch( $do ) {
            case 'edit':
            case 'Transfer':
            case 'Income':
            case 'Expense':
                $id = isset($_POST['id']) ? intval( $_POST['id'] ) : 0;
                $obj = new Nari100Transactions($id );

                $file = 'transactions/addfrm.php';
                $account_id = $obj->dbFields['accounts_id']['value'];
                $payer_id = $obj->dbFields['payer_id']['value'];
                $category_id = $obj->dbFields['category_id']['value'];
                $method_id = $obj->dbFields['method_id']['value'];

                $tag = $obj->dbFields['transactions_tag']['value'];
                $tax = $obj->dbFields['transactions_tax']['value'];
                $stu = $obj->dbFields['transactions_status']['value'];

                $type = $id ? $obj->dbFields['transactions_type']['value'] : $do;
                $date = $obj->dbFields['transactions_date']['value'];
                $amt = $obj->dbFields['transactions_amount']['value'];
                $ref = $obj->dbFields['transactions_ref']['value'];
                $des = $obj->dbFields['transactions_description']['value'];

                if( $type === 'Income' ){
                    $category = array('deposit', 'payers');
                    $titles   = 'Payer';
                } elseif ( $type == 'Transfer' ) {
                } else {
                    $category = array('expense', 'payees');
                    $titles   = 'Payee';
                }
                break;
            case 'deletedlst':
            case 'repeatlst':
            case 'pagination':
            case 'managelst':
                $file = 'transactions/managelst.php';
                global $wpdb;

                $balance_limit = intval(get_option('nari100_bal_limit'));
                $status = ($do === 'repeatlst') ? 'RepeatLimit' : (($do === 'deletedlst') ? 'Uncleared' : 'Cleared');
                $perPage = ( isset($_POST['flt']) && intval($_POST['flt']['per-page']) ) ? intval($_POST['flt']['per-page']) : 10;


                if( isset($_POST['flt']) ) {
                    $flt = $_POST['flt'];
                    $filter = Nari100Reports::DoReport( $flt );
                } else {
                    $filter = '';
                }

                if( isset($_POST['sort']) ) {
                    $sort = $_POST['sort'];
                    $sort = Nari100Reports::DoSort( $sort );
                } else {
                    $sort = '`t1`.`transactions_date` DESC';
                }

                $sAccountsTBL = Nari100Accounts::getInstance()->table;
                $sDefinitionTbl = Nari100Definitions::getInstance()->table;
                $sTable = self::getInstance()->table;
                $sql = "SELECT `t1`.*,
                          `t2`.`accounts_name`,
                          `t3`.`definitions_name` AS `cat`,
                          `t4`.`definitions_name` AS `payer`,
                          `t5`.`definitions_name` AS `method`,
                          (
                            SELECT SUM(`t6`.`transactions_amount`)
                            FROM `$sTable` as t6
                            WHERE `t1`.`accounts_id`=`t6`.`accounts_id`
                            AND t6.`transactions_date` <= t1.`transactions_date`
                            AND `t6`.`transactions_status` = '{$status}'
                          ) AS `bal`
                      FROM `$sTable` AS `t1`
                      LEFT JOIN `$sAccountsTBL` AS `t2` ON `t1`.`accounts_id`=`t2`.`accounts_id`
                      LEFT JOIN `$sDefinitionTbl` AS `t3` ON `t1`.`category_id`=`t3`.`definitions_id`
                      LEFT JOIN `$sDefinitionTbl` AS `t4` ON `t1`.`payer_id`=`t4`.`definitions_id`
                      LEFT JOIN `$sDefinitionTbl` AS `t5` ON `t1`.`method_id`=`t5`.`definitions_id`
                      WHERE `t1`.`transactions_status` = '{$status}' " . $filter . "
                      ORDER BY " . $sort;
                $pagination = new Nari100Pagination( $sql, $perPage, 2 );
                $lst = $wpdb->get_results( $sql . " LIMIT {$pagination->offset},{$pagination->per_page}");
                break;
            case 'addrec':
                $id = isset($_POST['id']) ? intval( $_POST['id'] ) : 0;
                $obj = new Nari100Transactions( $id );

                if ( $_POST['transactions_type'] === 'Transfer' ) {

                    $from = intval($_POST['accounts_id']);
                    $to   = intval($_POST['to_accounts_id']);
                    $pay  = intval($_POST['method_id']);
                    $msg  = '';

                    if ( $from ===  $to ) {
                        $msg .= __('You can not transfer amount to same account.', 'nari100') . '<br>';
                    }
                    if ( !$from || !$to || !$pay ) {
                        $msg .= __('Data wrong.', 'nari100') . '<br>';
                    }

                    if( $msg === '') {
                        $obj->dbFields['accounts_id']['value'] = $from;
                        $obj->dbFields['transactions_amount']['value'] = -1 * $_POST['transactions_amount'];
                        $res1 = $obj->save(true);

                        $obj->dbFields['accounts_id']['value'] = $to;
                        $obj->dbFields['transactions_amount']['value'] = $_POST['transactions_amount'];
                        $res2 = $obj->save(true);

                        if ( $res1['error'] === 'success' && $res2['error'] === 'success' )
                            return array('error' => 'success', 'msg' => __('Transaction created successfully', 'nari100'));
                        else
                            return array('error' => 'danger', 'msg' => __('Transfer is not successfully done.', 'nari100'));
                    }
                    return array( 'error' => 'danger', 'msg' => __('<strong>Error</strong><br/>', 'nari100') . $msg );
                } else {
                    return $obj->save();
                }
                break;
            case 'delete':
                $id = isset($_POST['id']) ? intval( $_POST['id'] ) : 0;
                $obj = new Nari100Transactions( $id );
                return $obj->delete();
                break;
            case 'change-status':
                $id = isset($_POST['id']) ? intval( $_POST['id'] ) : 0;
                $obj = new Nari100Transactions( $id );
                return $obj->change_satus();
                break;
            default:
                return 'Who are you? huuuuuun';
                break;
        }

        if ( isset($_POST['do']) ) {
            ob_start();
            include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/' . $file;
            return ($do === 'edit') ? array( 'id' => $id, 'form' => ob_get_clean() ) : ob_get_clean();
        } else {
            include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/pages.php';
        }

    }

    /**
     * @return string
     */
    static public function get_current_balance( $account_id ){
        global $wpdb;
        $sTable = self::getInstance()->table;

        $lastUpdate = $wpdb->get_row("
                SELECT SUM(`transactions_amount`) AS `cbal`
                FROM `$sTable`
                WHERE `accounts_id` =" . intval($account_id) . "
                AND `transactions_status` = 'Cleared'");
        return $lastUpdate->cbal;
    }

    /**
     * @return string
     */
    static public function repeat_limit_check(){
        global $wpdb;
        $sTable = self::getInstance()->table;

        $timeout = $wpdb->get_row("
                SELECT * FROM `$sTable`
                WHERE `transactions_date` < '" . current_time("Y-m-d H:i:s") . "'
                AND `transactions_status` = 'RepeatLimit'");
        return $timeout;
    }

    /**
     * @return string
     */
    public function repeat_limit_exceeded(){
        global $wpdb;

        $timeout = $wpdb->query(
            $wpdb->prepare("UPDATE $this->table
                SET `transactions_status` = '%s'
                WHERE `transactions_status` = '%s'
                AND `transactions_date` < '%s'",
                'Cleared',
                'RepeatLimit',
                current_time("Y-m-d H:i:s")
            )
        );
        return $timeout;
    }

}