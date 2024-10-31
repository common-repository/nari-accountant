<?php
/**
 * @category   class
 * @package    Nari100Accounts
 * @author     Seyed Shahrokh Nabavi <info@nabavi.nl>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0
 */

class Nari100Accounts {
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
     * @param $id
     */
    public function __construct( $id = null ){
        $this->dbFields = array(
            'accounts_id'          => array('value' => '', 'sql' => "bigint(20) unsigned NOT NULL AUTO_INCREMENT"),
            'accounts_name'        => array('value' => '', 'sql' => "varchar(100) COLLATE utf8_unicode_ci NOT NULL"),
            'accounts_description' => array('value' => '', 'sql' => "varchar(200) COLLATE utf8_unicode_ci NOT NULL"),
            'accounts_balance'     => array('value' => '', 'sql' => "decimal(18,2) NOT NULL DEFAULT '0.00'")
        );
        $this->table = NARI_DB . 'accounts';
        $this->key   = 'accounts_id';

        $this->get_by_id( $id );
    }

    /**
     * Load obj by id if exist
     *
     * @param $id
     */
    public function get_by_id( $id ){
        global $wpdb;
        $id = intval($id);

        $row = $id ? $wpdb->get_row("SELECT * FROM `$this->table` WHERE `accounts_id` = {$id}") : 0;

        $this->dbFields['accounts_id']['value'] = $row ? $id : (isset($_POST['accounts_id']) ? $_POST['accounts_id'] : 0);
        $this->dbFields['accounts_name']['value'] = $row ? $row->accounts_name : (isset($_POST['accounts_name']) ? $_POST['accounts_name'] : '');
        $this->dbFields['accounts_description']['value'] = $row ? $row->accounts_description : (isset($_POST['accounts_description']) ? $_POST['accounts_description'] : '');
        $this->dbFields['accounts_balance']['value'] = $row ? $row->accounts_balance : (isset($_POST['accounts_balance']) ? $_POST['accounts_balance'] : '0.00');
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
     * Delete account
     *
     * @return array
     */
    public function delete() {
        global $wpdb;
        $id = $this->dbFields['accounts_id']['value'];

        // Delete Account
        $wpdb->delete( $this->table, array('accounts_id' => $id), array('%d') );
        return array( 'error' => 'success', 'id' => $id );
    }

    /**
     * Add or Edit account record into Database
     *
     * @return array
     */
    public function save(){
        global $wpdb;

        $id = $this->dbFields['accounts_id']['value'];
        $account = $this->dbFields['accounts_name']['value'];
        $description = $this->dbFields['accounts_description']['value'];
        $balance = $this->dbFields['accounts_balance']['value'];
        $msg = '';

        if(nari100_strlen($account,2,100) == false){
            $msg .= __('Account title length error', 'nari100') . '<br>';
        }
        if (is_numeric($balance) == false) {
            $balance = '0.00';
        }

        //check with same name account is exist
        if( $this->is_duplicate()){
            $msg .= __('Account already exist', 'nari100') . '<br>';
        }


        if($msg == ''){
            if ( $id ){
                // Edit Account
                $wpdb->update(
                    $this->table,
                    array(
                        'accounts_name' => $account,
                        'accounts_description' => $description
                    ),
                    array(
                        'accounts_id' => $id
                    )
                );
                return array(
                    'error' => 'success',
                    'msg' => __('Account edited successfully', 'nari100'),
                    'row' => array( $account, $description )
                );
            } else {
                // Add Account
                $wpdb->insert(
                    $this->table,
                    array(
                        'accounts_name' => $account,
                        'accounts_description' => $description,
                        'accounts_balance' => $balance
                    ),
                    array(
                        '%s',
                        '%s',
                        '%d'
                    )
                );
                $id = $wpdb->insert_id;

                if( $balance != '0.00' ){
                    $save = array(
                        'accounts_id' => $id,
                        'payer_id' => 0,
                        'category_id' => 0,
                        'method_id' => 0,
                        'transactions_description' => __('[System] Initialize account.', 'nari100'),
                        'transactions_amount' => $balance,
                        'transactions_type' => 'Transfer',
                        'transactions_status' => 'Cleared',
                        'transactions_tag' => '',
                        'transactions_tax' => '',
                        'transactions_date' => current_time('Y-m-d H:i:s'),
                        'transactions_ref' => ''
                    );
                    Nari100Transactions::AddSystem( $save );
                }
                return array('error' => 'success', 'msg' => __('Account created successfully', 'nari100'));
            }
        }
        else{
            return array( 'error' => 'danger', 'msg' => __('<strong>Error</strong><br/>', 'nari100') . $msg );
        }
    }

    /**
     * Check if record with same name exist
     *
     * @return array|null|object|void
     */
    public function is_duplicate(){
        global $wpdb;

        $id = $this->dbFields['accounts_id']['value'];
        $account = $this->dbFields['accounts_name']['value'];

        $duplicate = $wpdb->get_row(
                    "SELECT * FROM `$this->table`
                    WHERE `accounts_name` = '" . sanitize_text_field( $account ) . "'
                    AND `accounts_id` != {$id}"
        );
        return $duplicate;
    }

        /**
     * @return string
     */
    static public function Admin(){
        $page_icon = 'fa fa-bank';
        $title     = str_replace('Nari100', '', get_class());
        $sub_menu  = array(
            'balancelst' => array(
                'selected' => 'active',
                'name'     => __('Accounts Balances', 'nari100'),
                'sub_menu' => array()
            ),
            'addfrm' => array(
                'selected' => '',
                'name'     => __('Add New Account', 'nari100'),
                'sub_menu' => array()
            ),
            'managelst' => array(
                'selected' => '',
                'name'     => __('Manage Account', 'nari100'),
                'sub_menu' => array()
            )
        );

        $do = isset($_POST['do']) ? $_POST['do'] : 'balancelst';

        switch( $do ) {
            case 'edit':
            case 'addfrm':
                $id = isset($_POST['id']) ? intval( $_POST['id'] ) : 0;
                $obj = new Nari100Accounts( $id );

                $file = 'accounts/addfrm.php';
                $name = $obj->dbFields['accounts_name']['value'];
                $desc = $obj->dbFields['accounts_description']['value'];
                break;
            case 'managelst':
            case 'balancelst':
                $file = 'accounts/' . $do . '.php';
                global $wpdb;
                $lst = $wpdb->get_results("SELECT * FROM `" . self::getInstance()->table . "`");
                $sum = 0;
                $balance_limit = intval(get_option('nari100_bal_limit'));
            break;
            case 'addrec':
                $id = isset($_POST['id']) ? intval( $_POST['id'] ) : 0;
                $obj = new Nari100Accounts( $id );
                return $obj->save();
                break;
            case 'delete':
                $obj = new Nari100Accounts( $_POST['id'] );
                return $obj->delete();
                break;
            default:
                return 'Who are you? huuuuuun';
                break;
        }

        if ( isset($_POST['do']) ) {
            ob_start();
            include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/' . $file;
            return ($_POST['do'] === 'edit') ? array( 'id' => $id, 'form' => ob_get_clean() ) : ob_get_clean();
        } else {
            include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/pages.php';
        }
    }

        /**
     * @param $id
     * @return string
     */
    static public function combobox( $id ) {
        global $wpdb;

        $combo = '';
        $id = intval($id);

        //load records
        $records = $wpdb->get_results("SELECT * FROM `" . self::getInstance()->table . "` ORDER BY `accounts_name` ASC");
        foreach( $records as $rec ){
            $selected = ($id == $rec->accounts_id) ? 'selected' : '';
            $combo .= '<option value="' . $rec->accounts_id . '" ' . $selected . '>' . $rec->accounts_name . '</option>';
        }
        return $combo;
    }

    /**
     * @param $id
     * @return string
     */
    static public function get_name_by_id( $id ) {
        global $wpdb;
        $sTable = self::getInstance()->table;
        $id = intval($id);

        //load records
        $records = $wpdb->get_row("SELECT * FROM `$sTable` WHERE `accounts_id` = " . $id );
        return $records ? $records->accounts_name : '';
    }

    /**
     * @return null|string
     */
    public function notification() {
        global $wpdb;
        $return = null;
        $sAccountsTBL = self::getInstance()->table;
        $sTransactionTBL = Nari100Transactions::getInstance()->table;

        $balance_limit  = get_option( 'nari100_bal_limit' );

        $accounts = $wpdb->get_results("
                SELECT `t1`.*, SUM(`t2`.`transactions_amount`) AS `balance`
                FROM `$sAccountsTBL` AS `t1`
                LEFT JOIN `$sTransactionTBL` AS `t2` ON `t1`.`accounts_id`=`t2`.`accounts_id`
                WHERE `transactions_status` = 'Cleared'
                GROUP BY `t2`.`accounts_id`
                HAVING  SUM(`t2`.`transactions_amount`) < " . $balance_limit);
        foreach ( $accounts as $amount ){
            $return .= 'Account = <strong>' . $amount->accounts_name . '</strong>, Current Balance = <strong>' . $amount->balance . '</strong><br/>';
        }
        if( $return )
            $return = '<p><h4>Balance limit exceed:</h4>' . $return . '</p>';

        return $return;
    }
}