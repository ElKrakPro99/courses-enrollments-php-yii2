//views/admin/dashboard.php

<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Panel de Administración';
?>

<div class="admin-dashboard">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <?= Html::a('+ Crear Nuevo Curso', ['create-course'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Ver Cursos Públicos', ['/cursos'], ['class' => 'btn btn-info', 'target' => '_blank']) ?>
            <?= Html::a('Cerrar Sesión', ['logout'], [
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
                    <th>Curso</th>
                    <th>Inicio inscripciones</th>
                    <th>Fin inscripciones</th>
                    <th>Docente</th>
                    <th>Inscritos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay cursos registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?= $course->id ?></td>
                            <td><?= Html::encode($course->course_name) ?></td>
                            <td><?= $course->date_begin_enrollments ?></td>
                            <td><?= $course->date_end_enrollments ?></td>
                            <td><?= Html::encode($course->teacher_name) ?></td>
                            <td class="text-center"><?= $course->enrollments_counter ?></td>
                            <td>
                                <?= Html::a('Métricas', ['metrics', 'id' => $course->id], ['class' => 'btn btn-info btn-sm']) ?>
                                <?= Html::a('Editar', ['update-course', 'id' => $course->id], ['class' => 'btn btn-warning btn-sm']) ?>
                                <?= Html::a('Eliminar', ['delete-course', 'id' => $course->id], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data'  => [
                                        'confirm' => '¿Estás seguro de eliminar este curso y todas sus inscripciones?',
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