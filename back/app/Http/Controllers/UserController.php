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
     * @OA\Get(
     *     path="/api/users",
     *     summary="Lista usuários paginados",
     *     description="Retorna uma lista paginada de usuários. Parâmetros de filtro e paginação podem ser usados. O parâmetro 'active' permite filtrar usuários ativos, deletados ou ambos.",
     *     tags={"Usuários"},
     *     @OA\Parameter(name="page", in="query", description="Número da página", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Itens por página (padrão: 10)", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Parameter(name="active", in="query", description="Filtra usuários ativos (true), deletados (false) ou ambos (não informado)", required=false, @OA\Schema(type="boolean", example=true)),
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
     *                         @OA\Property(property="level", type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="name", type="string", example="Admin"),
     *                                 @OA\Property(property="permission", type="string", example="C,R,U,D")
     *                             )
     *                         ),
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
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function index(Request $request)
    {
        try {
            $active = $request->query('active', null);
            if ($active === 'true' || $active === true) {
                $query = User::query();
            } elseif ($active === 'false' || $active === false) {
                $query = User::onlyTrashed();
            } else {
                $query = User::withTrashed();
            }
            $users = $query->paginate(10)->appends($request->all());
            $users->getCollection()->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'level' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                            'permission' => $role->permissions()->pluck('name')->implode(',')
                        ];
                    }),
                    'created_at' => $user->created_at ?? null,
                    'updated_at' => $user->updated_at ?? null,
                    'deleted_at' => $user->deleted_at ?? null,
                ];
            });
            return ResponseHelper::success('Lista de usuários obtida com sucesso.', $users);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id_user}",
     *     summary="Exibe um usuário",
     *     description="Retorna os dados de um usuário pelo ID. O parâmetro 'active' permite buscar um usuário ativo, deletado ou ambos.",
     *     tags={"Usuários"},
     *     @OA\Parameter(name="id", in="path", description="ID do usuário", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="active", in="query", description="Filtra usuário ativo (true), deletado (false) ou ambos (não informado)", required=false, @OA\Schema(type="boolean", example=true)),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário encontrado."),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João da Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@email.com"),
     *                 @OA\Property(property="level", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Admin"),
     *                         @OA\Property(property="permission", type="string", example="C,R,U,D")
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function show(Request $request, $id)
    {
        try {
            $active = $request->query('active', null);
            if ($active === 'true' || $active === true) {
                $user = User::find($id);
            } elseif ($active === 'false' || $active === false) {
                $user = User::onlyTrashed()->find($id);
            } else {
                $user = User::withTrashed()->find($id);
            }
            if (!$user) {
                return ResponseHelper::error('Usuário não encontrado.', 404);
            }
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'level' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'permission' => $role->permissions()->pluck('name')->implode(',')
                    ];
                }),
                'created_at' => $user->created_at ?? null,
                'updated_at' => $user->updated_at ?? null,
                'deleted_at' => $user->deleted_at ?? null,
            ];
            return ResponseHelper::success('Usuário encontrado.', $userData);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
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
     *             @OA\Property(property="data",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João da Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@email.com"),
     *                 @OA\Property(property="level", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Admin"),
     *                         @OA\Property(property="permission", type="string", example="C,R,U,D")
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
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
                'fk_action' => ActionModel::where('name', 'criou')->value('id'),
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
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id_user}",
     *     summary="Atualiza um usuário",
     *     description="Atualiza os dados de um usuário existente.",
     *     tags={"Usuários"},
     *     @OA\Parameter(name="id", in="path", description="ID do usuário", required=true, @OA\Schema(type="integer", example=1)),
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
     *             @OA\Property(property="data",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João da Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@email.com"),
     *                 @OA\Property(property="level", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Admin"),
     *                         @OA\Property(property="permission", type="string", example="C,R,U,D")
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
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

            // Lógica de verificação de e-mail duplicado
            if ($user->email !== $validated['email']) {
                $emailExists = User::where('email', $validated['email'])->where('id', '!=', $id)->exists();
                if ($emailExists) {
                    return ResponseHelper::error([
                        'email' => ['Este e-mail já está cadastrado.']
                    ], 422);
                }
            }
            $userOriginal = $user->getOriginal(); // Obtém os dados originais do usuário antes da atualização
            $user->update($validated);
            // Log de auditoria - atualização
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'editou')->value('id'),
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
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
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
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
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
                'fk_action' => ActionModel::where('name', 'removeu')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Usuário removido: ' . json_encode($user->toArray()),
            ]);
            DB::commit();
            return ResponseHelper::success('Usuário removido com sucesso.');
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
     * @OA\Post(
     *     path="/api/users/{id_user}/assign",
     *     summary="Atribui papéis e permissões ao usuário",
     *     description="Atribui um ou mais papéis (roles) e permissões ao usuário informado. Agora aceita um array de objetos, cada um com um role e suas permissions.",
     *     tags={"Usuários"},
     *     @OA\Parameter(name="id", in="path", description="ID do usuário", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="level",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="role", type="string", example="manager"),
     *                     @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="C"))
     *                 ),
     *                 example={
     *                     {"role": "manager", "permissions": {"C", "R"}},
     *                     {"role": "midia", "permissions": {"C", "R", "U", "D"}}
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Papéis e permissões atualizados com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Papéis e permissões atualizados com sucesso."),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="role", type="string", example="manager"),
     *                     @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="C"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Erro interno ao atribuir papéis/permissões")
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
            $levels = $request->input('level', []); // array de objetos {role, permissions}
            if (!is_array($levels) || empty($levels)) {
                return ResponseHelper::error('O campo level deve ser um array de objetos.', 422);
            }
            $roleNames = [];
            $rolePermissionsMap = [];
            foreach ($levels as $item) {
                if (!isset($item['role']) || !is_string($item['role'])) {
                    return ResponseHelper::error('Cada item de level deve conter o campo role (string).', 422);
                }
                if (!isset($item['permissions']) || !is_array($item['permissions'])) {
                    return ResponseHelper::error('Cada item de level deve conter o campo permissions (array).', 422);
                }
                $roleNames[] = $item['role'];
                $rolePermissionsMap[$item['role']] = $item['permissions'];
            }
            // Validação dos roles
            $validRoles = \App\Models\Role::whereIn('name', $roleNames)->pluck('name')->toArray();
            if (count($roleNames) !== count($validRoles)) {
                return ResponseHelper::error('Um ou mais papéis (roles) são inválidos.', 422);
            }
            // Validação das permissões (todas)
            $allPermissions = array_unique(array_merge(...array_values($rolePermissionsMap)));
            $validPermissions = \App\Models\Permission::whereIn('name', $allPermissions)->pluck('name')->toArray();
            if (count($allPermissions) !== count($validPermissions)) {
                return ResponseHelper::error('Uma ou mais permissões são inválidas.', 422);
            }
            // Sincroniza roles
            $roleIds = \App\Models\Role::whereIn('name', $roleNames)->pluck('id', 'name')->toArray();
            $user->roles()->sync(array_values($roleIds));
            // Sincroniza permissions para cada role
            foreach ($rolePermissionsMap as $roleName => $permissions) {
                $role = \App\Models\Role::where('name', $roleName)->first();
                if ($role) {
                    $permissionIds = \App\Models\Permission::whereIn('name', $permissions)->pluck('id')->toArray();
                    $role->permissions()->sync($permissionIds);
                }
            }
            // Log de auditoria - atribuição de papéis/permissões
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Editou nível de acesso/permissão')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Papéis e permissões atribuídos: ' . json_encode($levels),
            ]);
            DB::commit();
            // Retorna os roles e permissions atuais do usuário
            $response = $user->roles->map(function ($role) {
                return [
                    'role' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->values(),
                ];
            });
            return ResponseHelper::success('Papéis e permissões atualizados com sucesso.', $response);
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
     * @OA\Post(
     *     path="/api/users/update-password",
     *     summary="Usuário altera a própria senha",
     *     tags={"Usuários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password", "password_confirmation"},
     *             @OA\Property(property="password", type="string", example="novaSenha123"),
     *             @OA\Property(property="password_confirmation", type="string", example="novaSenha123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Senha alterada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Senha alterada com sucesso.")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Erro interno ao alterar senha")
     * )
     */
    public function updatePassword(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $oldPassword = $user->password;

            $validated = $request->validate(User::rulesUpdatePassword(), User::feedbackUpdatePassword());
            if (\Illuminate\Support\Facades\Hash::check($request->password, $oldPassword)) {
                return ResponseHelper::error('Senha já utilizada anteriormente.', 422);
            }

            if ($validated) {
                $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
                $user->save();
            }

            // Log de auditoria
            SystemLog::create([
                'fk_user' => $user->id,
                'fk_action' => ActionModel::where('name', 'editou')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Usuário atualizou a própria senha.'
            ]);
            DB::commit();
            return ResponseHelper::success('Senha alterada com sucesso.');
        } catch (ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro interno ao alterar senha.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Erro interno ao alterar senha.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/update-password-admin",
     *     summary="Admin altera a senha de um usuário",
     *     tags={"Usuários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password", "password_confirmation"},
     *             @OA\Property(property="password", type="string", example="novaSenha123"),
     *             @OA\Property(property="password_confirmation", type="string", example="novaSenha123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Senha alterada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Senha alterada com sucesso.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Sem permissão"),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Erro interno ao alterar senha")
     * )
     */
    public function updatePasswordAdmin(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $admin = $request->user();
            // Exemplo: checagem de permissão admin (ajuste conforme sua lógica de roles)
            if (!$admin->roles()->where('name', 'admin')->exists()) {
                return ResponseHelper::error('Você não tem permissão de acesso para seguir adiante.', 403);
            }

            $user = User::find($id);
            if (!$user) {
                return ResponseHelper::error('Nenhum usuário encontrado.', 404);
            }
            $validated = $request->validate(User::rulesUpdatePasswordAdmin(), User::feedbackUpdatePasswordAdmin());

            if ($validated) {
                $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
                $user->save();
            }
            // Log de auditoria
            SystemLog::create([
                'fk_user' => $admin->id,
                'fk_action' => ActionModel::where('name', 'editou')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Admin atualizou a senha do usuário.'
            ]);
            DB::commit();
            return ResponseHelper::success('Senha alterada com sucesso.');
        } catch (ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro interno ao alterar senha.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Erro interno ao alterar senha.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/restore",
     *     summary="Restaura um usuário excluído (soft delete)",
     *     description="Restaura um usuário previamente excluído, tornando-o ativo novamente.",
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
     *         description="Usuário restaurado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário restaurado com sucesso."),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João da Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@email.com"),
     *                 @OA\Property(property="level", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Admin"),
     *                         @OA\Property(property="permission", type="string", example="C,R,U,D")
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-01T10:00:00Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Usuário não encontrado ou não está excluído"),
     *     @OA\Response(response=500, description="Erro interno ao restaurar usuário")
     * )
     */
    public function restore(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::withTrashed()->find($id);
            if (!$user || !$user->trashed()) {
                return ResponseHelper::error('Usuário não encontrado ou não está excluído.', 404);
            }
            $user->restore();
            // Log de auditoria - restauração
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'restaurou')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Usuário restaurado: ' . json_encode($user->toArray()),
            ]);
            DB::commit();
            // Monta resposta padrão
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'level' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'permission' => $role->permissions()->pluck('name')->implode(',')
                    ];
                }),
                'created_at' => $user->created_at ?? null,
                'updated_at' => $user->updated_at ?? null,
                'deleted_at' => $user->deleted_at ?? null,
            ];
            return ResponseHelper::success('Usuário restaurado com sucesso.', $userData);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro interno ao restaurar usuário.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Erro interno ao restaurar usuário.', 500);
        }
    }
}