<?php

namespace App\Http\Controllers\Admin;


use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
      // Vérifie que l'utilisateur connecté est superadmin
      if (backpack_user()->role !== 'superadmin') {
        abort(403, 'Accès réservé au superadmin.');
    }

    CRUD::setModel(\App\Models\User::class);
    CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
    CRUD::setEntityNameStrings('user', 'Utilisateurs');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('email');
        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::field('name')->validationRules('required|min:5');
        CRUD::field('email')->validationRules('required|email|unique:users,email');
        CRUD::field('password')->validationRules('required');
        CRUD::field('role')->type('select_from_array')->options([
            'user' => 'User',
            'admin' => 'Admin',
            'superadmin' => 'Super Admin',
        ]);
    
        \App\Models\User::creating(function ($entry) {
            $entry->password = \Illuminate\Support\Facades\Hash::make($entry->password);
        });
    }
    
    protected function setupUpdateOperation()
    {
        CRUD::field('name')->validationRules('required|min:5');
        CRUD::field('email')->validationRules('required|email|unique:users,email,' . CRUD::getCurrentEntryId());
        CRUD::field('password')->validationRules('nullable'); // <-- mot de passe optionnel en update
        CRUD::field('role')->type('select_from_array')->options([
            'user' => 'User',
            'admin' => 'Admin',
            'superadmin' => 'Super Admin',
        ]);
    
        \App\Models\User::updating(function ($entry) {
            if (request('password')) {
                $entry->password = \Illuminate\Support\Facades\Hash::make(request('password'));
            } else {
                $entry->password = $entry->getOriginal('password');
            }
        });
    }
}
