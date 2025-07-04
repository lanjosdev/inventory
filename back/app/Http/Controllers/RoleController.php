<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Models\SystemLog;
use App\Models\Action;

/**
 * @OA\Schema(
 *   schema="Role",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="admin"),
 *   @OA\Property(property="description", type="string", example="Administrador do sistema"),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-04T14:21:51.000000Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-04T14:21:51.000000Z"),
 *   @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
 * )
 */
class RoleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Lista todos os papéis (roles)",
     *     tags={"Papéis"},
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
     *         description="Lista de papéis retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de papéis obtida com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Role")),
     *                 @OA\Property(property="total", type="integer", example=20),
     *                 @OA\Property(property="last_page", type="integer", example=2)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $roles = Role::with('permissions')->paginate($perPage)->appends($request->all());
            return ResponseHelper::success('Lista de papéis obtida com sucesso.', $roles);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/roles/{id}",
     *     summary="Exibe um papel (role)",
     *     tags={"Papéis"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Papel encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Papel encontrado."),
     *             @OA\Property(property="data", ref="#/components/schemas/Role")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Papel não encontrado")
     * )
     */
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        if (!$role) {
            return ResponseHelper::error('Papel não encontrado.', 404);
        }
        return ResponseHelper::success('Papel encontrado.', $role);
    }

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Cria um novo papel (role)",
     *     tags={"Papéis"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="admin"),
     *             @OA\Property(property="description", type="string", example="Administrador do sistema")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Papel criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Papel criado com sucesso."),
     *             @OA\Property(property="data", ref="#/components/schemas/Role")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate(Role::rules(), Role::feedback());
            $role = Role::create($validated);
            // Log de auditoria - criação
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => Action::where('name', 'criação')->value('id'),
                'name_table' => 'roles',
                'record_id' => $role->id,
                'description' => 'Papel criado: ' . json_encode($role->toArray()),
            ]);
            return ResponseHelper::success('Papel criado com sucesso.', $role, 201);
        } catch (ValidationException $ve) {
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro ao criar papel.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="Atualiza um papel (role)",
     *     tags={"Papéis"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="admin"),
     *             @OA\Property(property="description", type="string", example="Administrador do sistema")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Papel atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Papel atualizado com sucesso."),
     *             @OA\Property(property="data", ref="#/components/schemas/Role")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Papel não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return ResponseHelper::error('Papel não encontrado.', 404);
        }
        try {
            $validated = $request->validate(Role::rules($id), Role::feedback());
            $roleOriginal = $role->getOriginal();
            $role->update($validated);
            // Log de auditoria - atualização
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => Action::where('name', 'atualização')->value('id'),
                'name_table' => 'roles',
                'record_id' => $role->id,
                'description' => 'Papel atualizado: de ' . json_encode($roleOriginal) . ' para ' . json_encode($role->toArray()),
            ]);
            return ResponseHelper::success('Papel atualizado com sucesso.', $role);
        } catch (ValidationException $ve) {
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro ao atualizar papel.', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/roles/{id}",
     *     summary="Remove um papel (role)",
     *     tags={"Papéis"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Papel removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Papel removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Papel não encontrado")
     * )
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return ResponseHelper::error('Papel não encontrado.', 404);
        }
        try {
            $role->delete();
            // Log de auditoria - remoção
            SystemLog::create([
                'fk_user' => request()->user()->id ?? null,
                'fk_action' => Action::where('name', 'remoção')->value('id'),
                'name_table' => 'roles',
                'record_id' => $role->id,
                'description' => 'Papel removido: ' . json_encode($role->toArray()),
            ]);
            return ResponseHelper::success('Papel removido com sucesso.');
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro ao remover papel.', 500);
        }
    }
}