<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Gestion de Usuarios';
?>

<div class="admin-managers">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Flash messages -->
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
    <?php endif; ?>

    <!-- Pestañas para cambiar entre Managers y Revisores de Pagos -->
    <ul class="nav nav-tabs mb-3" id="userTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tab === 'managers' || !$tab ? 'active' : '' ?>" id="managers-tab" data-bs-toggle="tab" data-bs-target="#managers" type="button" role="tab">
                <strong>Managers</strong> <span class="badge bg-secondary"><?= count($managers) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tab === 'payments' ? 'active' : '' ?>" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                <strong>Revisores de Pagos</strong> <span class="badge bg-secondary"><?= count($payments) ?></span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="userTabsContent">
        <!-- TAB MANAGERS -->
        <div class="tab-pane fade <?= $tab === 'managers' || !$tab ? 'show active' : '' ?>" id="managers" role="tabpanel">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($managers)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No hay managers registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($managers as $manager): ?>
                            <tr>
                                <td><?= $manager->id ?></td>
                                <td><?= Html::encode($manager->user_nickname) ?></td>
                                <td><span class="badge bg-primary">Manager</span></td>
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
        </div>

        <!-- TAB REVISORES DE PAGOS -->
        <div class="tab-pane fade <?= $tab === 'payments' ? 'show active' : '' ?>" id="payments" role="tabpanel">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No hay revisores de pagos registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= $payment->id ?></td>
                                <td><?= Html::encode($payment->user_nickname) ?></td>
                                <td><span class="badge bg-warning text-dark">Revisor</span></td>
                                <td>
                                    <?= Html::a('Editar', ['update-payment-manager', 'id' => $payment->id], ['class' => 'btn btn-warning btn-sm']) ?>
                                    <?= Html::a('Eliminar', ['delete-payment-manager', 'id' => $payment->id], [
                                        'class' => 'btn btn-danger btn-sm',
                                        'data' => [
                                            'confirm' => 'Eliminar este revisor de pagos?',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div><!-- fin tab-content -->

    <!-- Botones de creacion (siempre visibles, fuera de las tabs) -->
    <?php if ($action === 'list' || !$action): ?>
        <p class="mt-3">
            <?= Html::a('+ Nuevo Manager', ['create-manager'], ['class' => 'btn btn-success me-2']) ?>
            <?= Html::a('+ Nuevo Revisor de Pagos', ['create-payment-manager'], ['class' => 'btn btn-warning']) ?>
        </p>
    <?php endif; ?>

    <!-- Formulario de creacion / edicion -->
    <?php if ($action === 'create' || $action === 'update'): ?>
        <div class="card mt-4">
            <div class="card-header <?= $type === 'payment' ? 'bg-warning text-dark' : 'bg-primary text-white' ?>">
                <strong>
                    <?php if ($action === 'create'): ?>
                        <?= $type === 'payment' ? 'Nuevo Revisor de Pagos' : 'Nuevo Manager' ?>
                    <?php else: ?>
                        Editar <?= $type === 'payment' ? 'Revisor de Pagos' : 'Manager' ?>: <?= Html::encode($model->user_nickname) ?>
                    <?php endif; ?>
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
                        $action === 'create' ? 'Crear Usuario' : 'Actualizar',
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