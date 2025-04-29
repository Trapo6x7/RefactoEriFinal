<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class InterlocutorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InterlocutorCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Interlocutor::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/interlocutor');
        CRUD::setEntityNameStrings('interlocutor', 'Interlocuteurs');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }
    protected function setupCreateOperation()
    {
        CRUD::field('name')->type('text')->validationRules('required|min:2');
        CRUD::field('lastname')->type('text');
        CRUD::field('fullname')->type('text');
        CRUD::field('societe')->type('text');
        CRUD::field('phone_fix')->type('text');
        CRUD::field('phone_mobile')->type('text');
        CRUD::field('email')->type('email');
        CRUD::field('id_teamviewer')->type('text');
        CRUD::field('service_connect')->type('checkbox');
        CRUD::field('service_cloody')->type('checkbox');
        CRUD::field('service_comptes')->type('checkbox');
        CRUD::field('service_mail')->type('checkbox');
        CRUD::field('infos_connect')->type('textarea');
        CRUD::field('infos_cloody')->type('textarea');
        CRUD::field('infos_comptes')->type('textarea');
        CRUD::field('infos_mail')->type('textarea');
    }
    
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
