<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Pagos - Inicio de Sesion';
?>
<div class="payment-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row"><div class="col-md-4">
        <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'user_nickname')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'hash_pass')->passwordInput() ?>
            <?= Html::submitButton('Ingresar', ['class' => 'btn btn-warning btn-block']) ?>
        <?php ActiveForm::end(); ?>
    </div></div>
</div>