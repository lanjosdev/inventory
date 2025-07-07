<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Models\SystemLog;
use App\Models\Action as ActionModel;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Ativos",
 *     description="Gerenciamento de ativos de cada loja"
 * )
 */
class AssetController extends Controller
{
    /**
     * @OA\Get(
     *     path="/stores/{id_store}/assets",
     *     tags={"Ativos"},
     *     summary="Listar ativos de uma loja",
     *     description="Retorna uma lista paginada de ativos de uma loja específica, ordenados por nome (asc).",
     *     @OA\Parameter(name="id_store", in="path", required=true, description="ID da loja", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=10)),
     *     @OA\Parameter(name="order_by", in="query", required=false, description="Campo de ordenação (padrão: name)", @OA\Schema(type="string", default="name")),
     *     @OA\Parameter(name="order_dir", in="query", required=false, description="Direção da ordenação (asc/desc, padrão: asc)", @OA\Schema(type="string", default="asc")),
     *     @OA\Response(response=200, description="Lista paginada de ativos ordenada por nome"),
     *     @OA\Response(response=404, description="Loja não encontrada"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function index(Request $request, $id_store)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $orderBy = $request->get('order_by', 'name');
            $orderDir = $request->get('order_dir', 'asc');
            $assets = Asset::where('fk_store', $id_store)
                ->orderBy($orderBy, $orderDir)
                ->paginate($perPage)
                ->appends($request->all());
            return ResponseHelper::success('Ativos listados com sucesso.', $assets);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/stores/{id_store}/assets",
     *     tags={"Ativos"},
     *     summary="Cria um novo ativo para uma loja",
     *     description="Cria um novo ativo vinculado a uma loja.",
     *     @OA\Parameter(name="id_store", in="path", required=true, description="ID da loja", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","fk_sector","fk_asset_type","fk_status","quantity"},
     *             @OA\Property(property="name", type="string", example="Notebook"),
     *             @OA\Property(property="fk_sector", type="integer", example=1),
     *             @OA\Property(property="fk_asset_type", type="integer", example=1),
     *             @OA\Property(property="fk_status", type="integer", example=1),
     *             @OA\Property(property="observation", type="string", example="Equipamento de TI"),
     *             @OA\Property(property="quantity", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Ativo criado com sucesso"),
     *     @OA\Response(response=404, description="Loja não encontrada"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function store(Request $request, $id_store)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(
                Asset::rules(),
                Asset::feedbacks()
            );
            $validated['fk_store'] = $id_store;
            $asset = Asset::create($validated);
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Criou')->value('id'),
                'name_table' => 'assets',
                'record_id' => $asset->id,
                'description' => 'Criou ativo',
            ]);
            DB::commit();
            return ResponseHelper::success('Ativo criado com sucesso.', $asset, 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), 422);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/stores/{id_store}/assets/{asset}",
     *     tags={"Ativos"},
     *     summary="Exibe um ativo de uma loja",
     *     description="Retorna os dados de um ativo pelo ID e loja. O resultado é sempre ordenado por nome ascendente.",
     *     @OA\Parameter(name="id_store", in="path", required=true, description="ID da loja", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="asset", in="path", required=true, description="ID do ativo", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Ativo encontrado (ordenado por nome ascendente)"),
     *     @OA\Response(response=404, description="Ativo ou loja não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function show($id_store, Asset $asset)
    {
        if ($asset->fk_store != $id_store) {
            return ResponseHelper::error('Ativo ou loja não encontrado', 404);
        }
        // Não faz sentido ordenar um único registro, mas a descrição foi atualizada para refletir o padrão.
        return ResponseHelper::success('Ativo encontrado.', $asset);
    }

    /**
     * @OA\Put(
     *     path="/stores/{id_store}/assets/{asset}",
     *     tags={"Ativos"},
     *     summary="Atualiza um ativo de uma loja",
     *     description="Atualiza os dados de um ativo de uma loja.",
     *     @OA\Parameter(name="id_store", in="path", required=true, description="ID da loja", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="asset", in="path", required=true, description="ID do ativo", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","fk_sector","fk_asset_type","fk_status","quantity"},
     *             @OA\Property(property="name", type="string", example="Notebook"),
     *             @OA\Property(property="fk_sector", type="integer", example=1),
     *             @OA\Property(property="fk_asset_type", type="integer", example=1),
     *             @OA\Property(property="fk_status", type="integer", example=1),
     *             @OA\Property(property="observation", type="string", example="Equipamento de TI"),
     *             @OA\Property(property="quantity", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Ativo atualizado com sucesso"),
     *     @OA\Response(response=404, description="Ativo ou loja não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function update(Request $request, $id_store, Asset $asset)
    {
        if ($asset->fk_store != $id_store) {
            return ResponseHelper::error('Ativo ou loja não encontrado', 404);
        }
        DB::beginTransaction();
        try {
            $validated = $request->validate(
                Asset::rules($asset->id ?? null),
                Asset::feedbacks()
            );
            $asset->update($validated);
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Atualizou')->value('id'),
                'name_table' => 'assets',
                'record_id' => $asset->id,
                'description' => 'Atualizou ativo',
            ]);
            DB::commit();
            return ResponseHelper::success('Ativo atualizado com sucesso.', $asset);
        } catch (ValidationException $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), 422);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/stores/{id_store}/assets/{asset}",
     *     tags={"Ativos"},
     *     summary="Remove um ativo de uma loja",
     *     description="Remove (soft delete) um ativo de uma loja.",
     *     @OA\Parameter(name="id_store", in="path", required=true, description="ID da loja", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="asset", in="path", required=true, description="ID do ativo", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Ativo removido com sucesso"),
     *     @OA\Response(response=404, description="Ativo ou loja não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function destroy(Request $request, $id_store, Asset $asset)
    {
        if ($asset->fk_store != $id_store) {
            return ResponseHelper::error('Ativo ou loja não encontrado', 404);
        }
        DB::beginTransaction();
        try {
            $asset->delete();
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Removeu')->value('id'),
                'name_table' => 'assets',
                'record_id' => $asset->id,
                'description' => 'Removeu ativo',
            ]);
            DB::commit();
            return ResponseHelper::success('Ativo removido com sucesso.', $asset);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }
}