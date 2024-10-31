jQuery(document).ready(function($) {
    DoDate();

    $('ul.nav.navbar-nav li').click(function(e){
        if ( $(this).hasClass('dropdown') )
            return true;

        var li = $(this);
        if ( li.parent('ul').hasClass('dropdown-menu') ) {
            li = li.closest('li.dropdown');
        }
        li.siblings().removeClass('active');
        li.addClass('active');

        data = {
            'action': 'nari100',
            'page': $('.page-header').data('page'),
            'do' : $(this).data('do'),
            'type' : $(this).data('type')
        };

        AjaxSend(data, 'load-page');

        $(this).dropdown('toggle');
        return false;
    });


    $('.work-area').on('submit', 'form.ajax', function(e){
        e.preventDefault();

        data = $(this).serialize();

        AjaxSend(data, 'add');
        return false;
    });


    $('#delConfirm').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        modal.find('.modal-body b').html( button.data('title') );
        modal.find('#yes-delete').data( 'id', button.data('id')).data( 'page', button.data('page'));
    })
        .on('shown.bs.modal', function (event) {
        $('#yes-delete').focus();
    })
        .on('click', '#yes-delete', function(){
        var button = $(this);
        $('#delConfirm').modal('hide');

        data = {
            'action': 'nari100',
            'page': button.data('page'),
            'do' : 'delete',
            'id' : button.data('id')
        };
        AjaxSend(data, 'delete');
    });


    $('.work-area').on('change', '#repeat', function(e){
        var index = parseInt($(this).val());

        if( index ) {
            $('.repeat-number').slideDown(300);
            $('#repeatdate').removeClass('hide');
            $('#dateLable').addClass('hide');
        } else {
            $('.repeat-number').slideUp(200);
            $('#dateLable').removeClass('hide');
            $('#repeatdate').addClass('hide');
        }
    });


    $('.work-area').on('click', '.cancel-edit', function(e){
        e.preventDefault();
        CloseEditWarp(0);
    });


    $('.work-area').on('click', '.change-status', function(e){
        e.preventDefault();

        var button = $(this);

        data = {
            'action' : 'nari100',
            'page'   : $('.page-header').data('page'),
            'do'     : 'change-status',
            'status' : button.data('status'),
            'id'     : button.data('id')
        };
        AjaxSend(data, 'change-status');
    });


    $('.work-area').on('click', '.edit', function(e){
        e.preventDefault();

        var button = $(this),
            tr = button.closest('tr'),
            col = tr.children().length;

        if( $('#edit-form-warp').length ) {
            CloseEditWarp(0);
        }

        $('<tr id="edit-form-warp"><td colspan="' + col + '" style="background-color: #FDEDD7"><div style="display: none;"></div></td></tr>').appendTo('body');

        data = {
            'action': 'nari100',
            'page': button.data('page'),
            'do' : 'edit',
            'id' : button.data('id')
        };
        AjaxSend(data, 'edit');
    });


    $('.work-area').on('click', '.backup', function(e){
        e.preventDefault();

        var button = $(this);

        data = {
            'action': 'nari100',
            'page': 'settings',
            'do' : button.data('do'),
            'id' : button.data('id')
        };
        AjaxSend(data, button.data('do'));
    });


    $('.work-area').on('click', '.paginate_button', function(e){
        e.preventDefault();

        var button = $(this),
            page   = button.data('page');

        if( parseInt(page) == 0 )
            return;

        var filter = {};
        $('.report').each( function(){
            filter[$(this).data('name')] = $(this).val();
        });

        data = {
            'action': 'nari100',
            'page': $('.page-header').data('page'),
            'do'  : 'pagination',
            'p'   : page,
            'type'   : $('.nari').data('type'),
            'flt': filter,
            'search': $('#search-list').val()
        };
        AjaxSend(data, 'load-page');
    });


    $('.work-area').on('change', '.report', function(e){
        e.preventDefault();

        var filter = $(this),
            type   = filter.data('type');

        var filter = {};
        $('.report').each( function(){
            filter[$(this).data('name')] = $(this).val();
        });

        data = {
            'action': 'nari100',
            'page': $('.page-header').data('page'),
            'do'  : type,
            'flt': filter,
            'search': $('#search-list').val()
        };
        AjaxSend(data, 'load-page');
    });


    $('.work-area').on('click', '.sort-icon', function(e){
        e.preventDefault();

        var sort = $(this);

        if( sort.hasClass('fa-sort') ){
            $('.sort-icon').removeClass('fa-sort-asc').removeClass('fa-sort-desc').addClass('fa-sort');
            sort.removeClass('fa-sort').addClass('fa-sort-asc');
            orderby = 'asc';
        } else if( sort.hasClass('fa-sort-asc') ) {
            sort.removeClass('fa-sort-asc').addClass('fa-sort-desc');
            orderby = 'desc';
        } else if( sort.hasClass('fa-sort-desc') ) {
            sort.removeClass('fa-sort-desc').addClass('fa-sort-asc');
            orderby = 'asc';
        }

        var filter = {};
        $('.report').each( function(){
            filter[$(this).data('name')] = $(this).val();
        });

        data = {
            'action': 'nari100',
            'page': $('.page-header').data('page'),
            'do'  : 'managelst',
            'sort': sort.data('field'),
            'by'  : orderby,
            'flt': filter
        };
        AjaxSend(data, 'load-page');
    });


    $('.work-area').on('click', '#search-list-submit', function(e){
        e.preventDefault();

        var search = $('#search-list'),
            type   = search.data('type');

        console.log( search.val() );
        var filter = {};
        $('.report').each( function(){
            filter[$(this).data('name')] = $(this).val();
        });

        data = {
            'action': 'nari100',
            'page': $('.page-header').data('page'),
            'do'  : type,
            'flt': filter,
            'search': search.val()
        };
        AjaxSend(data, 'load-page');
    });


    function AjaxSend(data, type){
        $('#ajax-load-img').show();
        $('.work-area .alert').remove();

        $.post(ajaxurl, data, function(respond){
            $('#ajax-load-img').hide();
            if( respond.result ){
                DoRespond(type, respond.msg);
            } else {
                console.log(respond);
                alert(respond.msg);
                return false;
            }
        }, 'json');
    }


    function DoRespond(type, msg){
        switch( type ){
            case 'load-page':
                $('.work-area').html(msg);
                DoDate();
                break;
            case 'edit':
                $('#edit-form-warp div').html( msg.form );
                $('.work-area #record' + msg.id).hide();
                $('#edit-form-warp').insertAfter('.work-area #record' + msg.id);
                $('#edit-form-warp div').slideDown( 300 );
                DoDate();
                break;
            case 'backupdel':
            case 'delete':
                if( msg.error == 'success' )
                    $('.work-area #record' + msg.id).fadeOut(300);
                else
                    alert( msg.msg );
                break;
            case 'change-status':
                var tr = CloseEditWarp(0),
                    col = tr.children().length;
                tr.html('<td class="status-changed" colspan="' + col + '">' + msg.msg + '</td>');
                break;
            case 'add':
                if( msg.error == 'success')
                {
                    if ( $('#edit-form-warp').length )
                        CloseEditWarp( msg.row );
                    else if ( !$('form.plugin-options').length )
                        $(':input','.work-area form')
                            .not(':button, :submit, :reset, :hidden, select')
                            .val('')
                            .removeAttr('checked')
                            .removeAttr('selected');
                }
                $('<div class="alert alert-' + msg.error + '">' + msg.msg + '</div>').prependTo('.work-area');
                break;
            case 'backuprst':
                $('<div class="alert alert-' + msg.error + '">' + msg.msg + '</div>').prependTo('.work-area');
                break;
            case 'getbackup':
                if( msg.error == 'success' )
                    $('.work-area #backup-list tbody').append( msg.row );
                $('<div class="alert alert-' + msg.error + '">' + msg.msg + '</div>').prependTo('.work-area');
                break;
        }
    }


    function CloseEditWarp( row ){
        var current = $('#edit-form-warp'),
            tr = current.prev(),
            $this = current.find('div');

        if( row ){
            for( el in row ){
                var child = parseInt(el)+1;
                tr.find('td:nth-child(' + child + ')').html( row[el] );
            }
        }

        $this.slideUp(200, function(){
            current.remove();
        });
        tr.show();
        return tr;
    }


    function DoDate(){
        if ( $('#date').length ) {
            $('#date').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
                sideBySide: true
            });
        }
    }
});