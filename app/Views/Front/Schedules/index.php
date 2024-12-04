<?php echo $this->extend('Front/Layout/main'); ?>

<?php echo $this->section('title'); ?>

<?php echo $title ?? 'Home'; ?>

<?php echo $this->endSection(); ?>


<?php echo $this->section('css'); ?>

<style>

    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=League+Gothic&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap');

    body {

        font-family: "Inter", sans-serif;;
    }

    .small {

        font-weight: bold;
    }

    .btn-calendar-day {
        max-width: 36px !important;
        min-width: 36px !important;
        line-height: 0px !important;
        padding: 10% !important;
        height: 30px !important;
        display: table-cell !important;
        vertical-align: middle !important;
    }

    .btn-calendar-day-chosen {
        color: #fff !important;
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

    .btn-hour {
        margin-bottom: 10px !important;
        max-width: 55px !important;
        min-width: 55px !important;
        padding-left: 8px !important;
        line-height: 0px !important;
        height: 30px !important;
    }

    .btn-hour-chosen {
        color: #fff !important;
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

    /** para centralizar o conteúdo dentro da célula do calendário */
    td {
        text-align: center;
        vertical-align: middle;
    }

    /** para aparecer os options dos dropdowns */
    .wizard .content .form-control {
        padding: .375rem 0.75rem !important;
    }
</style>

<?php echo $this->endSection(); ?>


<?php echo $this->section('content'); ?>

<div class="container pt-5">

    <h1 class="mt-5">Criar Reserva:</h1>

    <div class="row">

        <div class="col-md-8">

            <div class="mt-3">

                <?php if (session()->has('success')) : ?>

                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo session('success'); ?>
                        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"></span>
                        </button>
                    </div>

                <?php endif; ?>
            </div>

            <div class="row">

                <!-- unidades -->
                <div class="col-md-12 mb-4">

                    <p class="lead">Escolha uma unidade:</p>

                    <?php echo $units; ?>
                </div>

                <!-- Serviços da uniddade (oculto no Load da view) -->
                <div id="mainBoxServices" class="col-md-12 d-none mb-4">

                    <p class="lead">Escolha a sala</p>

                    <div id="boxServices">

                    </div>
                </div>

                <!-- Mês (oculto no Load da view) -->
                <div id="boxMonths" class="col-md-12 d-none mb-4">

                    <p class="lead">Escolha o mês</p>

                    <?php echo $months ?>

                </div>

                <div id="mainBoxCalendar" class="col-md-12 d-none mb-4">

                    <p class="lead">Escolha o dia e o horário</p>

                    <div class="row">

                        <div class="col-md-6 form-group">

                            <div id="boxCalendar">

                            </div>
                        </div>

                        <div class="col-md-6 form-group">

                            <div id="boxHours">

                            </div>
                        </div>
                    </div>
                </div>

                <div id="boxErros" class="mt-4 mb-3">

                </div>

                <div class="col-md-12 border-top">

                    <br>

                    <button id="btnTryCreate" class="btn btn-primary">Criar minha reserva</button>
                </div>

            </div>
        </div>

        <!-- preview do que for sendo escolhido -->
        <div class="col-md-2 ms-auto">

            <p class="lead mt-4">Unidade escolhida: <br><span id="chosenUnitText" class="text-muted small"></span></p>
            <p class="lead">Sala escolhida: <br><span id="chosenServiceText" class="text-muted small"></span></p>
            <p class="lead">Mês escolhido: <br><span id="chosenMonthText" class="text-muted small"></span></p>
            <p class="lead">Dia escolhido: <br><span id="chosenDayText" class="text-muted small"></span></p>
            <p class="lead">Horario escolhido: <br><span id="chosenHourText" class="text-muted small"></span></p>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?>


<?php echo $this->section('js'); ?>

<script>
    const URL_GET_SERVICES = '<?php echo route_to('get.unit.services'); ?>';
    const URL_GET_CALENDAR = '<?php echo route_to('get.calendar'); ?>';
    const URL_GET_HOURS = '<?php echo route_to('get.hours'); ?>';
    const URL_CREATION_SCHEDULE = '<?php echo route_to('create.schedule'); ?>';

    const boxErros = document.getElementById('boxErros');

    const mainBoxServices = document.getElementById('mainBoxServices');
    const boxServices = document.getElementById('boxServices');
    const boxMonths = document.getElementById('boxMonths');
    const mainBoxCalendar = document.getElementById('mainBoxCalendar');
    const boxCalendar = document.getElementById('boxCalendar');
    const boxHours = document.getElementById('boxHours');
    const btnTryCreate = document.getElementById('btnTryCreate');

    // preview do que esá sendo excolhido
    const chosenUnitText = document.getElementById('chosenUnitText');
    const chosenServiceText = document.getElementById('chosenServiceText');
    const chosenMonthText = document.getElementById('chosenMonthText');
    const chosenDayText = document.getElementById('chosenDayText');
    const chosenHourText = document.getElementById('chosenHourText');

    // variaveis de  escopo global que utilizaremos na criação do agendamento
    let unitId = null;
    let serviceId = null;
    let chosenMonth = null;
    let chosenDay = null;
    let chosenHour = null;

    // CSRF CODE PARA ENVIAR NO REQUEST 
    let csrfTokenName = '<?php echo csrf_token(); ?>'
    let csrfTokenValue = '<?php echo csrf_hash(); ?>'

    const units = document.getElementsByName('unit_id');

    units.forEach(element => {

        // adicionar para cada elemento um 'listener' ou ouvinte
        element.addEventListener('click', (event) => {

            mainBoxServices.classList.remove('d-none');

            // redefinimos as opções dos meses
            resetMonthOptions();

            // redefino o calendario 
            resetBoxCalendar();

            // atribuindo a varivael global o valor da unidade clicada 
            unitId = element.value;

            if (!unitId) {

                alert('Erro ao determinar a Unidade escolhida');
                return;
            }

            chosenUnitText.innerText = element.getAttribute('data-unit');
            chosenServiceText.innerText = '';
            chosenMonthText.innerText = '';
            chosenDayText.innerText = '';
            chosenHourText.innerText = '';

            getServices();

        });
    });

    // recupera serviços da unidade
    const getServices = async () => {

        // BOX ERRORS CRIAR DEPOIS 
        boxErros.innerHTML = '';

        let url = URL_GET_SERVICES + '?' + setParameters({

            unit_id: unitId
        });

        const response = await fetch(url, {

            method: 'get',
            headers: setHeadersRequest()
        });

        if (!response.ok) {

            boxErrors.innerHTML = showErrorMessage('Não foi possivel recuperar os Serviços');

            throw new Error(`HTTP error! Status: ${response.status}`);

            return;
        }

        const data = await response.json();

        // colocamos na div os serviços devolvidos no response
        boxServices.innerHTML = data.services;

        const elementService = document.getElementById('service_id');

        elementService.addEventListener('change', (event) => {

            serviceId = elementService.value ?? null;
            let serviceName = serviceId !== '' ? elementService.options[event.target.selectedIndex].text : null;

            chosenServiceText.innerText = serviceName;

            serviceId !== '' ? boxMonths.classList.remove('d-none') : boxMonths.classList.add('d-none');
        });
    }

    // mês 
    document.getElementById('month').addEventListener('change', (event) => {

        // limpa o preview do mês escolhido a cada mudança
        chosenMonthText.innerText = '';

        resetBoxCalendar();

        const month = event.target.value;

        if (!month) {

            resetMonthDataVariables();

            resetBoxCalendar();

            return;
        }

        // mês  valido escolhido ...

        // atribuimos a variavel de escopo global o valor do mês escolhido 
        chosenMonth = event.target.value;

        chosenMonthText.innerText = event.target.options[event.target.selectedIndex].text;

        // finalmente buscamos o calendario para o mês escolhido
        getCalendar();
    });

    btnTryCreate.addEventListener('click', (event) => {

        event.preventDefault();

        boxErros.innerHTML = '';

        // unidade foi escolhida? 
        if (unitId === null || unitId === '') {

            boxErros.innerHTML = showErrorMessage('Escolha a Unidade');
            return;
        }

        // serviço foi escolhida? 
        if (serviceId === null || serviceId === '') {

            boxErros.innerHTML = showErrorMessage('Escolha o Serviço');
            return;
        }

        // verificamos se os campos referente ao mês, dia e hora estão devidamente preenchidos 
        const dataFieldsAreFilled = (chosenMonth !== null && chosenDay !== null && chosenHour !== null);

        if (!dataFieldsAreFilled) {

            boxErros.innerHTML = showErrorMessage('Escolha o Mês, Dia e Hora para prosseguir');
            return;
        }

        // desabilitamos o botão 
        btnTryCreate.disabled = true;
        btnTryCreate.innerText = 'Estamos criando o seu agendamento...';

        // agora podemos criar seu agendamento
        tryCreateSchedule();
    })

    // ------------------------FUNÇÕES---------------------------------

    // tenta criar o agendamento 
    const tryCreateSchedule = async () => {

        boxErros.innerHTML = '';

        // o que será enviado no request 
        const body = {

            unit_id: parseInt(unitId),
            service_id: parseInt(serviceId),
            month: chosenMonth,
            day: chosenDay,
            hour: chosenHour
        }

        body[csrfTokenName] = csrfTokenValue;

        const response = await fetch(URL_CREATION_SCHEDULE, {

            method: 'post',
            headers: setHeadersRequest(),
            body: JSON.stringify(body)
        });

        if (!response.ok) {

            // temos erros de validação (status code = 400)?
            if (response.status === 400) {

                // habilito o botão para nova tentativa 
                btnTryCreate.disabled = false;
                btnTryCreate.innerText = 'Criar meu agendamento';

                const data = await response.json();
                const errors = data.errors;

                // atualizo o token do CSRF
                csrfTokenValue = data.token;

                // tranformo o array de erros em uma string 
                let message = Object.keys(errors).map(field => errors[field]).join(', ');

                boxErros.innerHTML = showErrorMessage(message);

                return;
            }

            // erro difirente de 400

            boxErros.innerHTML = showErrorMessage('Não foi possivel criar seu agendamento');

            throw new Error(`HTTP error! Status: ${response.status}`);

            return;
        }

        // tudo certo... agendamento criado

        // retornamos para a mesma viu para exibir a mensagem de sucesso
        window.location.href = window.location.href;

    }

    // calendario 
    const getCalendar = async () => {

        // limpa os erros
        boxErros.innerHTML = '';

        // limpa o preview do dia e da hora escolhida, pois o user precisará clicar no hórario novamente
        chosenDayText.innerText = '';
        chosenHourText.innerText = '';

        let url = URL_GET_CALENDAR + '?' + setParameters({

            month: chosenMonth
        });

        const response = await fetch(url, {

            method: 'get',
            headers: setHeadersRequest()
        });

        if (!response.ok) {

            boxErros.innerHTML = showErrorMessage('Não foi possivel recuperar o calemdario para o mês informado');

            throw new Error(`HTTP error! Status: ${response.status}`);

            return;
        }

        // recuperamos a resposta 
        const data = await response.json();

        // exibi a div do calendario e das horas 
        mainBoxCalendar.classList.remove('d-none');

        // colocamos na div do calendario 
        boxCalendar.innerHTML = data.calendar;

        // agora recupera os elementos que tenham a classe '.chosenDay', 
        // ou seja os dias que são buttons 
        const buttonsChosenDay = document.querySelectorAll('.chosenDay');

        // percorre todos os botões 
        buttonsChosenDay.forEach(element => {

            // e fico 'escutando' o click no elemento 
            // e para cada click recupera o valor de 'data-day'
            element.addEventListener('click', (event) => {

                // limpo o preview da hora 
                chosenHourText.innerText = '';

                // mensage
                boxHours.innerHTML = '<span class="text-info">Carregando as horas...</span>'

                // redefino para null para garantir 
                chosenHour = null;

                // antes precisamos remover 
                removeClassFromElements(buttonsChosenDay, 'btn-calendar-day-chosen');

                // adiciona a classe no elemento
                event.target.classList.add('btn-calendar-day-chosen');

                // armazena no variável global
                chosenDay = event.target.dataset.day;

                // dia escolhido no preview 
                chosenDayText.innerText = chosenDay;

                getHours();
            })

        });

    }

    const getHours = async () => {

        boxErros.innerHTML = '';

        if (!unitId) {

            boxErros.innerHTML = showErrorMessage('Você precisa escolher a Unidade de atendimento');
            return;
        }

        let url = URL_GET_HOURS + '?' + setParameters({

            unit_id: unitId,
            month: chosenMonth,
            day: chosenDay
        });

        const response = await fetch(url, {

            method: 'get',
            headers: setHeadersRequest()
        });

        if (!response.ok) {

            boxErros.innerHTML = showErrorMessage('Não foi possivel recuperar os horarios disponiveis');

            throw new Error(`HTTP error! Status: ${response.status}`);

            return;
        }

        // recuperamos a resposta 
        const data = await response.json();

        // recupero as horas 
        const hours = data.hours;

        if (hours === null) {

            boxHours.innerHTML = showErrorMessage(`Não há horarios disponiveis para o dia ${chosenDay}`);

            chosenDay = null;

            return;
        }

        // colocamos na div as horas 
        boxHours.innerHTML = hours;

        // agora recupera os elementos que tenham a classe '.btn-hour',
        // ou seja, os buttons dos horarios
        const buttonsBtnHour = document.querySelectorAll('.btn-hour');

        // percorro eles 
        buttonsBtnHour.forEach(element => {

            element.addEventListener('click', (event) => {

                // removo a classe antes 
                removeClassFromElements(buttonsBtnHour, 'btn-hour-chosen');

                // e agora adiciono so no elemento clicado
                event.target.classList.add('btn-hour-chosen');

                chosenHour = event.target.dataset.hour;

                // preview da hora escolhida 
                chosenHourText.innerText = chosenHour;
            });
        });
    }

    // redefinir as opções dos meses 
    const resetMonthOptions = () => {

        // oculatamos a div dos meses
        boxMonths.classList.add('d-none');

        // volta para a opção '--- Escolha ---'
        document.getElementById('month').selectedIndex = 0;

        // nulamos desses campos 
        resetMonthDataVariables();
    }

    // Redefini as variaveis pertinentes ao mês, dia, hora
    const resetMonthDataVariables = () => {

        chosenMonth = null;
        chosenDay = null;
        chosenHour = null;
    }

    // Redefini o calendario
    const resetBoxCalendar = () => {

        mainBoxCalendar.classList.add('d-none');

        boxCalendar.innerHTML = '';
        boxHours.innerHTML = '';
    }

    // remove a classe array dos elementos 
    const removeClassFromElements = (elements, className) => {

        elements.forEach(element => {

            if (element.classList.contains(className)) {

                element.classList.remove(className);
            }
        });
    }
</script>

<?php echo $this->endSection(); ?>