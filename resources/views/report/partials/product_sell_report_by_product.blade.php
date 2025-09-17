<div class="tab-pane" id="psr_by_product_tab">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" 
        id="product_sell_report_by_product" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('sale.product')</th>
                    <th>@lang('report.current_stock')</th>
                    <th>@lang('report.total_unit_sold')</th>
                    <th>@lang('sale.total')</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="bg-gray font-17 footer-total text-center">
                    <td><strong>@lang('sale.total'):</strong></td>
                    <td class="footer_psr_total_stock"></td>
                    <td class="footer_psr_total_sold"></td>
                    <td><span class="footer_psr_total_sell"></span></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>