<?php

namespace App\Notifications;

use App\Entities\Schedule;
use CodeIgniter\Email\Email;
use CodeIgniter\I18n\Time;
use Config\Services;

class CanceledScheduleNotification
{

    /** @var Email */
    protected Email $service;

    /** @var string */
    protected string $email;

    /** @var Schedule */
    protected Schedule $schedule;

    /** Construtor */
    public function __construct (string $email, Schedule $schedule)
    {

        $this->service = Services::email();

        $this->email = $email;

        $this->schedule = $schedule;
    }

    /**
     * Envia o email de notificação do email de agendamento criado 
     * 
     * @return boolean 
     */
    public function send():bool 
    {

        // destinatario
        $this->service->setTo($this->email); 
        $this->service->setSubject('Seu agendmento foi cancelado'); 

        $data = [

            'chosen_date' => $this->schedule->chosen_date, 
            'unit'        => $this->schedule->unit, 
            'service'     => $this->schedule->service, 
            'address'     => $this->schedule->address, 
            'when'        => Time::now()->format('d/m/Y H:i'), 
        ];

        $this->service->setMessage(view('Front/Email/schedule_canceled', $data)); 
        $this->service->setMailType('html');  

        if (!$this->service->send()) {

            log_message('error', $this->service->printDebugger());

            return false; 
        }

        return true; 
    }
}
