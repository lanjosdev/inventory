<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use App\Helpers\ResponseHelper;
use App\Models\Action;
use App\Models\SystemLog;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Endereços",
 *     description="Gerenciamento de endereços."
 * )
 */
class AddressController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/addresses/{id}",
     *     summary="Exibe um endereço",
     *     description="Retorna os dados de um endereço pelo ID.",
     *     tags={"Endereços"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Endereço encontrado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Endereço encontrado."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="country", type="string", example="Brasil"),
     *                 @OA\Property(property="state", type="string", example="São Paulo"),
     *                 @OA\Property(property="city", type="string", example="São Paulo"),
     *                 @OA\Property(property="address", type="string", example="Rua das Flores, 123"),
     *                 @OA\Property(property="cep", type="string", example="01234-567"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Endereço não encontrado"),
     *     @OA\Response(response=500, description="Erro ao buscar endereço.")
     * )
     */
    public function show($id)
    {
        try {
            $address = Address::find($id);
            if (!$address) {
                return ResponseHelper::error('Endereço não encontrado.', 404);
            }
            return ResponseHelper::success('Endereço encontrado.', $address);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro ao buscar endereço.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Erro ao buscar endereço.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/addresses/{id}",
     *     summary="Atualiza um endereço",
     *     description="Atualiza os dados de um endereço pelo ID.",
     *     tags={"Endereços"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"country", "state", "city", "address", "cep"},
     *             @OA\Property(property="country", type="string", example="Brasil"),
     *             @OA\Property(property="state", type="string", example="São Paulo"),
     *             @OA\Property(property="city", type="string", example="São Paulo"),
     *             @OA\Property(property="address", type="string", example="Rua das Flores, 123"),
     *             @OA\Property(property="cep", type="string", example="01234-567")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Endereço atualizado com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Endereço atualizado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="country", type="string", example="Brasil"),
     *                 @OA\Property(property="state", type="string", example="São Paulo"),
     *                 @OA\Property(property="city", type="string", example="São Paulo"),
     *                 @OA\Property(property="address", type="string", example="Rua das Flores, 123"),
     *                 @OA\Property(property="cep", type="string", example="01234-567"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Endereço não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Erro ao atualizar endereço.")
     * )
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $address = Address::find($id);
            if (!$address) {
                return ResponseHelper::error('Endereço não encontrado.', 404);
            }
            $validated = $request->validate(
                Address::rules(),
                Address::feedback()
            );
            $address->update($validated);
            // Log de sistema
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => Action::where('name', 'editou')->value('id'),
                'name_table' => 'addresses',
                'record_id' => $address->id,
                'description' => 'Endereço atualizado: ' . json_encode($address->toArray()),
            ]);
            DB::commit();
            return ResponseHelper::success('Endereço atualizado com sucesso.', $address);
        } catch (ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro ao atualizar endereço.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Erro ao atualizar endereço.', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/addresses/{id}",
     *     summary="Remove um endereço",
     *     description="Remove um endereço pelo ID.",
     *     tags={"Endereços"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Endereço removido com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Endereço removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Endereço não encontrado"),
     *     @OA\Response(response=500, description="Erro ao remover endereço.")
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $address = Address::find($id);
            if (!$address) {
                return ResponseHelper::error('Endereço não encontrado.', 404);
            }
            $address->delete();
            // Log de sistema
            SystemLog::create([
                'fk_user' => request()->user()->id ?? null,
                'fk_action' => Action::where('name', 'removeu')->value('id'),
                'name_table' => 'addresses',
                'record_id' => $address->id,
                'description' => 'Endereço removido: ' . json_encode($address->toArray()),
            ]);
            DB::commit();
            return ResponseHelper::success('Endereço removido com sucesso.');
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro ao remover endereço.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Erro ao remover endereço.', 500);
        }
    }
}