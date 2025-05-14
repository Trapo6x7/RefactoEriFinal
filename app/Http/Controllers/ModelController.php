<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModelController extends Controller
{
    protected $models = [
        'société'       => \App\Models\Society::class,
        'interlocuteur' => \App\Models\Interlocutor::class,
        'environnement' => \App\Models\Env::class,
        'problème'      => \App\Models\Problem::class,
        'outil'         => \App\Models\Tool::class,
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
                'id_main'                           => 'required|integer|min:0',
                'name'                              => 'required|string|max:255',
                'status_client'                     => 'required|integer',
                'status_distrib'                    => 'required|integer',
                'boss_name'                         => 'required|string|max:60',
                'boss_phone'                        => 'required|string|max:100',
                'recep_phone'                       => 'nullable|string|max:50',
                'address'                           => 'required|string|max:300',
                'service_backup'                    => 'required|integer',
                'infos_backup'                      => 'nullable|string',
                'service_connect'                   => 'required|integer',
                'infos_connect'                     => 'nullable|string',
                'service_cloody'                    => 'required|integer',
                'infos_cloody'                      => 'nullable|string',
                'service_maintenance'               => 'required|integer',
                'infos_maintenance'                 => 'nullable|string',
                'service_heberg_web'                => 'required|integer',
                'infos_heberg_web'                  => 'nullable|string',
                'service_mail'                      => 'required|integer',
                'infos_mail'                        => 'nullable|string',
                'service_EBP'                       => 'required|integer',
                'infos_EBP'                         => 'nullable|string',
                'service_maintenance_office'        => 'required|integer',
                'infos_maintenance_office'          => 'nullable|string',
                'service_maintenance_serveur'       => 'required|integer',
                'infos_maintenance_serveur'         => 'nullable|string',
                'service_maintenance_infra_rso'     => 'required|integer',
                'infos_maintenance_infra_rso'       => 'nullable|string',
                'service_maintenance_equip_rso'     => 'required|integer',
                'infos_maintenance_equip_rso'       => 'nullable|string',
                'service_maintenance_ESET'          => 'required|integer',
                'infos_maintenance_ESET'            => 'nullable|string',
                'service_maintenance_domaine_DNS'   => 'required|integer',
                'infos_maintenance_domaine_DNS'     => 'nullable|string',
                'status'                            => 'required|string|in:active,inactive',
            ],
            'interlocuteur' => [
                'name'              => 'required|string|max:50',
                'lastname'          => 'nullable|string|max:50',
                'fullname'          => 'nullable|string|max:110',
                'societe'           => 'required|integer',
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

        // Prépare les listes pour les selects selon le modèle
        $societies = null;
        $tools = null;
        $envs = null;

        // Pour le select société parente (id_main) dans le formulaire société
        if ($model === 'société') {
            $excludeId = $instance ? $instance->id : 0;
            $societies = \App\Models\Society::where('id', '!=', $excludeId)
                ->alphabetical()
                ->get(['id', 'name']);
        }

        // Pour les autres modèles qui ont besoin de sociétés
        if (in_array($model, ['interlocuteur', 'problème'])) {
            $societies = \App\Models\Society::alphabetical()->get(['id', 'name']);
        }
        if ($model === 'problème') {
            $tools = \App\Models\Tool::alphabetical()->get(['id', 'name']);
            $envs = \App\Models\Env::alphabetical()->get(['id', 'name']);
        }

        $viewData = compact('model', 'action', 'instance', 'fields', 'societies', 'tools', 'envs');

        // Si la requête est AJAX (modale), retourne la vue modale
        if (request()->ajax()) {
            return view('model.form_modal', $viewData);
        }

        // Sinon, retourne la vue classique
        return view('model.form', $viewData);
    }

    public function submit(Request $request, $model, $action, $id = null)
    {
        if (!isset($this->models[$model])) abort(404);
        $class = $this->models[$model];

        $rules = [
            'société' => [
                'id_main'                           => 'required|integer|min:0',
                'name'                              => 'required|string|max:255',
                'status_client'                     => 'required|integer',
                'status_distrib'                    => 'required|integer',
                'boss_name'                         => 'required|string|max:60',
                'boss_phone'                        => 'required|string|max:100',
                'recep_phone'                       => 'nullable|string|max:50',
                'address'                           => 'required|string|max:300',
                'service_backup'                    => 'required|integer',
                'infos_backup'                      => 'nullable|string',
                'service_connect'                   => 'required|integer',
                'infos_connect'                     => 'nullable|string',
                'service_cloody'                    => 'required|integer',
                'infos_cloody'                      => 'nullable|string',
                'service_maintenance'               => 'required|integer',
                'infos_maintenance'                 => 'nullable|string',
                'service_heberg_web'                => 'required|integer',
                'infos_heberg_web'                  => 'nullable|string',
                'service_mail'                      => 'required|integer',
                'infos_mail'                        => 'nullable|string',
                'service_EBP'                       => 'required|integer',
                'infos_EBP'                         => 'nullable|string',
                'service_maintenance_office'        => 'required|integer',
                'infos_maintenance_office'          => 'nullable|string',
                'service_maintenance_serveur'       => 'required|integer',
                'infos_maintenance_serveur'         => 'nullable|string',
                'service_maintenance_infra_rso'     => 'required|integer',
                'infos_maintenance_infra_rso'       => 'nullable|string',
                'service_maintenance_equip_rso'     => 'required|integer',
                'infos_maintenance_equip_rso'       => 'nullable|string',
                'service_maintenance_ESET'          => 'required|integer',
                'infos_maintenance_ESET'            => 'nullable|string',
                'service_maintenance_domaine_DNS'   => 'required|integer',
                'infos_maintenance_domaine_DNS'     => 'nullable|string',
                'status'                            => 'required|string|in:active,inactive',
            ],
            'interlocuteur' => [
                'name'              => 'required|string|max:50',
                'lastname'          => 'nullable|string|max:50',
                'fullname'          => 'nullable|string|max:110',
                'societe'           => 'required|integer',
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

        if ($model === 'interlocuteur') {
            $validated['fullname'] = trim(($validated['name'] ?? '') . ' ' . ($validated['lastname'] ?? ''));
        }

        if ($action === 'create') {
            $class::create($validated);
            $instance = null;
        } elseif ($action === 'edit' && $id) {
            $instance = $class::findOrFail($id);
            $instance->update($validated);
        } else {
            abort(400);
        }

        // Prépare les listes pour les selects selon le modèle
        $societies = null;
        $tools = null;
        $envs = null;

        if ($model === 'société') {
            $excludeId = $instance ? $instance->id : 0;
            $societies = \App\Models\Society::where('id', '!=', $excludeId)
                ->alphabetical()
                ->get(['id', 'name']);
        }
        if (in_array($model, ['interlocuteur', 'problème'])) {
            $societies = \App\Models\Society::alphabetical()->get(['id', 'name']);
        }
        if ($model === 'problème') {
            $tools = \App\Models\Tool::alphabetical()->get(['id', 'name']);
            $envs = \App\Models\Env::alphabetical()->get(['id', 'name']);
        }

        $fields = self::getFieldsFromRules($rules[$model]);

        if ($request->ajax()) {
            return view('model.form_modal', [
                'model' => $model,
                'action' => $action,
                'instance' => $instance,
                'fields' => $fields,
                'societies' => $societies,
                'tools' => $tools,
                'envs' => $envs,
                'success' => true,
            ]);
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

        if (request()->wantsJson() || request()->ajax()) {
            $data = $item->toArray();
            // Ajoute les services actifs si la méthode existe
            if (method_exists($item, 'activeServicesWithInfos')) {
                $data['active_services'] = $item->activeServicesWithInfos();
            }
            if ($model === 'société' && $item->main) {
                $data['main_obj'] = $item->main->toArray();
            }
            return response()->json($data);
        }

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

    public function apiShow($model, $id)
    {
        if (!isset($this->models[$model])) abort(404);
        $item = $this->models[$model]::findOrFail($id);
        return response()->json($item);
    }

    public function apiUpdate(Request $request, $model, $id)
    {
        if (!isset($this->models[$model])) abort(404);
        $item = $this->models[$model]::findOrFail($id);
        $item->update($request->only(array_keys($request->all())));
        return response()->json(['success' => true]);
    }

    public function listProblemes()
    {
        $problemes = Problem::with('societe')->get();

        $data = $problemes->map(function ($item) {
            $arr = $item->toArray();
            if ($item->societe) {
                $arr['societe_obj'] = $item->societe->toArray();
            }
            return $arr;
        });
        return response()->json($data);
    }
}
