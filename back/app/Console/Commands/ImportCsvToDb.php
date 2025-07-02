<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use Exception;

class ImportCsvToDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa um arquivo CSV para uma tabela do banco de dados de forma dinâmica';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error('Arquivo CSV não encontrado.');
            return 1;
        }

        try {
            $file = $this->argument('file');

            if (!file_exists($file)) {
                $this->error("Arquivo CSV não encontrado: $file");
                return;
            }

            $csv = Reader::createFromPath($file, 'r');
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(',');

            $companyCounters = [];
            foreach ($csv as $record) {
                if (!isset($record['name']) || !isset($record['rede'])) {
                    $this->error("Linha inválida no CSV: " . json_encode($record));
                    continue;
                }

                // Buscar o id da company pelo nome da rede
                $company = DB::table('companies')->where('name', $record['rede'])->first();
                if (!$company) {
                    $this->error("Empresa (rede) não encontrada: " . $record['rede']);
                    continue;
                }
                $fkCompanie = $company->id;

                if (!isset($companyCounters[$fkCompanie])) {
                    $companyCounters[$fkCompanie] = 1;
                } else {
                    $companyCounters[$fkCompanie]++;
                }
                $storeName = mb_strtoupper($record['name'], 'UTF-8') . '-' . $companyCounters[$fkCompanie];

                // Inserir loja e obter ID
                $storeId = DB::table('stores')->insertGetId([
                    'name' => $storeName,
                    'fk_companie' => $fkCompanie,
                    'cnpj' => $record['cnpj'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Inserir endereço e obter ID
                $addressId = DB::table('addresses')->insertGetId([
                    'country' => $record['pais'] ?? null,
                    'state' => $record['estado'] ?? null,
                    'city' => $record['cidade'] ?? null,
                    'address' => $record['endereço'] ?? $record['endereco'] ?? null,
                    'cep' => $record['cep'] ?? $record['cep'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Relacionar endereço e loja
                DB::table('address_store')->insert([
                    'address_id' => $addressId,
                    'store_id' => $storeId
                ]);
            }

            $this->info('Importação dos produtos concluída com sucesso!');
        } catch (Exception $e) {
            $this->error("Erro ao importar produtos: " . $e->getMessage());
            Log::error('Erro na importação CSV: ' . $e->getMessage());
        }
    }
}