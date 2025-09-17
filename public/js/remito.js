$(document).ready(function() {
    customer_set = false;
    //Prevent enter key function except texarea
    $('form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    $('select#select_location_id').change(function() {
        reset_pos_form();

        //Set default invoice scheme for location
        if ($('#invoice_scheme_id').length) {
            var invoice_scheme_id =  $(this).find(':selected').data('default_invoice_scheme_id');
            $("#invoice_scheme_id").val(invoice_scheme_id).change();
        }
    });

    //get customer
    $('select#customer_id').select2({
        ajax: {
            url: '/contacts/customers',
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
        templateResult: function (data) { 
            var template = '';
            if (data.supplier_business_name) {
                template += data.supplier_business_name + "<br>";
            }
            template += data.text + "<br>" + LANG.mobile + ": " + data.mobile;

            if (typeof(data.total_rp) != "undefined") {
                var rp = data.total_rp ? data.total_rp : 0;
                template += "<br><i class='fa fa-gift text-success'></i> " + rp;
            }

            return  template;
        },
        minimumInputLength: 1,
        language: {
            noResults: function() {
                var name = $('#customer_id')
                    .data('select2')
                    .dropdown.$search.val();
                return (
                    '<button type="button" data-name="' +
                    name +
                    '" class="btn btn-link add_new_customer"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' +
                    __translate('add_name_as_new_customer', { name: name }) +
                    '</button>'
                );
            },
        },
        escapeMarkup: function(markup) {
            return markup;
        },
    });
    $('#customer_id').on('select2:select', function(e) {
        var data = e.params.data;
        update_shipping_address(data);
    });
    function update_shipping_address(data) {
        if ($('#shipping_address_div').length) {
            var shipping_address = '';
            if (data.supplier_business_name) {
                shipping_address += data.supplier_business_name;
            }
            if (data.name) {
                shipping_address += ',<br>' + data.name;
            }
            if (data.text) {
                shipping_address += ',<br>' + data.text;
            }
            if(data.shipping_address) {
                shipping_address += ',<br>' + data.shipping_address ;
            }
            $('#shipping_address_div').html(shipping_address);
        }
        if ($('#billing_address_div').length) {
            var address = [];
            if (data.supplier_business_name) {
                address.push(data.supplier_business_name);
            }
            if (data.name) {
                address.push('<br>' + data.name);
            }
            if (data.text) {
                address.push('<br>' + data.text);
            }
            if (data.address_line_1) {
                address.push('<br>' + data.address_line_1);
            }
            if (data.address_line_2) {
                address.push('<br>' + data.address_line_2);
            }
            if (data.city) {
                address.push('<br>' + data.city);
            }
            if (data.state) {
                address.push(data.state);
            }
            if (data.country) {
                address.push(data.country);
            }
            if (data.zip_code) {
                address.push('<br>' + data.zip_code);
            }
            var billing_address = address.join(', ');
            $('#billing_address_div').html(billing_address);
        }
    
        if ($('#shipping_custom_field_1').length) {
            let shipping_custom_field_1 = data.shipping_custom_field_details != null ? data.shipping_custom_field_details.shipping_custom_field_1 : '';
            $('#shipping_custom_field_1').val(shipping_custom_field_1);
        }
    
        if ($('#shipping_custom_field_2').length) {
            let shipping_custom_field_2 = data.shipping_custom_field_details != null ? data.shipping_custom_field_details.shipping_custom_field_2 : '';
            $('#shipping_custom_field_2').val(shipping_custom_field_2);
        }
    
        if ($('#shipping_custom_field_3').length) {
            let shipping_custom_field_3 = data.shipping_custom_field_details != null ? data.shipping_custom_field_details.shipping_custom_field_3 : '';
            $('#shipping_custom_field_3').val(shipping_custom_field_3);
        }
    
        if ($('#shipping_custom_field_4').length) {
            let shipping_custom_field_4 = data.shipping_custom_field_details != null ? data.shipping_custom_field_details.shipping_custom_field_4 : '';
            $('#shipping_custom_field_4').val(shipping_custom_field_4);
        }
    
        if ($('#shipping_custom_field_5').length) {
            let shipping_custom_field_5 = data.shipping_custom_field_details != null ? data.shipping_custom_field_details.shipping_custom_field_5 : '';
            $('#shipping_custom_field_5').val(shipping_custom_field_5);
        }
        
        //update export fields
        if (data.is_export) {
            $('#is_export').prop('checked', true);
            $('div.export_div').show();
            if ($('#export_custom_field_1').length) {
                $('#export_custom_field_1').val(data.export_custom_field_1);
            }
            if ($('#export_custom_field_2').length) {
                $('#export_custom_field_2').val(data.export_custom_field_2);
            }
            if ($('#export_custom_field_3').length) {
                $('#export_custom_field_3').val(data.export_custom_field_3);
            }
            if ($('#export_custom_field_4').length) {
                $('#export_custom_field_4').val(data.export_custom_field_4);
            }
            if ($('#export_custom_field_5').length) {
                $('#export_custom_field_5').val(data.export_custom_field_5);
            }
            if ($('#export_custom_field_6').length) {
                $('#export_custom_field_6').val(data.export_custom_field_6);
            }
        } else {
            $('#export_custom_field_1, #export_custom_field_2, #export_custom_field_3, #export_custom_field_4, #export_custom_field_5, #export_custom_field_6').val('');
            $('#is_export').prop('checked', false);
            $('div.export_div').hide();
        }
        
        $('#shipping_address_modal').val(data.shipping_address);
        $('#shipping_address').val(data.shipping_address);
    }

    set_default_customer();

    if ($('#search_product').length) {
        //Add Product
        $('#search_product')
            .autocomplete({
                delay: 1000,
                source: function(request, response) {
                    var price_group = '';
                    var search_fields = [];
                    $('.search_fields:checked').each(function(i){
                      search_fields[i] = $(this).val();
                    });

                    if ($('#price_group').length > 0) {
                        price_group = $('#price_group').val();
                    }
                    $.getJSON(
                        '/products/list',
                        {
                            price_group: price_group,
                            location_id: $('input#location_id').val(),
                            term: request.term,
                            not_for_selling: 0,
                            search_fields: search_fields
                        },
                        response
                    );
                },
                minLength: 2,
                response: function(event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        if ((ui.item.enable_stock == 1 && ui.item.qty_available > 0) || ui.item.enable_stock == 0) {
                            $(this)
                                .data('ui-autocomplete')
                                ._trigger('select', 'autocompleteselect', ui);
                            $(this).autocomplete('close');
                        }
                    } else if (ui.content.length == 0) {
                        toastr.error(LANG.no_products_found);
                        $('input#search_product').select();
                    }
                },
                focus: function(event, ui) {
                    if (ui.item.qty_available <= 0) {
                        return false;
                    }
                },
                select: function(event, ui) {
                    var searched_term = $(this).val();

                    if (ui.item.enable_stock != 1 || ui.item.qty_available > 0) {
                        $(this).val(null);

                        //Pre select lot number only if the searched term is same as the lot number
                        var purchase_line_id = ui.item.purchase_line_id && searched_term == ui.item.lot_number ? ui.item.purchase_line_id : null;
                        remito_product_row(ui.item.variation_id, purchase_line_id);
                    } else {
                        alert(LANG.out_of_stock);
                    }
                },
            })
            .autocomplete('instance')._renderItem = function(ul, item) {
            if (item.enable_stock == 1 && item.qty_available <= 0) {
                var string = '<li class="ui-state-disabled">' + item.name;
                if (item.type == 'variable') {
                    string += '-' + item.variation;
                }
                string += ' (' + item.sub_sku + ')<br>(Out of stock) </li>';
                return $(string).appendTo(ul);
            } else {
                var string = '<div>' + item.name;
                if (item.type == 'variable') {
                    string += '-' + item.variation;
                }

                string += ' (' + item.sub_sku + ')';
                if (item.enable_stock == 1) {
                    var qty_available = __currency_trans_from_en(item.qty_available, false, false, __currency_precision, true);
                    string += ' - ' + qty_available + item.unit;
                }
                string += '</div>';

                return $('<li>')
                    .append(string)
                    .appendTo(ul);
            }
        };
    }

    //Remove row on click on remove row
    $('table#remito_edit_table tbody').on('click', 'i.remito_remove_row', function() {
        $(this)
            .parents('tr')
            .remove();
    });

    $(document).on('click', '.add_new_customer', function() {
        $('#customer_id').select2('close');
        var name = $(this).data('name');
        $('.contact_modal')
            .find('input#name')
            .val(name);
        $('.contact_modal')
            .find('select#contact_type')
            .val('customer')
            .closest('div.contact_type_div')
            .addClass('hide');
        $('.contact_modal').modal('show');
    });
    $('form#quick_add_contact')
        .submit(function(e) {
            e.preventDefault();
        })
        .validate({
            rules: {
                contact_id: {
                    remote: {
                        url: '/contacts/check-contacts-id',
                        type: 'post',
                        data: {
                            contact_id: function() {
                                return $('#contact_id').val();
                            },
                            hidden_id: function() {
                                if ($('#hidden_id').length) {
                                    return $('#hidden_id').val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },
            },
            messages: {
                contact_id: {
                    remote: LANG.contact_id_already_exists,
                },
            },
            submitHandler: function(form) {
                $.ajax({
                    method: 'POST',
                    url: base_path + '/check-mobile',
                    dataType: 'json',
                    data: {
                        contact_id: function() {
                            return $('#hidden_id').val();
                        },
                        mobile_number: function() {
                            return $('#mobile').val();
                        },
                    },
                    beforeSend: function(xhr) {
                        __disable_submit_button($(form).find('button[type="submit"]'));
                    },
                    success: function(result) {
                        if (result.is_mobile_exists == true) {
                            swal({
                                title: LANG.sure,
                                text: result.msg,
                                icon: 'warning',
                                buttons: true,
                                dangerMode: true,
                            }).then(willContinue => {
                                if (willContinue) {
                                    submitQuickContactForm(form);
                                } else {
                                    $('#mobile').select();
                                }
                            });
                            
                        } else {
                            submitQuickContactForm(form);
                        }
                    },
                });
            },
        });
    $('.contact_modal').on('hidden.bs.modal', function() {
        $('form#quick_add_contact')
            .find('button[type="submit"]')
            .removeAttr('disabled');
        $('form#quick_add_contact')[0].reset();
    });

    //Datetime picker
    $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    //Direct remito submit
    remito_form = $('form#add_remito_form');
    if ($('form#edit_remito_form').length) {
        remito_form = $('form#edit_remito_form');
    }
    remito_form_validator = remito_form.validate();

    $('button#submit-remito, button#save-and-print').click(function(e) {
        //Check if product is present or not.
        if ($('table#remito_edit_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        if ($(this).attr('id') == 'save-and-print') {
            $('#is_save_and_print').val(1);           
        } else {
            $('#is_save_and_print').val(0);
        }

        if (remito_form.valid()) {
            window.onbeforeunload = null;
            $(this).attr('disabled', true);
            
            const data = new FormData(remito_form[0]);
            $.ajax({
                method: $(remito_form).attr('method'),
                url: $(remito_form).attr('action'),
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success == 1) {
                        if(result.msg) toastr.success(result.msg);
                        if(result.redirect_url) window.location.href = result.redirect_url;
                    } else {
                        toastr.error(result.msg);
                    }
                    enable_remito_form_actions()
                },
            });
        }
    });

    //REPAIR MODULE:check if repair module field is present send data to filter product
    var is_enabled_stock = null;
    if ($("#is_enabled_stock").length) {
        is_enabled_stock = $("#is_enabled_stock").val();
    }

    //Press enter on search product to jump into last quantty and vice-versa
    $('#search_product').keydown(function(e) {
        var key = e.which;
        if (key == 9) {
            // the tab key code
            e.preventDefault();
            if ($('#remito_edit_table tbody tr').length > 0) {
                $('#remito_edit_table tbody tr:last')
                    .find('input.remito_quantity')
                    .focus()
                    .select();
            }
        }
    });
    $('#remito_edit_table').on('keypress', 'input.remito_quantity', function(e) {
        var key = e.which;
        if (key == 13) {
            // the enter key code
            $('#search_product').focus();
        }
    });

    $('table#remito_edit_table').on('change', 'select.sub_unit', function() {
        var tr = $(this).closest('tr');
        var selected_option = $(this).find(':selected');
        var multiplier = parseFloat(selected_option.data('multiplier'));
        var allow_decimal = parseInt(selected_option.data('allow_decimal'));
        tr.find('input.base_unit_multiplier').val(multiplier);

        var qty_element = tr.find('input.remito_quantity');
        var base_max_avlbl = qty_element.data('qty_available');
        var error_msg_line = 'pos_max_qty_error';

        if (tr.find('select.lot_number').length > 0) {
            var lot_select = tr.find('select.lot_number');
            if (lot_select.val()) {
                base_max_avlbl = lot_select.find(':selected').data('qty_available');
                error_msg_line = 'lot_max_qty_error';
            }
        }

        qty_element.attr('data-decimal', allow_decimal);
        var abs_digit = true;
        if (allow_decimal) {
            abs_digit = false;
        }
        qty_element.rules('add', {
            abs_digit: abs_digit,
        });

        if (base_max_avlbl) {
            var max_avlbl = parseFloat(base_max_avlbl) / multiplier;
            var formated_max_avlbl = __number_f(max_avlbl);
            var unit_name = selected_option.data('unit_name');
            var max_err_msg = __translate(error_msg_line, {
                max_val: formated_max_avlbl,
                unit_name: unit_name,
            });
            qty_element.attr('data-rule-max-value', max_avlbl);
            qty_element.attr('data-msg-max-value', max_err_msg);
            qty_element.rules('add', {
                'max-value': max_avlbl,
                messages: {
                    'max-value': max_err_msg,
                },
            });
            qty_element.trigger('change');
        }
        adjustComboQty(tr);
    });

    //Confirmation before page load.
    window.onbeforeunload = function() {
        if($('table#remito_edit_table tbody tr').length > 0) {
            return LANG.sure;
        } else {
            return null;
        }
    }
    $(window).resize(function() {
        var win_height = $(window).height();
        div_height = __calculate_amount('percentage', 63, win_height);
        $('div.remito_product_div').css('min-height', div_height + 'px');
        $('div.remito_product_div').css('max-height', div_height + 'px');
    });

    setInterval(function () {
        if ($('span.curr_datetime').length) {
            $('span.curr_datetime').html(__current_datetime());
        }
    }, 60000);
});

//variation_id is null when weighing_scale_barcode is used.
function remito_product_row(variation_id = null, purchase_line_id = null, weighing_scale_barcode = null, quantity = 1) {
    //Get item addition method
    var item_addtn_method = 0;
    var add_via_ajax = true;

    if (variation_id != null && $('#item_addition_method').length) {
        item_addtn_method = $('#item_addition_method').val();
    }

    if (item_addtn_method == 0) {
        add_via_ajax = true;
    } else {
        var is_added = false;

        //Search for variation id in each row of pos table
        $('#remito_edit_table tbody')
            .find('tr')
            .each(function() {
                var row_v_id = $(this)
                    .find('.row_variation_id')
                    .val();
                var enable_sr_no = $(this)
                    .find('.enable_sr_no')
                    .val();
                var modifiers_exist = false;
                if ($(this).find('input.modifiers_exist').length > 0) {
                    modifiers_exist = true;
                }

                if (
                    row_v_id == variation_id &&
                    enable_sr_no !== '1' &&
                    !modifiers_exist &&
                    !is_added
                ) {
                    add_via_ajax = false;
                    is_added = true;

                    //Increment product quantity
                    qty_element = $(this).find('.remito_quantity');
                    var qty = __read_number(qty_element);
                    __write_number(qty_element, qty + 1);
                    qty_element.change();

                    $('input#search_product')
                        .focus()
                        .select();
                }
        });
    }

    if (add_via_ajax) {
        var product_row = $('input#product_row_count').val();
        var location_id = $('input#location_id').val();
        var customer_id = $('select#customer_id').val();
        
        $.ajax({
            method: 'GET',
            url: '/sells/pos/get_product_row/' + variation_id + '/' + location_id,
            async: false,
            data: {
                product_row: product_row,
                customer_id: customer_id,
                purchase_line_id: purchase_line_id,
                quantity: quantity,
                type: 'remito'
            },
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    $('table#remito_edit_table tbody')
                        .append(result.html_content)
                        .find('input.remito_quantity');
                    //increment row count
                    $('input#product_row_count').val(parseInt(product_row) + 1);
                    var this_row = $('table#remito_edit_table tbody')
                        .find('tr')
                        .last();

                    //Check if multipler is present then multiply it when a new row is added.
                    if(__getUnitMultiplier(this_row) > 1){
                        this_row.find('select.sub_unit').trigger('change');
                    }

                    $('input#search_product')
                        .focus()
                        .select();

                    //scroll bottom of items list
                    $(".remito_product_div").animate({ scrollTop: $('.remito_product_div').prop("scrollHeight")}, 1000);
                } else {
                    toastr.error(result.msg);
                    $('input#search_product')
                        .focus()
                        .select();
                }
            },
        });
    }
}

