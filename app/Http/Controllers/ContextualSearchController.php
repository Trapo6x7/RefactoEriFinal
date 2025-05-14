<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContextualSearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $table = $request->input('table');

        // Initialisation vide
        $users = $societies = $techs = $problems = $problemStatuses = $interlocutors = $envs = $tools = $menus = collect();

        // Récupérer l'ID de l'élément courant depuis l'URL (3e segment)
        $segments = $request->segments();
        $currentId = $request->input('id');
        $currentItem = null;

        if ($table === 'users' || $table === 'user') {
            $users = \App\Models\User::where('name', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->alphabetical()
                ->get();
            $currentItem = \App\Models\User::find($currentId);
        }
        if ($table === 'societies' || $table === 'society') {
            $societies = \App\Models\Society::where('name', 'like', "%$q%")
                ->orWhere('status_client', 'like', "%$q%")
                ->orWhere('status_distrib', 'like', "%$q%")
                ->orWhere('service_backup', 'like', "%$q%")
                ->orWhere('infos_backup', 'like', "%$q%")
                ->orWhere('service_connect', 'like', "%$q%")
                ->orWhere('infos_connect', 'like', "%$q%")
                ->orWhere('service_cloody', 'like', "%$q%")
                ->orWhere('infos_cloody', 'like', "%$q%")
                ->orWhere('service_maintenance', 'like', "%$q%")
                ->orWhere('infos_maintenance', 'like', "%$q%")
                ->orWhere('service_heberg_web', 'like', "%$q%")
                ->orWhere('infos_heberg_web', 'like', "%$q%")
                ->orWhere('service_mail', 'like', "%$q%")
                ->orWhere('infos_mail', 'like', "%$q%")
                ->orWhere('service_EBP', 'like', "%$q%")
                ->orWhere('infos_EBP', 'like', "%$q%")
                ->orWhere('service_maintenance_office', 'like', "%$q%")
                ->orWhere('infos_maintenance_office', 'like', "%$q%")
                ->orWhere('service_maintenance_serveur', 'like', "%$q%")
                ->orWhere('infos_maintenance_serveur', 'like', "%$q%")
                ->orWhere('service_maintenance_infra_rso', 'like', "%$q%")
                ->orWhere('infos_maintenance_infra_rso', 'like', "%$q%")
                ->orWhere('service_maintenance_equip_rso', 'like', "%$q%")
                ->orWhere('infos_maintenance_equip_rso', 'like', "%$q%")
                ->orWhere('service_maintenance_ESET', 'like', "%$q%")
                ->orWhere('infos_maintenance_ESET', 'like', "%$q%")
                ->orWhere('service_maintenance_domaine_DNS', 'like', "%$q%")
                ->orWhere('infos_maintenance_domaine_DNS', 'like', "%$q%")
                ->orWhere('boss_name', 'like', "%$q%")
                ->orWhere('boss_phone', 'like', "%$q%")
                ->orWhere('recep_phone', 'like', "%$q%")
                ->orWhere('address', 'like', "%$q%")
                ->orWhere('status', 'like', "%$q%")
                ->alphabetical()
                ->get();
            $currentItem = \App\Models\Society::find($currentId);
        }
        if ($table === 'techs' || $table === 'tech') {
            $techs = \App\Models\Tech::where('name', 'like', "%$q%")
                ->alphabetical()
                ->get();
            $currentItem = \App\Models\Tech::find($currentId);
        }
        if ($table === 'problems' || $table === 'problem') {
            $problems = \App\Models\Problem::where('title', 'like', "%$q%")
                ->orWhere('description', 'like', "%$q%")
                ->alphabetical()
                ->get();
            $currentItem = \App\Models\Problem::find($currentId);
        }
        if ($table === 'problemStatuses' || $table === 'problemStatus') {
            $problemStatuses = \App\Models\ProblemStatus::where('name', 'like', "%$q%")
                ->alphabetical()
                ->get();
            $currentItem = \App\Models\ProblemStatus::find($currentId);
        }
        if ($table === 'interlocutors' || $table === 'interlocutor') {
            $interlocutors = \App\Models\Interlocutor::where('name', 'like', "%$q%")
                ->orWhere('lastname', 'like', "%$q%")
                ->orWhere('fullname', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->alphabetical()
                ->get();
            $currentItem = \App\Models\Interlocutor::find($currentId);
        }
        if ($table === 'envs' || $table === 'env') {
            $envs = \App\Models\Env::where('name', 'like', "%$q%")
                ->alphabetical()
                ->get();
            $currentItem = \App\Models\Env::find($currentId);
        }
        if ($table === 'tools' || $table === 'tool') {
            $tools = \App\Models\Tool::where('name', 'like', "%$q%")
                ->alphabetical()
                ->get();
            $currentItem = \App\Models\Tool::find($currentId);
        }
        if ($table === 'menus' || $table === 'menu') {
            $menus = \App\Models\Menu::where('title', 'like', "%$q%")
                ->alphabetical()
                ->get();
            $currentItem = \App\Models\Menu::find($currentId);
        }

        // Si la recherche correspond à un champ, retourne la valeur de ce champ pour l'élément courant
        $fieldValue = null;
        if ($currentItem && in_array(strtolower($q), array_keys($currentItem->getAttributes()))) {
            $fieldValue = $currentItem->{strtolower($q)};
        }
        
        return view('vendor.backpack.theme-tabler.global_search_results', compact(
            'q',
            'users',
            'societies',
            'techs',
            'problems',
            'problemStatuses',
            'interlocutors',
            'envs',
            'tools',
            'menus',
            'fieldValue',
            'currentId'
        ));
    }

    public function suggestions(Request $request)
    {
        $q = $request->input('q');
        $table = $request->input('table');

        $fields = [];

        if ($table === 'users' || $table === 'user') {
            $fields = ['name', 'email'];
        }
        if ($table === 'societies' || $table === 'society') {
            $fields = [
                'name',
                'status_client',
                'status_distrib',
                'service_backup',
                'infos_backup',
                'service_connect',
                'infos_connect',
                'service_cloody',
                'infos_cloody',
                'service_maintenance',
                'infos_maintenance',
                'service_heberg_web',
                'infos_heberg_web',
                'service_mail',
                'infos_mail',
                'service_EBP',
                'infos_EBP',
                'service_maintenance_office',
                'infos_maintenance_office',
                'service_maintenance_serveur',
                'infos_maintenance_serveur',
                'service_maintenance_infra_rso',
                'infos_maintenance_infra_rso',
                'service_maintenance_equip_rso',
                'infos_maintenance_equip_rso',
                'service_maintenance_ESET',
                'infos_maintenance_ESET',
                'service_maintenance_domaine_DNS',
                'infos_maintenance_domaine_DNS',
                'boss_name',
                'boss_phone',
                'recep_phone',
                'address',
                'status',
            ];
        }
        if ($table === 'techs' || $table === 'tech') {
            $fields = ['name'];
        }
        if ($table === 'problems' || $table === 'problem') {
            $fields = ['title', 'description'];
        }
        if ($table === 'problemStatuses' || $table === 'problemStatus') {
            $fields = ['name'];
        }
        if ($table === 'interlocutors' || $table === 'interlocutor') {
            $fields = ['name', 'lastname', 'fullname', 'email'];
        }
        if ($table === 'envs' || $table === 'env') {
            $fields = ['name'];
        }
        if ($table === 'tools' || $table === 'tool') {
            $fields = ['name'];
        }
        if ($table === 'menus' || $table === 'menu') {
            $fields = ['title'];
        }

        // Filtrer selon la saisie utilisateur
        $suggestions = collect($fields)
            ->filter(fn($field) => stripos($field, $q) !== false)
            ->values();

        return response()->json($suggestions);
    }
}
