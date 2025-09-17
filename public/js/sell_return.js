function generate_return_invoice() {
  // To check discount_type
  if($("#discount_type").val() == 'fixed') {
      toastr.error("Discount type not supported for e-invoice, select discount by percentage");
      return false;
  }

  event.preventDefault();
  let form_data = $("form#sell_return_form").serialize();
  let bill_data = {};
  
  form_data.split('&').forEach(function(pair) {
      let parts = pair.split('=');
      let name = decodeURIComponent(parts[0]);
      let value = decodeURIComponent(parts[1] || '');
      bill_data[name] = value;
  });
  bill_data['_method'] = 'POST';
  $.post('/pymo/sendReturnInvoice', bill_data, function(response) {
      if (response.status && response.status === 'SUCCESS') {
          toastr.success("Cfes recibidos correctamente.");
          let html = "<i class='fa fa-download'></i>";
          html += `<a class='m-2' href='/uploads/invoices/{{$sell_return->business_id}}/${response.invoice_id}.pdf' target='_blank'>Download Document</a>`;
          $(".electronic_bill_tab").html(html);
      }
      if (response.status === 'error') {
          if (response.data && response.data.message.code && response.data.message.code === 'DUPLICATED_KEY') {
              toastr.warning("Please update Invoice ID");
          } else if (response.message) {
              toastr.warning(response.message);
          } else if (response.data && response.data.message) {
              toastr.warning(response.data.message.value);
          }
      }
  }).fail(function(xhr, status, error) {
      console.error(error);
  });
}

function initialize_printer() {
  if ($('input#location_id').data('receipt_printer_type') == 'printer') {
      initializeSocket();
  }
}

function pos_print(receipt) {
  //If printer type then connect with websocket
  if (receipt.print_type == 'printer') {
      var content = receipt;
      content.type = 'print-receipt';

      //Check if ready or not, then print.
      if (socket.readyState != 1) {
          initializeSocket();
          setTimeout(function() {
              socket.send(JSON.stringify(content));
          }, 700);
      } else {
          socket.send(JSON.stringify(content));
      }
  } else if (receipt.html_content != '') {
      var title = document.title;
      if (typeof receipt.print_title != 'undefined') {
          document.title = receipt.print_title;
      }

      //If printer type browser then print content
      $('#receipt_section').html(receipt.html_content);
      __currency_convert_recursively($('#receipt_section'));
      setTimeout(function() {
          window.print();
          document.title = title;
      }, 1000);
  }
}

// //Set the location and initialize printer
// function set_location(){
// 	if($('input#location_id').length == 1){
// 	       $('input#location_id').val($('select#select_location_id').val());
// 	       //$('input#location_id').data('receipt_printer_type', $('select#select_location_id').find(':selected').data('receipt_printer_ty
// 	}

// 	if($('input#location_id').val()){
// 	       $('input#search_product').prop( "disabled", false ).focus();
// 	} else {
// 	       $('input#search_product').prop( "disabled", true );
// 	}

// 	initialize_printer();
// }

$(document).ready(function() {
  //For edit pos form
  if ($('form#sell_return_form').length > 0) {
      pos_form_obj = $('form#sell_return_form');
  } else {
      pos_form_obj = $('form#add_pos_sell_form');
  }
  if ($('form#sell_return_form').length > 0 || $('form#add_pos_sell_form').length > 0) {
      initialize_printer();
  }

  //Date picker
  $('#transaction_date').datetimepicker({
      format: moment_date_format + ' ' + moment_time_format,
      ignoreReadonly: true,
  });

  pos_form_validator = pos_form_obj.validate({
      submitHandler: function(form) {
          var cnf = true;

          if (cnf) {
              var data = $(form).serialize();
              var url = $(form).attr('action');
              $.ajax({
                  method: 'POST',
                  url: url,
                  data: data,
                  dataType: 'json',
                  success: function(result) {
                      if (result.success == 1) {
                          toastr.success(result.msg);
                          //Check if enabled or not
                          if (result.receipt.is_enabled) {
                              pos_print(result.receipt);
                          }
                          $("input[name='sell_return_id']").val(result.sell_return_id);

                          var el_generate_bill_after_create = $("#generate_bill_after_create")[0];
                          if (el_generate_bill_after_create.checked) {
                            generate_return_invoice();
                          }
                      } else {
                          toastr.error(result.msg);
                      }
                  },
              });
          }
          return false;
      },
  });

  $(".electronic_bill_tab").on('click', '#generate_bill', function(event) {
      generate_return_invoice();
  });
});