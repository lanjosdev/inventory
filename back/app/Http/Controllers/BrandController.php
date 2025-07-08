<?php

namespace App\Http\Controllers;

use App\Models\Brand;
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
 *     name="Marcas",
 *     description="Gerenciamento de marcas"
 * )
 */
class BrandController extends Controller
{
    /**
     * @OA\Get(
     *     path="/brands",
     *     tags={"Marcas"},
     *     summary="Listar marcas",
     *     description="Retorna uma lista paginada de marcas, ordenadas por nome. Permite filtrar por nome exato.",
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=10)),
     *     @OA\Parameter(name="order_by", in="query", required=false, description="Campo de ordenação (padrão: name)", @OA\Schema(type="string", default="name")),
     *     @OA\Parameter(name="order_dir", in="query", required=false, description="Direção da ordenação (asc/desc, padrão: asc)", @OA\Schema(type="string", default="asc")),
     *     @OA\Parameter(name="name", in="query", required=false, description="Filtrar por nome exato da marca", @OA\Schema(type="string", example="Samsung")),
     *     @OA\Response(response=200, description="Lista paginada de marcas"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $orderBy = $request->get('order_by', 'name');
            $orderDir = $request->get('order_dir', 'asc');
            $query = Brand::query();
            if ($request->has('name') && $request->get('name') !== null) {
                $query->where('name', $request->get('name'));
            }
            $brands = $query->orderBy($orderBy, $orderDir)
                ->paginate($perPage)
                ->appends($request->all());
            return ResponseHelper::success('Marcas listadas com sucesso.', $brands);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/brands",
     *     tags={"Marcas"},
     *     summary="Criar uma nova marca",
     *     description="Cria uma nova marca.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Samsung")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Marca criada com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(
                Brand::rules(),
                Brand::feedbacks()
            );
            $brand = Brand::create($validated);
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Criou')->value('id'),
                'name_table' => 'brands',
                'record_id' => $brand->id,
                'description' => 'Criou marca',
            ]);
            DB::commit();
            return ResponseHelper::success('Marca criada com sucesso.', $brand, 201);
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
     *     path="/brands/{id_brand}",
     *     tags={"Marcas"},
     *     summary="Exibir uma marca",
     *     description="Retorna os dados de uma marca pelo ID.",
     *     @OA\Parameter(name="id_brand", in="path", required=true, description="ID da marca", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Marca encontrada"),
     *     @OA\Response(response=404, description="Marca não encontrada"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function show($id_brand)
    {
        try {
            $brand = Brand::find($id_brand);
            if (!$brand) {
                return ResponseHelper::error('Marca não encontrada', 404);
            }
            return ResponseHelper::success('Marca encontrada.', $brand);
        } catch (ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        } catch (QueryException $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/brands/{id_brand}",
     *     tags={"Marcas"},
     *     summary="Atualizar uma marca",
     *     description="Atualiza os dados de uma marca.",
     *     @OA\Parameter(name="id_brand", in="path", required=true, description="ID da marca", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Samsung")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Marca atualizada com sucesso"),
     *     @OA\Response(response=404, description="Marca não encontrada"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function update(Request $request, $id_brand)
    {
        DB::beginTransaction();
        try {
            $brand = Brand::find($id_brand);
            if (!$brand) {
                return ResponseHelper::error('Marca não encontrada', 404);
            }
            $validated = $request->validate(
                Brand::rules($brand->id ?? null),
                Brand::feedbacks()
            );
            $brand->update($validated);
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Atualizou')->value('id'),
                'name_table' => 'brands',
                'record_id' => $brand->id,
                'description' => 'Atualizou marca',
            ]);
            DB::commit();
            return ResponseHelper::success('Marca atualizada com sucesso.', $brand);
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
     *     path="/brands/{id_brand}",
     *     tags={"Marcas"},
     *     summary="Remover uma marca",
     *     description="Remove (soft delete) uma marca.",
     *     @OA\Parameter(name="id_brand", in="path", required=true, description="ID da marca", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Marca removida com sucesso"),
     *     @OA\Response(response=404, description="Marca não encontrada"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function destroy(Request $request, $id_brand)
    {
        DB::beginTransaction();
        try {
            $brand = Brand::find($id_brand);
            if (!$brand) {
                return ResponseHelper::error('Marca não encontrada', 404);
            }
            $brand->delete();
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Removeu')->value('id'),
                'name_table' => 'brands',
                'record_id' => $brand->id,
                'description' => 'Removeu marca',
            ]);
            DB::commit();
            return ResponseHelper::success('Marca removida com sucesso.', $brand);
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