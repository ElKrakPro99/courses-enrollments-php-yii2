<?php

use yii\helpers\Html;

$this->title = 'Usuarios Publicos';
?>

<div class="admin-public-users">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>CI</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th>Edad</th>
                    <th>Entidad</th>
                    <th>Cursos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="9" class="text-center">No hay usuarios registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user->id ?></td>
                            <td><?= $user->ci ?: 'N/A' ?></td>
                            <td><?= Html::encode($user->getFullName()) ?></td>
                            <td><?= Html::mailto($user->email) ?></td>
                            <td><?= $user->phone ?: 'N/A' ?></td>
                            <td class="text-center"><?= $user->age ?: '-' ?></td>
                            <td><?= Html::encode($user->public_entity) ?: 'N/A' ?></td>
                            <td class="text-center"><?= $user->n_courses_enrollment ?></td>
                            <td>
                                <?= Html::a('Eliminar', ['delete-public-user', 'id' => $user->id], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data' => [
                                        'confirm' => 'Estas seguro de eliminar este usuario y TODAS sus inscripciones?',
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

    <p class="mt-3">
        <?= Html::a('Volver al Dashboard', ['dashboard'], ['class' => 'btn btn-secondary']) ?>
    </p>
</div>