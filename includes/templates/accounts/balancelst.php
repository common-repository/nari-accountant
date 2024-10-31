<header role="heading">
    <span class="widget-icon"><i class="fa fa-table"></i></span>
    <h2><?php _e('Accounts Balances', 'nari100'); ?></h2>
</header>
<div class="nari">
    <div class="widget-body no-padding">
        <div class="toolbar"></div>
        <table class="table table-striped" style="width: 100%;">
            <thead>
            <tr>
                <th><i class="fa fa-fw fa-bank hidden-md hidden-sm hidden-xs"></i><?php _e('Account Name', 'nari100'); ?></th>
                <th><i class="fa fa-fw fa-flag hidden-md hidden-sm hidden-xs"></i><?php _e('Description', 'nari100'); ?></th>
                <th><i class="fa fa-fw fa-hashtag hidden-md hidden-sm hidden-xs"></i><?php _e('Init. Balance', 'nari100'); ?></th>
                <th><?php _e('Current Balance', 'nari100'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if( $lst ) {
                foreach( $lst as $account ) {
                    $cBalance = Nari100Transactions::get_current_balance($account->accounts_id);
                    $sum += $cBalance;
                    $limitColor = $limitText  = '';
                    if ( $cBalance < 0 ) {
                        $limitText  = ' ( UNDER ZERO )';
                    } else if ( $cBalance < $balance_limit ) {
                        $limitText  = ' ( < ' . nari100_price($balance_limit) . ' )';
                    }
                    $limitColor = nari100_check_balance($cBalance, $balance_limit);
                    ?>
                    <tr class="record <?php echo $limitColor; ?>">
                        <td><?php echo $account->accounts_name; ?></td>
                        <td><?php echo $account->accounts_description; ?></td>
                        <td><?php echo nari100_price( $account->accounts_balance ); ?></td>
                        <td><?php echo nari100_price( $cBalance ) . $limitText; ?></td>
                    </tr>
                <?php } $class = $sum > 0 ? 'text-success' : 'text-danger'; ?>
                <tr class="total">
                    <td colspan="3" class="sum"><?php _e('Total', 'nari100');?></td>
                    <td class="<?php echo $class; ?>"><?php echo nari100_price( $sum ); ?></td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td colspan="4"><?php _e('The list is empty', 'nari100'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
