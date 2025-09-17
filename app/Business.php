<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'woocommerce_api_settings'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['woocommerce_api_settings'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ref_no_prefixes' => 'array',
        'enabled_modules' => 'array',
        'email_settings' => 'array',
        'sms_settings' => 'array',
        'common_settings' => 'array',
        'weighing_scale_setting' => 'array',
    ];

    /**
     * Returns the date formats
     */
    public static function date_formats()
    {
        return [
            'd-m-Y' => 'dd-mm-yyyy',
            'm-d-Y' => 'mm-dd-yyyy',
            'd/m/Y' => 'dd/mm/yyyy',
            'm/d/Y' => 'mm/dd/yyyy',
        ];
    }

    /**
     * Get the owner details
     */
    public function owner()
    {
        return $this->hasOne(\App\User::class, 'id', 'owner_id');
    }

    /**
     * Get the Business currency.
     */
    public function currency()
    {
        return $this->belongsTo(\App\Currency::class);
    }

    public function secondCurrency() {
        return $this->belongsTo(\App\Currency::class, 'second_currency_id');
    }

    /**
     * Get the Business currency.
     */
    public function locations()
    {
        return $this->hasMany(\App\BusinessLocation::class);
    }

    /**
     * Get the Business printers.
     */
    public function printers()
    {
        return $this->hasMany(\App\Printer::class);
    }

    /**
     * Get the Business subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany('\Modules\Superadmin\Entities\Subscription');
    }

    public function invoice_layouts() {
        return $this->hasMany(\App\InvoiceLayout::class);
    }

    /**
     * Creates a new business based on the input provided.
     *
     * @return object
     */
    public static function create_business($details)
    {
        $business = Business::create($details);

        return $business;
    }

    /**
     * Updates a business based on the input provided.
     *
     * @param  int  $business_id
     * @param  array  $details
     * @return object
     */
    public static function update_business($business_id, $details)
    {
        if (! empty($details)) {
            Business::where('id', $business_id)
                ->update($details);
        }
    }

    public function getBusinessAddressAttribute()
    {
        $location = $this->locations->first();
        $address = $location->landmark.', '.$location->city.
        ', '.$location->state.'<br>'.$location->country.', '.$location->zip_code;

        return $address;
    }

    public function isMainCurrency($currency_id) 
    {
        return ($currency_id == null || $currency_id == $this->currency_id);
    }

    public function isNationCurrency($currency_id)
    {
        return ($currency_id == null || $this->nation_currency_id == null || $currency_id == $this->nation_currency_id);
    }

    public function currencies()
    {
        $output = [];
        $currency = $this->currency;
        $firstCurrency = [
            'id' => $currency->id,
            'thousand_separator' => $currency->thousand_separator,
            'decimal_separator' => $currency->decimal_separator,
            'symbol' => $currency->symbol,
            'code' => $currency->code,
            'name' => $currency->currency . '(' . $currency->country . ')',
        ];
        $output[] = (object)$firstCurrency;

        if($business->second_currency_id > 0) {
            $currency = $this->secondCurrency;
            $secondCurrency = [
                'id' => $currency->id,
                'thousand_separator' => $currency->thousand_separator,
                'decimal_separator' => $currency->decimal_separator,
                'symbol' => $currency->symbol,
                'code' => $currency->code,
                'name' => $currency->currency . '(' . $currency->country . ')',
            ];
            $output[] = (object)$secondCurrency;
        }

        return $output;
    }

    public function currenciesDropdown() {
        $output = [];
        $currencies = $this->currencies();
        foreach($currencies as $currency) {
            $output[$currency->id] = $currency->name;
        }
        return $output;
    }
}
