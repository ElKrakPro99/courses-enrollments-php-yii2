<?php

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
        'brandLabel' => 'Sistema de Inscripción',
        'brandUrl' => ['/cursos'],
        'options' => [
            'class' => 'navbar-expand-lg navbar-dark bg-dark',
        ],
    ]);
    
    $menuItems = [
        ['label' => 'Cursos', 'url' => ['/cursos']],
    ];
    
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Admin', 'url' => ['/admin/login']];
    } else {
        $menuItems[] = '<li class="nav-item">'
            . Html::beginForm(['/admin/logout'], 'post', ['class' => 'd-flex'])
            . Html::submitButton(
                'Salir (' . Yii::$app->user->identity->user_nickname . ')',
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
        <span class="text-muted">Sistema de Inscripción de Cursos &copy; <?= date('Y') ?></span>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>