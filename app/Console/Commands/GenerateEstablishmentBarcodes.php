<?php

namespace App\Console\Commands;

use App\Models\Establishment;
use Illuminate\Console\Command;

class GenerateEstablishmentBarcodes extends Command
{
    protected $signature = 'establishments:generate-barcodes';
    protected $description = 'Generate barcodes for establishments that do not have one';

    public function handle()
    {
        $establishments = Establishment::whereNull('barcode')->get();

        if ($establishments->isEmpty()) {
            $this->info('All establishments already have barcodes.');
            return 0;
        }

        $this->info("Generating barcodes for {$establishments->count()} establishments...");

        $bar = $this->output->createProgressBar($establishments->count());
        $bar->start();

        foreach ($establishments as $establishment) {
            $establishment->barcode = Establishment::generateUniqueBarcode();
            $establishment->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Barcodes generated successfully!');

        return 0;
    }
}
