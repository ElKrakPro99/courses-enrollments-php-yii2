<?php

//views/admin/create-course.php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Crear nueva Formacion';
?>

<div class="admin-create-course">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-8">
            <?php $form = ActiveForm::begin(); ?>

            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong>nueva formacion</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <?= $form->field($model, 'course_name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'category')->dropDownList([
                                'curso' => 'Curso',
                                'diplomado' => 'Diplomado',
                                'taller' => 'Taller',
                                'seminario' => 'Seminario',
                            ], ['prompt' => 'Seleccione...']) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'teacher_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'class_days')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Ej: Lunes, Miercoles, Viernes'
                    ]) ?>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <strong>Fechas</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'date_begin_enrollments')->input('date') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'date_end_enrollments')->input('date') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'date_begin_course')->input('date') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'date_end_course')->input('date') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <strong>Modalidad y Pago</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'modality')->dropDownList([
                                'presencial' => 'Presencial',
                                'online' => 'Online',
                                'mixto' => 'Mixto',
                            ], ['prompt' => 'Seleccione...']) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'payment_type')->dropDownList([
                                'libre' => 'Libre',
                                'pago' => 'Pago',
                            ], [
                                'prompt' => 'Seleccione...',
                                'id' => 'payment-type-select'
                            ]) ?>
                        </div>
                        <div class="col-md-4" id="amount-field" style="display: none;">
                            <?= $form->field($model, 'amount')->textInput([
                                'type' => 'number',
                                'step' => '0.01',
                                'min' => '0',
                                'placeholder' => 'Ej: 150.00'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Guardar curso', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Cancelar', ['dashboard'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$script = <<<JS
// Mostrar/ocultar campo monto segun tipo de pago
$('#payment-type-select').on('change', function() {
    if ($(this).val() === 'pago') {
        $('#amount-field').slideDown();
    } else {
        $('#amount-field').slideUp();
    }
});

// Mostrar si ya esta seleccionado pago al cargar
if ($('#payment-type-select').val() === 'pago') {
    $('#amount-field').show();
}
JS;
$this->registerJs($script);
?>