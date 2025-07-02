<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

class RegisterController extends Controller
{
    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Registra um novo usuário",
     *     description="Cria um novo usuário e retorna um token de acesso.",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="João da Silva"),
     *             @OA\Property(property="email", type="string", example="usuario@email.com"),
     *             @OA\Property(property="password", type="string", example="senhaSegura123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário registrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuário registrado com sucesso"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="token.jwt.aqui"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João da Silva"),
     *                     @OA\Property(property="email", type="string", example="usuario@email.com")
     *                 )
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
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(User::rules(), User::feedback());
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;

            // Log de auditoria - registro
            SystemLog::create([
                'fk_user' => $user->id,
                'fk_action' => ActionModel::where('name', 'criou')->value('id'),
                'name_table' => 'users',
                'record_id' => $user->id,
                'description' => 'Usuário registrado: ' . json_encode($user->toArray()),
            ]);

            DB::commit();
            return ResponseHelper::success('Usuário registrado com sucesso', [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 201);
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
}
