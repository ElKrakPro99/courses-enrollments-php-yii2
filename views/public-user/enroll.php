<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Inscripción: ' . $course->course_name;
?>

<div class="public-enroll">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Datos del curso</h5>
                    <p><strong>Docente:</strong> <?= Html::encode($course->teacher_name) ?></p>
                    <p><strong>Período:</strong> <?= $course->date_begin_enrollments ?> al <?= $course->date_end_enrollments ?></p>
                </div>
            </div>

            <?php $form = ActiveForm::begin(); ?>
                <?= Html::hiddenInput('course_id', $course->id) ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'age')->textInput(['type' => 'number']) ?>
                <?= $form->field($model, 'public_entity')->textInput(['maxlength' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Confirmar inscripción', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Cancelar', ['available-courses'], ['class' => 'btn btn-secondary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>