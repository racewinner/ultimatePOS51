@extends('layouts.app')
@section('title', "Generate Invoice")

@section('content')
<div class="p-4">
    <h3>Generate Pymo Invoice</h3>
    <div class="mt-4">
        <div class="form-group d-flex align-items-center">
            <label>Invoice Number:</label>
            <div class="ms-4" style="width: 300px;">
                <input name='pymo_invoice' class="form-control" />
            </div>
            <button class="btn btn-primary ms-4" id="btn_generate_invoice">Generate</button>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $(document).on('click', 'button#btn_generate_invoice', function() {
        const pymo_invoice = $("input[name='pymo_invoice']").val();
        if(pymo_invoice) {
            $.ajax({
                method: "POST",
                url: '/pymo/generateInvoice',
                data: {
                    pymo_invoice
                },
                success:function(result){
                    if(result.success == true) {
                        toastr.success(result.message);
                        window.open(result.url);
                    } else {
                        toastr.error(result.message);
                    }
                }
            });
        }
    });
})
</script>
@endsection