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
        return view('model.form', compact('model', 'action', 'instance'));
    }

    public function submit(Request $request, $model, $action, $id = null)
    {
        if (!isset($this->models[$model])) abort(404);
        $class = $this->models[$model];

        $rules = [
            'société' => [
                'id_main'                           => 'nullable|integer',
                'name'                              => 'required|string|max:255',
                'status_client'                     => 'nullable|string|max:255',
                'status_distrib'                    => 'nullable|string|max:255',
                'service_backup'                    => 'nullable|string|max:255',
                'infos_backup'                      => 'nullable|string|max:255',
                'service_connect'                   => 'nullable|string|max:255',
                'infos_connect'                     => 'nullable|string|max:255',
                'service_cloody'                    => 'nullable|string|max:255',
                'infos_cloody'                      => 'nullable|string|max:255',
                'service_maintenance'               => 'nullable|string|max:255',
                'infos_maintenance'                 => 'nullable|string|max:255',
                'service_heberg_web'                => 'nullable|string|max:255',
                'infos_heberg_web'                  => 'nullable|string|max:255',
                'service_mail'                      => 'nullable|string|max:255',
                'infos_mail'                        => 'nullable|string|max:255',
                'service_EBP'                       => 'nullable|string|max:255',
                'infos_EBP'                         => 'nullable|string|max:255',
                'service_maintenance_office'        => 'nullable|string|max:255',
                'infos_maintenance_office'          => 'nullable|string|max:255',
                'service_maintenance_serveur'       => 'nullable|string|max:255',
                'infos_maintenance_serveur'         => 'nullable|string|max:255',
                'service_maintenance_infra_rso'     => 'nullable|string|max:255',
                'infos_maintenance_infra_rso'       => 'nullable|string|max:255',
                'service_maintenance_equip_rso'     => 'nullable|string|max:255',
                'infos_maintenance_equip_rso'       => 'nullable|string|max:255',
                'service_maintenance_ESET'          => 'nullable|string|max:255',
                'infos_maintenance_ESET'            => 'nullable|string|max:255',
                'service_maintenance_domaine_DNS'   => 'nullable|string|max:255',
                'infos_maintenance_domaine_DNS'     => 'nullable|string|max:255',
                'boss_name'                         => 'nullable|string|max:255',
                'boss_phone'                        => 'nullable|string|max:255',
                'recep_phone'                       => 'nullable|string|max:255',
                'address'                           => 'nullable|string|max:255',
                'status'                            => 'nullable|string|max:255',
            ],
            'interlocuteur' => [
                'name'              => 'required|string|max:255',
                'lastname'          => 'nullable|string|max:255',
                'fullname'          => 'nullable|string|max:255',
                'societe'           => 'nullable|string|max:255',
                'phone_fix'         => 'nullable|string|max:255',
                'phone_mobile'      => 'nullable|string|max:255',
                'email'             => 'nullable|email|max:255',
                'id_teamviewer'     => 'nullable|string|max:255',
                'service_connect'   => 'nullable|string|max:255',
                'service_cloody'    => 'nullable|string|max:255',
                'service_comptes'   => 'nullable|string|max:255',
                'service_mail'      => 'nullable|string|max:255',
                'infos_connect'     => 'nullable|string|max:255',
                'infos_cloody'      => 'nullable|string|max:255',
                'infos_comptes'     => 'nullable|string|max:255',
                'infos_mail'        => 'nullable|string|max:255',
            ],
            'environnement' => [
                'name' => 'required|string|max:255',
            ],
            'problème' => [
                'title'       => 'required|string|max:255',
                'env'         => 'nullable|string|max:255',
                'tool'        => 'nullable|string|max:255',
                'societe'     => 'nullable|string|max:255',
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
}
