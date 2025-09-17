<div class="pos-tab-content">
    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'notifications.notification_users' )])
            <div class="" style="display: flex; align-items:center">
                <div>
                    {!! Form::label('notify_user_id', __('notifications.document_type') . ':*') !!}
                    <div class="input-group">
                    {!! Form::select('document_type', ['purchase' => __( 'notifications.purchase' ), 'invoice' => __( 'notifications.invoice' ), 'payment' => __( 'notifications.payment' )], 'purchase', ['class' => 'form-control', 'id' => 'document_type']) !!}
                    </div>
                </div>
                <div class="add-notification-receivers" style="margin: 10px; min-width: 300px">
                    {!! Form::label('notify_user_id', __('notifications.add_receiver_modal') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                    {!! Form::select('notify_user_id', [], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;', 'id' => 'notify_user_id']) !!}
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="users_table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>@lang( 'business.username' )</th>
                            <th>@lang( 'user.name' )</th>
                            <th>@lang( 'user.role' )</th>
                            <th>@lang( 'business.email' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent
    </section>
    <!-- /.content -->
</div>


@section('receiver_javascript')
<script type="text/javascript">
    //Roles table
    $(document).ready( function(){
        var users_table = $('#users_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/notificationsusers',
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                    d.document_type = $('#document_type').val();
                }
            },
            columnDefs: [ {
                "targets": [4],
                "orderable": false,
                "searchable": false
            } ],
            "columns":[
                {"data":"username"},
                {"data":"full_name"},
                {"data":"role"},
                {"data":"email"},
                {"data":"action"}
            ],
            searching: false
        });
        
        $('.add-notification-receivers #notify_user_id').select2({
            ajax: {
                url: '/notifications/get_users',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data,
                    };
                },
            },
            minimumInputLength: 1, // Show the search field even for a small number of options
            escapeMarkup: function(m) {
                return m;
            },
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                var html = (data.surname ? data.surname + ' ' : '') + 
                           (data.first_name ? data.first_name + ' ' : '') + 
                           (data.last_name ? data.last_name : '');
                return html;
            }
        });
        $('.add-notification-receivers #notify_user_id').on('select2:selecting', function(e) {
            var selectedValue = e.params.args.data;
            $.post( "/notificationsusers/update", {id: selectedValue.id, type: $('#document_type').val()}, function( data ) {
                if (data) {
                    users_table.ajax.reload();
                }
            });
            return true;
        });
        $('select#document_type').change(function() {
            users_table.ajax.reload();
        });
        $(document).on('click', '#users_table button.delete_user_button', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_user,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: {...data, document_type: $('#document_type').val()},
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                users_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });

    
</script>
@endsection