function reset_pos_form(){
	set_default_customer();
	set_location();

	$('tr.product_row').remove();

    if($('#invoice_layout_id').length > 0){
        $('#invoice_layout_id').trigger('change');
    };
}

function set_default_customer() {
    var default_customer_id = $('#default_customer_id').val();
    var default_customer_name = $('#default_customer_name').val();
    var default_customer_address = $('#default_customer_address').val();
    var exists = default_customer_id ? $('select#customer_id option[value=' + default_customer_id + ']').length : 0;
    if (exists == 0 && default_customer_id) {
        $('select#customer_id').append(
            $('<option>', { value: default_customer_id, text: default_customer_name })
        );
    }
    if (default_customer_address) {
        $('#shipping_address').val(default_customer_address);
    }
    $('select#customer_id')
        .val(default_customer_id)
        .trigger('change');

    customer_set = true;
}

//Set the location and initialize printer
function set_location() {
    if ($('select#select_location_id').length == 1) {
        $('input#location_id').val($('select#select_location_id').val());
        $('input#location_id').data(
            'receipt_printer_type',
            $('select#select_location_id')
                .find(':selected')
                .data('receipt_printer_type')
        );
    }

    if ($('input#location_id').val()) {
        $('input#search_product')
            .prop('disabled', false)
            .focus();
    } else {
        $('input#search_product').prop('disabled', true);
    }

    initialize_printer();
}

