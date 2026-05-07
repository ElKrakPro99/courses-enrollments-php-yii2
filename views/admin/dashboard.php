<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Panel de Administracion';
?>

<div class="admin-dashboard">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <?= Html::a('+ Crear Nueva Formacion', ['create-course'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Administrar Managers', ['managers'], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Ver Cursos Publicos', ['/cursos'], ['class' => 'btn btn-info', 'target' => '_blank']) ?>
            <?= Html::a('Cerrar Sesion', ['logout'], [
                'class' => 'btn btn-danger',
                'data'  => ['method' => 'post'],
            ]) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Formacion</th>
                    <th>Categoria</th>
                    <th>Modalidad</th>
                    <th>Inicio Formacion</th>
                    <th>Fin Formacion</th>
                    <th>Docente</th>
                    <th>Inscritos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="9" class="text-center">No hay cursos registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?= $course->id ?></td>
                            <td><?= Html::encode($course->course_name) ?></td>
                            <td><span class="badge bg-secondary"><?= Html::encode($course->category) ?></span></td>
                            <td><span class="badge bg-info"><?= Html::encode($course->modality) ?></span></td>
                            <td><?= $course->date_begin_course ?></td>
                            <td><?= $course->date_end_course ?></td>
                            <td><?= Html::encode($course->teacher_name) ?></td>
                            <td class="text-center"><?= $course->enrollments_counter ?></td>
                            <td>
                                <?= Html::a('Metricas', ['metrics', 'id' => $course->id], ['class' => 'btn btn-info btn-sm']) ?>
                                <?= Html::a('Editar', ['update-course', 'id' => $course->id], ['class' => 'btn btn-warning btn-sm']) ?>
                                <?= Html::a('Eliminar', ['delete-course', 'id' => $course->id], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data'  => [
                                        'confirm' => 'Estas seguro de eliminar este curso y todas sus inscripciones?',
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
</div>