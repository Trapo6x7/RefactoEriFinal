<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $table = $request->input('table');

        // Initialisation vide
        $users = $societies = $techs = $problems = $problemStatuses = $interlocutors = $envs = $tools = $menus = collect();

        if (
            !$table ||
            $table === 'users' || $table === 'user'
        ) {
            $users = \App\Models\User::where('name', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->get();
        }
        if (
            !$table ||
            $table === 'societies' || $table === 'society'
        ) {
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
                ->get();
        }
        if (
            !$table ||
            $table === 'techs' || $table === 'tech'
        ) {
            $techs = \App\Models\Tech::where('name', 'like', "%$q%")->get();
        }
        if (
            !$table ||
            $table === 'problems' || $table === 'problem'
        ) {
            $problems = \App\Models\Problem::where('title', 'like', "%$q%")
                ->orWhere('description', 'like', "%$q%")
                ->get();
        }
        if (
            !$table ||
            $table === 'problemStatuses' || $table === 'problemStatus'
        ) {
            $problemStatuses = \App\Models\ProblemStatus::where('name', 'like', "%$q%")->get();
        }
        if (
            !$table ||
            $table === 'interlocutors' || $table === 'interlocutor'
        ) {
            $interlocutors = \App\Models\Interlocutor::where('name', 'like', "%$q%")
                ->orWhere('lastname', 'like', "%$q%")
                ->orWhere('fullname', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->get();
        }
        if (
            !$table ||
            $table === 'envs' || $table === 'env'
        ) {
            $envs = \App\Models\Env::where('name', 'like', "%$q%")->get();
        }
        if (
            !$table ||
            $table === 'tools' || $table === 'tool'
        ) {
            $tools = \App\Models\Tool::where('name', 'like', "%$q%")->get();
        }
        if (
            !$table ||
            $table === 'menus' || $table === 'menu'
        ) {
            $menus = \App\Models\Menu::where('title', 'like', "%$q%")->get();
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
        ));
    }

    public function suggestions(Request $request)
    {
        $q = $request->input('q');
        $table = $request->input('table');

        $users = $societies = $techs = $problems = $problemStatuses = $interlocutors = $envs = $tools = $menus = collect();

        if (
            !$table ||
            $table === 'users' || $table === 'user'
        ) {
            $users = \App\Models\User::where('name', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->limit(5)
                ->pluck('name');
        }
        if (
            !$table ||
            $table === 'societies' || $table === 'society'
        ) {
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
                ->limit(5)
                ->pluck('name');
        }
        if (
            !$table ||
            $table === 'techs' || $table === 'tech'
        ) {
            $techs = \App\Models\Tech::where('name', 'like', "%$q%")
                ->limit(5)
                ->pluck('name');
        }
        if (
            !$table ||
            $table === 'problems' || $table === 'problem'
        ) {
            $problems = \App\Models\Problem::where('title', 'like', "%$q%")
                ->orWhere('description', 'like', "%$q%")
                ->limit(5)
                ->pluck('title');
        }
        if (
            !$table ||
            $table === 'problemStatuses' || $table === 'problemStatus'
        ) {
            $problemStatuses = \App\Models\ProblemStatus::where('name', 'like', "%$q%")
                ->limit(5)
                ->pluck('name');
        }
        if (
            !$table ||
            $table === 'interlocutors' || $table === 'interlocutor'
        ) {
            $interlocutors = \App\Models\Interlocutor::where('name', 'like', "%$q%")
                ->orWhere('lastname', 'like', "%$q%")
                ->orWhere('fullname', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->limit(5)
                ->pluck('fullname');
        }
        if (
            !$table ||
            $table === 'envs' || $table === 'env'
        ) {
            $envs = \App\Models\Env::where('name', 'like', "%$q%")
                ->limit(5)
                ->pluck('name');
        }
        if (
            !$table ||
            $table === 'tools' || $table === 'tool'
        ) {
            $tools = \App\Models\Tool::where('name', 'like', "%$q%")
                ->limit(5)
                ->pluck('name');
        }
        if (
            !$table ||
            $table === 'menus' || $table === 'menu'
        ) {
            $menus = \App\Models\Menu::where('title', 'like', "%$q%")
                ->limit(5)
                ->pluck('title');
        }

        $suggestions = $users
            ->merge($societies)
            ->merge($techs)
            ->merge($problems)
            ->merge($problemStatuses)
            ->merge($interlocutors)
            ->merge($envs)
            ->merge($tools)
            ->merge($menus)
            ->unique()
            ->values();

        return response()->json($suggestions);
    }
}
