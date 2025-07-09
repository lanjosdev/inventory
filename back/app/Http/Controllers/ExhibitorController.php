<?php

namespace App\Http\Controllers;

use App\Models\Exhibitor;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Models\SystemLog;
use App\Models\Action as ActionModel;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Expositores",
 *     description="Gerenciamento de expositores"
 * )
 */

class ExhibitorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/exhibitors",
     *     tags={"Expositores"},
     *     summary="Listar expositores",
     *     description="Retorna uma lista paginada de expositores, ordenados por nome. Permite filtrar por nome exato.",
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=10)),
     *     @OA\Parameter(name="order_by", in="query", required=false, description="Campo de ordenação (padrão: name)", @OA\Schema(type="string", default="name")),
     *     @OA\Parameter(name="order_dir", in="query", required=false, description="Direção da ordenação (asc/desc, padrão: asc)", @OA\Schema(type="string", default="asc")),
     *     @OA\Parameter(name="name", in="query", required=false, description="Filtrar por nome exato do expositor", @OA\Schema(type="string", example="Expositor ABC")),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de expositores",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expositores listados com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Expositor ABC"),
     *                         @OA\Property(property="description", type="string", example="Expositor especializado em tecnologia"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time"),
     *                         @OA\Property(property="contacts", type="array", @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="João Silva"),
     *                             @OA\Property(property="email", type="string", example="joao@expositorabc.com"),
     *                             @OA\Property(property="phone", type="string", example="11999999999"),
     *                             @OA\Property(property="observation", type="string", example="Contato principal", nullable=true)
     *                         ))
     *                     )
     *                 ),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $orderBy = $request->get('order_by', 'name');
            $orderDir = $request->get('order_dir', 'asc');

            $query = Exhibitor::with('contacts');

            if ($request->has('name') && $request->get('name') !== null) {
                $query->where('name', $request->get('name'));
            }

            $exhibitors = $query->orderBy($orderBy, $orderDir)
                ->paginate($perPage)
                ->appends($request->all());

            return ResponseHelper::success('Expositores listados com sucesso.', $exhibitors);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/exhibitors",
     *     tags={"Expositores"},
     *     summary="Criar um novo expositor",
     *     description="Cria um novo expositor.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "contacts"},
     *             @OA\Property(property="name", type="string", example="Expositor ABC"),
     *             @OA\Property(property="description", type="string", example="Expositor especializado em tecnologia"),
     *             @OA\Property(property="contacts", type="array", minItems=1, @OA\Items(
     *                 required={"name", "email", "phone"},
     *                 @OA\Property(property="name", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", format="email", example="joao@expositorabc.com"),
     *                 @OA\Property(property="phone", type="string", example="11999999999"),
     *                 @OA\Property(property="observation", type="string", example="Contato principal", nullable=true)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Expositor criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expositor criado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Expositor ABC"),
     *                 @OA\Property(property="description", type="string", example="Expositor especializado em tecnologia"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@expositorabc.com"),
     *                     @OA\Property(property="phone", type="string", example="11999999999"),
     *                     @OA\Property(property="observation", type="string", example="Contato principal", nullable=true)
     *                 ))
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
            $validated = $request->validate(
                Exhibitor::rulesCreate(),
                Exhibitor::feedbackCreate()
            );

            $exhibitor = Exhibitor::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null
            ]);

            // Criar contatos e associar ao expositor
            $contactIds = [];
            foreach ($validated['contacts'] as $contactData) {
                $contact = Contact::create($contactData);
                $contactIds[] = $contact->id;
            }
            $exhibitor->contacts()->sync($contactIds);

            // Log de auditoria
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Criou')->value('id'),
                'name_table' => 'exhibitors',
                'record_id' => $exhibitor->id,
                'description' => 'Expositor criado: ' . json_encode($exhibitor->toArray()),
            ]);

            DB::commit();

            // Carregar os contatos para retornar na resposta
            $exhibitor->load('contacts');

            return ResponseHelper::success('Expositor criado com sucesso.', $exhibitor, 201);
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
     *     path="/exhibitors/{id_exhibitor}",
     *     tags={"Expositores"},
     *     summary="Exibir um expositor",
     *     description="Retorna os dados de um expositor pelo ID.",
     *     @OA\Parameter(name="id_exhibitor", in="path", required=true, description="ID do expositor", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Expositor encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expositor encontrado."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Expositor ABC"),
     *                 @OA\Property(property="description", type="string", example="Expositor especializado em tecnologia"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@expositorabc.com"),
     *                     @OA\Property(property="phone", type="string", example="11999999999"),
     *                     @OA\Property(property="observation", type="string", example="Contato principal", nullable=true)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Expositor não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function show($id_exhibitor)
    {
        try {
            $exhibitor = Exhibitor::with('contacts')->find($id_exhibitor);

            if (!$exhibitor) {
                return ResponseHelper::error('Expositor não encontrado', 404);
            }

            return ResponseHelper::success('Expositor encontrado.', $exhibitor);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/exhibitors/{id_exhibitor}",
     *     tags={"Expositores"},
     *     summary="Atualizar um expositor",
     *     description="Atualiza os dados de um expositor.",
     *     @OA\Parameter(name="id_exhibitor", in="path", required=true, description="ID do expositor", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Expositor ABC Atualizado"),
     *             @OA\Property(property="description", type="string", example="Expositor especializado em tecnologia atualizado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Expositor atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expositor atualizado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Expositor ABC Atualizado"),
     *                 @OA\Property(property="description", type="string", example="Expositor especializado em tecnologia atualizado"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@expositorabc.com"),
     *                     @OA\Property(property="phone", type="string", example="11999999999"),
     *                     @OA\Property(property="observation", type="string", example="Contato principal", nullable=true)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Expositor não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function update(Request $request, $id_exhibitor)
    {
        DB::beginTransaction();
        try {
            $exhibitor = Exhibitor::find($id_exhibitor);

            if (!$exhibitor) {
                return ResponseHelper::error('Expositor não encontrado', 404);
            }

            $validated = $request->validate(
                Exhibitor::rulesUpdate(),
                Exhibitor::feedbackUpdate()
            );

            $exhibitor->update($validated);

            // Carregar os contatos para retornar na resposta
            $exhibitor->load('contacts');

            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Atualizou')->value('id'),
                'name_table' => 'exhibitors',
                'record_id' => $exhibitor->id,
                'description' => 'Expositor atualizado: ' . json_encode($exhibitor->toArray()),
            ]);

            DB::commit();
            return ResponseHelper::success('Expositor atualizado com sucesso.', $exhibitor);
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
     *     path="/exhibitors/{id_exhibitor}",
     *     tags={"Expositores"},
     *     summary="Remover um expositor",
     *     description="Remove (soft delete) um expositor.",
     *     @OA\Parameter(name="id_exhibitor", in="path", required=true, description="ID do expositor", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Expositor removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expositor removido com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Expositor ABC"),
     *                 @OA\Property(property="description", type="string", example="Expositor especializado em tecnologia"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@expositorabc.com"),
     *                     @OA\Property(property="phone", type="string", example="11999999999"),
     *                     @OA\Property(property="observation", type="string", example="Contato principal", nullable=true)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Expositor não encontrado"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function destroy(Request $request, $id_exhibitor)
    {
        DB::beginTransaction();
        try {
            $exhibitor = Exhibitor::with('contacts')->find($id_exhibitor);

            if (!$exhibitor) {
                return ResponseHelper::error('Expositor não encontrado', 404);
            }

            $exhibitor->delete();

            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Removeu')->value('id'),
                'name_table' => 'exhibitors',
                'record_id' => $exhibitor->id,
                'description' => 'Expositor removido: ' . json_encode($exhibitor->toArray()),
            ]);

            DB::commit();
            return ResponseHelper::success('Expositor removido com sucesso.', $exhibitor);
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
