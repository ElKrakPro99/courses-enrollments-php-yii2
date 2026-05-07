<?php

//views/public-user/available.php

use yii\helpers\Html;

$this->title = 'Cursos Disponibles';
?>

<div class="public-available-courses">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (empty($courses)): ?>
        <div class="alert alert-info">
            No hay cursos disponibles para inscripcion en este momento.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <span class="badge bg-light text-dark"><?= Html::encode($course->category) ?></span>
                            <span class="badge bg-light text-dark float-end"><?= Html::encode($course->modality) ?></span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= Html::encode($course->course_name) ?></h5>
                            <p class="card-text">
                                <strong>Docente:</strong> <?= Html::encode($course->teacher_name) ?><br>
                                <strong>Inscripcion:</strong> <?= $course->date_begin_enrollments ?> al <?= $course->date_end_enrollments ?><br>
                                <strong>Clases:</strong> <?= $course->date_begin_course ?> al <?= $course->date_end_course ?><br>
                                <?php if ($course->class_days): ?>
                                    <strong>Dias:</strong> <?= Html::encode($course->class_days) ?><br>
                                <?php endif; ?>
                                <strong>Tipo:</strong> <?= $course->payment_type === 'pago' ? 'Pago' : 'Libre' ?><br>
                                <strong>Inscritos:</strong> <?= $course->enrollments_counter ?>
                            </p>
                            <?= Html::a('Ver detalles', ['course-data', 'id' => $course->id], ['class' => 'btn btn-info']) ?>
                            <?= Html::a('Inscribirse', ['enroll', 'course_id' => $course->id], ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>