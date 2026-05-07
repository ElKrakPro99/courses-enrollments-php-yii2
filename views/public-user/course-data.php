<?php

//views/public-user/course-data.php

use yii\helpers\Html;

$this->title = $course->course_name;
?>

<div class="public-course-data">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Categoria:</strong> <?= Html::encode($course->category) ?></p>
                    <p><strong>Docente:</strong> <?= Html::encode($course->teacher_name) ?></p>
                    <p><strong>Modalidad:</strong> <?= Html::encode($course->modality) ?></p>
                    <p><strong>Tipo:</strong> <?= $course->payment_type === 'pago' ? 'Pago' : 'Libre' ?></p>
                    <?php if ($course->class_days): ?>
                        <p><strong>Dias de clase:</strong> <?= Html::encode($course->class_days) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <p><strong>Inscripciones:</strong> <?= $course->date_begin_enrollments ?> al <?= $course->date_end_enrollments ?></p>
                    <p><strong>Clases:</strong> <?= $course->date_begin_course ?> al <?= $course->date_end_course ?></p>
                    <p><strong>Total inscritos:</strong> <?= $course->enrollments_counter ?></p>
                </div>
            </div>
            
            <?= Html::a('Inscribirse ahora', ['enroll', 'course_id' => $course->id], ['class' => 'btn btn-success btn-lg']) ?>
            <?= Html::a('Volver a cursos', ['available-courses'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>
</div>