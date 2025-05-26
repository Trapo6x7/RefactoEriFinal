<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModelController extends Controller
{
    protected $models = [
        'societe'       => \App\Models\Society::class,
        'interlocuteur' => \App\Models\Interlocutor::class,
        'environnement' => \App\Models\Env::class,
        'probleme'      => \App\Models\Problem::class,
        'outil'         => \App\Models\Tool::class,
        'user'          => \App\Models\User::class,
        'tech'          => \App\Models\Tech::class,
    ];

    public function index($model)
    {
        if (!isset($this->models[$model])) {
            abort(404);
        }
        $modelClass = $this->models[$model];
        $items = $modelClass::alphabetical()->get();
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
            'societe' => [
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
            'probleme' => [
                'title'       => 'required|string',
                'env'         => 'nullable|integer',
                'tool'        => 'nullable|integer',
                'societe'     => 'nullable|integer',
                'description' => 'nullable|string',
            ],
            'outil' => [
                'name' => 'required|string|max:255',
            ],
            'user' => [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|max:255|unique:users,email' . ($id ? ',' . $id : ''),
                'password' => $id ? 'nullable|string|min:8' : 'required|string|min:8',
                'role'     => 'required|string|max:50',
            ],
            'tech' => [
                'name' => 'required|string|max:255',
            ],
        ];

        $fields = self::getFieldsFromRules($rules[$model]);

        // Prépare les listes pour les selects selon le modèle
        $societies = null;
        $tools = null;
        $envs = null;

        if ($model === 'societe') {
            $excludeId = $instance ? $instance->id : 0;
            $societies = \App\Models\Society::where('id', '!=', $excludeId)
                ->alphabetical()
                ->get(['id', 'name']);
        }

        if (in_array($model, ['interlocuteur', 'probleme'])) {
            $societies = \App\Models\Society::alphabetical()->get(['id', 'name']);
        }
        if ($model === 'probleme') {
            $tools = \App\Models\Tool::alphabetical()->get(['id', 'name']);
            $envs = \App\Models\Env::alphabetical()->get(['id', 'name']);
        }

        $viewData = compact('model', 'action', 'instance', 'fields', 'societies', 'tools', 'envs');

        if (request()->ajax()) {
            return view('model.form_modal', $viewData);
        }

        return view('model.form', $viewData);
    }

    public function submit(Request $request, $model, $action, $id = null)
    {
        if (!isset($this->models[$model])) abort(404);
        $class = $this->models[$model];

        $rules = [
            'societe' => [
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
            'probleme' => [
                'title'       => 'required|string',
                'env'         => 'nullable|integer',
                'tool'        => 'nullable|integer',
                'societe'     => 'nullable|integer',
                'description' => 'nullable|string',
            ],
            'outil' => [
                'name' => 'required|string|max:255',
            ],
            'user' => [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|max:255|unique:users,email' . ($id ? ',' . $id : ''),
                'password' => $id ? 'nullable|string|min:8' : 'required|string|min:8',
                'role'     => 'required|string|max:50',
            ],
            'tech' => [
                'name' => 'required|string|max:255',
            ],
        ];

        $validated = $request->validate($rules[$model]);

        if ($model === 'interlocuteur') {
            $validated['fullname'] = trim(($validated['name'] ?? '') . ' ' . ($validated['lastname'] ?? ''));
        }

        // Hash du mot de passe pour user
        if ($model === 'user' && !empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } elseif ($model === 'user') {
            unset($validated['password']);
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

        if ($model === 'societe') {
            $excludeId = $instance ? $instance->id : 0;
            $societies = \App\Models\Society::where('id', '!=', $excludeId)
                ->alphabetical()
                ->get(['id', 'name']);
        }
        if (in_array($model, ['interlocuteur', 'probleme'])) {
            $societies = \App\Models\Society::alphabetical()->get(['id', 'name']);
        }
        if ($model === 'probleme') {
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

        // On charge les relations connues pour chaque modèle
        $with = [];
        if ($model === 'probleme') {
            $with = ['tool', 'env', 'society'];
        } elseif ($model === 'interlocuteur') {
            $with = ['society'];
        }
        // Ajoute ici d'autres modèles/relations si besoin

        $item = $modelClass::with($with)->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            $data = $item->toArray();
            if (method_exists($item, 'activeServicesWithInfos')) {
                $data['active_services'] = $item->activeServicesWithInfos();
            }
            if ($model === 'societe' && $item->main) {
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

    public function suggestions(Request $request, $model)
    {
        try {
            if (!isset($this->models[$model])) {
                abort(404);
            }
            $modelClass = $this->models[$model];
            $query = $request->input('q', '');

            // Détermine le champ à rechercher selon le modèle
            $searchField = 'name';
            if (in_array($model, ['probleme', 'problem'])) {
                $searchField = 'title';
            }
            if (in_array($model, ['interlocuteur', 'interlocutor'])) {
                $searchField = 'fullname';
            }

            $items = $modelClass::when($query, function ($q) use ($searchField, $query) {
                $q->where($searchField, 'like', "%$query%");
            })->get();

            return view('model.partials.table-rows', [
                'items' => $items,
                'model' => $model
            ]);
        } catch (\Throwable $e) {
            return response($e->getMessage(), 500);
        }
    }

    public function delete(Request $request, $model, $id)
    {
        if (!isset($this->models[$model])) {
            return response()->json(['success' => false, 'message' => 'Modèle inconnu'], 404);
        }
        $modelClass = $this->models[$model];
        $item = $modelClass::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Élément introuvable'], 404);
        }
        $item->delete();
        return response()->json(['success' => true]);
    }

    public function uploadFile(Request $request, $model, $id)
    {
        if (!isset($this->models[$model])) {
            return response()->json(['success' => false, 'message' => 'Modèle inconnu'], 404);
        }
        $modelClass = $this->models[$model];
        $item = $modelClass::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Élément introuvable'], 404);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // 10 Mo max
        ]);

        $path = $request->file('file')->store("uploads/{$model}/{$id}", 'public');
        // Optionnel : stocke le chemin en base si tu veux

        return response()->json(['success' => true, 'path' => $path, 'url' => asset("storage/$path")]);
    }

    public function listFiles($model, $id)
    {
        $dir = "uploads/{$model}/{$id}";
        $files = [];
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($dir)) {
            $files = collect(\Illuminate\Support\Facades\Storage::disk('public')->files($dir))
                ->map(function ($path) {
                    return asset('storage/' . $path);
                })
                ->values();
        }
        return response()->json(['files' => $files]);
    }

    public function getServices($model, $id)
    {
        if (!isset($this->models[$model])) {
            abort(404);
        }
        $modelClass = $this->models[$model];
        $item = $modelClass::findOrFail($id);

        // Liste des services à afficher (adapte selon ton modèle)
        $serviceFields = [
            'service_backup' => 'Sauvegarde',
            'service_connect' => 'Connexion',
            'service_cloody' => 'Cloody',
            'service_maintenance' => 'Maintenance',
            'service_heberg_web' => 'Hébergement Web',
            'service_mail' => 'Mail',
            'service_EBP' => 'EBP',
            'service_maintenance_office' => 'Maintenance Office',
            'service_maintenance_serveur' => 'Maintenance Serveur',
            'service_maintenance_infra_rso' => 'Infra RSO',
            'service_maintenance_equip_rso' => 'Equip RSO',
            'service_maintenance_ESET' => 'ESET',
            'service_maintenance_domaine_DNS' => 'Domaine DNS',
            'service_comptes' => 'Comptes',
        ];

        $services = [];
        foreach ($serviceFields as $field => $label) {
            if (isset($item->$field)) {
                $services[] = [
                    'id' => $field,
                    'label' => $label,
                    'actif' => (bool)$item->$field,
                ];
            }
        }

        return response()->json(['services' => $services]);
    }

public function updateServices(Request $request, $model, $id)
{
    if (!isset($this->models[$model])) {
        abort(404);
    }
    $modelClass = $this->models[$model];
    $item = $modelClass::findOrFail($id);

    // Liste adaptée selon le modèle
    if ($model === 'societe') {
        $serviceFields = [
            'service_backup',
            'service_connect',
            'service_cloody',
            'service_maintenance',
            'service_heberg_web',
            'service_mail',
            'service_EBP',
            'service_maintenance_office',
            'service_maintenance_serveur',
            'service_maintenance_infra_rso',
            'service_maintenance_equip_rso',
            'service_maintenance_ESET',
            'service_maintenance_domaine_DNS',
        ];
    } elseif ($model === 'interlocuteur') {
        $serviceFields = [
            'service_connect',
            'service_cloody',
            'service_comptes',
            'service_mail',
        ];
    } else {
        abort(400, 'Modèle non supporté pour les services');
    }

    $checked = $request->input('services', []);
    foreach ($serviceFields as $field) {
        $item->$field = in_array($field, $checked) ? 1 : 0;
    }
    $item->save();

    return response()->json(['success' => true]);
}
}
