<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use App\Models\SystemLog;
use App\Models\Action as ActionModel;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Marcas",
 *     description="Gerenciamento de marcas"
 * )
 */
class BrandController extends Controller
{
    /**
     * @OA\Get(
     *     path="/brands",
     *     tags={"Marcas"},
     *     summary="Listar marcas",
     *     description="Retorna uma lista paginada de marcas com seus contatos, ordenadas por nome. Permite filtrar por nome exato.",
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=10)),
     *     @OA\Parameter(name="order_by", in="query", required=false, description="Campo de ordenação (padrão: name)", @OA\Schema(type="string", default="name")),
     *     @OA\Parameter(name="order_dir", in="query", required=false, description="Direção da ordenação (asc/desc, padrão: asc)", @OA\Schema(type="string", default="asc")),
     *     @OA\Parameter(name="name", in="query", required=false, description="Filtrar por nome exato da marca", @OA\Schema(type="string", example="Samsung")),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de marcas",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Marcas listadas com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Samsung"),
     *                         @OA\Property(property="observation", type="string", example="Marca de eletrônicos"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time"),
     *                         @OA\Property(property="contacts", type="array", @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="João Silva"),
     *                             @OA\Property(property="email", type="string", example="joao@samsung.com"),
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

            $query = Brand::with('contacts');

            if ($request->has('name') && $request->get('name') !== null) {
                $query->where('name', $request->get('name'));
            }
            $brands = $query->orderBy($orderBy, $orderDir)
                ->paginate($perPage)
                ->appends($request->all());
            return ResponseHelper::success('Marcas listadas com sucesso.', $brands);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/brands",
     *     tags={"Marcas"},
     *     summary="Criar uma nova marca",
     *     description="Cria uma nova marca com ao menos um contato associado.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "contacts"},
     *             @OA\Property(property="name", type="string", example="Samsung"),
     *             @OA\Property(property="observation", type="string", example="Marca de eletrônicos"),
     *             @OA\Property(property="contacts", type="array", minItems=1, @OA\Items(
     *                 required={"name", "email", "phone"},
     *                 @OA\Property(property="name", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", format="email", example="joao@samsung.com"),
     *                 @OA\Property(property="phone", type="string", example="11999999999"),
     *                 @OA\Property(property="observation", type="string", example="Contato principal", nullable=true)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Marca criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Marca criada com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Samsung"),
     *                 @OA\Property(property="observation", type="string", example="Marca de eletrônicos"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@samsung.com"),
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
                Brand::rulesCreate(),
                Brand::feedbackCreate()
            );

            $brand = Brand::create([
                'name' => $validated['name'],
                'observation' => $validated['observation'] ?? null
            ]);

            // Criar contatos e associar à marca
            $contactIds = [];
            foreach ($validated['contacts'] as $contactData) {
                $contact = Contact::create($contactData);
                $contactIds[] = $contact->id;
            }
            $brand->contacts()->sync($contactIds);

            // Log de auditoria
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Criou')->value('id'),
                'name_table' => 'brands',
                'record_id' => $brand->id,
                'description' => 'Marca criada: ' . json_encode($brand->toArray()),
            ]);

            DB::commit();

            // Carregar os contatos para retornar na resposta
            $brand->load('contacts');

            return ResponseHelper::success($brand, 'Marca criada com sucesso.', 201);
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
     *     path="/brands/{id_brand}",
     *     tags={"Marcas"},
     *     summary="Exibir uma marca",
     *     description="Retorna os dados de uma marca pelo ID, incluindo seus contatos associados.",
     *     @OA\Parameter(name="id_brand", in="path", required=true, description="ID da marca", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Marca encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Marca encontrada."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Samsung"),
     *                 @OA\Property(property="observation", type="string", example="Marca de eletrônicos"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="João Silva"),
     *                     @OA\Property(property="email", type="string", example="joao@samsung.com"),
     *                     @OA\Property(property="phone", type="string", example="11999999999"),
     *                     @OA\Property(property="observation", type="string", example="Contato principal", nullable=true)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Marca não encontrada"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function show($id_brand)
    {
        try {
            $brand = Brand::with('contacts')->find($id_brand);

            if (!$brand) {
                return ResponseHelper::error('Marca não encontrada', 404);
            }

            return ResponseHelper::success('Marca encontrada.', $brand);
        } catch (ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        } catch (QueryException $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (\Exception $e) {
            Log::error($e);
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/brands/{id_brand}",
     *     tags={"Marcas"},
     *     summary="Atualizar uma marca",
     *     description="Atualiza os dados de uma marca.",
     *     @OA\Parameter(name="id_brand", in="path", required=true, description="ID da marca", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Samsung"),
     *             @OA\Property(property="observation", type="string", example="Marca de eletrônicos atualizada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Marca atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Marca atualizada com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Samsung"),
     *                 @OA\Property(property="observation", type="string", example="Marca de eletrônicos atualizada"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Marca não encontrada"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function update(Request $request, $id_brand)
    {
        DB::beginTransaction();
        try {
            $brand = Brand::find($id_brand);

            if (!$brand) {
                return ResponseHelper::error('Marca não encontrada', 404);
            }

            $validated = $request->validate(
                Brand::rulesUpdate(),
                Brand::feedbackUpdate()
            );

            $brand->update($validated);

            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Atualizou')->value('id'),
                'name_table' => 'brands',
                'record_id' => $brand->id,
                'description' => 'Atualizou marca',
            ]);
            DB::commit();
            return ResponseHelper::success('Marca atualizada com sucesso.', $brand);
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
     *     path="/brands/{id_brand}",
     *     tags={"Marcas"},
     *     summary="Remover uma marca",
     *     description="Remove (soft delete) uma marca.",
     *     @OA\Parameter(name="id_brand", in="path", required=true, description="ID da marca", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Marca removida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Marca removida com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Samsung"),
     *                 @OA\Property(property="observation", type="string", example="Marca de eletrônicos"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Marca não encontrada"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function destroy(Request $request, $id_brand)
    {
        DB::beginTransaction();
        try {
            $brand = Brand::find($id_brand);

            if (!$brand) {
                return ResponseHelper::error('Marca não encontrada', 404);
            }

            $brand->delete();

            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Removeu')->value('id'),
                'name_table' => 'brands',
                'record_id' => $brand->id,
                'description' => 'Removeu marca',
            ]);
            DB::commit();
            return ResponseHelper::success('Marca removida com sucesso.', $brand);
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
     *     path="/brands/{id_brand}/contacts",
     *     tags={"Marcas"},
     *     summary="Adicionar contato a uma marca",
     *     description="Cria um novo contato e o associa a uma marca específica.",
     *     @OA\Parameter(name="id_brand", in="path", required=true, description="ID da marca", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "phone"},
     *             @OA\Property(property="name", type="string", example="Maria Santos"),
     *             @OA\Property(property="email", type="string", format="email", example="maria@samsung.com"),
     *             @OA\Property(property="phone", type="string", example="11888888888"),
     *             @OA\Property(property="observation", type="string", example="Contato comercial", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contato adicionado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Contato adicionado à marca com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="brand", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Samsung")
     *                 ),
     *                 @OA\Property(property="contact", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="Maria Santos"),
     *                     @OA\Property(property="email", type="string", example="maria@samsung.com"),
     *                     @OA\Property(property="phone", type="string", example="11888888888"),
     *                     @OA\Property(property="observation", type="string", example="Contato comercial"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Marca não encontrada"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function addContactToBrand(Request $request, $id_brand)
    {
        DB::beginTransaction();
        try {
            $brand = Brand::find($id_brand);
            
            if (!$brand) {
                return ResponseHelper::error('Marca não encontrada', 404);
            }

            $validated = $request->validate(
                Contact::rules(),
                Contact::feedback()
            );

            $contact = Contact::create($validated);
            $brand->contacts()->attach($contact->id);

            // Log de auditoria
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Criou')->value('id'),
                'name_table' => 'brand_contacts',
                'record_id' => $contact->id,
                'description' => 'Contato adicionado à marca: ' . $brand->name . ' - Contato: ' . $contact->name,
            ]);

            DB::commit();

            return ResponseHelper::success([
                'brand' => [
                    'id' => $brand->id,
                    'name' => $brand->name
                ],
                'contact' => $contact
            ], 'Contato adicionado à marca com sucesso.', 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), 422);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error DB: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }
}