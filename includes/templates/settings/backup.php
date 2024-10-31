<?php
global $wpdb;

$options = get_option('nari100_backups');
$new_opt = array();
?><div class="panel panel-info">
    <div class="panel-body">

        <div class="tab-content">
            <div class="tab-pane active"  id="db_home">
                <p class="submit">
                    <a href="#" class="button-primary backup" data-do="getbackup">
                        <?php _e('Create Backup', 'nari100'); ?>
                    </a>
                </p>

                <?php if($options) { ?>
                <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline" role="grid">
                    <table class="table table-striped table-bordered table-hover display" id="backup-list">
                        <thead>
                        <tr class="wpdb-header">
                            <th class="manage-column" scope="col" width="25%"><?php _e('Date', 'nari100'); ?></th>
                            <th class="manage-column" scope="col" width="15%"><?php _e('Backup File', 'nari100'); ?></th>
                            <th class="manage-column" scope="col" width="10%"><?php _e('Size', 'nari100'); ?></th>
                            <th class="manage-column" scope="col" width="15%"></th>
                            <th class="manage-column" scope="col" width="15%"></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $count = 0;
                        foreach($options as $option) {
                            if( file_exists($option['dir']) ) {
                                ?>
                                <tr id="record<?php echo $count; ?>">
                                    <td>
                                        <?php echo date('jS, F Y', $option['date']); ?>
                                        - <?php echo date('h:i:s A', $option['date']); ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $option['url']; ?>">
                                            <?php _e('Download', 'nari100'); ?>
                                        </a>
                                    </td>
                                    <td><?php echo round($option["size"]/1024,2); ?></td>
                                    <td>
                                        <a href="#" class="button-secondary backup" data-do="backupdel" data-id="<?php echo $count; ?>">
                                            <?php _e('Remove Backup', 'nari100'); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="#" class="button-secondary backup" data-do="backuprst" data-id="<?php echo $count; ?>">
                                            <?php _e('Restore Backup', 'nari100'); ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                $count++;
                                $new_opt[] = $option;
                            }
                        }
                        update_option('nari100_backups', $new_opt);
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } else { ?>
                <p><?php _e('No Backups Created!', 'nari100'); ?></p>
            <?php } ?>
        </div>
    </div>

</div>