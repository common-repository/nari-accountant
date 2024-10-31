<form role="form" class="ajax plugin-options" method="post" action="">
    <div class="form-group">
        <label for="dec_point"><?php _e( 'Decimal Point', 'nari100' ); ?></label>
        <input type="text" class="form-control" id="dec_point" name="dec_point" value="<?php echo $dec; ?>">
    </div>
    <div class="form-group">
        <label for="thousands_sep"><?php _e( 'Thousands Separator', 'nari100' ); ?></label>
        <input type="text" class="form-control" id="thousands_sep" name="thousands_sep" value="<?php echo $thousands; ?>">
    </div>
    <div class="form-group">
        <label for="currency"><?php _e( 'Currency Code', 'nari100' ); ?></label>
        <input type="text" class="form-control" id="currency" name="currency" value="<?php echo $currency; ?>">
        <span class="help-block"><?php _e( 'Keep it blank if you do not want to show currency code', 'nari100' ); ?></span>
    </div>
    <div class="form-group">
        <label for="bal_limit"><?php _e( 'Balance Limit Exceed', 'nari100' ); ?></label>
        <input type="text" class="form-control" id="bal_limit" name="bal_limit" value="<?php echo $balance_limit; ?>">
        <span class="help-block"><?php _e( 'If your account balance go lower than this amount, system will show you alarm.', 'nari100' ); ?></span>
    </div>
    <div class="form-group">
        <label for="payee_users"><?php _e( 'Payee', 'nari100' ); ?>:</label>
        <input type="checkbox" class="form-control" id="payee_users" name="payee_users" value="1" <?php echo $userPayee ? 'checked="checked"' : ''; ?>>
        <span class="help-block"><?php _e( 'If you check this checkbox when you are adding Expense in Transactions menu your payee will be selected from Wordpress Users.', 'nari100' ); ?></span>
    </div>
    <div class="form-group">
        <label for="payer_users"><?php _e( 'Payer', 'nari100' ); ?>:</label>
        <input type="checkbox" class="form-control" id="payer_users" name="payer_users" value="1" <?php echo $userPayer ? 'checked="checked"' : ''; ?>>
        <span class="help-block"><?php _e( 'If you check this checkbox when you are adding Deposit in Transactions menu your payer will be selected from Wordpress Users.', 'nari100' ); ?></span>
    </div>
    <button type="submit" class="btn btn-primary"><?php _e('Submit', 'nari100' ); ?></button>
    <input type="hidden" name="action" value="nari100" />
    <input type="hidden" name="page" value="settings" />
    <input type="hidden" name="do" value="saveopt" />
</form>