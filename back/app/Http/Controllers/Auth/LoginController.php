<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use App\Models\User;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use App\Models\SystemLog;
use App\Models\Action as ActionModel;
use App\Models\Role;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Realiza login do usuário",
     *     description="Autentica o usuário e retorna um token de acesso.",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="usuario@email.com"),
     *             @OA\Property(property="password", type="string", example="senhaSegura123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login realizado com sucesso"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="token.jwt.aqui"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João da Silva"),
     *                     @OA\Property(property="email", type="string", example="usuario@email.com")
     *                 ),
     *                 @OA\Property(property="level", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="admin")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ops, algo inesperado aconteceu. Tente novamente mais tarde."
     *     )
     * )
     */
    public function login(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(User::loginRules(), User::loginFeedback());

            $user = User::where(DB::raw('BINARY `email`'), $validated['email'])->first();
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                DB::rollBack();
                return ResponseHelper::error('Credenciais inválidas', 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $roles = DB::table('user_roles')
                ->join('roles', 'user_roles.fk_role', '=', 'roles.id')
                ->where('user_roles.fk_user', $user->id)
                ->select('roles.id', 'roles.name')
                ->get();

            // Log de auditoria - login
            SystemLog::create([
                'fk_user' => $user->id,
                'fk_action' => ActionModel::where('name', 'Conectou')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Login realizado com sucesso',
            ]);

            DB::commit();
            return ResponseHelper::success('Login realizado com sucesso', [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'level' => $roles,
            ]);
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
