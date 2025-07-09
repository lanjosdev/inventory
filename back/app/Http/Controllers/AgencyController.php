<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\SystemLog;
use App\Models\Action as ActionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Helpers\ResponseHelper;
use App\Models\Contact;

/**
 * @OA\Tag(
 *     name="Agências",
 *     description="Gerenciamento das agências"
 * )
 */

class AgencyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/agencies",
     *     tags={"Agências"},
     *     summary="Listar agências",
     *     description="Retorna uma lista paginada de agências",
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
     *         description="Número de itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de agências retornada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $agencies = Agency::query()->paginate($perPage)->appends($request->all());
            return ResponseHelper::success($agencies);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/agencies",
     *     tags={"Agências"},
     *     summary="Criar nova agência",
     *     description="Cria uma nova agência no sistema",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Agência Exemplo"),
     *             @OA\Property(property="observation", type="string", example="Observação da agência")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Agência criada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $validated = $request->validate(
                Agency::rulesCreate(),
                Agency::feedbackCreate()
            );

            $agency = Agency::create($validated);

            // Log de auditoria
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Criou')->value('id'),
                'name_table' => 'agencies',
                'record_id' => $agency->id,
                'description' => 'Agência criada: ' . json_encode($agency->toArray()),
            ]);

            DB::commit();
            return ResponseHelper::success($agency, 'Agência criada com sucesso.', 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), 422);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/agencies/{id_agency}",
     *     tags={"Agências"},
     *     summary="Exibir agência específica",
     *     description="Retorna os dados de uma agência específica",
     *     @OA\Parameter(
     *         name="id_agency",
     *         in="path",
     *         required=true,
     *         description="ID da agência",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agência encontrada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agência não encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function show($id_agency)
    {
        try {
            $agency = Agency::find($id_agency);

            if (!$agency) {
                return ResponseHelper::error('Agência não encontrada.', 404);
            }
            
            return ResponseHelper::success($agency);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/agencies/{id_agency}",
     *     tags={"Agências"},
     *     summary="Atualizar agência",
     *     description="Atualiza os dados de uma agência específica",
     *     @OA\Parameter(
     *         name="id_agency",
     *         in="path",
     *         required=true,
     *         description="ID da agência",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Agência Atualizada"),
     *             @OA\Property(property="observation", type="string", example="Nova observação")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agência atualizada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agência não encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function update(Request $request, $id_agency)
    {
        DB::beginTransaction();
        try {
            $agency = Agency::find($id_agency);

            if (!$agency) {
                return ResponseHelper::error('Agência não encontrada.', 404);
            }

            $validated = $request->validate(
                Agency::rulesUpdate(),
                Agency::feedbackUpdate()
            );
            $agency->update($validated);

            // Log de auditoria
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Editou')->value('id'),
                'name_table' => 'agencies',
                'record_id' => $agency->id,
                'description' => 'Agência atualizada: ' . json_encode($agency->toArray()),
            ]);

            DB::commit();
            return ResponseHelper::success($agency, 'Agência atualizada com sucesso.');
        } catch (ValidationException $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), 422);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/agencies/{id_agency}",
     *     tags={"Agências"},
     *     summary="Remover agência",
     *     description="Remove uma agência específica (soft delete)",
     *     @OA\Parameter(
     *         name="id_agency",
     *         in="path",
     *         required=true,
     *         description="ID da agência",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agência removida com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agência não encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function destroy($id_agency)
    {
        DB::beginTransaction();
        try {
            $agency = Agency::find($id_agency);

            if (!$agency) {
                return ResponseHelper::error('Agência não encontrada.', 404);
            }
            
            $agency->delete();

            // Log de auditoria
            SystemLog::create([
                'fk_user' => request()->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Removeu')->value('id'),
                'name_table' => 'agencies',
                'record_id' => $agency->id,
                'description' => 'Agência removida: ' . json_encode($agency->toArray()),
            ]);

            DB::commit();
            return ResponseHelper::success(null, 'Agência removida com sucesso.');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/agencies/{id_agency}/contacts",
     *     tags={"Agências"},
     *     summary="Adicionar contato à agência",
     *     description="Cria um novo contato e o adiciona à agência especificada",
     *     @OA\Parameter(
     *         name="id_agency",
     *         in="path",
     *         required=true,
     *         description="ID da agência",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="joao@exemplo.com"),
     *             @OA\Property(property="phone", type="string", example="(11) 99999-9999")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contato adicionado à agência com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agência não encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function addContactToAgency(Request $request, $id_agency)
    {
        DB::beginTransaction();
        try {
            $agency = Agency::find($id_agency);
            if (!$agency) {
                return ResponseHelper::error('Agência não encontrada.', 404);
            }
            
            $validated = $request->validate(
                Contact::rules(),
                Contact::feedback()
            );

            if ($validated) {
                $contact = Contact::create($validated);
                $agency->contacts()->attach($contact->id);

                // Log de auditoria
                SystemLog::create([
                    'fk_user' => $request->user()->id ?? null,
                    'fk_action' => ActionModel::where('name', 'Criou')->value('id'),
                    'name_table' => 'agency_contacts',
                    'record_id' => $agency->id,
                    'description' => 'Contato adicionado à agência: ' . json_encode([
                        'agency_id' => $agency->id,
                        'agency_name' => $agency->name,
                        'contact_id' => $contact->id,
                        'contact_name' => $contact->name ?? 'N/A'
                    ]),
                ]);
            }
            DB::commit();
            
            return ResponseHelper::success('Contato adicionado à agência com sucesso.', [
                'agency_id' => $agency->id,
                'contact' => $contact
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), 422);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }
}