function initialize_printer() {
    if ($('input#location_id').data('receipt_printer_type') == 'printer') {
        initializeSocket();
    }
}

$('body').on('click', 'label', function(e) {
    var field_id = $(this).attr('for');
    if (field_id) {
        if ($('#' + field_id).hasClass('select2')) {
            $('#' + field_id).select2('open');
            return false;
        }
    }
});

$('body').on('focus', 'select', function(e) {
    var field_id = $(this).attr('id');
    if (field_id) {
        if ($('#' + field_id).hasClass('select2')) {
            $('#' + field_id).select2('open');
            return false;
        }
    }
});

function remito_print(receipt) {
    //If printer type then connect with websocket
    if (receipt.print_type == 'printer') {
        var content = receipt;
        content.type = 'print-receipt';

        //Check if ready or not, then print.
        if (socket != null && socket.readyState == 1) {
            socket.send(JSON.stringify(content));
        } else {
            initializeSocket();
            setTimeout(function() {
                socket.send(JSON.stringify(content));
            }, 700);
        }

    } else if (receipt.html_content != '') {
        var title = document.title;
        if (typeof receipt.print_title != 'undefined') {
            document.title = receipt.print_title;
        }

        //If printer type browser then print content
        $('#receipt_section').html(receipt.html_content);
        __currency_convert_recursively($('#receipt_section'), true);
        __print_receipt('receipt_section');

        setTimeout(function() {
            document.title = title;
        }, 1200);
    }
}

$(document).on('change', 'select#customer_id', function(){
    var default_customer_id = $('#default_customer_id').val();
});

function adjustComboQty(tr){
    if(tr.find('input.product_type').val() == 'combo'){
        var qty = __read_number(tr.find('input.remito_quantity'));
        var multiplier = __getUnitMultiplier(tr);

        tr.find('input.combo_product_qty').each(function(){
            $(this).val($(this).data('unit_quantity') * qty * multiplier);
        });
    }
}

function disable_remito_form_actions(){
    if (!window.navigator.onLine) {
        return false;
    }

    $('div.remito-processing').show();
    $('#pos-save').attr('disabled', 'true');
    $('div.pos-form-actions').find('button').attr('disabled', 'true');
}

function enable_remito_form_actions() {
    $('div.remito-processing').hide();
    $('button#submit-remito').removeAttr('disabled');
    $('button#save-and-print').removeAttr('disabled');
}

var service_staff_availability_interval = null;