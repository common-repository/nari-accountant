<?php
$lPage  = $pagination->last_page;
$cPage  = $pagination->current;
$offset = $pagination->page_offset;

$prev = ($cPage == 1) ? 0 : $cPage - 1;
$next = ($cPage == $lPage) ? 0 : $cPage + 1;
?><div class="toolbar-footer">
    <div class="col-sm-6 col-xs-12 hidden-xs page-nav-desc">
        Showing
        <span class="txt-color-darken"><?php echo $pagination->offset+1; ?></span> to
        <span class="txt-color-darken"><?php echo $pagination->until(); ?></span> of
        <span class="text-primary"><?php echo $pagination->total_record; ?></span> entries
    </div>
    <div class="col-xs-12 col-sm-6 page-nav-btn">
        <ul class="pagination pagination-sm">
            <li class="paginate_button previous<?php echo ($prev ? '' : ' disabled'); ?>" data-page="<?php echo $prev; ?>">
                <a href="#">Previous</a>
            </li>
            <?php
            $count = $offset*2+1;
            $i = $cPage - $offset;
            if( $cPage + $offset > $lPage )
                $i = $cPage - 1 - $offset;
            if ( $i < 2 )
                $i = 1;


            if( $i == 2 ){
                echo '<li class="paginate_button" data-page="1"><a href="#">1</a></li>';
            }else if( $i > 2 ){
                echo '<li class="paginate_button" data-page="1"><a href="#">1</a></li>';
                echo '<li class="paginate_button disabled" data-page="0"><a href="#">…</a></li>';
            }

            for( ; $count > 0  && $i <= $lPage; $i++, $count-- ) {
                $active = $i === $cPage ? 'active' : '';
                echo '<li class="paginate_button '. $active . '" data-page="' . $i . '"><a href="#">' . $i . '</a></li>';
            }

            if ( $i == $lPage ) {
                echo '<li class="paginate_button" data-page="' . $lPage . '"><a href="#">' . $lPage . '</a></li>';
            } else
            if ( $i < $lPage ) {
                echo '<li class="paginate_button disabled" data-page="0"><a href="#">…</a></li>';
                echo '<li class="paginate_button" data-page="' . $lPage . '"><a href="#">' . $lPage . '</a></li>';
            }?>

            <li class="paginate_button next<?php echo ($next ? '' : ' disabled'); ?>" data-page="<?php echo $next; ?>">
                <a href="#">Next</a>
            </li>
        </ul>
    </div>
</div>