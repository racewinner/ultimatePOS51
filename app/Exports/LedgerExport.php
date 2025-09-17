<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromArray;
use App\Utils\ContactUtil;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Lang;

class LedgerExport implements FromArray
{
    protected $ledger_details;
    protected $contact;

    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @return void
     */
    public function __construct(
        $ledger_details,
        $contact,
    ) {
        $this->ledger_details = $ledger_details;
        $this->contact = $contact;
    }

    public function array(): array
    {
        $ledger_array = [];
        //set headers
        $ledger_array[] = [trans('lang_v1.to').':', $this->contact->contact_address, '', '', '', '', '', '', 'Account summary', ''];

        $ledger_array[] = ['', '', '', '', '', '', '', '', $this->ledger_details['start_date'] . trans('lang_v1.to') . $this->ledger_details['end_date'], ''];

        $ledger_array[] = [trans('business.email') . ':' . $this->contact->email, '', '', '', '', '', '', '', trans('lang_v1.opening_balance'), $this->ledger_details['beginning_balance']];

        $ledger_array[] = [trans('contact.mobile') . ':' . $this->contact->mobile, '', '', '', '', '', '', '', trans('report.total_purchase'), $this->ledger_details['total_purchase']];

        $ledger_array[] = [$this->contact->tax_number ? (trans('contact.tax_no') . ':' . $this->contact->tax_number) : '', '', '', '', '', '', '', '', trans('lang_v1.total_invoice'), $this->ledger_details['total_invoice']];

        $ledger_array[] = ['', '', '', '', '', '', '', '', trans('sale.total_paid'), $this->ledger_details['total_paid']];

        $ledger_array[] = ['', '', '', '', '', '', '', '', trans('lang_v1.advance_balance'), $this->ledger_details['balance_due'] > 0 ? 0 : abs($this->ledger_details['balance_due'])];

        $ledger_array[] = ['', '', '', '', '', '', '', '', trans('lang_v1.balance_due'), $this->ledger_details['balance_due'] - $this->ledger_details['ledger_discount']];

        $ledger_array[] = [''];

        //set headers
        $ledger_array[] = [trans('lang_v1.date'), trans('purchase.ref_no'), trans('lang_v1.type'), trans('business.location'), trans('purchase.payment_status'), trans('account.debit'), trans('account.credit'), trans('lang_v1.balance'), trans('lang_v1.payment_method'), trans('lang_v1.others')];
        foreach ($this->ledger_details['ledger'] as $data) {
            $pattern = '/<small>(.*?)<\/small>/';
            $matches = [];
            preg_match($pattern, $data['others'], $matches);
            $product_arr = [
                $data['date'],
                $data['ref_no'],
                $data['type'],
                $data['location'],
                $data['payment_status'],
                $data['debit'] != '' ? $data['debit'] : '',
                $data['credit'] != '' ? $data['credit'] : '',
                $data['balance'],
                $data['payment_method'],
                !empty($matches) ? $matches[1] : ''
            ];

            $ledger_array[] = $product_arr;
        }

        return $ledger_array;
    }
}
