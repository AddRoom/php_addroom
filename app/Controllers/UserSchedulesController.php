<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\UserScheduleService;
use CodeIgniter\Config\Factories;

class UserSchedulesController extends BaseController
{

    /** @var UserScheduleService */
    private UserScheduleService $userScheduleService;

    /** Construtor */
    public function __construct()
    {

        $this->userScheduleService = Factories::class(UserScheduleService::class);
    }

    /**
     * Renderiza a view para a view para o user Logado gerenciar seus agendamentos 
     * 
     * @return RenderInterface 
     */
    public function index()
    {

        $data = [

            'title'        => 'Meus agendamentos',
        ];

        return view('Front/Schedules/user_schedules', $data);
    }

    /**
     * Recupera os agendamentos do usuario logado
     * 
     * @return ResponseInterface
     */
    public function all()
    {

        try {

            $this->checkMethod('ajax');

            $schedules = $this->userScheduleService->all();

            return $this->response->setJSON([

                'schedules' => $schedules
            ]);
        } catch (\Throwable $th) {

            log_message('error', '[ERROR] {exception}', ['exception' => $th]);

            $this->response->setStatusCode(500);
        }
    }

    /**
     * Recupera os agendamentos do usuario logado
     * 
     * @return ResponseInterface
     */
    public function cancel()
    {

        try {

            $this->checkMethod('ajax');

            $request = (object) $this->request->getJSON();

            $this->userScheduleService->cancelUserSchedule ((int) $request->schedule);

            return $this->response->setJSON([

                'success' => true, 
                'token'   => csrf_hash(),
            ]);
        } catch (\Throwable $th) {

            log_message('error', '[ERROR] {exception}', ['exception' => $th]);

            $this->response->setStatusCode(500);
        }
    }
}
