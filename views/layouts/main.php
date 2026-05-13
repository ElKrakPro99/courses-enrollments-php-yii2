<?php

// layouts/main.php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'CNEH - Sistema de Inscripcion',
        'brandUrl' => ['/cursos'],
        'options' => [
            'class' => 'navbar-expand-lg navbar-dark bg-dark',
        ],
    ]);

    $menuItems = [
        ['label' => 'Formaciones', 'url' => ['/cursos']],
    ];

    if (Yii::$app->user->isGuest) {
        // Usuario no autenticado: mostrar opciones de login
        $menuItems[] = [
            'label' => 'Acceso',
            'items' => [
                ['label' => 'Admin', 'url' => ['/admin/login']],
                ['label' => 'Manager', 'url' => ['/manager/login']],
                ['label' => 'Pagos', 'url' => ['/payment/login']],
            ],
        ];
    } else {
        // Usuario autenticado: mostrar panel y salir
        $role = Yii::$app->user->identity->role ?? '';
        $username = Yii::$app->user->identity->username ?? 'Usuario';

        // Enlace al panel segun el rol
        if ($role === 'admin') {
            $menuItems[] = ['label' => 'Panel Admin', 'url' => ['/admin']];
            $logoutUrl = ['/admin/logout'];
        } elseif ($role === 'manager') {
            $menuItems[] = ['label' => 'Panel Manager', 'url' => ['/manager']];
            $logoutUrl = ['/manager/logout'];
        } elseif ($role === 'payment') {
            $menuItems[] = ['label' => 'Panel Pagos', 'url' => ['/payment']];
            $logoutUrl = ['/payment/logout'];
        } else {
            $logoutUrl = ['/admin/logout'];
        }

        // Boton de salir con nombre de usuario y rol
        $menuItems[] = '<li class="nav-item">'
            . Html::beginForm($logoutUrl, 'post', ['class' => 'd-flex'])
            . Html::submitButton(
                'Salir (' . $username . ' - ' . strtoupper($role) . ')',
                ['class' => 'btn btn-link nav-link logout']
            )
            . Html::endForm()
            . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => $menuItems,
    ]);

    NavBar::end();
    ?>

    <div class="container mt-4">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </div>
</div>

<footer class="footer bg-light mt-5 py-3">
    <div class="container">
        <span class="text-muted">Sistema de Inscripcion para Formaciones&copy; <?= date('Y') ?></span>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>