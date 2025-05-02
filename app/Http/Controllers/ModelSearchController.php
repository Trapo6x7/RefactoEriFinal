<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModelSearchController extends Controller
{
    protected $models = [
        'société'       => \App\Models\Society::class,
        'interlocuteur' => \App\Models\Interlocutor::class,
        'environnement' => \App\Models\Env::class,
        'problème'      => \App\Models\Problem::class,
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
            'société', 'outil', 'environnement' => ['name'],
            'interlocuteur' => ['name', 'lastname', 'fullname', 'email'],
            'problème' => ['title', 'description'],
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
            ->get();

        return view('model.partials.table_body', compact('items'))->render();
    }
}