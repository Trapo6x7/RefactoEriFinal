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

        // Search in all tables if $table is not specified, otherwise only in the specified table
        if (!$table || $table === 'societies') {
            $results['societies'] = \App\Models\Society::where('name', 'like', "%$q%")->get();
        }
        if (!$table || $table === 'problems') {
            $results['problems'] = \App\Models\Problem::where('title', 'like', "%$q%")->get();
        }
        if (!$table || $table === 'problemStatuses') {
            $results['problemStatuses'] = \App\Models\ProblemStatus::where('name', 'like', "%$q%")->get();
        }
        if (!$table || $table === 'interlocutors') {
            $results['interlocutors'] = \App\Models\Interlocutor::where('fullname', 'like', "%$q%")->orWhere('name', 'like', "%$q%")->get();
        }
        if (!$table || $table === 'envs') {
            $results['envs'] = \App\Models\Env::where('name', 'like', "%$q%")->get();
        }
        if (!$table || $table === 'tools') {
            $results['tools'] = \App\Models\Tool::where('name', 'like', "%$q%")->get();
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
                ->limit(5)
                ->pluck('name')
                ->map(fn($name) => ['label' => $name])
                ->toArray()
        );
    }
    if (!$table || $table === 'problems') {
        $suggestions = array_merge(
            $suggestions,
            \App\Models\Problem::where('title', 'like', "%$q%")
                ->limit(5)
                ->pluck('title')
                ->map(fn($title) => ['label' => $title])
                ->toArray()
        );
    }
    if (!$table || $table === 'problemStatuses') {
        $suggestions = array_merge(
            $suggestions,
            \App\Models\ProblemStatus::where('name', 'like', "%$q%")
                ->limit(5)
                ->pluck('name')
                ->map(fn($name) => ['label' => $name])
                ->toArray()
        );
    }
    if (!$table || $table === 'interlocutors') {
        $suggestions = array_merge(
            $suggestions,
            \App\Models\Interlocutor::where('fullname', 'like', "%$q%")
                ->orWhere('name', 'like', "%$q%")
                ->limit(5)
                ->get()
                ->map(fn($i) => ['label' => $i->fullname ?? $i->name])
                ->toArray()
        );
    }
    if (!$table || $table === 'envs') {
        $suggestions = array_merge(
            $suggestions,
            \App\Models\Env::where('name', 'like', "%$q%")
                ->limit(5)
                ->pluck('name')
                ->map(fn($name) => ['label' => $name])
                ->toArray()
        );
    }
    if (!$table || $table === 'tools') {
        $suggestions = array_merge(
            $suggestions,
            \App\Models\Tool::where('name', 'like', "%$q%")
                ->limit(5)
                ->pluck('name')
                ->map(fn($name) => ['label' => $name])
                ->toArray()
        );
    }

    // Déduplique les suggestions
    $suggestions = array_unique($suggestions, SORT_REGULAR);

    return response()->json($suggestions);
}
}
