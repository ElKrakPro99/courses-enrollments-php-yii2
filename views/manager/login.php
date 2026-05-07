<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Manager - Inicio de Sesión';
?>

<div class="manager-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'user_nickname')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'hash_pass')->passwordInput() ?>
                <div class="form-group">
                    <?= Html::submitButton('Ingresar', ['class' => 'btn btn-success btn-block']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>