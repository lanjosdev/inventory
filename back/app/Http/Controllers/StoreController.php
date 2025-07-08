<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\ResponseHelper;
use Illuminate\Database\QueryException;
use Exception;

class StoreController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/stores",
     *     summary="Lista lojas paginadas",
     *     description="Retorna uma lista paginada de lojas, incluindo contatos associados.",
     *     tags={"Lojas"},
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
     *         description="Itens por página (padrão: 10)",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de lojas retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de lojas obtida com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id_store", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Loja XPTO"),
     *                         @OA\Property(property="cnpj", type="string", example="12345678000195"),
     *                         @OA\Property(property="company", type="object",
     *                              @OA\Property(property="id", type="integer", example=1),
     *                              @OA\Property(property="name", type="string", example="Rede Exemplo")
     *                         ),
     *                         @OA\Property(property="contacts", type="array", @OA\Items(
     *                             @OA\Property(property="id_contact", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Contato 1"),
     *                             @OA\Property(property="email", type="string", example="contato@email.com"),
     *                             @OA\Property(property="phone", type="string", example="11999999999"),
     *                             @OA\Property(property="observation", type="string", example="Observação")
     *                         ))
     *                     )
     *                 ),
     *                 @OA\Property(property="total", type="integer", example=20),
     *                 @OA\Property(property="last_page", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $stores = Store::with(['contacts', 'company'])->paginate($perPage);
            $stores->getCollection()->transform(function ($store) {
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'cnpj' => $store->cnpj,
                    'company' => $store->company ? [
                        'id' => $store->company->id,
                        'name' => $store->company->name,
                    ] : null,
                    'contacts' => $store->contacts->map(function ($contact) {
                        return [
                            'id' => $contact->id,
                            'name' => $contact->name,
                            'email' => $contact->email,
                            'phone' => $contact->phone,
                            'observation' => $contact->observation,
                        ];
                    }),
                ];
            });
            return ResponseHelper::success('Lista de lojas obtida com sucesso.', $stores);
        } catch (QueryException $qe) {
            Log::error('Erro DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Erro: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/stores/{id_store}",
     *     summary="Exibe uma loja",
     *     description="Retorna os dados de uma loja pelo ID, incluindo contatos.",
     *     tags={"Lojas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da loja",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Loja encontrada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Loja encontrada."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Loja XPTO"),
     *                 @OA\Property(property="cnpj", type="string", example="12345678000195"),
     *                 @OA\Property(property="company", type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Rede Exemplo")
     *                 ),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id_contact", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Contato 1"),
     *                     @OA\Property(property="email", type="string", example="contato@email.com"),
     *                     @OA\Property(property="phone", type="string", example="11999999999"),
     *                     @OA\Property(property="observation", type="string", example="Observação")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Loja não encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde."
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $store = Store::with(['contacts', 'company'])->find($id);
            if (!$store) {
                return ResponseHelper::error('Loja não encontrada.', 404);
            }
            $data = [
                'id' => $store->id,
                'name' => $store->name,
                'cnpj' => $store->cnpj,
                'company' => $store->company ? [
                    'id' => $store->company->id,
                    'name' => $store->company->name,
                ] : null,
                'contacts' => $store->contacts->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'name' => $contact->name,
                        'email' => $contact->email,
                        'phone' => $contact->phone,
                        'observation' => $contact->observation,
                    ];
                }),
            ];
            return ResponseHelper::success('Loja encontrada.', $data);
        } catch (QueryException $qe) {
            Log::error('Erro DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Erro: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stores",
     *     summary="Cria uma nova loja",
     *     description="Cria uma loja e associa contatos.",
     *     tags={"Lojas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "contacts", "fk_companie", "cnpj"},
     *             @OA\Property(property="name", type="string", example="Loja XPTO"),
     *             @OA\Property(property="fk_companie", type="integer", example=1, description="ID da rede (empresa) à qual a loja pertence."),
     *             @OA\Property(property="cnpj", type="string", example="12345678000195"),
     *             @OA\Property(property="contacts", type="array", minItems=1, @OA\Items(
     *                 required={"name", "email", "phone"},
     *                 @OA\Property(property="name", type="string", example="Contato 1"),
     *                 @OA\Property(property="email", type="string", example="contato@email.com"),
     *                 @OA\Property(property="phone", type="string", example="11999999999"),
     *                 @OA\Property(property="observation", type="string", example="Observação")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Loja criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Loja criada com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Loja XPTO"),
     *                 @OA\Property(property="cnpj", type="string", example="12345678000195"),
     *                 @OA\Property(property="fk_companie", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao criar loja"
     *     )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate(
                Store::rules(),
                Store::feedback(),
            );
            $store = Store::create([
                'name' => $validated['name'],
                'fk_companie' => $validated['fk_companie'],
                'cnpj' => $validated['cnpj'],
            ]);
            $contactIds = [];
            foreach ($validated['contacts'] as $contactData) {
                $contact = Contact::create($contactData);
                $contactIds[] = $contact->id;
            }
            $store->contacts()->sync($contactIds);
            DB::commit();
            $store->load('contacts');
            return ResponseHelper::success('Loja criada com sucesso.', $store, 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Erro DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/stores/{id_store}",
     *     summary="Atualiza uma loja",
     *     description="Atualiza os dados de uma loja e seus contatos.",
     *     tags={"Lojas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da loja",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "fk_companie", "cnpj"},
     *             @OA\Property(property="name", type="string", example="Loja XPTO Atualizada"),
     *             @OA\Property(property="fk_companie", type="integer", example=1, description="ID da rede (empresa) à qual a loja pertence."),
     *             @OA\Property(property="cnpj", type="string", example="12345678000195"),
     *             @OA\Property(property="contacts", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Contato 1 Atualizado"),
     *                 @OA\Property(property="email", type="string", example="contato.novo@email.com"),
     *                 @OA\Property(property="phone", type="string", example="11999999999"),
     *                 @OA\Property(property="observation", type="string", example="Observação")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Loja atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Loja atualizada com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Loja XPTO Atualizada"),
     *                 @OA\Property(property="cnpj", type="string", example="12345678000195"),
     *                 @OA\Property(property="company", type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Rede Exemplo")
     *                 ),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Contato 1 Atualizado")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Loja não encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao atualizar loja"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $store = Store::find($id);
            if (!$store) {
                return ResponseHelper::error('Loja não encontrada.', 404);
            }
            $validated = $request->validate(
                Store::rules(),
                Store::feedback(),
            );
            $store->update([
                'name' => $validated['name'],
                'fk_companie' => $validated['fk_companie'],
                'cnpj' => $validated['cnpj'],
            ]);
            $contactIds = [];
            foreach ($validated['contacts'] as $contactData) {
                if (isset($contactData['id'])) {
                    $contact = Contact::find($contactData['id']);
                    if ($contact) {
                        $contact->update($contactData);
                        $contactIds[] = $contact->id;
                    }
                } else {
                    $contact = Contact::create($contactData);
                    $contactIds[] = $contact->id;
                }
            }
            $store->contacts()->sync($contactIds);
            DB::commit();
            $store->load(['contacts', 'company']);
            return ResponseHelper::success('Loja atualizada com sucesso.', $store);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Erro DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/stores/{id_store}",
     *     summary="Remove uma loja",
     *     description="Remove uma loja e desassocia os contatos.",
     *     tags={"Lojas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da loja",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Loja removida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Loja removida com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Loja não encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao remover loja"
     *     )
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $store = Store::find($id);
            if (!$store) {
                return ResponseHelper::error('Loja não encontrada.', 404);
            }
            $store->contacts()->detach();
            $store->delete();
            DB::commit();
            return ResponseHelper::success('Loja removida com sucesso.');
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Erro DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stores/{id_store}/contacts",
     *     summary="Adiciona um contato a uma loja",
     *     description="Cria um novo contato e faz a relação com a loja informada.",
     *     tags={"Lojas"},
     *     @OA\Parameter(name="id_store", in="path", required=true, description="ID da loja", @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "phone"},
     *             @OA\Property(property="name", type="string", example="Contato Novo"),
     *             @OA\Property(property="email", type="string", example="contato@loja.com"),
     *             @OA\Property(property="phone", type="string", example="11999999999"),
     *             @OA\Property(property="observation", type="string", example="Observação do contato", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contato criado e relacionado à loja com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Contato adicionado à loja com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="store_id", type="integer", example=1),
     *                 @OA\Property(property="contact", type="object",
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="name", type="string", example="Contato Novo"),
     *                     @OA\Property(property="email", type="string", example="contato@loja.com"),
     *                     @OA\Property(property="phone", type="string", example="11999999999"),
     *                     @OA\Property(property="observation", type="string", example="Observação do contato", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Loja não encontrada"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Erro interno ao adicionar contato")
     * )
     */
    public function addContactToStore(Request $request, $id_store)
    {
        DB::beginTransaction();
        try {
            $store = Store::find($id_store);
            if (!$store) {
                return ResponseHelper::error('Loja não encontrada.', 404);
            }
            $validated = $request->validate(
                Contact::rules(),
                Contact::feedback()
            );
            $contact = Contact::create($validated);
            $store->contacts()->attach($contact->id);
            DB::commit();
            return ResponseHelper::success('Contato adicionado à loja com sucesso.', [
                'store_id' => $store->id,
                'contact' => $contact
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Erro DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro interno ao adicionar contato.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro: ' . $e->getMessage());
            return ResponseHelper::error('Erro interno ao adicionar contato.', 500);
        }
    }
}
