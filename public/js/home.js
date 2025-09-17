$(document).ready(function() {
    if ($('#dashboard_date_filter').length == 1) {
        dateRangeSettings.startDate = moment();
        dateRangeSettings.endDate = moment();
        $('#dashboard_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#dashboard_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            update_statistics(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            if ($('#quotation_table').length && $('#dashboard_location').length) {
                quotation_datatable.ajax.reload();
            }
        });

        update_statistics(moment().format('YYYY-MM-DD'), moment().format('YYYY-MM-DD'));
    }

    $('#dashboard_location').change( function(e) {
        var start = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');

        var end = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');

        update_statistics(start, end);
    });

    //atock alert datatables
    var stock_alert_table = $('#stock_alert_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            "url": '/home/product-stock-alert',
            "data": function ( d ) {
                if ($('#stock_alert_location').length > 0) {
                    d.location_id = $('#stock_alert_location').val();
                }
            }
        },
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#stock_alert_table'));
        },
    });

    $('#stock_alert_location').change( function(){
        stock_alert_table.ajax.reload();
    });
    //payment dues datatables
    purchase_payment_dues_table = $('#purchase_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            "url": '/home/purchase-payment-dues',
            "data": function ( d ) {
                if ($('#purchase_payment_dues_location').length > 0) {
                    d.location_id = $('#purchase_payment_dues_location').val();
                }
            }
        },
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#purchase_payment_dues_table'));
        },
    });

    $('#purchase_payment_dues_location').change( function(){
        purchase_payment_dues_table.ajax.reload();
    });

    //Sales dues datatables
    sales_payment_dues_table = $('#sales_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            "url": '/home/sales-payment-dues',
            "data": function ( d ) {
                if ($('#sales_payment_dues_location').length > 0) {
                    d.location_id = $('#sales_payment_dues_location').val();
                }
            }
        },
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#sales_payment_dues_table'));
        },
    });

    $('#sales_payment_dues_location').change( function(){
        sales_payment_dues_table.ajax.reload();
    });

    //Stock expiry report table
    stock_expiry_alert_table = $('#stock_expiry_alert_table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            url: '/reports/stock-expiry',
            data: function(d) {
                d.exp_date_filter = $('#stock_expiry_alert_days').val();
            },
        },
        order: [[3, 'asc']],
        columns: [
            { data: 'product', name: 'p.name' },
            { data: 'location', name: 'l.name' },
            { data: 'stock_left', name: 'stock_left' },
            { data: 'exp_date', name: 'exp_date' },
        ],
        fnDrawCallback: function(oSettings) {
            __show_date_diff_for_human($('#stock_expiry_alert_table'));
            __currency_convert_recursively($('#stock_expiry_alert_table'));
        },
    });

    if ($('#quotation_table').length) {
        quotation_datatable = $('#quotation_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": '/sells/draft-dt?is_quotation=1',
                "data": function ( d ) {
                    if ($('#dashboard_location').length > 0) {
                        d.location_id = $('#dashboard_location').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": 4,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'action', name: 'action'}
            ]            
        });
    }
});

function update_statistics(start, end) {
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    var data = { start: start, end: end, location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.invoice_due').html(loader);
    $('.total_expense').html(loader);
    $('.total_purchase_return').html(loader);
    $('.total_sell_return').html(loader);
    $('.net').html(loader);
    $.ajax({
        method: 'get',
        url: '/home/get-totals',
        dataType: 'json',
        data: data,
        success: function(data) {
            //purchase details
            $('.total_purchase').html(
                __currency_trans_from(data.total_purchase, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(data.total_purchase_currency2, true, currencies[1]) : '')
            );
            $('.purchase_due').html(
                __currency_trans_from(data.purchase_due, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(data.purchase_due_currency2, true, currencies[1]) : '')
            );

            //sell details
            $('.total_sell').html(
                __currency_trans_from(data.total_sell, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(data.total_sell_currency2, true, currencies[1]) : '')
            );
            $('.invoice_due').html(
                __currency_trans_from(data.invoice_due, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(data.invoice_due_currency2, true, currencies[1]) : '')
            );
            //expense details
            $('.total_expense').html(
                __currency_trans_from(data.total_expense, true, currencies[0]) +
                (currencies.length > 1 ? ' / ' + __currency_trans_from(data.total_expense_currency2, true, currencies[1]) : '')
            );

            var total_purchase_return = data.total_purchase_return - data.total_purchase_return_paid;
            var total_purchase_return_currency2 = data.total_purchase_return_currency2 - data.total_purchase_return_paid_currency2;
            $('.total_purchase_return').html(
                __currency_trans_from(total_purchase_return, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(total_purchase_return_currency2, true, currencies[1]) : '')
            );

            var total_sell_return_due = data.total_sell_return - data.total_sell_return_paid;
            var total_sell_return_due_currency2 = data.total_sell_return_currency2 - data.total_sell_return_paid_currency2;
            $('.total_sell_return').html(
                __currency_trans_from(total_sell_return_due, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(total_sell_return_due_currency2, true, currencies[1]) : '')
            );

            $('.total_sr').html(
                __currency_trans_from(data.total_sell_return, true, currencies[0]) +
                (currencies.length > 1 ? ' / ' + __currency_trans_from(data.total_sell_return_currency2, true, currencies[1]) : '')
            );
            $('.total_srp').html(
                __currency_trans_from(data.total_sell_return_paid, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(data.total_sell_return_paid_currency2, true, currencies[1]) : '')
            );
            $('.total_pr').html(
                __currency_trans_from(data.total_purchase_return, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(data.total_purchase_return_currency2, true, currencies[1]) : '')
            );
            $('.total_prp').html(
                __currency_trans_from(data.total_purchase_return_paid, true, currencies[0]) +
                (currencies.length > 1 ? ' / ' + __currency_trans_from(data.total_purchase_return_paid_currency2, true, currencies[1]) : '')
            );
            $('.net').html(
                __currency_trans_from(data.net, true, currencies[0]) + 
                (currencies.length > 1 ? ' / ' + __currency_trans_from(data.net_currency2, true, currencies[1]) : '')
            );
        },
    });
}
