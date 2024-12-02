<?php

namespace App\Models;

use App\Entities\Schedule;
use App\Models\MyBaseModel;
use Exception;

class ScheduleModel extends MyBaseModel
{

    protected $DBGroup          = 'default'; // eu criei
    protected $table            = 'schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Schedule::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [

        'unit_id',
        'service_id',
        'user_id',
        'finished',
        'canceled',           
        'chosen_date',
    ];

    // protected bool $allowEmptyInserts = false;
    // protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = []; // temos uma classe especifica de validação 

    protected $validationMessages   = [];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['escapeData', 'setUserId'];
    protected $beforeUpdate   = ['escapeData'];

    /**
     * Define no array de dados o id do usuario logado 
     * 
     * @param array $data
     * @return array
     */
    protected function setUserId (array $data) {

        // o usuario está logado 
        if (!auth()->loggedIn()) {

            throw new Exception('Não existe uma sessão válida'); 
        }

        if (!isset($data['data'])) {

            return $data; 
        }

        $data['data']['user_id'] = auth()->user()->id; 

        return $data; 
    }

    /**
     * Verifica se a data e horário escolhidos não estão agendados
     * Ùtil para fazer um ultimo 'check' antes de inserir o registro, pois o usuario pode ficar com a pagina aberta por bastante tempo
     * 
     * @param integer|string
     * @param string $chosenDate
     * @return boolean 
     */
    public function chosenDateIsFree (int|string $unitId, string $chosenDate): bool 
    {

        return $this->where('unit_id', $unitId)->where('chosen_date', $chosenDate)->first() === null; 

    }

    /**
     * Recupera o agendamento de acordo com o id 
     * 
     * @param integer|string $id
     * @return Schedule 
     */
    public function getSchedule (int|string $id): Schedule 
    {

        $this->select([

            'schedules.*', 
            'units.name AS unit', 
            'units.address', 
            'services.name AS service'
        ]); 

        $this->join('units', 'units.id = schedules.unit_id'); 
        $this->join('services', 'services.id = schedules.service_id'); 

        return $this->findOrFail($id); 
    }

    /**
     * Recupera os agendamentos não finalizados de acordo com a unidade 
     * 
     * @param integer|string $unitId
     * @param string $dateWanted Exemplo: 2023-11-29
     * @return array horas. Exemplo: ['07:00', '07:30', 'HH:ss', etc]
     */
    public function getScheduledHoursByDate (int|string $unitId, string $dateWanted): array 
    {

        $this->select('DATE_FORMAT(chosen_date, "%H:%i") AS hour');  // terei: 15:10
        $this->where('unit_id', $unitId); 
        $this->where('finished', 0); // agendamentos em aberto 
        $this->where('DATE_FORMAT(chosen_date, "%Y-%m-%d")', $dateWanted); // apenas de acordo com a data desejada 

        $result = $this->findAll(); 

        if (empty($result)) {

            return []; 
        }

        return array_column($result, 'hour'); // getScheduledHoursByDate
    }

    /**
     * Recupera os agendamentos do usuário Logado 
     * 
     * @return array
     */
    public function getLoggedUserSchedules (): array 
    {

        if (!auth()->loggedIn()) {

            return []; 
        }

        $this->select ([

            'schedules.*', 
            'DATE_FORMAT(schedules.chosen_date, "%d/%m/%Y às %H:%i") AS formated_chosen_date', // 23/03/2023 às 15:15
            'units.name AS unit',
            'units.address AS unit',
            'services.name AS service',
        ]); 

        $this->join('units', 'units.id = schedules.unit_id'); 
        $this->join('services', 'services.id = schedules.service_id'); 

        $this->where('schedules.user_id', auth()->user()->id); // do user Logado 

        $this->orderBy('schedules.id', 'DESC'); 

        return $this->findAll(); 
    }
}
