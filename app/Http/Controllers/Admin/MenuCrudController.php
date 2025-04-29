<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


class MenuCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;


    public function setup()
    {
        CRUD::setModel(\App\Models\Menu::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/menu');
        CRUD::setEntityNameStrings('menu', 'Accès rapides');
    }

    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.


    }


    protected function setupCreateOperation()
    {
        CRUD::field('title')->type('text')->label('Titre')->validationRules('required|min:2');
        CRUD::field('link')->type('text')->label('Lien')->validationRules('required');
        CRUD::field('order')->type('number')->label('Ordre')->default(0);
        CRUD::field('role')
            ->type('select_from_array')
            ->label('Rôle')
            ->options([
            '' => 'Tous les rôles',
            'superadmin' => 'Superadmin',
            'admin' => 'Admin',
            ])
            ->allows_null(true);
    }


    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
