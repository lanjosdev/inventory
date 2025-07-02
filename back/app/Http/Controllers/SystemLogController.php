<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="SystemLog",
 *     description="Logs do sistema"
 * )
 */
class SystemLogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/system-logs",
     *     tags={"SystemLog"},
     *     summary="Listar logs do sistema",
     *     description="Retorna uma lista paginada de logs do sistema.",
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
     *     @OA\Response(response=500, description="Ops, algo inesperado aconteceu. Tente novamente mais tarde.")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $logs = SystemLog::paginate($perPage)->appends($request->all());
            return ResponseHelper::success('Logs listados com sucesso.', $logs);
        } catch (\Exception $e) {
            Log::error('Erro ao listar logs do sistema: ' . $e->getMessage());
            return ResponseHelper::error('Ops, algo inesperado aconteceu. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/system-logs/{systemLog}",
     *     tags={"SystemLog"},
     *     summary="Exibir log do sistema",
     *     description="Exibe um log do sistema específico.",
     *     @OA\Parameter(name="systemLog", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso"),
     *     @OA\Response(response=404, description="Não encontrado"),
     *     @OA\Response(response=500, description="Ops, algo inesperado aconteceu. Tente novamente mais tarde.")
     * )
     */
    public function show(SystemLog $systemLog)
    {
        try {
            return ResponseHelper::success('Log encontrado.', $systemLog);
        } catch (\Exception $e) {
            Log::error('Erro ao exibir log do sistema: ' . $e->getMessage());
            return ResponseHelper::error('Ops, algo inesperado aconteceu. Tente novamente mais tarde.', 500);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SystemLog $systemLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemLog $systemLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemLog $systemLog)
    {
        //
    }
}
