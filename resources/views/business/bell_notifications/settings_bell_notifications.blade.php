<div class="pos-tab-content">
    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'notifications.bell_notifications' )])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="notifications_table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>@lang( 'notifications.type' )</th>
                            <th>@lang( 'notifications.receivers' )</th>
                            <th>@lang( 'lang_v1.created_at' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent

        <div class="modal fade user_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->
</div>


@section('notification_javascript')
<script type="text/javascript">
    //Roles table
    $(document).ready( function(){
        var notifications_table = $('#notifications_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/notifications',
            columnDefs: [ {
                "targets": [3],
                "orderable": false,
                "searchable": false
            } ],
            "columns":[
                {"data":"type"},
                {"data":"receivers"},
                {"data":"created_at"},
                {"data":"action"}
            ]
        });
        
        $('#addReceiverModal #receiver_id').select2({
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
            dropdownParent: $('#addReceiverModal'), //
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
        $('#addReceiverModal #receiver_id').on('select2:selecting', function(e) {
            var selectedValue = e.params.args.data;
            let editingRow = $('tr.editing');
            let editingCol = editingRow.find('td:nth-child(2)');
            var displayName = (selectedValue.surname ? selectedValue.surname + ' ' : '') + 
                           (selectedValue.first_name ? selectedValue.first_name + ' ' : '') + 
                           (selectedValue.last_name ? selectedValue.last_name : '');
            let userOption = $('<div class="btn btn-primary" style="margin: 5px;" id="' + selectedValue.id + '">' + displayName + '<a href="#" style="color: white; margin-left: 5px;"><i class="glyphicon glyphicon-remove"></i></a></div>');
            editingCol.prepend(userOption);
            $('#addReceiverModal').modal('hide');
            return true;
        });

        $(document).on('click', '.update-button', function(e) {
            var row = $(this).closest('tr');
            var cell = $(this).closest('td');
            var receiversCell = row.find('td:nth-child(2)');
            var recevierIds = [];
            receiversCell.find('div').each(function (){
                recevierIds.push($(this).attr('id'));
            })
            $.post( "/notifications/update", {receivers: recevierIds, id: cell.find('.edit-button').attr('notify_id')}, function( data ) {
                if (data) {
                    notifications_table.ajax.reload();
                }
            });
        });
        
        $(document).on('click', 'button.delete_user_button', function(e) {
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
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                notifications_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });


    $(document).on('click', 'button.edit-button', function(e) {
        e.preventDefault();
        // Get the current row and find the cell containing the button
        var row = $(this).closest('tr');
        var cell = $(this).closest('td');
        // update click buttons
        cell.find('.edit-button, .delete_user_button').addClass('hidden');
        cell.find('.update-button').removeClass('hidden');
        cell.find('.cancel_button').removeClass('hidden');
        var receiversCell = row.find('td:nth-child(2)');
        receiversCell.html('');
        var receivers = $(this).attr('receivers').split(',');
        var receiverIds = $(this).attr('receiverIds').split(',');

        receivers.forEach((receiver, index) => {
            let userOption = $('<div class="btn btn-primary" style="margin: 5px;" id="' + parseInt(receiverIds[index]) + '">' + receiver + '<a href="#" style="color: white; margin-left: 5px;"><i class="glyphicon glyphicon-remove"></i></a></div>');
            receiversCell.append(userOption);
        });
        var addButton = $('<a href="#" id="openAddReceiverModal" style="margin: 5px; text-wrap:nowrap;"><i class="glyphicon glyphicon-plus"></i> Add</a>');
        addButton.bind('click', function () {
            $('div#addReceiverModal').modal('show');
        });
        receiversCell.append(addButton);
        receiversCell.find('div a').bind('click', function(e) {
            $(this).parent().remove();
        });
        row.addClass('editing');
    });

    $(document).on('click', '.cancel_button', function(e) {
        var row = $(this).closest('tr');
        row.removeClass('editing');
        var cell = $(this).closest('td');
        cell.find('.update-button, .cancel_button').addClass('hidden');
        cell.find('.edit-button').removeClass('hidden');
        cell.find('.delete_user_button').removeClass('hidden');
        var receiversCell = row.find('td:nth-child(2)');
        receiversCell.html(cell.find('.edit-button').attr('receivers'));
    });
    
</script>
@endsection