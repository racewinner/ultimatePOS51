<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \biller\bcu\Cotizaciones;
use App\Business;
use App\NationExchgRateHistory;

class BcuController extends Controller
{
    public function getCurrentRate() {
        $rate = Cotizaciones::obtener();
        return response()->json([
            'exchg_rate' => $rate
        ]);
    }

    public function cronTask() {
        \Log::info("running bcu task...");

        try {
            // To add history
            $localtimezone = new \DateTimeZone('America/Montevideo');
            $now = new \DateTime('now', $localtimezone);
            $nowStr = $now->format('Y-m-d');
                            
            // To get exchange_rate from BCU.
            $rate = Cotizaciones::obtener();

            if($rate > 0) {
                // To update exchange_rate in businesses table.
                $businesses = Business::where("enable_bcu", 1)->get();
                foreach($businesses as $business) {
                    $secondExchgRate = ($business->currency_id == $business->nation_currency_id) ? $rate : round(1 / $rate, 2);
                    $business->update([
                        'nation_exchg_rate' => $rate,
                        'second_currency_exchg_rate' => $secondExchgRate
                    ]);

                    $nerh = NationExchgRateHistory::where('business_id', $business->id)->where('date', $nowStr)->first();
                    if(empty($nerh)) $nerh = new NationExchgRateHistory();

                    $nerh->date = $nowStr;
                    $nerh->business_id = $business->id;
                    $nerh->nation_exchg_rate = $rate;
                    $nerh->save();
                }
            }
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            exit($e->getMessage());
        }
    }
}
