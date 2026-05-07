<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Gestion de Managers';
?>

<div class="admin-managers">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Flash messages -->
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>

    <!-- Tabla de managers -->
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($managers)): ?>
                <tr>
                    <td colspan="3" class="text-center">No hay managers registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($managers as $manager): ?>
                    <tr>
                        <td><?= $manager->id ?></td>
                        <td><?= Html::encode($manager->user_nickname) ?></td>
                        <td>
                            <?= Html::a('Editar', ['update-manager', 'id' => $manager->id], ['class' => 'btn btn-warning btn-sm']) ?>
                            <?= Html::a('Eliminar', ['delete-manager', 'id' => $manager->id], [
                                'class' => 'btn btn-danger btn-sm',
                                'data' => [
                                    'confirm' => 'Eliminar este manager?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Boton para crear (solo si no estamos editando) -->
    <?php if ($action === 'list'): ?>
        <p>
            <?= Html::a('+ Nuevo Manager', ['create-manager'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <!-- Formulario de creacion / edicion -->
    <?php if ($action === 'create' || $action === 'update'): ?>
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <strong>
                    <?= $action === 'create' ? 'Nuevo Manager' : 'Editar Manager: ' . Html::encode($model->user_nickname) ?>
                </strong>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'user_nickname')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'hash_pass')->passwordInput()->hint(
                    $action === 'update' ? 'Dejar vacio para no cambiar la contrasena' : ''
                ) ?>

                <div class="form-group">
                    <?= Html::submitButton(
                        $action === 'create' ? 'Crear Manager' : 'Actualizar',
                        ['class' => 'btn btn-primary']
                    ) ?>
                    <?= Html::a('Cancelar', ['managers'], ['class' => 'btn btn-secondary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Volver al dashboard -->
    <p class="mt-3">
        <?= Html::a('Volver al Dashboard', ['dashboard'], ['class' => 'btn btn-secondary']) ?>
    </p>
</div>