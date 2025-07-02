<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SystemLog;
use App\Models\Action as ActionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use App\Helpers\ResponseHelper;

class LogoutController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Realiza logout do usuário",
     *     description="Revoga o token de acesso do usuário autenticado.",
     *     tags={"Autenticação"},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado"
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
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            // Log de auditoria - logout
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Desconectou')->value('id'),
                'name_table' => 'users',
                'record_id' => $request->user()->id ?? null,
                'description' => 'Logout realizado com sucesso',
            ]);
            return ResponseHelper::success('Logout realizado com sucesso');
        } catch (ValidationException $ve) {
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ops, algo inesperado aconteceu. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ops, algo aconteceu. Tente novamente mais tarde.', 500);
        }
    }
}
