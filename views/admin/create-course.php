//views/admin/create-course.php

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Crear Nuevo Curso';
?>

<div class="admin-create-course">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'course_name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'teacher_name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'date_begin_enrollments')->input('date') ?>

                <?= $form->field($model, 'date_end_enrollments')->input('date') ?>

                <div class="form-group">
                    <?= Html::submitButton('Guardar curso', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Cancelar', ['dashboard'], ['class' => 'btn btn-secondary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>