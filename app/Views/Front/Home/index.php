<?php echo $this->extend('Front/Layout/main'); ?>

<?php echo $this->section('title'); ?>

<?php echo $title ?? 'Home'; ?>

<?php echo $this->endSection(); ?>


<?php echo $this->section('css'); ?>



<?php echo $this->endSection(); ?>


<?php echo $this->section('content'); ?>

<div class="container pt-5 text-center">
    <h1 class="mt-5">Veja como é facil criar o seu agendamento</h1>

    <div class="row mt-4">

        <div class="col">

            <div class="card">
                <div class="card-header">
                    Primeiro
                </div>

                <div class="card-body">
                    <h5 class="card-title">Autentique-se</h5>
                    <p>Realize o Login ou Crie sua Conta</p>
                </div>
            </div>
        </div>

        <div class="col">

            <div class="card">
                <div class="card-header">
                    Segundo
                </div>

                <div class="card-body">
                    <h5 class="card-title">Escolha Unidade</h5>
                    <p>Onde você gostaria de ser atendido</p>
                </div>
            </div>
        </div>

        <div class="col">

            <div class="card">
                <div class="card-header">
                    Terceiro
                </div>

                <div class="card-body">
                    <h5 class="card-title">Escolha o Serviço</h5>
                    <p>Serviço que deseja atendimento</p>
                </div>
            </div>
        </div>
    
        <div class="col">

            <div class="card">
                <div class="card-header">
                    Quarto
                </div>

                <div class="card-body">
                    <h5 class="card-title">Escolha a data</h5>
                    <p>Escolha a melhro data e horario</p>
                </div>
            </div>
        </div>

        <div class="col">

            <div class="card">
                <div class="card-header">
                    Pronto
                </div>

                <div class="card-body">
                    <h5 class="card-title">Confirmação</h5>
                    <p>Revise os dados e crie o agendamento</p>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-4">

        <div class="col-m-12">

            <a href="<?php echo route_to('schedules.new'); ?>" class="btn btn-lg btn-primary">Criar agendamento</a>
        </div>
    </div>

</div>

<?php echo $this->endSection(); ?>


<?php echo $this->section('js'); ?>



<?php echo $this->endSection(); ?>