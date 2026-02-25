<?php

namespace App\Console\Commands;

use App\Models\ItemMaster;
use App\Models\ItemVariant;
use App\Models\StockCurrent;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Console\Command;
use League\Csv\Reader;
use League\Csv\Statement;

class ImportItemsCsv extends Command
{
    /**
     * Contoh:
     * php artisan import:items storage/app/import/items.csv --delimiter=";" --dry
     */
    protected $signature = 'import:items
        {csv : Path ke file CSV (mis. storage/app/import/items.csv)}
        {--dry : Dry-run (tidak menulis DB)}
        {--delimiter=, : Pembatas kolom (default ",")}';

    protected $description = 'Import Item Master + Variant + StockCurrent dari file CSV';

    public function handle()
    {
        $csvPath   = $this->argument('csv');
        $delimiter = (string) $this->option('delimiter') ?: ',';
        $dry       = (bool) $this->option('dry');

        $this->info('Mulai import…');
        $this->line('File  : ' . $csvPath);
        $this->line('Mode  : ' . ($dry ? 'DRY-RUN (tidak menulis DB)' : 'WRITE'));
        $this->line('Delim : "' . $delimiter . '"');
        $this->line('DB    : ' . config('database.connections.mysql.database'));

        if (! file_exists($csvPath)) {
            $this->error('File CSV tidak ditemukan.');
            return self::FAILURE;
        }

        // ───────────── CSV setup ─────────────
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setDelimiter($delimiter);
        $csv->setHeaderOffset(0);

        $headerRaw = $csv->getHeader();

        $normalizeCol = function ($h) {
            $h = preg_replace('/^\xEF\xBB\xBF/', '', $h); // buang BOM
            return strtolower(trim($h));
        };

        $headerNorm = array_map($normalizeCol, $headerRaw);

        $expected = [
            'item_code',
            'item_name',
            'category',
            'base_unit',
            'brand',
            'lot_number',
            'expiration_date',
            'warnings',
            'storage_temp',
            'size',
        ];
        $expectedNorm = array_map($normalizeCol, $expected);

        $missing = array_diff($expectedNorm, $headerNorm);
        if (count($missing) > 0) {
            $this->error('Header CSV tidak sesuai.');
            $this->line('Header sekarang : ' . implode(',', $headerNorm));
            $this->line('Header diharapkan: ' . implode(',', $expectedNorm));
            $this->line('Kolom yang belum ada: ' . implode(', ', $missing));
            $this->comment('Periksa lagi header di baris pertama file CSV.');
            return self::FAILURE;
        }

        $indexToKey = [];
        foreach ($headerRaw as $i => $nameRaw) {
            $indexToKey[$i] = $normalizeCol($nameRaw);
        }

        $stmt    = (new Statement());
        $records = $stmt->process($csv);

        if (count($records) === 0) {
            $this->warn('Tidak ada baris data di CSV.');
            return self::SUCCESS;
        }

        $createdMasters  = 0;
        $updatedMasters  = 0;
        $createdVariants = 0;

        $this->info('Jumlah baris data: ' . count($records));
        $bar = $this->output->createProgressBar(count($records));
        $bar->start();

        foreach ($records as $rowRaw) {
            $row = [];
            foreach ($rowRaw as $i => $val) {
                $key       = $indexToKey[$i] ?? null;
                if ($key === null) {
                    continue;
                }
                $row[$key] = is_string($val) ? trim($val) : $val;
            }

            $itemCode = $row['item_code'] ?? '';
            $itemName = $row['item_name'] ?? '';
            if ($itemCode === '' && $itemName === '') {
                $bar->advance();
                continue;
            }

            $categoryName = $row['category'] ?? null;
            $baseUnit     = $row['base_unit'] ?? null;
            $brand        = $row['brand'] ?? null;
            $lotNumber    = $row['lot_number'] ?? null;
            $expDateRaw   = $row['expiration_date'] ?? null;
            $warnings     = $row['warnings'] ?? null;
            $storageTemp  = $row['storage_temp'] ?? null;
            $size         = $row['size'] ?? null;

            $expDate = null;
            if (! empty($expDateRaw)) {
                try {
                    $expDate = Carbon::parse($expDateRaw)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $this->warn("Tanggal exp tidak valid untuk kode {$itemCode}: {$expDateRaw}");
                }
            }

            if (! $dry) {
                // ── Category ──
                $categoryId = null;
                if ($categoryName) {
                    $category = Category::firstOrCreate(
                        ['category_name' => $categoryName],
                        ['description'   => null]
                    );
                    $categoryId = $category->id;
                }

                // ── Item Master ──
                $master = ItemMaster::firstOrCreate(
                    ['item_code' => $itemCode],
                    [
                        'item_name'    => $itemName,
                        'base_unit'    => $baseUnit,
                        'category_id'  => $categoryId,
                        'warnings'     => $warnings,
                        'storage_temp' => $storageTemp,
                        'size'         => $size,
                    ]
                );

                if ($master->wasRecentlyCreated) {
                    $createdMasters++;
                } else {
                    // update info tambahan bila ada yang baru
                    $dirty = false;
                    $fill  = [];

                    if ($itemName && $itemName !== $master->item_name) {
                        $fill['item_name'] = $itemName;
                    }
                    if ($baseUnit && $baseUnit !== $master->base_unit) {
                        $fill['base_unit'] = $baseUnit;
                    }
                    if ($categoryId && $categoryId !== $master->category_id) {
                        $fill['category_id'] = $categoryId;
                    }
                    if ($warnings && $warnings !== $master->warnings) {
                        $fill['warnings'] = $warnings;
                    }
                    if ($storageTemp && $storageTemp !== $master->storage_temp) {
                        $fill['storage_temp'] = $storageTemp;
                    }
                    if ($size && $size !== $master->size) {
                        $fill['size'] = $size;
                    }

                    if (! empty($fill)) {
                        $master->fill($fill)->save();
                        $updatedMasters++;
                    }
                }

                // ── Variant ──
                if (! empty($brand)) {
                    $variant = ItemVariant::firstOrCreate(
                        [
                            'item_master_id'  => $master->id,
                            'brand'           => $brand,
                            'lot_number'      => $lotNumber,
                            'expiration_date' => $expDate,
                        ],
                        []
                    );

                    if ($variant->wasRecentlyCreated) {
                        $createdVariants++;
                    }

                    $variant->stock()->firstOrCreate(
                        ['item_variant_id' => $variant->id],
                        ['current_quantity' => 0]
                    );
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->line('Import selesai.');
        $this->line('Master baru     : ' . $createdMasters);
        $this->line('Master diupdate : ' . $updatedMasters);
        $this->line('Variant baru    : ' . $createdVariants);

        return self::SUCCESS;
    }
}
