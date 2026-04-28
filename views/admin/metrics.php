<?php

use yii\helpers\Html;

$this->title = 'Métricas: ' . $course->course_name;
?>

<div class="admin-metrics">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total de inscritos</h5>
                    <p class="card-text display-4"><?= $count ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Docente</h5>
                    <p class="card-text"><?= Html::encode($course->teacher_name) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Período de inscripción</h5>
                    <p class="card-text">
                        <?= $course->date_begin_enrollments ?> al <?= $course->date_end_enrollments ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <h3>Lista de inscripciones</h3>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Entidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enrollments)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No hay inscripciones registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($enrollments as $enrollment): ?>
                        <tr>
                            <td><?= $enrollment->id ?></td>
                            <td>
                                <?= $enrollment->publicUser ? Html::encode($enrollment->publicUser->getFullName()) : 'N/A' ?>
                            </td>
                            <td>
                                <?= $enrollment->publicUser ? $enrollment->publicUser->email : 'N/A' ?>
                            </td>
                            <td>
                                <?= $enrollment->publicUser ? $enrollment->publicUser->phone : 'N/A' ?>
                            </td>
                            <td>
                                <?= $enrollment->publicUser ? Html::encode($enrollment->publicUser->public_entity) : 'N/A' ?>
                            </td>
                            <td>
                                <?= Html::a('Eliminar', ['delete-enrollment', 'id' => $enrollment->id], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data'  => [
                                        'confirm' => '¿Eliminar esta inscripción?',
                                        'method'  => 'post',
                                    ],
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p>
        <?= Html::a('Volver al dashboard', ['dashboard'], ['class' => 'btn btn-secondary']) ?>
    </p>
</div>