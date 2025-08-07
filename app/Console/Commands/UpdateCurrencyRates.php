<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Currencies\Models\Currency;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:update-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency exchange rates from external API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating currency exchange rates...');
        
        try {
            // In a real application, you would fetch rates from an API
            // For now, we'll just simulate the update
            
            $currencies = Currency::where('code', '!=', 'TRY')->get();
            
            foreach ($currencies as $currency) {
                // Simulate rate fluctuation
                $oldRate = $currency->exchange_rate;
                $fluctuation = (rand(-5, 5) / 100); // -5% to +5% change
                $newRate = $oldRate * (1 + $fluctuation);
                
                $currency->updateExchangeRate($newRate);
                
                $this->info("Updated {$currency->code}: {$oldRate} → {$newRate}");
            }
            
            $this->info('Currency rates updated successfully!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to update currency rates: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
