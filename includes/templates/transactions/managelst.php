<header role="heading">
    <span class="widget-icon"><i class="fa fa-exchange"></i></span>
    <h2><?php _e('View Transaction', 'nari100'); ?></h2>
</header>
<div class="nari">
    <?php if( $do === 'managelst' || $do === 'pagination' ) { ?>
    <div class="filter">
        <?php _e('Account', 'nari100'); ?>:
        <select data-name="by-account" class="report" data-type="managelst">
            <option value="0"><?php _e('All', 'nari100'); ?></option>
            <?php echo Nari100Accounts::combobox( (isset($flt) ? $flt['by-account'] : 0) ); ?>
        </select>
        | <?php _e('Type', 'nari100'); ?>:
        <select data-name="by-type" class="report" data-type="managelst">
            <option value="0"><?php _e('All', 'nari100'); ?></option>
            <?php foreach( Nari100Transactions::getInstance()->type as $type ) { ?>
            <option value="<?php echo $type; ?>" <?php echo (isset($flt) && $type == $flt['by-type']? 'selected' : ''); ?> ><?php _e($type, 'nari100'); ?></option>
            <?php } ?>
        </select>
        | <?php _e('Payer', 'nari100'); ?>:
        <select data-name="by-payer" class="report" data-type="managelst">
            <option value="0"><?php _e('All', 'nari100'); ?></option>
            <?php echo Nari100Definitions::combobox( (isset($flt) ? $flt['by-payer'] : 0) , 'payers'); ?>
        </select>
        | <?php _e('Payee', 'nari100'); ?>:
        <select data-name="by-payee" class="report" data-type="managelst">
            <option value="0"><?php _e('All', 'nari100'); ?></option>
            <?php echo Nari100Definitions::combobox( (isset($flt) ? $flt['by-payee'] : 0) , 'payees'); ?>
        </select>
        | <?php _e('Method', 'nari100'); ?>:
        <select data-name="by-method" class="report" data-type="managelst">
            <option value="0"><?php _e('All', 'nari100'); ?></option>
            <?php echo Nari100Definitions::combobox( (isset($flt) ? $flt['by-method'] : 0) , 'paymethod'); ?>
        </select>
        <div class="search-list">
            <?php _e('Search keyword in Description', 'nari100'); ?>:
            <input id="search-list" data-type="managelst" value=""/>
            <input id="search-list-submit" type="button" class="button" value="<?php _e('Search'); ?>">
        </div>
        <div class="per-page">
            <?php _e('Transaction Per Page', 'nari100'); ?>:
            <select data-name="per-page" class="report" data-type="managelst">
                <option value="10" <?php if( isset($flt['per-page']) && $flt['per-page'] == '10' ) echo "selected"; ?>>10</option>
                <option value="25" <?php if( isset($flt['per-page']) && $flt['per-page'] == '25' ) echo "selected"; ?>>25</option>
                <option value="50" <?php if( isset($flt['per-page']) && $flt['per-page'] == '50' ) echo "selected"; ?>>50</option>
                <option value="100" <?php if( isset($flt['per-page']) && $flt['per-page'] == '100' ) echo "selected"; ?>>100</option>
            </select>
        </div>
    </div>
    <?php } ?>

    <div class="widget-body no-padding">
        <div class="toolbar"></div>
        <table class="table table-striped" style="width: 100%;">
            <thead>
            <tr>
                <th>
                    <i class="fa fa-calendar fa-fw hidden-md hidden-sm hidden-xs"></i>
                    <?php _e('Date', 'nari100'); ?>
                    <i class="fa fa-sort<?php echo isset($_POST['by']) && isset($_POST['sort']) && $_POST['sort'] === 'date' ? '-' . $_POST['by'] : ''; ?> sort-icon hidden-md hidden-sm hidden-xs" data-field="date"></i>
                </th>
                <th style="width:115px">
                    <i class="fa fa-bank fa-fw hidden-md hidden-sm hidden-xs"></i>
                    <?php _e('Account', 'nari100'); ?>
                    <i class="fa fa-sort<?php echo isset($_POST['by']) && isset($_POST['sort']) && $_POST['sort'] === 'account' ? '-' . $_POST['by'] : ''; ?> sort-icon hidden-md hidden-sm hidden-xs" data-field="account"></i>
                </th>
                <th>
                    <?php _e('Type', 'nari100'); ?>
                </th>
                <th>
                    <i class="fa fa-folder fa-fw hidden-md hidden-sm hidden-xs"></i>
                    <?php _e('Category', 'nari100'); ?>
                </th>
                <th style="width:110px">
                    <i class="fa fa-money fa-fw hidden-md hidden-sm hidden-xs"></i>
                    <?php _e('Amount', 'nari100'); ?>
                </th>
                <th>
                    <?php _e('Payer', 'nari100'); ?>
                </th>
                <th>
                    <?php _e('Payee', 'nari100'); ?>
                </th>
                <th>
                    <?php _e('Method', 'nari100'); ?>
                </th>
                <th>
                    <?php _e('Ref#', 'nari100'); ?>
                </th>
                <th><i class="fa fa-flag fa-fw hidden-md hidden-sm hidden-xs"></i><?php _e('Description', 'nari100'); ?></th>
                <th class="text-right"><?php _e('Dr.', 'nari100'); ?></th>
                <th class="text-right"><?php _e('Cr.', 'nari100'); ?></th>
                <?php if( $do !== 'deletedlst' ) { ?>
                <th class="text-right"><?php _e('Balance', 'nari100'); ?></th>
                <?php } ?>
                <th class="manage"><i class="fa fa-fw fa-cog hidden-md hidden-sm hidden-xs"></i></th>
            </tr>
            </thead>
            <tbody>

            <?php
            if( $lst ) {
                $sumCr = $sumDr = 0;
                foreach( $lst as $record ) {
                    $payer = $payee = $prove = '';
                    $dr = $cr = '0.00';
                    if( $record->transactions_status === 'RepeatLimit' && $record->transactions_date < date("Y-m-d H:i:s") ) {
                        $limit_color = 'danger';
                        $prove = '<a href="#" data-id="' . $record->transactions_id . '" data-page="transactions" class="btn btn-xs btn-danger prove">' . __('Prove', 'nari100') . '</a>';
                    } else {
                        $limit_color = '';
                    }

                    $color = nari100_check_balance( $record->bal, $balance_limit );

                    if( $record->transactions_amount < 0 ) {
                        $icon  = '<i class="fa fa-arrow-circle-down down"></i>';

                        if ( get_option( 'nari100_payee_users') == '0' )
                            $payee = $record->payer;
                        else {
                            $user_info = get_userdata( $record->payer_id );
                            $payee = isset( $user_info->display_name ) ? $user_info->display_name : $record->payer;
                        }
                        $dr    = $record->transactions_amount;
                    } else {
                        $icon  = '<i class="fa fa-arrow-circle-up up"></i>';

                        if ( get_option( 'nari100_payer_users') == '0' )
                            $payer = $record->payer;
                        else {
                            $user_info = get_userdata( $record->payer_id );
                            $payer = isset( $user_info->display_name ) ? $user_info->display_name : $record->payer;
                        }
                        $cr    = $record->transactions_amount;
                    }
                    $sumCr += $cr;
                    $sumDr += $dr;

                    ?>
                    <tr id="record<?php echo $record->transactions_id; ?>" class="<?php echo $limit_color; ?> record">
                        <td><?php echo $record->transactions_date; ?></td>
                        <td><?php echo $record->accounts_name; ?></td>
                        <td><?php _e( $record->transactions_type, 'nari100'); ?></td>
                        <td><?php echo $record->cat; ?></td>
                        <td class="price-align price-sign"><?php echo $icon . ' ' . nari100_price($record->transactions_amount); ?></td>
                        <td><?php echo $payer; ?></td>
                        <td><?php echo $payee; ?></td>
                        <td><?php echo $record->method; ?></td>
                        <td><?php echo $record->transactions_ref; ?></td>
                        <td><?php echo $record->transactions_description; ?></td>
                        <td class="price-align"><?php echo nari100_price($dr); ?></td>
                        <td class="price-align"><?php echo nari100_price($cr); ?></td>
                        <?php if( $do !== 'deletedlst' ) { ?>
                        <td class="price-align <?php echo $color; ?>"><?php echo nari100_price($record->bal); ?></td>
                        <?php } ?>
                        <td>
                            <a href="#" data-id="<?php echo $record->transactions_id; ?>" data-page="transactions" class="btn btn-xs btn-primary edit"><?php _e('Edit', 'nari100'); ?></a>
                            <?php echo $prove; ?>
                        </td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan="10" class="sum-cell">
                            <?php _e('Total:', 'nari100'); ?>
                        </td>
                        <td><?php echo nari100_price($sumDr); ?></td>
                        <td><?php echo nari100_price($sumCr); ?></td>
                        <td colspan="2"></td>
                    </tr>
            <?php } else { ?>
                <tr>
                    <td colspan="14"><?php _e('The list is empty', 'nari100'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php include NARI_ACCOUNTANT_DIR_PLUGIN . 'includes/templates/navigation.php'; ?>
    </div>
</div>