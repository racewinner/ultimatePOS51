<div class="d-flex justify-content-end mb-4">
    {!! Form::text('searchTerm', request()->searchTerm, ['class' => 'form-control w-200px', 'placeholder' => 'search', 'id' => 'searchTerm']) !!}
</div>

<table class="table table-bordered" id="contact_payments_table">
    <thead>
        <tr>
            <th>@lang('lang_v1.paid_on')</th>
            <th>@lang('lang_v1.customer')</th>
            <th>@lang('purchase.ref_no')</th>
            <th>@lang('sale.amount')</th>
            <th>@lang('lang_v1.payment_method')</th>
            <th>@lang('account.payment_for')</th>
            <th>@lang('invoice.parent_invoice')</th>
            <th>@lang('invoice.e_receipt')</th>
            <th>@lang('messages.action')</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payments as $payment)
            @php
                $count_child_payments = count($payment->child_payments);
                if ($payment->currency_id == null || $payment->currency_id == $business->currency_id) {
                    $currency = $business->currency;
                } else {
                    $currency = $business->secondCurrency;
                }
            @endphp
            @include('contact.partials.payment_row', compact('payment', 'currency', 'count_child_payments', 'payment_types'))

            @if($count_child_payments > 0)
                @foreach($payment->child_payments as $child_payment)
                    @include('contact.partials.payment_row', ['payment' => $child_payment, 'currency', 'count_child_payments' => 0, 'payment_types' => $payment_types, 'parent_payment_ref_no' => $payment->payment_ref_no])
                @endforeach
            @endif
        @empty
            <tr>
                <td colspan="6" class="text-center">@lang('purchase.no_records_found')</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="text-right" style="width: 100%;" id="contact_payments_pagination">{{ $payments->links() }}</div>