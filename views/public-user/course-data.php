//views/public-user/course-data.php

<?php

use yii\helpers\Html;

$this->title = $course->course_name;
?>

<div class="public-course-data">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-body">
            <p><strong>Docente:</strong> <?= Html::encode($course->teacher_name) ?></p>
            <p><strong>Período de inscripción:</strong> 
                <?= $course->date_begin_enrollments ?> al <?= $course->date_end_enrollments ?>
            </p>
            <p><strong>Total inscritos:</strong> <?= $course->enrollments_counter ?></p>
            
            <?= Html::a('Inscribirse ahora', ['enroll', 'course_id' => $course->id], ['class' => 'btn btn-success btn-lg']) ?>
            <?= Html::a('Volver a cursos', ['available-courses'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>
</div>