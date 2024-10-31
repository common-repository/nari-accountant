<?php
/**
 * @category   class
 * @package    Nari100Backup
 * @author     Seyed Shahrokh Nabavi <info@nabavi.nl>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0
 */

class Nari100Backup {

    /**
     * @var array
     */
    private $nari100_table = array(
        'wp_nari100_accounts',
        'wp_nari100_definitions',
        'wp_nari100_transactions'
    );

    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     *
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
    public function admin(){
        return 'settings/backup.php';
    }

    function mysql_backup() {
        global $wpdb;

        $tables = $wpdb->get_col('SHOW TABLES');
        $output = '';
        foreach($tables as $table) {
            if( in_array($table, $this->nari100_table) ){
                $result = $wpdb->get_results("SELECT * FROM {$table}", ARRAY_N);
                $row2 = $wpdb->get_row('SHOW CREATE TABLE '.$table, ARRAY_N);
                $output .= "\n\n".$row2[1].";\n\n";
                for($i = 0; $i < count($result); $i++) {
                    $row = $result[$i];
                    $output .= 'INSERT INTO '.$table.' VALUES(';
                    for($j=0; $j<count($result[0]); $j++) {
                        $row[$j] = ($row[$j]);
                        $output .= (isset($row[$j])) ? '"'.$row[$j].'"'	: '""';
                        if ($j < (count($result[0])-1)) {
                            $output .= ',';
                        }
                    }
                    $output .= ");\n";
                }
                $output .= "\n";
            }
        }
        $wpdb->flush();
        return $output;
    }

    /**
     * @return array
     */
    private function create_bkp_file() {

        $path_info = wp_upload_dir();
        $path = $path_info['basedir'] . '/nari_backup';
        $url  = $path_info['baseurl'] . '/nari_backup';

        wp_mkdir_p($path);
        fclose( fopen( $path . '/index.php', 'w' ) );


        $filename = 'nari_' . date("[Y_m_d][H_i_s]") . '_' . rand(9, 9999);
        $sqlFilename  = $filename . '.sql';
        $zipFilename = $filename . '.zip';


        $handle = fopen($path . '/' . $sqlFilename, 'w+');
        fwrite($handle, $this->mysql_backup());
        fclose($handle);


        if ( class_exists( 'ZipArchive' ) ) {
            $zip = new ZipArchive;
            $zip->open($path . '/' . $zipFilename, ZipArchive::CREATE);
            $zip->addFile($path . '/' . $sqlFilename, $sqlFilename);
            $zip->close();
        } else {
            error_log("Class ZipArchive Is Not Exist");
        }

        $upload_path = array(
            'date' => time(),
            'filename' => $zipFilename,
            'dir' => $path . '/' . $zipFilename,
            'url' => $url . '/' . $zipFilename,
            'sql' => $path . '/' . $sqlFilename,
            'size' => filesize( $path . '/' . $zipFilename )
        );

        if( file_exists( $path . '/' . $sqlFilename ) ) unlink($path . '/' . $sqlFilename);
        return $upload_path;

    }

    /**
     * @return array
     */
    public function create_backup() {
        $options = get_option('nari100_backups');

        if(!$options) {
            $options = array();
        }

        $last_opt  = $this->create_bkp_file();
        $options[] = $last_opt;
        update_option('nari100_backups', $options);

        $count = count($options)-1;
        return array(
            'error' => 'success',
            'msg' => __('Backup successfully created.', 'nari100'),
            'row' => '<tr id="record' . $count . '">
                                    <td>' . date('jS, F Y', $last_opt['date']) . ' - ' . date('h:i:s A', $last_opt['date']) . '</td>
                                    <td>
                                        <a href="' . $last_opt['url'] . '">
                                            ' . __('Download', 'nari100') . '
                                        </a>
                                    </td>
                                    <td>' . round($last_opt["size"]/1024,2) . '</td>
                                    <td>
                                        <a href="#" class="button-secondary backup" data-do="backupdel" data-id="' . $count . '">
                                            ' . __('Remove Backup', 'nari100') . '
                                        </a>
                                    </td>
                                    <td>
                                        <a href="#" class="button-secondary backup" data-do="backuprst" data-id="' . $count . '">
                                            ' . __('Restore Backup', 'nari100') . '
                                        </a>
                                    </td>
                                </tr>'
        );
    }

    /**
     * @return array
     */
    public function delete_backup() {
        $id = intval($_POST['id']);

        $options = get_option('nari100_backups');
        $new_opt = array();

        $count = 0;
        foreach($options as $option) {
            if($count != $id) {
                $new_opt[] = $option;
            }
            $count++;
        }

        if( file_exists($options[$id]['dir']) ) unlink($options[$id]['dir']);
        if( file_exists($options[$id]['sql']) ) unlink($options[$id]['sql']);

        update_option('nari100_backups', $new_opt);
        return array(
            'error' => 'success',
            'id' => $id
        );
    }

    /**
     * @return array
     */
    public function restore_backup() {
        global $wpdb;

        $id = intval($_POST['id']);
        $options = get_option('nari100_backups');
        $zip_file = $options[$id]['dir'];
        $ext_path = dirname($zip_file);

        if ( class_exists( 'ZipArchive' ) ) {
            $zip = new ZipArchive;
            if ($zip->open($zip_file) === TRUE) {
                $zip->extractTo( $ext_path );
                $zip->close();
            }
        }

        ini_set("max_execution_time", "5000");
        ini_set("max_input_time",     "5000");
        ini_set('memory_limit', '1000M');
        set_time_limit(0);

        if( defined('DB_NAME') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_HOST') && $conn = @mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) ) {

            if( !mysql_select_db( DB_NAME, $conn ) ) {
                $sql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`";
                mysql_query( $sql, $conn );
                mysql_select_db( DB_NAME, $conn );
            }

            /*Remove Tables */
            foreach( $this->nari100_table as $table ) {
                mysql_query("DROP TABLE `" . DB_NAME . "`.{$table}", $conn);
            }

            /*BEGIN: Restore Database Content*/
            if( file_exists($options[$id]['sql']) )
            {
                $sql_file = file_get_contents($options[$id]['sql'], true);
                $sql_queries = explode(";\n", $sql_file);

                for($i = 0; $i < count($sql_queries); $i++) {
                    mysql_query($sql_queries[$i], $conn);
                }

                unlink($options[$id]['sql']);
            }
        }

        return array(
            'error' => 'success',
            'msg' => __('Backup successfully restored.', 'nari100')
        );
    }
}