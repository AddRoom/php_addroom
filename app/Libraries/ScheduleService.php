<?php

namespace App\Libraries;

use App\Entities\Schedule;
use App\Models\ScheduleModel;
use App\Models\ServiceModel;
use App\Models\UnitModel;
use CodeIgniter\I18n\Time;
use InvalidArgumentException;
use CodeIgniter\Events\Events;

class ScheduleService  
{

    /**
     * Renderiza a lista com as opções de unidades ativas e que possuam serviços associados para serem escolhidos para serem escolhidas no agendamento
     * 
     * @param array|null $existingServiceIds
     * @return string
     */
    public function renderUnits (): string
    {
        // uniades ativas e com serviços associados
        $where = [
            'active'      => 1, 
            'services !=' => null,
            'services !=' => '',
        ];

        $units = model(UnitModel::class)->where($where)->orderBy('name', 'ASC')->findAll();

        if (empty($units)) {
            
            return '<div class="text-info mt-4">Não há unidades disponíveis para agendamentos!</div>';;
        }
        
        // valor padrão
        $radios = '';

        foreach ($units as $unit) {
 
            $radios .= '<div class="form-check mb-2">';
            $radios .= "<input type='radio' name='unit_id' data-unit='{$unit->name} \nEndereço: {$unit->address}' value='{$unit->id}' class='custom-form-check-input' id='radio-{$unit->id}'>";
            $radios .= "<label class='form-check-label' for='radio-{$unit->id}'>{$unit->name}<br>  Endereço:{$unit->address}</label>";
            $radios .= '</div>';
        }

        // retorna as opções 
        return $radios;
    }

    /**
     * recupera os serviços associados a unidade informada como um dropdown HMTL
     * 
     * 
     */
    public function renderUnitServices (int $unitId): string 
    {
        // validamos a existencia da  unidade, ativa, com serviços
        $unit = model(UnitModel::class)->where(['active' => 1, 'services !=' => null, 'services !=' => ''])->findOrFail($unitId);

        // buscamos o serviço dessa unidade 
        $services = model(ServiceModel::class)->whereIn('id', $unit->services)->where('active', 1)->orderBy('name', 'ASC')->findAll(); 

        if (empty($services)) {

            throw new InvalidArgumentException("Os serviços associados á Unidade {$unit->name} não estão ativos ou não existem");
        }

        $options = [];
        $options[null] = '--- Escolha ---';

        foreach ($services as $service) {
            
            $options[$service->id] = $service->name; 
        }

        return form_dropdown(data: 'service', options: $options, selected: [], extra: ['id' => 'service_id', 'class' => 'form-select']); 
    }

    public function createSchedule (array $request): bool|string
    {

        try {
            
            $model = model(ScheduleModel::class); 

            $request = (object) $request; 

            $currentYear = Time::now()->getYear(); 

            // terei algo assim: 2023-07-17 15:15
            $chosenDate = "{$currentYear}-{$request->month}-{$request->day} {$request->hour}"; 

            if (!$model->chosenDateIsFree(unitId: $request->unit_id, chosenDate: $chosenDate)) {

                return "A data escolhida não está mais disponiveis"; 
            }

            $schedule = new Schedule([

                'unit_id'     => $request->unit_id, 
                'service_id'  => $request->service_id, 
                'chosen_date' => $chosenDate
            ]);

            // conseguimos criar o agendamento? 
            if (!$createdId = $model->insert($schedule)) {

                log_message('error', 'Erro ao criar agendamento', $model->errors()); 

                return "Não foi possivel criar o agendamento"; 
            }

            /**
            * disparar email para o usuario com os dados do agendamento criado
            */
            Events::trigger('schedule_created', auth()->user()->email, $model->getSchedule(id: $createdId)); 

            // retornamos true 
            return true; 
        } catch (\Throwable $th) {
            
            log_message('error', '[ERROR] {exception}', ['exception' => $th]); 

            return "Internal Server Error";
        }
    }
}
