<?php

use App\Controllers\HomeController as WebController;
use App\Controllers\SchedulesController;
use CodeIgniter\Router\RouteCollection;
use App\Controllers\Super\HomeController;
use App\Controllers\Super\ServicesController;
use App\Controllers\Super\UnitsController;
use App\Controllers\Super\UnitsServicesController;
use App\Controllers\UserSchedulesController;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', [WebController::class, 'index'], ['as' => 'home']);

// rotas de autentificação 
service('auth')->routes($routes);

$routes->group('super', ['filter' => 'group:superadmin'], static function ($routes) {

    // home
    $routes->get('/', [HomeController::class, 'index'], ['as' => 'super.home']);

    // rotas de uniaades
    $routes->group('units', static function ($routes) {

        $routes->get('/', [UnitsController::class, 'index'], ['as' => 'units']);
        $routes->get('new', [UnitsController::class, 'new'], ['as' => 'units.new']);
        $routes->get('edit/(:num)', [UnitsController::class, 'edit/$1'], ['as' => 'units.edit']);
        $routes->post('create', [UnitsController::class, 'create'], ['as' => 'units.create']);
        $routes->put('update/(:num)', [UnitsController::class, 'update/$1'], ['as' => 'units.update']);
        $routes->put('action/(:num)', [UnitsController::class, 'action/$1'], ['as' => 'units.action']); // ativa / desativa um registro
        $routes->delete('destroy/(:num)', [UnitsController::class, 'destroy/$1'], ['as' => 'units.destroy']);
        $routes->get('schedules/(:num)', [UnitsController::class, 'schedules/$1'], ['as' => 'units.schedules']);

        // rotas dos serviços da unidade
        $routes->get('services/(:num)', [UnitsServicesController::class, 'services/$1'], ['as' => 'units.services']);
        $routes->put('services/store(:num)', [UnitsServicesController::class, 'store/$1'], ['as' => 'units.services.store']);
    });

    // rotas de serviços
    $routes->group('services', static function ($routes) {

        $routes->get('/', [ServicesController::class, 'index'], ['as' => 'services']);
        $routes->get('new', [ServicesController::class, 'new'], ['as' => 'services.new']);
        $routes->get('edit/(:num)', [ServicesController::class, 'edit/$1'], ['as' => 'services.edit']);
        $routes->post('create', [ServicesController::class, 'create'], ['as' => 'services.create']);
        $routes->put('update/(:num)', [ServicesController::class, 'update/$1'], ['as' => 'services.update']);
        $routes->put('action/(:num)', [ServicesController::class, 'action/$1'], ['as' => 'services.action']); // ativa / desativa um registro
        $routes->delete('destroy/(:num)', [ServicesController::class, 'destroy/$1'], ['as' => 'services.destroy']);
    });
});

// rotas de agendamentos do user logado
$routes->group('schedules', static function ($routes) {

    $routes->get('/', [SchedulesController::class, 'index'], ['as' => 'schedules.new']);
    $routes->get('services', [SchedulesController::class, 'unitServices'], ['as' => 'get.unit.services']);  // recuperamos via fetch API os serviços da unidade 
    $routes->get('calendar', [SchedulesController::class, 'getCalendar'], ['as' => 'get.calendar']); // recuperamos via fetch API o calendario para o mês desejado
    $routes->get('hours', [SchedulesController::class, 'getHours'], ['as' => 'get.hours']); // recuperamos via fetch API os horários disponiveis
    $routes->post('create', [SchedulesController::class, 'createSchedule'], ['as' => 'create.schedule']); // criamos o agendamento via fetch API   

    $routes->group('my', static function ($routes) {

        // agendemtos do user logado 
        $routes->get('/', [UserSchedulesController::class, 'index'], ['as' => 'schedules.my']);
        $routes->get('all', [UserSchedulesController::class, 'all'], ['as' => 'schedules.my.all']); // recuperamos via fetch API
        $routes->delete('cancel', [UserSchedulesController::class, 'cancel'], ['as' => 'schedules.my.cancel']); // cancela via fetch API 
    });
});
