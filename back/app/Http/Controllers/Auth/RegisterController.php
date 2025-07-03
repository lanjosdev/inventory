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
     *     summary="Registra um novo usuário (apenas admin)",
     *     description="Cria um novo usuário. Apenas administradores autenticados podem acessar este endpoint.",
     *     tags={"Autenticação"},
     *     security={{"sanctum":{}}},
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
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João da Silva"),
     *                     @OA\Property(property="email", type="string", example="usuario@email.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Apenas administradores podem registrar novos usuários.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Apenas administradores podem registrar novos usuários.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="object", example={"email": {"O campo email é obrigatório."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        // Permitir apenas admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return ResponseHelper::error('Apenas administradores podem registrar novos usuários.', 403);
        }
        DB::beginTransaction();
        try {
            $validated = $request->validate(User::rules(), User::feedback());
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

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
