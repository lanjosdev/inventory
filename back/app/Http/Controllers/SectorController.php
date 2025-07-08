<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;

class SectorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/sectors",
     *     summary="Lista setores paginados",
     *     description="Retorna uma lista paginada de setores. Parâmetros de filtro e paginação podem ser usados.",
     *     tags={"Setores"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página (padrão: 10)",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de setores retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de setores obtida com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id_sector", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Financeiro"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                         @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *                     )
     *                 ),
     *                 @OA\Property(property="total", type="integer", example=20),
     *                 @OA\Property(property="last_page", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $sectors = Sector::paginate(10)->appends(request()->all());
            $sectors->getCollection()->transform(function ($sector) {
                return [
                    'id' => $sector->id,
                    'name' => $sector->name,
                    'created_at' => $sector->created_at ?? null ?? null,
                    'updated_at' => $sector->updated_at ?? null ?? null,
                    'deleted_at' => $sector->deleted_at ?? null,
                ];
            });
            return ResponseHelper::success('Lista de setores obtida com sucesso.', $sectors);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/sectors",
     *     summary="Cria um novo setor",
     *     description="Cria um setor com os dados fornecidos.",
     *     tags={"Setores"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Financeiro")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Setor criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Setor criado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Financeiro")
     *             )
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
            $validated = $request->validate(Sector::rules(), Sector::feedback());
            $sector = Sector::create($validated);
            DB::commit();
            return ResponseHelper::success('Setor criado com sucesso.', $sector, 201);
        } catch (ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/sectors/{id_sector}",
     *     summary="Exibe um setor",
     *     description="Retorna os dados de um setor pelo ID.",
     *     tags={"Setores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do setor",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Setor encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Setor encontrado."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Financeiro"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Setor não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
     *     )
     * )
     */
    public function show($id_sector)
    {
        try {
            $sector = Sector::find($id_sector);
            if (!$sector) {
                return ResponseHelper::error('Setor não encontrado.', 404);
            }
            $sectorData = [
                'id' => $sector->id,
                'name' => $sector->name,
                'created_at' => $sector->created_at ?? null ?? null,
                'updated_at' => $sector->updated_at ?? null ?? null,
                'deleted_at' => $sector->deleted_at ?? null,
            ];
            return ResponseHelper::success('Setor encontrado.', $sectorData);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/sectors/{id_sector}",
     *     summary="Atualiza um setor",
     *     description="Atualiza os dados de um setor existente.",
     *     tags={"Setores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do setor",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Financeiro")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Setor atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Setor atualizado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Financeiro")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Setor não encontrado"
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
    public function update(Request $request, $id_sector)
    {
        DB::beginTransaction();
        try {
            $sector = Sector::find($id_sector);
            if (!$sector) {
                return ResponseHelper::error('Setor não encontrado.', 404);
            }
            $validated = $request->validate(Sector::rules(), Sector::feedback());
            $sector->update($validated);
            DB::commit();
            return ResponseHelper::success('Setor atualizado com sucesso.', $sector);
        } catch (ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/sectors/{id_sector}",
     *     summary="Remove um setor",
     *     description="Remove um setor pelo ID.",
     *     tags={"Setores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do setor",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Setor removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Setor removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Setor não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
     *     )
     * )
     */
    public function destroy($id_sector)
    {
        DB::beginTransaction();
        try {
            $sector = Sector::find($id_sector);
            if (!$sector) {
                return ResponseHelper::error('Setor não encontrado.', 404);
            }
            $sector->delete();
            DB::commit();
            return ResponseHelper::success('Setor removido com sucesso.');
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }
}