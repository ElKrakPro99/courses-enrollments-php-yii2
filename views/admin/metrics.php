<?php

use yii\helpers\Html;

$this->title = 'Métricas: ' . $course->course_name;
?>

<div class="admin-metrics">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Filtros por estado (opcional, si agregaste el campo status) -->
    <div class="mb-3">
        <div class="btn-group" role="group">
            <?= Html::a('Todos', ['metrics', 'id' => $course->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
            <?= Html::a('Pendientes', ['metrics', 'id' => $course->id, 'status' => 'pending'], ['class' => 'btn btn-outline-warning btn-sm']) ?>
            <?= Html::a('Confirmados', ['metrics', 'id' => $course->id, 'status' => 'confirmed'], ['class' => 'btn btn-outline-success btn-sm']) ?>
            <?= Html::a('Cancelados', ['metrics', 'id' => $course->id, 'status' => 'cancelled'], ['class' => 'btn btn-outline-danger btn-sm']) ?>
        </div>
    </div>

    <!-- Cards de resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Inscritos</h5>
                    <p class="card-text display-4"><?= $count ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Cupos Disponibles</h5>
                    <p class="card-text display-4"><?= $course->enrollments_counter ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Docente</h5>
                    <p class="card-text"><?= Html::encode($course->teacher_name) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Período</h5>
                    <p class="card-text small">
                        <?= $course->date_begin_enrollments ?><br>
                        al <?= $course->date_end_enrollments ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de inscripciones -->
    <h3>Lista de Inscripciones</h3>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>CI</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Edad</th>
                    <th>Entidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enrollments)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            No hay inscripciones registradas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($enrollments as $enrollment): ?>
                        <tr>
                            <td><span class="badge bg-secondary">#<?= $enrollment->id ?></span></td>
                            
                            <!-- CI del usuario -->
                            <td>
                                <?= $enrollment->publicUser && $enrollment->publicUser->ci 
                                    ? Html::encode($enrollment->publicUser->ci) 
                                    : '<span class="text-muted">N/A</span>' ?>
                            </td>
                            
                            <!-- Nombre completo -->
                            <td>
                                <?= $enrollment->publicUser 
                                    ? Html::encode($enrollment->publicUser->getFullName()) 
                                    : '<span class="text-muted">N/A</span>' ?>
                            </td>
                            
                            <!-- Email -->
                            <td>
                                <?= $enrollment->publicUser 
                                    ? Html::mailto($enrollment->publicUser->email) 
                                    : '<span class="text-muted">N/A</span>' ?>
                            </td>
                            
                            <!-- Teléfono -->
                            <td>
                                <?= $enrollment->publicUser && $enrollment->publicUser->phone 
                                    ? Html::encode($enrollment->publicUser->phone) 
                                    : '<span class="text-muted">N/A</span>' ?>
                            </td>
                            
                            <!-- Edad -->
                            <td class="text-center">
                                <?= $enrollment->publicUser && $enrollment->publicUser->age 
                                    ? $enrollment->publicUser->age 
                                    : '<span class="text-muted">-</span>' ?>
                            </td>
                            
                            <!-- Entidad -->
                            <td>
                                <?= $enrollment->publicUser && $enrollment->publicUser->public_entity 
                                    ? Html::encode($enrollment->publicUser->public_entity) 
                                    : '<span class="text-muted">N/A</span>' ?>
                            </td>
                            
                            <!-- Estado de la inscripción -->
                            <td>
                                <?php if (isset($enrollment->status)): ?>
                                    <?php
                                    $statusBadges = [
                                        'pending' => '<span class="badge bg-warning text-dark">Pendiente</span>',
                                        'confirmed' => '<span class="badge bg-success">Confirmado</span>',
                                        'cancelled' => '<span class="badge bg-danger">Cancelado</span>',
                                    ];
                                    echo $statusBadges[$enrollment->status] ?? $enrollment->status;
                                    ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary">N/A</span>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Acciones -->
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <?php if (isset($enrollment->status) && $enrollment->status === 'pending'): ?>
                                        <?= Html::a('✓', 
                                            ['update-enrollment-status', 'id' => $enrollment->id, 'status' => 'confirmed'], 
                                            [
                                                'class' => 'btn btn-success',
                                                'title' => 'Confirmar',
                                                'data-method' => 'post',
                                            ]
                                        ) ?>
                                        <?= Html::a('✗', 
                                            ['update-enrollment-status', 'id' => $enrollment->id, 'status' => 'cancelled'], 
                                            [
                                                'class' => 'btn btn-warning',
                                                'title' => 'Cancelar',
                                                'data-method' => 'post',
                                            ]
                                        ) ?>
                                    <?php elseif (isset($enrollment->status) && $enrollment->status === 'cancelled'): ?>
                                        <?= Html::a('↺', 
                                            ['update-enrollment-status', 'id' => $enrollment->id, 'status' => 'pending'], 
                                            [
                                                'class' => 'btn btn-info',
                                                'title' => 'Reactivar',
                                                'data-method' => 'post',
                                            ]
                                        ) ?>
                                    <?php endif; ?>
                                    
                                    <?= Html::a('🗑', ['delete-enrollment', 'id' => $enrollment->id], [
                                        'class' => 'btn btn-danger',
                                        'title' => 'Eliminar',
                                        'data' => [
                                            'confirm' => '¿Estás seguro de eliminar esta inscripción?',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pie de página -->
    <div class="d-flex justify-content-between mt-3">
        <div>
            <?= Html::a('← Volver al Dashboard', ['dashboard'], ['class' => 'btn btn-secondary']) ?>
        </div>
        <div>
            <?= Html::a('Exportar CSV', ['export-metrics', 'id' => $course->id], ['class' => 'btn btn-outline-success btn-sm']) ?>
        </div>
    </div>
</div>