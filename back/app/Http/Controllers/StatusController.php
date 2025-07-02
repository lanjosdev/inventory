<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/status",
     *     summary="Lista status paginados",
     *     description="Retorna uma lista paginada de status.",
     *     tags={"Status"},
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
     *         description="Lista de status retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de status obtida com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Ativo"),
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
            $status = Status::paginate(10)->appends(request()->all());
            $status->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'created_at' => $item->created_at ?? null ?? null,
                    'updated_at' => $item->updated_at ?? null ?? null,
                    'deleted_at' => $item->deleted_at ?? null,
                ];
            });
            return ResponseHelper::success('Lista de status obtida com sucesso.', $status);
        } catch (\Illuminate\Database\QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/status",
     *     summary="Cria um novo status",
     *     description="Cria um status com os dados fornecidos.",
     *     tags={"Status"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Ativo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Status criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status criado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ativo")
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
            $validated = $request->validate(Status::rules(), Status::feedback());
            $status = Status::create($validated);
            DB::commit();
            return ResponseHelper::success('Status criado com sucesso.', $status, 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (\Illuminate\Database\QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/status/{id}",
     *     summary="Exibe um status",
     *     description="Retorna os dados de um status pelo ID.",
     *     tags={"Status"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do status",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status encontrado."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ativo"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Status não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $status = Status::find($id);
            if (!$status) {
                return ResponseHelper::error('Status não encontrado.', 404);
            }
            $data = [
                'id' => $status->id,
                'name' => $status->name,
                'created_at' => $status->created_at ?? null ?? null,
                'updated_at' => $status->updated_at ?? null ?? null,
                'deleted_at' => $status->deleted_at ?? null,
            ];
            return ResponseHelper::success('Status encontrado.', $data);
        } catch (\Illuminate\Database\QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Status $status)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/status/{id}",
     *     summary="Atualiza um status",
     *     description="Atualiza os dados de um status existente.",
     *     tags={"Status"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do status",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Ativo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status atualizado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ativo")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Status não encontrado"
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
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $status = Status::find($id);
            if (!$status) {
                return ResponseHelper::error('Status não encontrado.', 404);
            }
            $validated = $request->validate(Status::rules(), Status::feedback());
            $status->update($validated);
            DB::commit();
            return ResponseHelper::success('Status atualizado com sucesso.', $status);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (\Illuminate\Database\QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/status/{id}",
     *     summary="Remove um status",
     *     description="Remove um status pelo ID.",
     *     tags={"Status"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do status",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Status não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
     *     )
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $status = Status::find($id);
            if (!$status) {
                return ResponseHelper::error('Status não encontrado.', 404);
            }
            $status->delete();
            DB::commit();
            return ResponseHelper::success('Status removido com sucesso.');
        } catch (\Illuminate\Database\QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }
}
