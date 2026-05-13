<?php

// views/public-users/enroll.php

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
                    <strong>formación</strong>
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
                            <p><strong>Inicio de clases:</strong> <?= $course->date_begin_course ?></p>
                            <p><strong>Fin de clases:</strong> <?= $course->date_end_course ?></p>
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
                    <?php $form = ActiveForm::begin(['id' => 'enrollment-form']); ?>
                        <?= Html::hiddenInput('course_id', $course->id) ?>

                        <!-- PASO 1: Nacionalidad y Cedula -->
                        <div class="card mb-3 border-info">
                            <div class="card-header bg-info text-white">
                                <strong>Paso 1: Identificacion</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'nationality', [
                                            'inputOptions' => ['id' => 'publicusertab-nationality']
                                        ])->dropDownList([
                                            'venezolano' => 'Venezolano',
                                            'extranjero' => 'Extranjero',
                                        ], ['prompt' => 'Seleccione...']) ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'ci', [
                                            'inputOptions' => [
                                                'id' => 'publicusertab-ci',
                                                'type' => 'number',
                                                'maxlength' => 10,
                                                'placeholder' => 'Ej: 12345678'
                                            ]
                                        ])->hint('Sin puntos ni guiones. Al escribir, se buscan sus datos.') ?>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-center">
                                        <div id="ci-status" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PASO 2: Datos personales -->
                        <div class="card mb-3 border-success" id="personal-data-card">
                            <div class="card-header bg-success text-white">
                                <strong>Paso 2: Datos personales</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'name', [
                                            'inputOptions' => ['id' => 'publicusertab-name', 'placeholder' => 'Nombre']
                                        ])->textInput(['maxlength' => true]) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'last_name', [
                                            'inputOptions' => ['id' => 'publicusertab-last_name', 'placeholder' => 'Apellido']
                                        ])->textInput(['maxlength' => true]) ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'email', [
                                            'inputOptions' => ['id' => 'publicusertab-email', 'placeholder' => 'correo@ejemplo.com']
                                        ])->textInput(['maxlength' => true]) ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'phone', [
                                            'inputOptions' => ['id' => 'publicusertab-phone', 'placeholder' => 'Ej: +58 424 1234567']
                                        ])->textInput(['maxlength' => true]) ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'age', [
                                            'inputOptions' => ['id' => 'publicusertab-age', 'type' => 'number', 'min' => 1, 'max' => 120, 'placeholder' => 'Edad']
                                        ])->textInput() ?>
                                    </div>
                                </div>

                                <?= $form->field($model, 'public_entity', [
                                    'inputOptions' => ['id' => 'publicusertab-public_entity', 'placeholder' => 'Ej: Ministerio de Educacion']
                                ])->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>

                        <!-- PASO 3: Comprobante de pago (SOLO si el curso es de pago) -->
                        <?php if ($course->payment_type === 'pago'): ?>
                        <div class="card mb-3 border-warning" id="voucher-card">
                            <div class="card-header bg-warning text-dark">
                                <strong>Paso 3: Comprobante de pago</strong>
                            </div>
                            <div class="card-body">
                                <p>Esta formación requiere pago (<?= $course->getFormattedAmount() ?>). Por favor suba una captura o foto del comprobante.</p>
                                <p class="text-muted small">Formatos aceptados: JPG, PNG, PDF. Tamano maximo: 2MB</p>
                                <?= $form->field($model, 'voucher')->fileInput([
                                    'accept' => 'image/*,.pdf',
                                    'id' => 'voucher-file'
                                ])->label('Comprobante de pago') ?>
                                <div id="voucher-preview" class="mt-2"></div>
                            </div>
                        </div>
                        <?php endif; ?>

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
                    <p>Complete todos los campos requeridos.</p>
                    <p class="text-muted small">Primero seleccione su nacionalidad y escriba su numero de cedula. El sistema buscara sus datos automaticamente si ya esta registrado.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-warning">
                    <strong>Resumen</strong>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>formación:</strong> <?= Html::encode($course->course_name) ?></li>
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

<?php
$script = <<<JS
$('#publicusertab-ci').on('blur', function() {
    var ci = $(this).val();
    var nationality = $('#publicusertab-nationality').val();
    
    if (ci && nationality) {
        $.getJSON('/api/user-by-ci/' + ci, function(response) {
            if (response.success) {
                $('#publicusertab-name').val(response.data.name);
                $('#publicusertab-last_name').val(response.data.last_name);
                $('#publicusertab-email').val(response.data.email);
                $('#publicusertab-phone').val(response.data.phone);
                $('#publicusertab-age').val(response.data.age);
                $('#publicusertab-public_entity').val(response.data.public_entity);
                $('#publicusertab-nationality').val(response.data.nationality);
                $('#ci-status').html('<span class="badge bg-success">Usuario encontrado</span>');
            } else {
                $('#publicusertab-name').val('');
                $('#publicusertab-last_name').val('');
                $('#publicusertab-email').val('');
                $('#publicusertab-phone').val('');
                $('#publicusertab-age').val('');
                $('#publicusertab-public_entity').val('');
                $('#ci-status').html('<span class="badge bg-warning">Nuevo usuario</span>');
            }
        });
    }
});

$('#publicusertab-nationality').on('change', function() {
    $('#publicusertab-ci').trigger('blur');
});

$('#voucher-file').on('change', function() {
    var file = this.files[0];
    if (file && file.type.match('image.*')) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#voucher-preview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 200px;">');
        };
        reader.readAsDataURL(file);
    }
});
JS;
$this->registerJs($script);
?>

