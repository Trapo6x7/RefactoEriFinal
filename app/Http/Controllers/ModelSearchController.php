<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModelSearchController extends Controller
{
    protected $models = [
        'societe'       => \App\Models\Society::class,
        'interlocuteur' => \App\Models\Interlocutor::class,
        'environnement' => \App\Models\Env::class,
        'probleme'      => \App\Models\Problem::class,
        'outil'         => \App\Models\Tool::class,
    ];

    public function search(Request $request, $model)
    {
        if (!isset($this->models[$model])) {
            abort(404);
        }
        $modelClass = $this->models[$model];
        $search = $request->query('q');

        $fields = match ($model) {
            'societe', 'outil', 'environnement' => ['name'],
            'interlocuteur' => ['name', 'lastname', 'fullname', 'email'],
            'probleme' => ['title', 'description'],
            default => ['name'],
        };

        $items = $modelClass::query()
        ->when($search, function ($query) use ($search, $fields) {
            $query->where(function ($q) use ($search, $fields) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        })
        ->orderBy('name', 'asc')
        ->get();

        return view('model.partials.table_body', [
            'items' => $items,
            'model' => $model
        ])->render();
    }
}