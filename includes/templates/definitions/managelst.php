<header role="heading">
    <span class="widget-icon"><i class="<?php echo $sub_menu[$type]['icon']; ?>"></i></span>
    <h2><?php _e('Expense Categories', 'nari100'); ?></h2>
</header>
<div class="nari" data-type="<?php echo $type; ?>">
    <div class="widget-body no-padding">
        <div class="toolbar"></div>
        <table class="table table-striped" style="width: 100%;">
            <thead>
            <tr>
                <th><i class="<?php echo $sub_menu[$type]['icon']; ?> fa-fw hidden-md hidden-sm hidden-xs"></i><?php _e('Name', 'nari100'); ?></th>
                <th class="manage"><i class="fa fa-fw fa-cog hidden-md hidden-sm hidden-xs"></i><?php _e('Manage', 'nari100'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if( $lst ) {
                foreach( $lst as $record ) { ?>
                    <tr id="record<?php echo $record->definitions_id; ?>" class="record">
                        <td><?php echo $record->definitions_name; ?></td>
                        <td class="manage">
                            <a href="#" class="btn btn-warning edit" data-id="<?php echo $record->definitions_id; ?>" data-page="definitions" ><i class="fa fa-edit"></i></a>
                            <a href="#" class="btn btn-danger delete" data-id="<?php echo $record->definitions_id; ?>" data-page="definitions" data-title="<?php echo $record->definitions_name; ?>" data-toggle="modal" data-target="#delConfirm"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="2"><?php _e('The list is empty', 'nari100'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/navigation.php'; ?>
    </div>
</div>