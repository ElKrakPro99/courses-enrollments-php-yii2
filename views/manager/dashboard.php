<?php
use yii\helpers\Html;
$this->title = 'Panel de Manager';
?>
<div class="manager-dashboard">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('+ Crear Curso', ['create-course'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Ver Cursos Públicos', ['/cursos'], ['class' => 'btn btn-info', 'target' => '_blank']) ?>
        <?= Html::a('Cerrar Sesión', ['logout'], ['class' => 'btn btn-danger', 'data' => ['method' => 'post']]) ?>
    </p>

    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th><th>Formación</th><th>Categoría</th><th>Modalidad</th>
                <th>Inicio</th><th>Fin</th><th>Docente</th><th>Inscritos</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= $course->id ?></td>
                    <td><?= Html::encode($course->course_name) ?></td>
                    <td><?= Html::encode($course->category) ?></td>
                    <td><?= Html::encode($course->modality) ?></td>
                    <td><?= $course->date_begin_course ?></td>
                    <td><?= $course->date_end_course ?></td>
                    <td><?= Html::encode($course->teacher_name) ?></td>
                    <td><?= $course->enrollments_counter ?></td>
                    <td>
                        <?= Html::a('Métricas', ['metrics', 'id' => $course->id], ['class' => 'btn btn-info btn-sm']) ?>
                        <?= Html::a('Editar', ['update-course', 'id' => $course->id], ['class' => 'btn btn-warning btn-sm']) ?>
                        <!-- Sin botón de eliminar -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>