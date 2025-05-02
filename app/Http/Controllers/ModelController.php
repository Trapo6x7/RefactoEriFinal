<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModelController extends Controller
{
    protected $models = [
        'société'       => \App\Models\Society::class,
        'interlocuteur' => \App\Models\Interlocutor::class,
        'environnement' => \App\Models\Env::class,
        'problème'       => \App\Models\Problem::class,
        'outil'          => \App\Models\Tool::class,
    ];

    public function index($model)
    {
        if (!isset($this->models[$model])) {
            abort(404);
        }
        $modelClass = $this->models[$model];
        $items = $modelClass::all();
        return view('model.index', [
            'items' => $items,
            'model' => $model
        ]);
    }

    public function form($model, $action, $id = null)
    {
        if (!isset($this->models[$model])) abort(404);
        $instance = $id ? $this->models[$model]::findOrFail($id) : null;

        $rules = [
            'société' => [
                'id_main'                           => 'nullable|integer',
                'name'                              => 'required|string|max:255',
                'status_client'                     => 'nullable|integer',
                'status_distrib'                    => 'nullable|integer',
                'service_backup'                    => 'nullable|integer',
                'infos_backup'                      => 'nullable|string',
                'service_connect'                   => 'nullable|integer',
                'infos_connect'                     => 'nullable|string',
                'service_cloody'                    => 'nullable|integer',
                'infos_cloody'                      => 'nullable|string',
                'service_maintenance'               => 'nullable|integer',
                'infos_maintenance'                 => 'nullable|string',
                'service_heberg_web'                => 'nullable|integer',
                'infos_heberg_web'                  => 'nullable|string',
                'service_mail'                      => 'nullable|integer',
                'infos_mail'                        => 'nullable|string',
                'service_EBP'                       => 'nullable|integer',
                'infos_EBP'                         => 'nullable|string',
                'service_maintenance_office'        => 'nullable|integer',
                'infos_maintenance_office'          => 'nullable|string',
                'service_maintenance_serveur'       => 'nullable|integer',
                'infos_maintenance_serveur'         => 'nullable|string',
                'service_maintenance_infra_rso'     => 'nullable|integer',
                'infos_maintenance_infra_rso'       => 'nullable|string',
                'service_maintenance_equip_rso'     => 'nullable|integer',
                'infos_maintenance_equip_rso'       => 'nullable|string',
                'service_maintenance_ESET'          => 'nullable|integer',
                'infos_maintenance_ESET'            => 'nullable|string',
                'service_maintenance_domaine_DNS'   => 'nullable|integer',
                'infos_maintenance_domaine_DNS'     => 'nullable|string',
                'boss_name'                         => 'nullable|string|max:60',
                'boss_phone'                        => 'nullable|string|max:100',
                'recep_phone'                       => 'nullable|string|max:50',
                'address'                           => 'nullable|string|max:300',
                'status'                            => 'nullable|string', // ou 'in:active,inactive' si tu veux valider l'enum
            ],
            'interlocuteur' => [
                'name'              => 'required|string|max:50',
                'lastname'          => 'nullable|string|max:50',
                'fullname'          => 'nullable|string|max:110',
                'societe'           => 'required|integer', // correspond à l'id de la société
                'phone_fix'         => 'nullable|string|max:50',
                'phone_mobile'      => 'nullable|string|max:50',
                'email'             => 'nullable|email|max:100',
                'id_teamviewer'     => 'nullable|string|max:50',
                'service_connect'   => 'nullable|integer',
                'service_cloody'    => 'nullable|integer',
                'service_comptes'   => 'nullable|integer',
                'service_mail'      => 'nullable|integer',
                'infos_connect'     => 'nullable|string',
                'infos_cloody'      => 'nullable|string',
                'infos_comptes'     => 'nullable|string',
                'infos_mail'        => 'nullable|string',
            ],
            'environnement' => [
                'name' => 'required|string|max:255',
            ],
            'problème' => [
                'title'       => 'required|string',
                'env'         => 'nullable|integer',
                'tool'        => 'nullable|integer',
                'societe'     => 'nullable|integer',
                'description' => 'nullable|string',
            ],
            'outil' => [
                'name' => 'required|string|max:255',
            ],
        ];

        $fields = self::getFieldsFromRules($rules[$model]);

        // Si on est sur le modèle interlocuteur, on passe la liste des sociétés
        $societies = null;
        if ($model === 'interlocuteur') {
            $societies = \App\Models\Society::all();
        }

        return view('model.form', compact('model', 'action', 'instance', 'fields', 'societies'));
    }

    public function submit(Request $request, $model, $action, $id = null)
    {
        if (!isset($this->models[$model])) abort(404);
        $class = $this->models[$model];

        $rules = [
            'société' => [
                'id_main'                           => 'nullable|integer',
                'name'                              => 'required|string|max:255',
                'status_client'                     => 'nullable|integer',
                'status_distrib'                    => 'nullable|integer',
                'service_backup'                    => 'nullable|integer',
                'infos_backup'                      => 'nullable|string',
                'service_connect'                   => 'nullable|integer',
                'infos_connect'                     => 'nullable|string',
                'service_cloody'                    => 'nullable|integer',
                'infos_cloody'                      => 'nullable|string',
                'service_maintenance'               => 'nullable|integer',
                'infos_maintenance'                 => 'nullable|string',
                'service_heberg_web'                => 'nullable|integer',
                'infos_heberg_web'                  => 'nullable|string',
                'service_mail'                      => 'nullable|integer',
                'infos_mail'                        => 'nullable|string',
                'service_EBP'                       => 'nullable|integer',
                'infos_EBP'                         => 'nullable|string',
                'service_maintenance_office'        => 'nullable|integer',
                'infos_maintenance_office'          => 'nullable|string',
                'service_maintenance_serveur'       => 'nullable|integer',
                'infos_maintenance_serveur'         => 'nullable|string',
                'service_maintenance_infra_rso'     => 'nullable|integer',
                'infos_maintenance_infra_rso'       => 'nullable|string',
                'service_maintenance_equip_rso'     => 'nullable|integer',
                'infos_maintenance_equip_rso'       => 'nullable|string',
                'service_maintenance_ESET'          => 'nullable|integer',
                'infos_maintenance_ESET'            => 'nullable|string',
                'service_maintenance_domaine_DNS'   => 'nullable|integer',
                'infos_maintenance_domaine_DNS'     => 'nullable|string',
                'boss_name'                         => 'nullable|string|max:60',
                'boss_phone'                        => 'nullable|string|max:100',
                'recep_phone'                       => 'nullable|string|max:50',
                'address'                           => 'nullable|string|max:300',
                'status'                            => 'nullable|string', // ou 'in:active,inactive' si tu veux valider l'enum
            ],
            'interlocuteur' => [
                'name'              => 'required|string|max:50',
                'lastname'          => 'nullable|string|max:50',
                'fullname'          => 'nullable|string|max:110',
                'societe'           => 'required|integer', // correspond à l'id de la société
                'phone_fix'         => 'nullable|string|max:50',
                'phone_mobile'      => 'nullable|string|max:50',
                'email'             => 'nullable|email|max:100',
                'id_teamviewer'     => 'nullable|string|max:50',
                'service_connect'   => 'nullable|integer',
                'service_cloody'    => 'nullable|integer',
                'service_comptes'   => 'nullable|integer',
                'service_mail'      => 'nullable|integer',
                'infos_connect'     => 'nullable|string',
                'infos_cloody'      => 'nullable|string',
                'infos_comptes'     => 'nullable|string',
                'infos_mail'        => 'nullable|string',
            ],
            'environnement' => [
                'name' => 'required|string|max:255',
            ],
            'problème' => [
                'title'       => 'required|string',
                'env'         => 'nullable|integer',
                'tool'        => 'nullable|integer',
                'societe'     => 'nullable|integer',
                'description' => 'nullable|string',
            ],
            'outil' => [
                'name' => 'required|string|max:255',
            ],
        ];

        $validated = $request->validate($rules[$model]);

        if ($action === 'create') {
            $class::create($validated);
        } elseif ($action === 'edit' && $id) {
            $instance = $class::findOrFail($id);
            $instance->update($validated);
        } else {
            abort(400);
        }

        return redirect()->route('dashboard')->with('status', 'Opération réussie !');
    }

    public function show($model, $id)
    {
        if (!isset($this->models[$model])) {
            abort(404);
        }
        $modelClass = $this->models[$model];
        $item = $modelClass::findOrFail($id);
        return view('model.show', compact('item', 'model'));
    }

    public function updateField(Request $request, $model, $id)
    {
        if (!isset($this->models[$model])) {
            return response()->json(['success' => false, 'message' => 'Model not found'], 404);
        }
        $modelClass = $this->models[$model];
        $item = $modelClass::findOrFail($id);

        $field = $request->input('field');
        $value = $request->input('value');

        // Sécurise les champs éditables
        if (!array_key_exists($field, $item->getAttributes())) {
            return response()->json(['success' => false, 'message' => 'Invalid field'], 400);
        }

        $item->$field = $value;
        $item->save();

        return response()->json(['success' => true]);
    }

    public static function getFieldsFromRules($rules)
    {
        $fieldTypes = [
            'integer' => 'number',
            'email' => 'email',
            'string' => 'text',
        ];

        $fields = [];
        foreach ($rules as $name => $rule) {
            $type = 'text';
            foreach ($fieldTypes as $ruleKey => $inputType) {
                if (str_contains($rule, $ruleKey)) {
                    $type = $inputType;
                    break;
                }
            }
            $fields[$name] = [
                'label' => ucfirst(str_replace('_', ' ', $name)),
                'type' => $type,
                'required' => str_contains($rule, 'required'),
            ];
        }
        return $fields;
    }
}
