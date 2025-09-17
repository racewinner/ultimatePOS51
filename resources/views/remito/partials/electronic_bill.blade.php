<div class="electronic_bill_tab" data-type="card" >
	@if ($transaction->pymo_invoice)
		<i class="fa fa-download"></i>
		<a class="m-2" href="/uploads/invoices/{{$transaction->business_id}}/{{$transaction->pymo_invoice . '.pdf'}}" target="_blank">Download Document</a>
	@else
		<button href="" id="generate_bill">@lang( 'lang_v1.generate_invoice' )</button>
	@endif
</div>
@section('bill_javascript')
<script>
	$(document).ready(function() {
		$(".electronic_bill_tab #generate_bill").on('click', function(event) {
			event.preventDefault();
			let form_data = $("form#edit_remito_form").serialize();
			let bill_data = {};
			
			form_data.split('&').forEach(function(pair) {
				let parts = pair.split('=');
				let name = decodeURIComponent(parts[0]);
				let value = decodeURIComponent(parts[1] || '');
				bill_data[name] = value;
			});
			bill_data['_method'] = 'POST';
			$.post('/pymo/sendRemitoInvoice', bill_data, function(response) {
                if (response.status && response.status === 'SUCCESS') {
					toastr.success("Cfes recibidos correctamente.");
					let html = "<i class='fa fa-download'></i>";
					html += `<a class='m-2' href='/uploads/invoices/{{$transaction->business_id}}/${response.invoice_id}.pdf' target='_blank'>Download Document</a>`;
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
		});
	});
</script>
@endsection