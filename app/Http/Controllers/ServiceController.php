<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || Auth::user()->role !== 'superadmin') {
                abort(403);
            }
            return $next($request);
        })->only(['store', 'destroy']);
    }

    public function index()
    {
        $services = Service::all();
        return view('service.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:255',
        ]);
        Service::create($request->only('name', 'link'));
        return redirect()->route('service.index')->with('success', 'Service ajouté !');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('service.index')->with('success', 'Service supprimé !');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:255',
        ]);
        $service->update($request->only('name', 'link'));
        return response()->json(['success' => true]);
    }
}
