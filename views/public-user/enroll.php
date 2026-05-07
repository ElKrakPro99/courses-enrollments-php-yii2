<?php

# //views/public-user/enroll.php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Inscripcion: ' . $course->course_name;
?>

<div class="public-enroll">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-8">
            <!-- Datos del curso -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong>Datos del curso</strong>
                </div>
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
                            <p><strong>Inscripciones:</strong><br>
                                <?= $course->date_begin_enrollments ?> al <?= $course->date_end_enrollments ?>
                            </p>
                            <p><strong>Clases:</strong><br>
                                <?= $course->date_begin_course ?> al <?= $course->date_end_course ?>
                            </p>
                            <p><strong>Inscritos:</strong> <?= $course->enrollments_counter ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de inscripcion -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <strong>Datos personales</strong>
                </div>
                <div class="card-body">
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
                                    'placeholder' => 'Ej: Ministerio de Educacion'
                                ]) ?>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <?= Html::submitButton('Confirmar inscripcion', [
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

        <!-- Panel informativo -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <strong>Informacion</strong>
                </div>
                <div class="card-body">
                    <p>Complete todos los campos requeridos. La cedula de identidad es unica y permite identificarlo en futuras inscripciones.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-warning">
                    <strong>Resumen del curso</strong>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>Curso:</strong> <?= Html::encode($course->course_name) ?></li>
                        <li><strong>Categoria:</strong> <?= Html::encode($course->category) ?></li>
                        <li><strong>Modalidad:</strong> <?= Html::encode($course->modality) ?></li>
                        <li><strong>Docente:</strong> <?= Html::encode($course->teacher_name) ?></li>
                        <?php if ($course->class_days): ?>
                            <li><strong>Dias:</strong> <?= Html::encode($course->class_days) ?></li>
                        <?php endif; ?>
                        <li><strong>Inicio clases:</strong> <?= $course->date_begin_course ?></li>
                        <li><strong>Fin clases:</strong> <?= $course->date_end_course ?></li>
                        <li><strong>Tipo:</strong> <?= $course->payment_type === 'pago' ? 'Pago' : 'Libre' ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>