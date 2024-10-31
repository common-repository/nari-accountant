<!-- Modal -->
<div class="modal fade" id="delConfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php _e('Confirm Box', 'nari100'); ?></h4>
            </div>
            <div class="modal-body">
                <?php _e('Are you sure to delete "<b></b>"?', 'nari100'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('NO, That was a mistake', 'nari100'); ?></button>
                <button type="button" class="btn btn-primary" id="yes-delete"><?php _e('YES, Please', 'nari100'); ?></button>
            </div>
        </div>
    </div>
</div>