<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use App\Models\SystemLog;
use App\Models\Action as ActionModel;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use App\Http\Requests\AssetTypeRequest;

class AssetTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/asset-types",
     *     summary="Lista tipos de ativos",
     *     description="Retorna uma lista paginada de tipos de ativos.",
     *     tags={"Tipos de Ativo"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de tipos de ativos",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $assetTypes = AssetType::query()->paginate($perPage)->appends($request->all());
        return ResponseHelper::success('Tipos de ativos listados com sucesso.', $assetTypes);
    }

    /**
     * @OA\Post(
     *     path="/asset-types",
     *     summary="Cria um novo tipo de ativo",
     *     description="Cria um novo tipo de ativo.",
     *     tags={"Tipos de Ativo"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Notebook"),
     *             @OA\Property(property="observation", type="string", example="Equipamento de TI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de ativo criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
     *     )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(
                AssetType::rules(),
                AssetType::feedback()
            );
            $assetType = AssetType::create($validated);
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Criou')->value('id'),
                'name_table' => 'asset_types',
                'record_id' => $assetType->id,
                'description' => 'Criou tipo de ativo',
            ]);
            DB::commit();
            return ResponseHelper::success('Tipo de ativo criado com sucesso.', $assetType, 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), 422);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/asset-types/{id}",
     *     summary="Exibe um tipo de ativo",
     *     description="Retorna os dados de um tipo de ativo pelo ID.",
     *     tags={"Tipos de Ativo"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de ativo encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de ativo não encontrado"
     *     )
     * )
     */
    public function show(AssetType $assetType)
    {
        return ResponseHelper::success('Tipo de ativo encontrado.', $assetType);
    }

    /**
     * @OA\Put(
     *     path="/asset-types/{id}",
     *     summary="Atualiza um tipo de ativo",
     *     description="Atualiza os dados de um tipo de ativo.",
     *     tags={"Tipos de Ativo"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Notebook"),
     *             @OA\Property(property="observation", type="string", example="Equipamento de TI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de ativo atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de ativo não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
     *     )
     * )
     */
    public function update(Request $request, AssetType $assetType)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(
                AssetType::rules($assetType->id),
                AssetType::feedback()
            );
            $assetType->update($validated);
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Atualizou')->value('id'),
                'name_table' => 'asset_types',
                'record_id' => $assetType->id,
                'description' => 'Atualizou tipo de ativo',
            ]);
            DB::commit();
            return ResponseHelper::success('Tipo de ativo atualizado com sucesso.', $assetType);
        } catch (ValidationException $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), 422);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/asset-types/{id}",
     *     summary="Remove um tipo de ativo",
     *     description="Remove (soft delete) um tipo de ativo.",
     *     tags={"Tipos de Ativo"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de ativo removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de ativo não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
     *     )
     * )
     */
    public function destroy(Request $request, AssetType $assetType)
    {
        DB::beginTransaction();
        try {
            $assetType->delete();
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Removeu')->value('id'),
                'name_table' => 'asset_types',
                'record_id' => $assetType->id,
                'description' => 'Removeu tipo de ativo',
            ]);
            DB::commit();
            return ResponseHelper::success('Tipo de ativo removido com sucesso.', $assetType);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }
}
