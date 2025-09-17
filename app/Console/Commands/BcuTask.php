<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \biller\bcu\Cotizaciones;
use App\Business;

class BcuTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bcu:update_exchg_rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update exchange rate from BCU';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("running bcu task...");

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
            }
        }

        return Command::SUCCESS;
    }
}
