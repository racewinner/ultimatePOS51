$(document).ready(function() {
    $(document).on('change', '.purchase_quantity', function() {
        update_table_total($(this).closest('table'));
    });
    $(document).on('change', '.unit_price', function() {
        update_table_total($(this).closest('table'));
    });

    $('.os_exp_date').datepicker({
        autoclose: true,
        format: datepicker_date_format,
    });

    $(document).on('click', '.add_stock_row', function() {
        var tr = $(this).data('row-html');
        var key = parseInt($(this).data('sub-key'));
        tr = tr.replace(/\__subkey__/g, key);
        $(this).data('sub-key', key + 1);

        $(tr)
            .insertAfter($(this).closest('tr'))
            .find('.os_exp_date')
            .datepicker({
                autoclose: true,
                format: datepicker_date_format,
            });
            
            $(this).closest('tr').next('tr').find('.os_date').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
    });

    $(document).on('change', 'select[name="currency_id"]', function(e){
        const option = $(e.currentTarget).find(':selected');
    
        __p_currency_symbol = option.attr("data-symbol");
        __p_currency_thousand_separator = option.attr("data-thousand-separator") === 'NA' ? "" : option.attr("data-thousand-separator");
        __p_currency_decimal_separator = option.attr("data-decimal-separator") === 'NA' ? "" : option.attr("data-decimal-separator");

        __currency_convert_recursively($("#pos_table"), true, true);
        calculate_balance_due();
    });

    $(document).on('click', '.add-opening-stock', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function(result) {
                $('#opening_stock_modal')
                    .html(result)
                    .modal('show');
            },
        });
    });
});

//Re-initialize data picker on modal opening
 $('#opening_stock_modal').on('shown.bs.modal', function(e) {
    $('#opening_stock_modal .os_exp_date').datepicker({
        autoclose: true,
        format: datepicker_date_format,
    });
    $('#opening_stock_modal .os_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
        widgetPositioning: {
            horizontal: 'right',
            vertical: 'bottom'
        }
    });
 });

$(document).on('click', 'button#add_opening_stock_btn', function(e) {
    e.preventDefault();
    var btn = $(this);
    var data = $('form#add_opening_stock_form').serialize();

    $.ajax({
        method: 'POST',
        url: $('form#add_opening_stock_form').attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function(xhr) {
            __disable_submit_button(btn);
        },
        success: function(result) {
            if (result.success == true) {
                $('#opening_stock_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
    return false;
});

function update_table_total(table) {
    var total_subtotal = 0;
    table.find('tbody tr').each(function() {
        var qty = __read_number($(this).find('.purchase_quantity'));
        var unit_price = __read_number($(this).find('.unit_price'));
        var row_subtotal = qty * unit_price;
        $(this)
            .find('.row_subtotal_before_tax')
            .text(__number_f(row_subtotal));
        total_subtotal += row_subtotal;
    });
    table.find('tfoot tr #total_subtotal').text(__currency_trans_from_en(total_subtotal, true, true));
    table.find('tfoot tr #total_subtotal_hidden').val(total_subtotal);
}
