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
                            <label class="label" for="account"><?php _e('Account Name', 'nari100'); ?></label>
                            <label class="input">
                                <i class="icon-append fa fa-bank"></i>
                                <input type="text" id="account" name="accounts_name" value="<?php echo $name; ?>" />
                            </label>
                        </section>
                    </div>
                    <div class="row">
                        <section>
                            <label class="label" for="description"><?php _e('Description', 'nari100'); ?></label>
                            <label class="input">
                                <i class="icon-append fa fa-flag"></i>
                                <input type="text" class="form-control" id="description" name="accounts_description" value="<?php echo $desc; ?>" />
                            </label>
                        </section>
                    </div>
                    <?php if ( !$id ) { ?>
                    <div class="row">
                            <section>
                            <label class="label"for="balance"><?php _e('Init. Balance', 'nari100'); ?></label>
                            <label class="input">
                                <i class="icon-append fa fa-hashtag"></i>
                                <input type="text" class="form-control" id="balance" name="accounts_balance" />
                            </label>
                        </section>
                    </div>
                    <?php } ?>
                </fieldset>

                <footer>
                    <input type="hidden" name="action" value="nari100" />
                    <input type="hidden" name="page" value="accounts" />
                    <input type="hidden" name="do" value="addrec" />
                    <input type="hidden" name="accounts_id" value="<?php echo $id; ?>" />
                    <button type="submit" class="btn btn-primary"><?php _e('Submit', 'nari100'); ?></button>
                    <?php if ( $id ) { ?>
                        <button type="button" class="btn btn-default cancel-edit"><?php _e('Cancel', 'nari100'); ?></button>
                        <input type="hidden" name="accounts_balance" value="" />
                    <?php } ?>
                </footer>
            </form>
        </div>
    </div>
</div>