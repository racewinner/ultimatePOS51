<div class="table-responsive">
    <table class="table table-bordered table-striped table-text-center" id="profit_by_day_table">
        <thead>
            <tr>
                <th>@lang('lang_v1.days')</th>
                <th>@lang('lang_v1.gross_profit')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($days as $day)
                <tr>
                    <td>@lang('lang_v1.' . $day)</td>
                    <td>
                        <span class="gross-profit" data-orig-value="{{$profits[$day] ?? 0}}" data-orig-value-currency2="{{ $profits_currency2
                            [$day] ?? 0 }}">
                            {{ \App\Utils\Util::format_currency($profits[$day], $currencies[0]) }}
                            @if($business->second_currency_id > 0) 
                                / {{ \App\Utils\Util::format_currency($profits_currency2[$day], $currencies[1]) }} 
                            @endif
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray font-17 footer-total">
                <td><strong>@lang('sale.total'):</strong></td>
                <td><span class="footer_total"></span></td>
            </tr>
        </tfoot>
    </table>
</div>