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
     *     path="/assets",
     *     tags={"Ativos"},
     *     summary="Listar ativos",
     *     description="Retorna uma lista paginada de ativos.",
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=10)),
     *     @OA\Response(response=200, description="Lista paginada de ativos"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $assets = Asset::query()->paginate($perPage)->appends($request->all());
            return ResponseHelper::success('Ativos listados com sucesso.', $assets);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/assets",
     *     tags={"Ativos"},
     *     summary="Cria um novo ativo",
     *     description="Cria um novo ativo.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","fk_store","fk_sector","fk_asset_type","fk_status","quantity"},
     *             @OA\Property(property="name", type="string", example="Notebook"),
     *             @OA\Property(property="fk_store", type="integer", example=1),
     *             @OA\Property(property="fk_sector", type="integer", example=1),
     *             @OA\Property(property="fk_asset_type", type="integer", example=1),
     *             @OA\Property(property="fk_status", type="integer", example=1),
     *             @OA\Property(property="observation", type="string", example="Equipamento de TI"),
     *             @OA\Property(property="quantity", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Ativo criado com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(
                Asset::rules(),
                Asset::feedbacks()
            );
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
     *     path="/assets/{asset}",
     *     tags={"Ativos"},
     *     summary="Exibe um ativo",
     *     description="Retorna os dados de um ativo pelo ID.",
     *     @OA\Parameter(name="asset", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Ativo encontrado"),
     *     @OA\Response(response=404, description="Ativo não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function show(Asset $asset)
    {
        return ResponseHelper::success('Ativo encontrado.', $asset);
    }

    /**
     * @OA\Put(
     *     path="/assets/{asset}",
     *     tags={"Ativos"},
     *     summary="Atualiza um ativo",
     *     description="Atualiza os dados de um ativo.",
     *     @OA\Parameter(name="asset", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","fk_store","fk_sector","fk_asset_type","fk_status","quantity"},
     *             @OA\Property(property="name", type="string", example="Notebook"),
     *             @OA\Property(property="fk_store", type="integer", example=1),
     *             @OA\Property(property="fk_sector", type="integer", example=1),
     *             @OA\Property(property="fk_asset_type", type="integer", example=1),
     *             @OA\Property(property="fk_status", type="integer", example=1),
     *             @OA\Property(property="observation", type="string", example="Equipamento de TI"),
     *             @OA\Property(property="quantity", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Ativo atualizado com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=404, description="Ativo não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function update(Request $request, Asset $asset)
    {
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
     *     path="/assets/{asset}",
     *     tags={"Ativos"},
     *     summary="Remove um ativo",
     *     description="Remove (soft delete) um ativo.",
     *     @OA\Parameter(name="asset", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Ativo removido com sucesso"),
     *     @OA\Response(response=404, description="Ativo não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function destroy(Request $request, Asset $asset)
    {
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