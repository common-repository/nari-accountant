<?php
/**
 * @category   class
 * @package    Nari100Definitions
 * @author     Seyed Shahrokh Nabavi <info@nabavi.nl>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0
 */

class Nari100Definitions {
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
    public $definitions = array(
        'keys' => array( 'expense', 'deposit', 'payees', 'payers', 'paymethod' ),
        'expense' => 'Expense Categories',
        'deposit' => 'Income Categories',
        'payees' => 'Payee',
        'payers' => 'Payer',
        'paymethod' => 'Payment Method'
    );

    /**
     * @param $id
     */
    public function __construct( $id = null ){
        $types = $this->definitions['keys'];
        $this->dbFields = array(
            'definitions_id'    => array('value' => '', 'sql' => "bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT"),
            'definitions_name'  => array('value' => '', 'sql' => "varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL"),
            'definitions_type'  => array('value' => '', 'sql' => "enum('" . implode("', '", $types) . "') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL"),
            'definitions_order' => array('value' => '', 'sql' => "tinyint(3) UNSIGNED NOT NULL DEFAULT '0'")
        );
        $this->table = NARI_DB . 'definitions';
        $this->key   = 'definitions_id';

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

        $row  = $id ? $wpdb->get_row("SELECT * FROM `$this->table` WHERE `definitions_id` = {$id}") : 0;

        $this->dbFields['definitions_id']['value']   = $row ?  $row->definitions_id : 0;
        $this->dbFields['definitions_name']['value'] = $row ? $row->definitions_name : '';
        $this->dbFields['definitions_type']['value'] = $row ? $row->definitions_type : 'expense';
        $this->dbFields['definitions_order']['value'] = $row ? $row->definitions_order : 0;
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
     * Delete account
     *
     * @return array
     */
    public function delete() {
        global $wpdb;
        $id = $this->dbFields['definitions_id']['value'];

        // Delete Account
        $wpdb->delete( $this->table, array('definitions_id' => $id), array('%d') );
        return array( 'error' => 'success', 'id' => $id );
    }

    /**
     * Add or Edit account record into Database
     *
     * @return array
     */
    public function save(){
        global $wpdb;

        $id = $this->dbFields['definitions_id']['value'];
        if ( isset( $_POST['type'] ) ) $_POST['definitions_type'] = $_POST['type'];

        $definitions_name = isset($_POST['definitions_name']) ? $_POST['definitions_name'] : $this->dbFields['definitions_name']['value'];
        $definitions_type = isset($_POST['definitions_type']) ? $_POST['definitions_type'] : $this->dbFields['definitions_type']['value'];
        $definitions_order = isset($_POST['definitions_order']) ? $_POST['definitions_order'] : $this->dbFields['definitions_order']['value'];

        $msg = '';

        if(nari100_strlen($definitions_name,2,100) == false){
            $msg .= __('Record name length error', 'nari100') . '<br>';
        }

        //check with same name account is exist
        if( $this->is_duplicate() ){
            $msg .= __('This name already exist', 'nari100') . '<br>';
        }

        //check if type is allowe
        if( !in_array( $definitions_type, $this->definitions['keys'] ) ){
            $msg .= __('You put invalid data type.', 'nari100') . '<br>';
        }

        if($msg == ''){
            if ( $id ){
                $wpdb->update(
                    $this->table,
                    array(
                        'definitions_name' => $definitions_name
                    ),
                    array(
                        'definitions_id' => $id
                    )
                );
                return array(
                    'error' => 'success',
                    'msg' => __('Record edited successfully', 'nari100'),
                    'row' => array( $definitions_name )
                );
            } else {
                // Add Record
                $wpdb->insert(
                    $this->table,
                    array(
                        'definitions_name' => $definitions_name,
                        'definitions_type' => $definitions_type,
                        'definitions_order' => $definitions_order
                    ),
                    array(
                        '%s',
                        '%s',
                        '%d'
                    )
                );
                return array('error' => 'success', 'msg' => __('Record created successfully', 'nari100'));
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

        $id = $this->dbFields['definitions_id']['value'];
        $name = $this->dbFields['definitions_name']['value'];
        $type = $this->dbFields['definitions_type']['value'];

        $duplicate = $wpdb->get_row(
            "SELECT * FROM `$this->table`
                    WHERE `definitions_name` = '" . sanitize_text_field( $name ) . "'
                    AND `definitions_id` != {$id}
                    AND `definitions_type` = '{$type}'"
        );
        return $duplicate;
    }



    /**
     * @return string
     */
    static public function Admin(){
        $page_icon = 'fa fa-book';
        $title = str_replace('Nari100', '', get_class());
        $sub_menu = array(
            'expense' => array(
                'selected' => 'active',
                'name'     => __('Expense Categories', 'nari100'),
                'sub_menu' => array(
                    'addfrm'    => __('Add a new category', 'nari100'),
                    'managelst' => __('List of all categories', 'nari100')
                ),
                'icon' => 'fa fa-folder-open'
            ),
            'deposit' => array(
                'selected' => '',
                'name'     => __('Income Categories', 'nari100'),
                'sub_menu' => array(
                    'addfrm'    => __('Add a new category', 'nari100'),
                    'managelst' => __('List of all categories', 'nari100')
                ),
                'icon' => 'fa fa-folder-open'
            ),
            'payees' => array(
                'selected' => '',
                'name'     => __('Payee', 'nari100'),
                'sub_menu' => array(
                    'addfrm'    => __('Add a new payee', 'nari100'),
                    'managelst' => __('List of all payees', 'nari100')
                ),
                'icon' => 'fa fa-user'
            ),
            'payers' => array(
                'selected' => '',
                'name'     => __('Payer', 'nari100'),
                'sub_menu' => array(
                    'addfrm'    => __('Add a new payer', 'nari100'),
                    'managelst' => __('List of all payers', 'nari100')
                ),
                'icon' => 'fa fa-user'
            ),
            'paymethod' => array(
                'selected' => '',
                'name'     => __('Payment Method', 'nari100'),
                'sub_menu' => array(
                    'addfrm'    => __('Add a new method', 'nari100'),
                    'managelst' => __('List of all methods', 'nari100')
                ),
                'icon' => 'fa fa-credit-card'
            )
        );

        $do = isset($_POST['do']) ? $_POST['do'] : 'addfrm';

        switch( $do ) {
            case 'edit':
            case 'addfrm':
                $id = isset($_POST['id']) ? intval( $_POST['id'] ) : 0;
                $obj = new Nari100Definitions( $id );

                $file  = 'definitions/addfrm.php';
                $name  = $obj->dbFields['definitions_name']['value'];
                $type  = ( $do == 'addfrm' && isset($_POST['type']) ) ? $_POST['type'] : $obj->dbFields['definitions_type']['value'];
                $order = $obj->dbFields['definitions_order']['value'];
                break;

            case 'pagination':
            case 'managelst':
                $file = 'definitions/managelst.php';
                $obj = self::getInstance();
                $type = $_POST['type'];
                if( !in_array($type, $obj->definitions['keys']) )
                    $type = 'expense';

                global $wpdb;
                $sql = "SELECT * FROM `" . $obj->table . "` WHERE `definitions_type` = '{$type}' ORDER BY `definitions_order`, `definitions_name` ASC";
                $pagination = new Nari100Pagination( $sql, 10, 1 );
                $lst = $wpdb->get_results( $sql . " LIMIT {$pagination->offset},{$pagination->per_page}");
                break;

            case 'addrec':
                $id = isset($_POST['definitions_id']) ? intval( $_POST['definitions_id'] ) : 0;
                $obj = new Nari100Definitions( $id );
                return $obj->save();
                break;

            case 'delete':
                $id = isset($_POST['id']) ? intval( $_POST['id'] ) : 0;
                $obj = new Nari100Definitions( $id );
                return $obj->delete();
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
     * @param $id
     * @return string
     */
    static public function combobox( $id, $type ) {
        global $wpdb;
        $sTable = self::getInstance()->table;
        $combo = '';
        $id = intval($id);

        if ( !in_array( $type, self::getInstance()->definitions['keys'] ) )
            $type = self::getInstance()->definitions['keys'][0];

        //load records
        $records = $wpdb->get_results("SELECT * FROM `$sTable` WHERE `definitions_type` = '" . $type . "' ORDER BY `definitions_order`, `definitions_name` ASC");
        foreach( $records as $rec ){
            $selected = $id == $rec->definitions_id ? 'selected' : '';
            $combo .= '<option value="' . $rec->definitions_id . '" ' . $selected . '>' . $rec->definitions_name . '</option>';
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
        $records = $wpdb->get_row("SELECT * FROM `$sTable` WHERE `definitions_id` = " . $id );
        return $records ? $records->definitions_name : '';
    }
};