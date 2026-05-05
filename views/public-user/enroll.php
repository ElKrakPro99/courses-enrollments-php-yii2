<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Inscripción: ' . $course->course_name;
?>

<div class="public-enroll">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Datos del curso</h5>
                    <p><strong>Docente:</strong> <?= Html::encode($course->teacher_name) ?></p>
                    <p><strong>Período:</strong> <?= $course->date_begin_enrollments ?> al <?= $course->date_end_enrollments ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5>Datos personales</h5>
                    
                    <?php $form = ActiveForm::begin(); ?>
                        <?= Html::hiddenInput('course_id', $course->id) ?>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'name')->textInput([
                                    'maxlength' => true, 
                                    'placeholder' => 'Nombre'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'last_name')->textInput([
                                    'maxlength' => true, 
                                    'placeholder' => 'Apellido'
                                ]) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'ci')->textInput([
                                    'type' => 'number',
                                    'maxlength' => 10,
                                    'placeholder' => 'Ej: 12345678'
                                ])->hint('Sin puntos ni guiones') ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'email')->textInput([
                                    'maxlength' => true, 
                                    'placeholder' => 'correo@ejemplo.com'
                                ]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'age')->textInput([
                                    'type' => 'number',
                                    'min' => 1,
                                    'max' => 120,
                                    'placeholder' => 'Edad'
                                ]) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'phone')->textInput([
                                    'maxlength' => true, 
                                    'placeholder' => 'Ej: +58 424 1234567'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'public_entity')->textInput([
                                    'maxlength' => true, 
                                    'placeholder' => 'Ej: Ministerio de Educación'
                                ]) ?>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <?= Html::submitButton('Confirmar inscripción', [
                                'class' => 'btn btn-success btn-lg'
                            ]) ?>
                            <?= Html::a('Cancelar', ['available-courses'], [
                                'class' => 'btn btn-secondary btn-lg'
                            ]) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <strong>Información</strong>
                </div>
                <div class="card-body">
                    <p><small>Complete todos los campos requeridos. La cédula de identidad es única y nos permite identificarlo en futuras inscripciones.</small></p>
                </div>
            </div>
        </div>
    </div>
</div>