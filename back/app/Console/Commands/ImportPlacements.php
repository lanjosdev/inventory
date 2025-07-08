<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use League\Csv\Reader;
use Exception;

class ImportPlacements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:locais-veiculacao {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa locais de veiculação do CSV para a tabela assets, associando ao asset_type correto.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("Arquivo CSV não encontrado: $file");
            return 1;
        }

        try {
            $csv = Reader::createFromPath($file, 'r');
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(',');

            // OTIMIZAÇÃO 1: Busca as lojas APENAS UMA VEZ, antes de começar o loop.
            $stores = Store::all();
            if ($stores->isEmpty()) {
                $this->error('Nenhuma loja encontrada no banco de dados.');
                return 1;
            }
            // OTIMIZAÇÃO 2: Cria um array para guardar todos os assets que serão inseridos.
            $allAssetsToInsert = [];

            DB::beginTransaction();

            foreach ($csv as $record) {
                // Valida se as colunas essenciais existem e não estão vazias
                if (empty($record['grupo']) || empty($record['nome']) || empty($record['quantidade'])) {
                    $this->warn("Linha inválida ou com dados faltando: " . json_encode($record));
                    continue;
                }

                // A busca pelo 'grupo' está correta conforme sua última versão.
                $asset_type = DB::table('asset_types')->where('name', trim($record['grupo']))->first();
                if (!$asset_type) {
                    $this->warn("Tipo de local (grupo) não encontrado: " . $record['grupo']);
                    continue;
                }

                $assetName = mb_strtoupper(trim($record['nome']), 'UTF-8');

                foreach ($stores as $store) {
                    for ($i = 0; $i < (int)$record['quantidade']; $i++) {
                        // OTIMIZAÇÃO 2: Adiciona o novo asset no array em vez de inserir direto no banco.
                        $allAssetsToInsert[] = [
                            'name'          => $assetName,
                            'fk_store'      => $store->id,
                            'fk_sector'     => 1,
                            'fk_asset_type' => $asset_type->id,
                            'fk_status'     => 1,
                            'observation'   => null,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ];
                    }
                }
            }

            // OTIMIZAÇÃO 2: Insere TODOS os assets de uma só vez.
            // O chunk(500) divide as inserções em lotes de 500 para não sobrecarregar a memória e o BD.
            if (!empty($allAssetsToInsert)) {
                foreach (array_chunk($allAssetsToInsert, 500) as $chunk) {
                    DB::table('assets')->insert($chunk);
                }
            }

            DB::commit();
            $this->info('Importação dos locais de veiculação realizada com sucesso.');
            return 0;
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Erro ao importar: ' . $e->getMessage());
            return 1;
        }
    }
}