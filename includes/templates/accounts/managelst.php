<header role="heading">
    <span class="widget-icon"><i class="fa fa-cogs"></i></span>
    <h2><?php _e('Manage Account', 'nari100'); ?></h2>
</header>
<div class="nari">
    <div class="widget-body no-padding">
        <div class="toolbar"></div>
        <table class="table table-striped" style="width: 100%;">
            <thead>
            <tr>
                <th><i class="fa fa-fw fa-bank hidden-md hidden-sm hidden-xs"></i><?php _e('Account Name', 'nari100'); ?></th>
                <th><i class="fa fa-fw fa-flag hidden-md hidden-sm hidden-xs"></i><?php _e('Description', 'nari100'); ?></th>
                <th class="manage"><i class="fa fa-fw fa-cog hidden-md hidden-sm hidden-xs"></i><?php _e('Manage', 'nari100'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if( $lst ) {
                foreach( $lst as $account ) { ?>
                    <tr id="record<?php echo $account->accounts_id; ?>" class="record">
                        <td><?php echo $account->accounts_name; ?></td>
                        <td><?php echo $account->accounts_description; ?></td>
                        <td class="manage">
                            <a href="#" class="btn btn-warning edit" data-id="<?php echo $account->accounts_id; ?>" data-page="accounts" ><i class="fa fa-edit"></i></a>
                            <a href="#" class="btn btn-danger delete" data-id="<?php echo $account->accounts_id; ?>" data-page="accounts" data-title="<?php echo $account->accounts_name; ?>" data-toggle="modal" data-target="#delConfirm"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="3"><?php _e('The list is empty', 'nari100'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>