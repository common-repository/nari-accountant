<?php
/*
 *
 */
function nari100_register_menu() {

    $page_title		=	__('Nari Accountant', 'nari100');
    $menu_title		=	__('Nari Accountant', 'nari100');
    $capability		=	'manage_options';
    $menu_slug		=	'nari100';
    $function		=	'nari100_dashboard';
    $icon			=	NARI_ACCOUNTANT_URL_PLUGIN . 'assets/images/logo.png';
    add_menu_page($page_title,$menu_title,$capability,$menu_slug,$function,$icon,'8.101');

    foreach( nari100_pages( true ) as $item ){
        $name = ucfirst($item);
        $page 	= add_submenu_page(
            'nari100',
            __( $name, 'nari100' ),
            __( $name, 'nari100' ),
            'edit_pages',
            'nari100_' . $item,
            'Nari100' . $name . '::Admin'
        );
        //Include css or js scripts on pages load
        add_action( 'admin_print_scripts-'.$page, 'nari100_admin_print_scripts' );
    }
}

/*
 * Enqueue Plugin Scripts and Styles
 */
function nari100_admin_print_scripts() {
    //** Admin Scripts
    wp_enqueue_script( 'nari100-bs-scripts', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/javascript/bootstrap.min.js', array('jquery' ), '1.0', true );
    wp_enqueue_script( 'nari100-admin', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/javascript/admin.js', array('jquery' ), '1.0', true );
    wp_enqueue_script( 'nari100-moment', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/javascript/moment.js', array('jquery' ), '1.0', true );
    wp_enqueue_script( 'nari100-datepiker', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/javascript/bootstrap-datetimepicker.js', array('jquery' ), '1.0', true );

    //** Admin Styles
    wp_enqueue_style( 'nari100-bs-css', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/styles/bootstrap.min.css' );
    wp_enqueue_style( 'nari100-admin-css', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/styles/sb-admin.css' );
    wp_enqueue_style( 'nari100-fa-css', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/styles/font-awesome.min.css' );
    wp_enqueue_style( 'nari100-bs-datepiker', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/styles/bootstrap-datetimepicker.css' );
    wp_enqueue_style( 'nari100-new', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/styles/nari.css' );
    if ( is_rtl() ) {
        wp_enqueue_style( 'nari100-admin-rtl-css', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/styles/rtl.css' );
    }

//    wp_localize_script( 'nari100-admin', 'WPAkismet', array(
//        'comment_author_url_nonce' => wp_create_nonce( 'comment_author_url_nonce' ),
//        'strings' => array(
//            'Remove this URL' => __( 'Remove this URL' , 'akismet')
//        )
//    ) );
}

/*
 * Load Styles for all admin panel
 */
function nari100_admin_scripts() {
    //** Admin Styles
    wp_enqueue_style( 'nari100-all-css', NARI_ACCOUNTANT_URL_PLUGIN . 'assets/styles/all.css' );
}

/*
 * Ajax load functionality
 */
function nari100_ajax() {
    $response  = array(
        'result' => false,
        'msg' => 'The page is not valid!'
    );

    if( in_array( $_POST['page'], nari100_pages( true ) ) ){

        $response  = array(
            'result' => false,
            'msg' => 'The action is not valid!'
        );

        $actions = array(
            'addfrm', 'addrec', 'balancelst', 'managelst', 'delete',
            'edit', 'saveopt', 'Expense', 'Income', 'Transfer', 'repeatlst',
            'general', 'backupfrm', 'getbackup', 'backuprst', 'backupdel',
            'pagination', 'change-status', 'deletedlst'
        );

        if( in_array( $_POST['do'], $actions) ) {
            $response = array(
                'result' => true,
                'msg' => call_user_func('Nari100' . ucfirst($_POST['page']) . '::Admin')
            );
        }

    }

    echo json_encode( $response );
    die();
}

/*
 * This function show dashboard
 */
function nari100_dashboard(){
    include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/dashboard.php';
}

/*
 * The system notification
 */
function nari100_notification() {
    $message = Nari100Accounts::getInstance()->notification();

    if ( $message ) {
        $title = __('Nari Accountant Notification', 'nari100');
        echo "<div class=\"error\"> <p><strong>$title</strong><br/>$message</p></div>";
    }
}