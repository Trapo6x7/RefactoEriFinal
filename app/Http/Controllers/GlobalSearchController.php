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
        $societies = $interlocutors = collect();

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
                ->alphabetical()
                ->get();
        }

        if (
            !$table ||
            $table === 'interlocutors' || $table === 'interlocutor'
        ) {
            $interlocutors = \App\Models\Interlocutor::where('name', 'like', "%$q%")
                ->orWhere('lastname', 'like', "%$q%")
                ->orWhere('fullname', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->alphabetical()
                ->get();
        }

        return view('vendor.backpack.theme-tabler.global_search_results', compact(
            'q',
            'societies',
            'interlocutors',
        ));
    }

    public function suggestions(Request $request)
    {
        $q = $request->input('q');
        $table = $request->input('table');

        $societies = $interlocutors = collect();

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
                ->alphabetical()
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
                ->alphabetical()
                ->pluck('fullname');
        }

        $suggestions = $societies
            ->merge($interlocutors)
            ->unique()
            ->values();

        $suggestions = $suggestions->sortBy(function ($item) {
            if (is_object($item)) {
                return $item->label ?? $item->name ?? '';
            }
            return $item;
        }, SORT_NATURAL | SORT_FLAG_CASE)->values();

        return response()->json($suggestions);
    }
}
