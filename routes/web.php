<?php

use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\ContextualSearchController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocietyController;
use App\Http\Controllers\UserSearchController;
use App\Http\Controllers\UserTechController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/global-search', [GlobalSearchController::class, 'index'])->name('global-search');
Route::get('/global-suggestions', [GlobalSearchController::class, 'suggestions'])->name('global-suggestions');

Route::get('/contextual-search', [ContextualSearchController::class, 'index'])->name('contextual-search');
Route::get('/contextual-suggestions', [ContextualSearchController::class, 'suggestions'])->name('contextual-suggestions');

Route::get('/user-search', [UserSearchController::class, 'index'])->name('user-search');
Route::post('/user-search', [UserSearchController::class, 'search'])->name('user-search.post');

Route::get('/user-suggestions', [UserSearchController::class, 'suggestions'])->name('user-suggestions');

Route::get('/model/{model}/suggestions', [ModelController::class, 'suggestions'])->name('model-suggestions');

Route::delete('/model/{model}/delete/{id}', [ModelController::class, 'delete'])->name('model.delete');

Route::get('/model/{model}/files/{id}', [ModelController::class, 'listFiles'])->name('model.files');

Route::post('/model/{model}/upload/{id}', [ModelController::class, 'uploadFile'])->name('model.upload');

Route::get('/model/{model}/show/{id}', [ModelController::class, 'show'])->name('model.show');

Route::post('/model/{model}/update-field/{id}', [ModelController::class, 'updateField'])->name('model.updateField');

Route::get('/user-tech', [UserTechController::class, 'index'])->name('user-tech.index');


Route::get('/societe/{id}/interlocuteurs', function($id) {
    return \App\Models\Interlocutor::where('societe', $id)->get();
});

Route::get('/societe/{id}/problemes', [SocietyController::class, 'problemes']);

Route::post('/problemes/update-description/{id}', function ($id, \Illuminate\Http\Request $request) {
    $problem = \App\Models\Problem::findOrFail($id);
    $problem->description = $request->input('description');
    $problem->save();
    return response()->json(['success' => true]);
})->name('problemes.update-description')->middleware('auth');

Route::get('/problemes/search', function (\Illuminate\Http\Request $request) {
    $q = $request->input('q');
    $tool = $request->input('tool');
    $env = $request->input('env');
    $societe = $request->input('societe');

    $problems = \App\Models\Problem::select('id', 'title', 'description', 'tool', 'env', 'societe')
        ->when($q, function ($query, $q) {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('title', 'like', "%$q%")
                         ->orWhere('description', 'like', "%$q%");
            });
        })
        ->when($tool, function ($query, $tool) {
            $query->where('tool', $tool);
        })
        ->when($env, function ($query, $env) {
            $query->where('env', $env);
        })
        ->when($societe, function ($query, $societe) {
            $query->where('societe', $societe)
                  ->orWhereHas('society', function ($q2) use ($societe) {
                      $q2->where('name', 'like', "%$societe%");
                  });
        })
        ->alphabetical()
        ->limit(50)
        ->get();

    return [
        'problems' => $problems,
        'tools' => \App\Models\Tool::select('id', 'name')->alphabetical()->get(),
        'envs' => \App\Models\Env::select('id', 'name')->alphabetical()->get(),
        'societies' => \App\Models\Society::select('id', 'name')->alphabetical()->get(),
    ];
});

Route::get('/model/{model}/services/{id}', [ModelController::class, 'getServices'])->name('model.getServices');
Route::post('/model/{model}/services/{id}', [ModelController::class, 'updateServices'])->name('model.updateServices');

Route::middleware(['auth'])->group(function () {
    Route::get('/service', [\App\Http\Controllers\ServiceController::class, 'index'])->name('service.index');
    Route::post('/service', [\App\Http\Controllers\ServiceController::class, 'store'])->name('service.store');
    Route::delete('/service/{service}', [\App\Http\Controllers\ServiceController::class, 'destroy'])->name('service.destroy');
    Route::put('/service/{service}', [\App\Http\Controllers\ServiceController::class, 'update'])->name('service.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user-tech', [UserTechController::class, 'index'])->name('user-tech.index');
    Route::post('/user-tech/user', [UserTechController::class, 'storeUser'])->name('user-tech.user.store');
    Route::put('/user-tech/user/{user}', [UserTechController::class, 'updateUser'])->name('user-tech.user.update');
    Route::delete('/user-tech/user/{user}', [UserTechController::class, 'destroyUser'])->name('user-tech.user.destroy');

    Route::post('/user-tech/tech', [UserTechController::class, 'storeTech'])->name('user-tech.tech.store');
    Route::put('/user-tech/tech/{tech}', [UserTechController::class, 'updateTech'])->name('user-tech.tech.update');
    Route::delete('/user-tech/tech/{tech}', [UserTechController::class, 'destroyTech'])->name('user-tech.tech.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/model/{model}', [ModelController::class, 'index'])
        ->name('model.index');
    Route::get('/model/{model}/{action}/{id?}', [ModelController::class, 'form'])
        ->name('model.form');
    Route::post('/model/{model}/{action}/{id?}', [ModelController::class, 'submit'])
        ->name('model.submit');
});

require __DIR__ . '/auth.php';