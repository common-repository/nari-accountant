<?php
/**
 * @category   class
 * @package    Nari100Pagination
 * @author     Seyed Shahrokh Nabavi <info@nabavi.nl>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0
 */

class Nari100Pagination {

    /**
     * @var
     */
    public $total_record;

    /**
     * @var
     */
    public $per_page;

    /**
     * @var
     */
    public $page_offset;

    /**
     * @var
     */
    public $offset;

    /**
     * @var
     */
    public $current;

    /**
     * @var
     */
    public $last_page;

    /**
     *
     */
    public function __construct( $sql, $perPage = 3, $pageOffset = 1 ){
        $this->per_page    = $perPage;
        $this->page_offset = $pageOffset;

        global $wpdb;
        $wpdb->get_results( $sql );
        $this->total_record = $wpdb->num_rows;
        $wpdb->flush();

        $this->last_page = ceil($this->total_record / $this->per_page);

        $cPage = isset($_POST['p']) ? intval($_POST['p']) : 1;
        $this->current = $cPage <= 1 ? 1 : ($cPage > $this->last_page ? $this->last_page : $cPage);
        $this->offset  = ($this->current-1) * $this->per_page;
    }

    public function until(){
        $til = $this->offset + $this->per_page;
        return $til > $this->total_record ? $this->total_record : $til;
    }
}