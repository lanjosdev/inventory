<?php

namespace App\Http\Controllers;

use App\Models\Contact;
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
 *     name="Contatos",
 *     description="Gerenciamento de contatos."
 * )
 */

class ContactController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/contacts/{id}",
     *     summary="Exibe um contato",
     *     description="Retorna os dados de um contato pelo ID.",
     *     tags={"Contatos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Contato encontrado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Contato encontrado."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Contato Exemplo"),
     *                 @OA\Property(property="email", type="string", example="contato@email.com"),
     *                 @OA\Property(property="phone", type="string", example="11999999999"),
     *                 @OA\Property(property="observation", type="string", example="Observação do contato", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Contato não encontrado"),
     *     @OA\Response(response=500, description="Erro ao buscar contato.")
     * )
     */
    public function show($id)
    {
        try {
            $contact = Contact::find($id);
            if (!$contact) {
                return ResponseHelper::error('Contato não encontrado.', 404);
            }
            return ResponseHelper::success('Contato encontrado.', $contact);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro ao buscar contato.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Erro ao buscar contato.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/contacts/{id}",
     *     summary="Atualiza um contato",
     *     description="Atualiza os dados de um contato pelo ID.",
     *     tags={"Contatos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "phone"},
     *             @OA\Property(property="name", type="string", example="Contato Atualizado"),
     *             @OA\Property(property="email", type="string", format="email", example="novo@email.com"),
     *             @OA\Property(property="phone", type="string", example="11988887777"),
     *             @OA\Property(property="observation", type="string", example="Nova observação", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contato atualizado com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Contato atualizado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Contato Atualizado"),
     *                 @OA\Property(property="email", type="string", example="novo@email.com"),
     *                 @OA\Property(property="phone", type="string", example="11988887777"),
     *                 @OA\Property(property="observation", type="string", example="Nova observação", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Contato não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Erro ao atualizar contato.")
     * )
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $contact = Contact::find($id);
            if (!$contact) {
                return ResponseHelper::error('Contato não encontrado.', 404);
            }
            $validated = $request->validate(
                Contact::rules(),
                Contact::feedback()
            );
            
            $contact->update($validated);
            
            // Log de sistema
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => Action::where('name', 'editou')->value('id'),
                'name_table' => 'contacts',
                'record_id' => $contact->id,
                'description' => 'Contato atualizado: ' . json_encode($contact->toArray()),
            ]);
            DB::commit();
            return ResponseHelper::success('Contato atualizado com sucesso.', $contact);
        } catch (ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro ao atualizar contato.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Erro ao atualizar contato.', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/contacts/{id}",
     *     summary="Remove um contato",
     *     description="Remove um contato pelo ID.",
     *     tags={"Contatos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Contato removido com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Contato removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Contato não encontrado"),
     *     @OA\Response(response=500, description="Erro ao remover contato.")
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $contact = Contact::find($id);
            
            if (!$contact) {
                return ResponseHelper::error('Contato não encontrado.', 404);
            }
            
            $contact->delete();
            
            // Log de sistema
            SystemLog::create([
                'fk_user' => request()->user()->id ?? null,
                'fk_action' => Action::where('name', 'removeu')->value('id'),
                'name_table' => 'contacts',
                'record_id' => $contact->id,
                'description' => 'Contato removido: ' . json_encode($contact->toArray()),
            ]);
            DB::commit();
            return ResponseHelper::success('Contato removido com sucesso.');
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro ao remover contato.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Erro ao remover contato.', 500);
        }
    }
}