<div class="page-header" data-page="<?php echo strtolower($title); ?>">
    <h1><i class="<?php echo isset($page_icon) ? $page_icon : ''; ?>"></i><?php _e( $title, 'nari100' ); ?></h1>
</div>
<div class="navbar navbar-default">
    <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
            <?php
            foreach ( $sub_menu as $key=>$item ) {
                $item['selected'] .= sizeof($item['sub_menu']) ? ' dropdown' : '';
            ?>
            <li class="<?php echo $item['selected']; ?>" data-do="<?php echo $key; ?>">
                <?php if( sizeof($item['sub_menu']) ) { ?>
                    <a id="sm-<?php echo $key; ?>" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <?php echo $item['name']; ?>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="sm-<?php echo $key; ?>">
                        <?php foreach ( $item['sub_menu'] as $subkey=>$item ) { ?>
                        <li data-do="<?php echo $subkey; ?>" data-type="<?php echo $key; ?>"><a href="#"><?php echo $item; ?></a></li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                <a href="#"><?php echo $item['name']; ?></a>
                <?php } ?>
            </li>
            <?php } ?>
        </ul>
        <div id="ajax-load-img"><img src="<?php echo NARI_ACCOUNTANT_URL_PLUGIN; ?>assets/images/ajax.gif" /> <?php _e( 'Please wait...', 'nari100' ); ?></div>
    </div>
    <!--/.nav-collapse -->
</div>
<div class="work-area">
    <?php include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/' . $file; ?>
</div>
<?php
include 'alert.php';