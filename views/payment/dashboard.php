<?php
use yii\helpers\Html;
use app\models\EnrollmentsTab;

$this->title = 'Panel de Pagos';
?>
<div class="payment-dashboard">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('Cerrar Sesion', ['logout'], ['class' => 'btn btn-danger', 'data' => ['method' => 'post']]) ?></p>

    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Formación</th>
                <th>Categoria</th>
                <th>Monto</th>
                <th>Pendientes</th>
                <th>Aprobados</th>
                <th>Rechazados</th>
                <th>Total</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($courses)): ?>
                <tr><td colspan="9" class="text-center">No hay cursos de pago registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($courses as $course): 
                    $pending = $course->getEnrollmentsTabs()->where(['status' => EnrollmentsTab::STATUS_PAYMENT_PENDING])->count();
                    $approved = $course->getEnrollmentsTabs()->where(['status' => EnrollmentsTab::STATUS_CONFIRMED])->count();
                    $rejected = $course->getEnrollmentsTabs()->where(['status' => EnrollmentsTab::STATUS_CANCELLED])->count();
                    $total = $pending + $approved + $rejected;
                ?>
                    <tr>
                        <td>#<?= $course->id ?></td>
                        <td><?= Html::encode($course->course_name) ?></td>
                        <td><span class="badge bg-secondary"><?= Html::encode($course->category) ?></span></td>
                        <td><?= $course->getFormattedAmount() ?></td>
                        <td><span class="badge bg-warning text-dark"><?= $pending ?></span></td>
                        <td><span class="badge bg-success"><?= $approved ?></span></td>
                        <td><span class="badge bg-danger"><?= $rejected ?></span></td>
                        <td><strong><?= $total ?></strong></td>
                        <td>
                            <?= Html::a('Ver inscripciones', ['view-course', 'id' => $course->id], ['class' => 'btn btn-info btn-sm']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>