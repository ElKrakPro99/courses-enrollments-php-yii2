//views/admin/admin.php

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Inicio de Sesión - Administración';
?>

<div class="admin-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'user_nickname')
                    ->textInput(['autofocus' => true, 'placeholder' => 'Usuario administrador'])
                    ->label('Usuario') ?>

                <?= $form->field($model, 'hash_pass')
                    ->passwordInput(['placeholder' => 'Contraseña'])
                    ->label('Contraseña') ?>

                <div class="form-group">
                    <?= Html::submitButton('Ingresar al panel', ['class' => 'btn btn-primary btn-block']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>