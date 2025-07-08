<?php

namespace App\Http\Controllers;

use App\Models\Companies;
use App\Models\Contact;
use App\Models\SystemLog;
use App\Models\Action as ActionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use App\Helpers\ResponseHelper;
use App\Models\ContactCompanies;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Redes",
 *     description="Gerenciamento de empresas (redes) e seus contatos."
 * )
 */
class CompaniesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/companies",
     *     summary="Lista empresas paginadas",
     *     description="Retorna uma lista paginada de empresas, incluindo contatos. O parâmetro 'active' permite filtrar empresas ativas, deletadas ou ambas.",
     *     tags={"Redes"},
     *     @OA\Parameter(name="page", in="query", description="Número da página", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Itens por página", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Parameter(name="active", in="query", description="Filtra empresas ativas (true), deletadas (false) ou ambas (não informado)", required=false, @OA\Schema(type="boolean", example=true)),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empresas",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de empresas obtida com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Empresa XPTO"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="contacts", type="array", @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="email", type="string"),
     *                         @OA\Property(property="phone", type="string"),
     *                         @OA\Property(property="observation", type="string", nullable=true)
     *                     ))
     *                 )),
     *                 @OA\Property(property="total", type="integer", example=20),
     *                 @OA\Property(property="last_page", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $active = $request->query('active', null);
            if ($active === 'true' || $active === true) {
                $query = Companies::query();
            } elseif ($active === 'false' || $active === false) {
                $query = Companies::onlyTrashed();
            } else {
                $query = Companies::withTrashed();
            }

            $companies = $query->with('contacts')->whereNull('deleted_at')->paginate($perPage)->appends($request->all());
            $companies->getCollection()->transform(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'created_at' => $company->created_at ?? null,
                    'updated_at' => $company->updated_at ?? null,
                    'deleted_at' => $company->deleted_at ?? null,
                    'contacts' => $company->contacts->map(function ($contact) {
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

            return ResponseHelper::success('Lista de empresas obtida com sucesso.', $companies);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/companies",
     *     summary="Cria uma empresa",
     *     description="Cria uma empresa e associa contatos.",
     *     tags={"Redes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "contacts"},
     *             @OA\Property(property="name", type="string", example="Empresa XPTO"),
     *             @OA\Property(property="contacts", type="array", minItems=1, @OA\Items(
     *                 required={"name", "email", "phone"},
     *                 @OA\Property(property="name", type="string", example="Contato Principal"),
     *                 @OA\Property(property="email", type="string", format="email", example="contato@email.com"),
     *                 @OA\Property(property="phone", type="string", example="11999999999"),
     *                 @OA\Property(property="observation", type="string", example="Observação do contato", nullable=true)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empresa criada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Empresa criada com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Empresa XPTO"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Contato Principal"),
     *                     @OA\Property(property="email", type="string", example="contato@email.com"),
     *                     @OA\Property(property="phone", type="string", example="11999999999"),
     *                     @OA\Property(property="observation", type="string", example="Observação do contato", nullable=true)
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
                Companies::rulesCreate(),
                Companies::feedbackCreate()
            );
            $company = Companies::create(['name' => $validated['name']]);
            $contactIds = [];
            foreach ($validated['contacts'] as $contactData) {
                $contact = Contact::create($contactData);
                $contactIds[] = $contact->id;
            }
            $company->contacts()->sync($contactIds);
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'criou')->value('id'),
                'name_table' => 'companies',
                'record_id' => $company->id,
                'description' => 'Empresa criada: ' . json_encode($company->toArray()),
            ]);
            DB::commit();
            $company->load('contacts');
            return ResponseHelper::success('Empresa criada com sucesso.', $company, 201);
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

    /**
     * @OA\Get(
     *     path="/api/companies/{id_companie}",
     *     summary="Exibe uma empresa",
     *     description="Retorna os dados de uma empresa pelo ID, incluindo contatos. O parâmetro 'active' permite buscar uma empresa ativa, deletada ou ambas.",
     *     tags={"Redes"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="active", in="query", description="Filtra empresa ativa (true), deletada (false) ou ambas (não informado)", required=false, @OA\Schema(type="boolean", example=true)),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Empresa encontrada."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Empresa XPTO"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="contacts", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="phone", type="string"),
     *                     @OA\Property(property="observation", type="string", nullable=true)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Empresa não encontrada"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function show(Request $request, $id_company)
    {
        try {
            $active = $request->query('active', null);
            if ($active === 'true' || $active === true) {
                $company = Companies::with('contacts')->find($id_company);
            } elseif ($active === 'false' || $active === false) {
                $company = Companies::onlyTrashed()->with('contacts')->find($id_company);
            } else {
                $company = Companies::withTrashed()->with('contacts')->find($id_company);
            }
            if (!$company) {
                return ResponseHelper::error('Empresa não encontrada.', 404);
            }
            $companyData = [
                'id' => $company->id,
                'name' => $company->name,
                'created_at' => $company->created_at ?? null,
                'updated_at' => $company->updated_at ?? null,
                'deleted_at' => $company->deleted_at ?? null,
                'contacts' => $company->contacts->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'name' => $contact->name,
                        'email' => $contact->email,
                        'phone' => $contact->phone,
                        'observation' => $contact->observation,
                    ];
                }),
            ];
            return ResponseHelper::success('Empresa encontrada.', $companyData);
        } catch (QueryException $qe) {
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/companies/{id_companie}",
     *     summary="Atualiza o nome de uma empresa",
     *     description="Atualiza apenas o nome da empresa (rede) pelo ID.",
     *     tags={"Redes"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Empresa XPTO Atualizada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa atualizada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Empresa atualizada com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Empresa XPTO Atualizada")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Empresa não encontrada"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function update(Request $request, $id_company)
    {
        DB::beginTransaction();
        try {
            $company = Companies::find($id_company);
            if (!$company) {
                return ResponseHelper::error('Empresa não encontrada.', 404);
            }
            $validated = $request->validate(
                Companies::rulesUpdate(),
                Companies::feedbackUpdate()
            );
            $company->update(['name' => $validated['name']]);
            $company->save();
            
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'Editou')->value('id'),
                'name_table' => 'companies',
                'record_id' => $company->id,
                'description' => 'Empresa atualizada: ' . json_encode($company->toArray()),
            ]);
            DB::commit();
            return ResponseHelper::success('Empresa atualizada com sucesso.', $company);
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

    /**
     * @OA\Delete(
     *     path="/api/companies/{id_companie}",
     *     summary="Remove uma empresa",
     *     description="Remove uma empresa e seus contatos.",
     *     tags={"Redes"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa removida",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Empresa removida com sucesso.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Empresa não encontrada"),
     *     @OA\Response(response=500, description="Ocorreu um erro inesperado ao processar sua solicitação. Tente novamente mais tarde.")
     * )
     */
    public function destroy(Request $request, $id_company)
    {
        DB::beginTransaction();
        try {
            $company = Companies::find($id_company);
            if (!$company) {
                return ResponseHelper::error('Empresa não encontrada.', 404);
            }
            $company->delete();
            SystemLog::create([
                'fk_user' => $request->user()->id ?? null,
                'fk_action' => ActionModel::where('name', 'removeu')->value('id'),
                'name_table' => 'companies',
                'record_id' => $company->id,
                'description' => 'Empresa removida: ' . json_encode($company->toArray()),
            ]);
            DB::commit();
            return ResponseHelper::success('Empresa removida com sucesso.');
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

    /**
     * @OA\Post(
     *     path="/api/companies/{id_companie}/contacts",
     *     summary="Adiciona um contato a uma empresa (rede)",
     *     description="Cria um novo contato e faz a relação com a empresa informada.",
     *     tags={"Redes"},
     *     @OA\Parameter(name="id_companie", in="path", required=true, description="ID da empresa (rede)", @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "phone"},
     *             @OA\Property(property="name", type="string", example="Contato Novo"),
     *             @OA\Property(property="email", type="string", example="contato@empresa.com"),
     *             @OA\Property(property="phone", type="string", example="11999999999"),
     *             @OA\Property(property="observation", type="string", example="Observação do contato", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contato criado e relacionado à empresa com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Contato adicionado à empresa com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="company_id", type="integer", example=1),
     *                 @OA\Property(property="contact", type="object",
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="name", type="string", example="Contato Novo"),
     *                     @OA\Property(property="email", type="string", example="contato@empresa.com"),
     *                     @OA\Property(property="phone", type="string", example="11999999999"),
     *                     @OA\Property(property="observation", type="string", example="Observação do contato", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Empresa não encontrada."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="object", example={"email": {"O campo email é obrigatório."}}),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao adicionar contato",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro interno ao adicionar contato."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function addContactToCompany(Request $request, $id_companie)
    {
        DB::beginTransaction();
        try {
            $company = Companies::find($id_companie);
            if (!$company) {
                return ResponseHelper::error('Empresa não encontrada.', 404);
            }
            
            $validated = $request->validate(
                Contact::rules(),
                Contact::feedback()
            );

            if ($validated) {
                $contact = Contact::create($validated);
                $company->contacts()->attach($contact->id);
            }
            DB::commit();
            
            return ResponseHelper::success('Contato adicionado à empresa com sucesso.', [
                'company_id' => $company->id,
                'contact' => $contact
            ], 201);
        } catch (ValidationException $ve) {
            DB::rollBack();
            return ResponseHelper::error($ve->errors(), 422);
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('Error DB: ' . $qe->getMessage());
            return ResponseHelper::error('Erro interno ao adicionar contato.', 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return ResponseHelper::error('Erro interno ao adicionar contato.', 500);
        }
    }
}