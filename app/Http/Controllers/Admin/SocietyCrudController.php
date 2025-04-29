<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SocietyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SocietyCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Society::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/society');
        CRUD::setEntityNameStrings('society', 'Sociétés');
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
        CRUD::field('id_main')->type('number');
        CRUD::field('name')->type('text')->validationRules('required|min:3');
        CRUD::field('status_client')->type('checkbox');
        CRUD::field('status_distrib')->type('checkbox');
        CRUD::field('service_backup')->type('checkbox');
        CRUD::field('infos_backup')->type('textarea');
        CRUD::field('service_connect')->type('checkbox');
        CRUD::field('infos_connect')->type('textarea');
        CRUD::field('service_cloody')->type('checkbox');
        CRUD::field('infos_cloody')->type('textarea');
        CRUD::field('service_maintenance')->type('checkbox');
        CRUD::field('infos_maintenance')->type('textarea');
        CRUD::field('service_heberg_web')->type('checkbox');
        CRUD::field('infos_heberg_web')->type('textarea');
        CRUD::field('service_mail')->type('checkbox');
        CRUD::field('infos_mail')->type('textarea');
        CRUD::field('service_EBP')->type('checkbox');
        CRUD::field('infos_EBP')->type('textarea');
        CRUD::field('service_maintenance_office')->type('checkbox');
        CRUD::field('infos_maintenance_office')->type('textarea');
        CRUD::field('service_maintenance_serveur')->type('checkbox');
        CRUD::field('infos_maintenance_serveur')->type('textarea');
        CRUD::field('service_maintenance_infra_rso')->type('checkbox');
        CRUD::field('infos_maintenance_infra_rso')->type('textarea');
        CRUD::field('service_maintenance_equip_rso')->type('checkbox');
        CRUD::field('infos_maintenance_equip_rso')->type('textarea');
        CRUD::field('service_maintenance_ESET')->type('checkbox');
        CRUD::field('infos_maintenance_ESET')->type('textarea');
        CRUD::field('service_maintenance_domaine_DNS')->type('checkbox');
        CRUD::field('infos_maintenance_domaine_DNS')->type('textarea');
        CRUD::field('boss_name')->type('text');
        CRUD::field('boss_phone')->type('text');
        CRUD::field('recep_phone')->type('text');
        CRUD::field('address')->type('textarea');
        CRUD::field('status')->type('text');
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
