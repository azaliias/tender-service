<?php

namespace App\Console\Commands;

use App\Models\Tender;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportTenders extends Command
{
    protected $signature = 'import:tenders';
    protected $description = 'Import tenders from CSV file';

    public function handle()
    {
        $path = storage_path('app/test_task_data.csv');

        if (!file_exists($path)) {
            $this->error("CSV file not found at: {$path}");
            return;
        }

        $file = fopen($path, 'r');

        // Пропускаем заголовок если есть
        fgetcsv($file);

        $count = 0;
        while (($row = fgetcsv($file)) !== false) {
            Tender::create([
                'external_code' => $row[0],
                'number'       => $row[1],
                'status'       => $row[2],
                'name'         => $row[3],
                'created_at'  => Carbon::createFromFormat('d.m.Y H:i:s', $row[4]),
                'updated_at'  => Carbon::createFromFormat('d.m.Y H:i:s', $row[4]),
            ]);
            $count++;
        }

        fclose($file);
        $this->info("Successfully imported {$count} tenders!");
    }
}
