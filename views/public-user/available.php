//views/public-user/available.php

<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Cursos Disponibles';
?>

<div class="public-available-courses">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (empty($courses)): ?>
        <div class="alert alert-info">
            No hay cursos disponibles para inscripción en este momento.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= Html::encode($course->course_name) ?></h5>
                            <p class="card-text">
                                <strong>Docente:</strong> <?= Html::encode($course->teacher_name) ?><br>
                                <strong>Período:</strong> <?= $course->date_begin_enrollments ?> al <?= $course->date_end_enrollments ?><br>
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