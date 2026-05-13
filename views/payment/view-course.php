<?php

// views/payment/view-course.php

use yii\helpers\Html;
use app\models\EnrollmentsTab;

$this->title = 'Inscripciones: ' . $course->course_name;
?>
<div class="payment-view-course">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-info text-white"><div class="card-body text-center">
                <h5>Monto</h5><p class="h4"><?= $course->getFormattedAmount() ?></p>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark"><div class="card-body text-center">
                <h5>Pendientes</h5><p class="h4"><?= $countPending ?></p>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white"><div class="card-body text-center">
                <h5>Total</h5><p class="h4"><?= count($enrollments) ?></p>
            </div></div>
        </div>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr><th>ID</th><th>CI</th><th>Usuario</th><th>Email</th><th>Comprobante</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            <?php foreach ($enrollments as $enrollment): ?>
                <tr>
                    <td>#<?= $enrollment->id ?></td>
                    <td><?= $enrollment->publicUser->ci ?? 'N/A' ?></td>
                    <td><?= Html::encode($enrollment->publicUser->getFullName()) ?></td>
                    <td><?= $enrollment->publicUser->email ?></td>
                    <td>
                        <?php if ($enrollment->voucher_path): ?>
                            <?= Html::a('Ver', ['/' . $enrollment->voucher_path], ['target' => '_blank', 'class' => 'btn btn-info btn-sm']) ?>
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $badges = [
                            EnrollmentsTab::STATUS_PAYMENT_PENDING => '<span class="badge bg-warning text-dark">Pendiente</span>',
                            EnrollmentsTab::STATUS_CONFIRMED => '<span class="badge bg-success">Aprobado</span>',
                            EnrollmentsTab::STATUS_CANCELLED => '<span class="badge bg-danger">Rechazado</span>',
                            EnrollmentsTab::STATUS_PENDING => '<span class="badge bg-secondary">Normal</span>',
                        ];
                        echo $badges[$enrollment->status] ?? $enrollment->status;
                        ?>
                    </td>
                    <td>
                        <?php if ($enrollment->status === EnrollmentsTab::STATUS_PAYMENT_PENDING): ?>
                            <?= Html::a('Aprobar', ['approve', 'id' => $enrollment->id], ['class' => 'btn btn-success btn-sm', 'data-method' => 'post']) ?>
                            <?= Html::a('Rechazar', ['reject', 'id' => $enrollment->id], ['class' => 'btn btn-danger btn-sm', 'data-method' => 'post']) ?>
                        <?php elseif ($enrollment->status === EnrollmentsTab::STATUS_CONFIRMED): ?>
                            <span class="text-success">✓ Aprobado</span>
                            <?php if ($enrollment->payment_verified_at): ?>
                                <br><small><?= date('d/m/Y', strtotime($enrollment->payment_verified_at)) ?></small>
                            <?php endif; ?>
                        <?php elseif ($enrollment->status === EnrollmentsTab::STATUS_CANCELLED): ?>
                            <span class="text-danger">✗ Rechazado</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?= Html::a('← Volver a formaciones', ['dashboard'], ['class' => 'btn btn-secondary']) ?>
</div>