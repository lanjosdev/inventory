<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use App\Models\SystemLog;
use App\Models\Action as ActionModel;

class UserController extends Controller
{
    /**
     * Padrão: Todo método index (get-all) deve retornar resultados paginados (10 por página).
     *
     * @OA\Get(
     *     path="/api/users",
     *     summary="Lista usuários paginados",
     *     description="Retorna uma lista paginada de usuários. Parâmetros de filtro e paginação podem ser usados.",
     *     tags={"Usuários"},
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
     *         description="Lista de usuários retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de usuários obtida com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="João da Silva"),
     *                         @OA\Property(property="email", type="string", example="joao@email.com"),
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
     *         description="Erro interno ao buscar usuários"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $users = User::paginate(10)->appends(request()->all());
            $users->getCollection()->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at ?? null ?? null,
                    'updated_at' => $user->updated_at ?? null ?? null,
                    'deleted_at' => $user->deleted_at ?? null,
                ];
            });
            return ResponseHelper::success('Lista de usuários obtida com sucesso.', $users);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ops, algo inesperado aconteceu. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ops, algo aconteceu. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Exibe um usuário",
     *     description="Retorna os dados de um usuário pelo ID.",
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário encontrado."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João da Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@email.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar usuário"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return ResponseHelper::error('Usuário não encontrado.', 404);
            }
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at ?? null ?? null,
                'updated_at' => $user->updated_at ?? null ?? null,
                'deleted_at' => $user->deleted_at ?? null,
            ];
            return ResponseHelper::success('Usuário encontrado.', $userData);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ops, algo inesperado aconteceu. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ops, algo aconteceu. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Cria um novo usuário",
     *     description="Cria um usuário com os dados fornecidos.",
     *     tags={"Usuários"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="João da Silva"),
     *             @OA\Property(property="email", type="string", example="joao@email.com"),
     *             @OA\Property(property="password", type="string", example="senhaSegura123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário criado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João da Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@email.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao criar usuário"
     *     )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(User::rules(), User::feedback());
            $user = User::create($validated);
            // Log de auditoria - criação
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'criação')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Usuário criado: ' . json_encode($user->toArray()),
            ]);
            DB::commit();
            return ResponseHelper::success('Usuário criado com sucesso.', $user, 201);
        } catch (ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ops, algo inesperado aconteceu. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ops, algo aconteceu. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Atualiza um usuário",
     *     description="Atualiza os dados de um usuário existente.",
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="João da Silva"),
     *             @OA\Property(property="email", type="string", example="joao@email.com"),
     *             @OA\Property(property="password", type="string", example="novaSenha123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário atualizado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João da Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@email.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao atualizar usuário"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if (!$user) {
                return ResponseHelper::error('Usuário não encontrado.', 404);
            }
            $validated = $request->validate(User::rules(), User::feedback());
            $userOriginal = $user->getOriginal(); // Obtém os dados originais do usuário antes da atualização
            $user->update($validated);
            // Log de auditoria - atualização
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'atualização')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Usuário atualizado: de ' . json_encode($userOriginal) . ' para ' . json_encode($user->toArray()),
            ]);
            DB::commit();
            return ResponseHelper::success('Usuário atualizado com sucesso.', $user);
        } catch (ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ops, algo inesperado aconteceu. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ops, algo aconteceu. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Remove um usuário",
     *     description="Remove um usuário pelo ID.",
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao remover usuário"
     *     )
     * )
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if (!$user) {
                return ResponseHelper::error('Usuário não encontrado.', 404);
            }
            $user->delete();
            // Log de auditoria - remoção
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'remoção')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Usuário removido: ' . json_encode($user->toArray()),
            ]);
            DB::commit();
            return ResponseHelper::success('Usuário removido com sucesso.');
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ops, algo inesperado aconteceu. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ops, algo aconteceu. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/assign",
     *     summary="Atribui papéis e permissões ao usuário",
     *     description="Atribui um ou mais papéis (roles) e permissões ao usuário informado.",
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"admin"}),
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"read users"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Papéis e permissões atualizados com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Papéis e permissões atualizados com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="permissions", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao atribuir papéis/permissões"
     *     )
     * )
     */
    public function assignRolesPermissions(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if (!$user) {
                return ResponseHelper::error('Usuário não encontrado.', 404);
            }
            $roles = $request->input('roles', []); // array de nomes de roles
            $permissions = $request->input('permissions', []); // array de nomes de permissões

            if (!empty($roles)) {
                $user->syncRoles($roles);
            }
            if (!empty($permissions)) {
                $user->syncPermissions($permissions);
            }
            // Log de auditoria - atribuição de papéis/permissões
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'atribuição')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Papéis e permissões atribuídos: roles=' . json_encode($roles) . ', permissions=' . json_encode($permissions),
            ]);
            DB::commit();
            return ResponseHelper::success('Papéis e permissões atualizados com sucesso.', $user->load('roles', 'permissions'));
        } catch (ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ops, algo inesperado aconteceu. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ops, algo aconteceu. Tente novamente mais tarde.', 500);
        }
    }
}
