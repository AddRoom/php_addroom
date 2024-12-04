<?php echo $this->extend('Front/Layout/main'); ?>

<?php echo $this->section('title'); ?>

<?php echo $title ?? 'Home'; ?>

<?php echo $this->endSection(); ?>


<?php echo $this->section('css'); ?>

<style>

    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=League+Gothic&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap');

    body {

        font-family: "Inter", sans-serif;
    }

    .excluir {

        display: none;
    }

    .container {

        margin-top: 70px;
    }

    .col {

        border: 2px solid #f5f5f5;
    }
</style>

<?php echo $this->endSection(); ?>


<?php echo $this->section('content'); ?>

<div class="container pt-5 text-center">
    <h1 class="mt-5">Veja como é facil criar sua reserva</h1>

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
                    <h5 class="card-title">Escolha uma Escola</h5>
                    <p>Escola que Estuda</p>
                </div>
            </div>
        </div>

        <div class="col">

            <div class="card">
                <div class="card-header">
                    Terceiro
                </div>

                <div class="card-body">
                    <h5 class="card-title">Escolha a Sala</h5>
                    <p>Sala que Quer reservar</p>
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
                    <p>Escolha a melhor data e horario</p>
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

            <a href="<?php echo route_to('schedules.new'); ?>" class="btn cor btn-lg btn-primary">Criar reserva</a>
        </div>
    </div>

</div>

<?php echo $this->endSection(); ?>


<?php echo $this->section('js'); ?>



<?php echo $this->endSection(); ?>