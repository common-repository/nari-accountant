<?php if ( $id !== 0 && $type === 'Transfer' ) { ?>
    <div class="col-sm-12">
        <?php _e('In this version you can not edit transfer transaction.', 'nari100'); ?>
        <button type="button" class="btn btn-default cancel-edit"><?php _e('Cancel', 'nari100'); ?></button>
    </div>
<?php return; } ?>

<div class="color-blueLight">
    <header>
        <?php if ( $id ) { ?>
            <span class="widget-icon"><i class="fa fa-edit"></i></span>
            <h2><?php _e('Edit', 'nari100'); ?></h2>
        <?php } else { ?>
            <span class="widget-icon"><i class="fa fa-plus"></i></span>
            <h2><?php _e('Add', 'nari100'); ?></h2>
        <?php } ?>
    </header>
    <div class="nari">
        <div class="widget-body no-padding">
            <form action="" method="post" class="form ajax">
                <fieldset>
                    <div class="row">
                        <section>
                            <label for="account" class="label"><?php _e('From Account', 'nari100');?></label>
                            <label class="select">
                                <select id="account" name="accounts_id" class="select2-offscreen">
                                    <?php echo Nari100Accounts::combobox( $account_id ); ?>
                                </select>
                                <i></i>
                            </label>
                        </section>
                    </div>

                    <?php if ( $type === 'Transfer' ) { ?>
                        <div class="row">
                            <section>
                                <label for="account" class="label"><?php _e('To Account', 'nari100'); ?></label>
                                <label class="select">
                                    <select id="account" name="to_accounts_id" class="select2-offscreen">
                                        <option value="0" selected><?php _e('Choose one account please.', 'nari100'); ?></option>
                                        <?php echo Nari100Accounts::combobox( $account_id ); ?>
                                    </select>
                                    <i></i>
                                </label>
                            </section>
                        </div>
                    <?php } ?>


                    <?php if ( $id === 0 && $type !== 'Transfer' ) { ?>
                        <div class="row">
                            <section>
                                <label for="repeat" class="label"><?php _e('Frequency', 'nari100'); ?></label>
                                <label class="select">
                                    <select class="select2-offscreen" id="repeat" name="repeat">
                                        <option value="0">Once Only</option>
                                        <option value="1">Everyday</option>
                                        <option value="2">Weekly</option>
                                        <option value="3">Every 2 Week</option>
                                        <option value="4">Monthly</option>
                                        <option value="5">Every 2 Month</option>
                                        <option value="6">Quarterly ( Every 3 Month )</option>
                                        <option value="7">Every 6 Month</option>
                                        <option value="8">Yearly</option>
                                    </select>
                                    <i></i>
                                </label>
                            </section>
                        </div>

                        <div class="row repeat-number">
                            <section>
                                <label for="repeat_number" class="label"><?php _e('Repetitions', 'nari100'); ?></label>
                                <label class="input">
                                    <input type="text" class="form-control" id="repeat_number" name="repeat_number" />
                                </label>
                            </section>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <section>
                            <label for="date" class="label hide" id="repeatdate"><?php _e('The first occurrence date', 'nari100'); ?></label>
                            <label for="date" class="label" id="dateLable"><?php _e('Date', 'nari100'); ?></label>
                            <label class="input-append date input">
                                <i class="icon-prepend fa fa-calendar"></i>
                                <input type="text" class="form-control" value="<?php echo $date; ?>" name="transactions_date" id="date" />
                            </label>
                        </section>
                    </div>

                    <div class="row">
                        <section>
                            <label for="amount" class="label"><?php _e('Amount', 'nari100'); ?></label>
                            <label class="input">
                                <input type="text" class="form-control" id="amount" name="transactions_amount" value="<?php echo nari100_price($amt, false); ?>">
                            </label>
                        </section>
                    </div>

                    <?php if ( $type !== 'Transfer' ) { ?>
                        <div class="row">
                            <section>
                                <label for="payer" class="label"><?php _e($titles, 'nari100'); ?></label>
                                <label class="select">
                                    <?php if ( $category[1] == 'payees' && get_option( 'nari100_payee_users') == '0' ) { ?>
                                        <select id="payer" name="payer_id" class="select2-offscreen">
                                            <?php echo Nari100Definitions::combobox($payer_id, $category[1]); ?>
                                        </select>
                                    <?php
                                    } elseif ( $category[1] == 'payers' && get_option( 'nari100_payer_users') == '0' ) { ?>
                                        <select id="payer" name="payer_id" class="select2-offscreen">
                                            <?php echo Nari100Definitions::combobox($payer_id, $category[1]); ?>
                                        </select>
                                    <?php
                                    } else {
                                        wp_dropdown_users(array('name' => 'payer_id','id'=>'payer','class'=>'select2-offscreen'));
                                    } ?>
                                    <i></i>
                                </label>
                            </section>
                        </div>
                        <div class="row">
                            <section>
                                <label for="cats" class="label"><?php _e('Category', 'nari100'); ?></label>
                                <label class="select">
                                    <select id="cats" name="category_id" class="select2-offscreen">
                                        <?php echo Nari100Definitions::combobox( $category_id, $category[0] ); ?>
                                    </select>
                                    <i></i>
                                </label>
                            </section>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <section>
                            <label for="pmethod" class="label"><?php _e('Payment Method', 'nari100'); ?></label>
                            <label class="select">
                                <select id="pmethod" name="method_id" class="select2-offscreen">
                                    <?php echo Nari100Definitions::combobox( $method_id, 'paymethod' ); ?>
                                </select>
                                <i></i>
                            </label>
                        </section>
                    </div>

                    <div class="row">
                        <section>
                            <label for="ref" class="label"><?php _e('Ref#', 'nari100'); ?></label>
                            <label class="input">
                                <input type="text" class="form-control" id="ref" name="transactions_ref" value="<?php echo $ref; ?>">
                            </label>
                            <span class="note"><?php _e('e.g. Transaction ID, Check No.', 'nari100'); ?></span>
                        </section>
                    </div>

                    <div class="row">
                        <section>
                            <label for="description" class="label"><?php _e('Description', 'nari100'); ?></label>
                            <label class="input">
                                <input type="text" class="form-control" id="description" name="transactions_description" value="<?php echo $des; ?>">
                            </label>
                        </section>
                    </div>
                </fieldset>

                <footer>
                    <button type="submit" class="btn btn-primary"><?php _e('Submit', 'nari100'); ?></button>
                    <?php if ( $id ) { ?>
                        <button type="button" class="btn btn-default cancel-edit"><?php _e('Cancel', 'nari100'); ?></button>
                        <?php if ( $stu === 'Uncleared' ) { ?>
                        <button type="button" class="btn btn-warning change-status" data-id="<?php echo $id; ?>" data-status="restore"><?php _e('Restore It', 'nari100'); ?></button>
                        <?php } else { ?>
                        <button type="button" class="btn btn-danger change-status" data-id="<?php echo $id; ?>" data-status="remove"><?php _e('Remove It', 'nari100'); ?></button>
                        <?php } ?>
                    <?php } ?>
                    <input type="hidden" name="action" value="nari100" />
                    <input type="hidden" name="page" value="transactions" />
                    <input type="hidden" name="do" value="addrec" />
                    <input type="hidden" name="transactions_type" value="<?php echo $type; ?>" />
                    <input type="hidden" name="id" value="<?php echo $id; ?>" />
                </footer>
            </form>
        </div>
    </div>
</div>