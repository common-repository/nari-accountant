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
                            <label class="label" for="name"><?php echo sprintf( __('Name [%s]', 'nari100'), $sub_menu[$type]['name']); ?></label>
                            <label class="input">
                                <i class="icon-append <?php echo $sub_menu[$type]['icon']; ?>"></i>
                                <input type="text" class="form-control" id="name" name="definitions_name" value="<?php echo $name; ?>" />
                            </label>
                        </section>
                    </div>
                </fieldset>

                <footer>
                    <input type="hidden" name="action" value="nari100" />
                    <input type="hidden" name="page" value="definitions" />
                    <input type="hidden" name="do" value="addrec" />
                    <input type="hidden" name="definitions_type" value="<?php echo $type; ?>" />
                    <input type="hidden" name="definitions_order" value="<?php echo $order; ?>" />
                    <input type="hidden" name="definitions_id" value="<?php echo $id; ?>" />
                    <button type="submit" class="btn btn-primary"><?php _e('Submit', 'nari100'); ?></button>
                    <?php if ( $id ) { ?>
                        <button type="button" class="btn btn-default cancel-edit"><?php _e('Cancel', 'nari100'); ?></button>
                    <?php } ?>
                </footer>
            </form>
        </div>
    </div>
</div>