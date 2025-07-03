<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Ações",
 *     description="Gerenciamento de ações do sistema"
 * )
 */
class ActionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/actions",
     *     tags={"Ações"},
     *     summary="Listar ações",
     *     description="Retorna uma lista paginada de ações.",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Número da página para paginação",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Quantidade de itens por página",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(response=200, description="Sucesso"),
     *     @OA\Response(response=401, description="Não autorizado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $actions = Action::paginate($perPage)->appends($request->all());
            return ResponseHelper::success('Ações listadas com sucesso.', $actions);
        } catch (\Exception $e) {
            Log::error('Erro ao listar ações: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/action",
     *     tags={"Ações"},
     *     summary="Criar ação",
     *     description="Cria uma nova ação.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Criado"),
     *     @OA\Response(response=400, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(Action::rules(), Action::feedback());
            $action = Action::create($validated);
            DB::commit();
            return ResponseHelper::success('Ação criada com sucesso.', $action, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar ação: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/actions/{id_action}",
     *     tags={"Ações"},
     *     summary="Exibir ação",
     *     description="Exibe uma ação específica.",
     *     @OA\Parameter(name="id_action", in="path", required=true, description="ID da ação", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso"),
     *     @OA\Response(response=404, description="Não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function show(Action $id_action)
    {
        try {
            return ResponseHelper::success('Ação encontrada.', $id_action);
        } catch (\Exception $e) {
            Log::error('Erro ao exibir ação: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/actions/{id_action}",
     *     tags={"Ações"},
     *     summary="Atualizar ação",
     *     description="Atualiza uma ação existente.",
     *     @OA\Parameter(name="id_action", in="path", required=true, description="ID da ação", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Ação atualizada com sucesso"),
     *     @OA\Response(response=400, description="Erro de validação"),
     *     @OA\Response(response=404, description="Não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function update(Request $request, Action $id_action)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(Action::rules($id_action->id), Action::feedback());
            $id_action->update($validated);
            DB::commit();
            return ResponseHelper::success('Ação atualizada com sucesso.', $id_action);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar ação: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/actions/{id_action}",
     *     tags={"Ações"},
     *     summary="Remover ação",
     *     description="Remove uma ação existente.",
     *     @OA\Parameter(name="id_action", in="path", required=true, description="ID da ação", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Ação removida com sucesso"),
     *     @OA\Response(response=404, description="Não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function destroy(Request $request, Action $id_action)
    {
        DB::beginTransaction();
        try {
            $id_action->delete();
            DB::commit();
            return ResponseHelper::success('Ação removida com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao remover ação: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }
}
