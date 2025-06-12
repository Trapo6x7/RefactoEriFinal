<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q');
        $table = $request->input('table');
        $results = [];

        // Recherche avec tri alphabétique
        if (!$table || $table === 'societies') {
            $results['societies'] = \App\Models\Society::where('name', 'like', "%$q%")
                ->alphabetical()
                ->get();
        }
        if (!$table || $table === 'problems') {
            $results['problems'] = \App\Models\Problem::where('title', 'like', "%$q%")
                ->alphabetical()
                ->get();
        }
        if (!$table || $table === 'interlocutors') {
            $results['interlocutors'] = \App\Models\Interlocutor::where('fullname', 'like', "%$q%")
                ->orWhere('name', 'like', "%$q%")
                ->alphabetical()
                ->get();
        }
        if (!$table || $table === 'envs') {
            $results['envs'] = \App\Models\Env::where('name', 'like', "%$q%")
                ->alphabetical()
                ->get();
        }
        if (!$table || $table === 'tools') {
            $results['tools'] = \App\Models\Tool::where('name', 'like', "%$q%")
                ->alphabetical()
                ->get();
        }

        return view('profile.partials.global_user_search_results', [
            'societies' => $results['societies'] ?? collect(),
            'problems' => $results['problems'] ?? collect(),
            'problemStatuses' => $results['problemStatuses'] ?? collect(),
            'interlocutors' => $results['interlocutors'] ?? collect(),
            'envs' => $results['envs'] ?? collect(),
            'tools' => $results['tools'] ?? collect(),
        ]);
    }

    public function suggestions(Request $request)
    {
        $q = $request->input('q');
        $table = $request->input('table');
        $suggestions = [];

        if (!$table || $table === 'societies') {
            $suggestions = array_merge(
                $suggestions,
                \App\Models\Society::where('name', 'like', "%$q%")
                    ->alphabetical()
                    ->limit(15)
                    ->get()
                    ->map(fn($s) => [
                        'label' => $s->name,
                        'id' => $s->id,
                        'model' => 'societe'
                    ])
                    ->toArray()
            );
        }

        if (!$table || $table === 'interlocutors') {
            $suggestions = array_merge(
                $suggestions,
                \App\Models\Interlocutor::where('fullname', 'like', "%$q%")
                    ->orWhere('name', 'like', "%$q%")
                    ->alphabetical()
                    ->limit(15)
                    ->get()
                    ->map(fn($i) => [
                        'label' => $i->fullname ?? $i->name,
                        'id' => $i->id,
                        'model' => 'interlocuteur'
                    ])
                    ->toArray()
            );
        }

        // Déduplique les suggestions
        $suggestions = array_unique($suggestions, SORT_REGULAR);

        // Trie alphabétiquement sur le champ 'label'
        usort($suggestions, function($a, $b) {
            return strcasecmp($a['label'], $b['label']);
        });

        return response()->json($suggestions);
    }
}