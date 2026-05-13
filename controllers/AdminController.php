<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\AdminTab;
use app\models\ManagerTab;
use app\models\PaymentTab;
use app\models\CoursesTab;
use app\models\EnrollmentsTab;
use app\models\UserIdentity;

class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'dashboard',
                    'create-course', 'update-course', 'delete-course',
                    'metrics', 'delete-enrollment',
                    'managers',
                    'create-manager', 'update-manager', 'delete-manager',
                    'create-payment-manager', 'update-payment-manager', 'delete-payment-manager',
                    'public-users', 'delete-public-user',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
            ],
        ];
    }

    // ==================== LOGIN / LOGOUT ====================

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard']);
        }

        $model = new AdminTab(['scenario' => 'login']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $admin = AdminTab::findByUsername($model->user_nickname);
            if ($admin && $admin->validatePassword($model->hash_pass)) {
                $identity = new UserIdentity([
                    'id'       => 'admin-' . $admin->id,
                    'username' => $admin->user_nickname,
                    'role'     => 'admin',
                    'model'    => $admin,
                    'realId'   => $admin->id,
                ]);
                Yii::$app->user->login($identity, 86400);
                Yii::$app->session->setFlash('success', 'Bienvenido Admin.');
                return $this->redirect(['dashboard']);
            }
            Yii::$app->session->setFlash('error', 'Usuario o contraseña incorrectos.');
        }

        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login']);
    }

    // ==================== DASHBOARD ====================

    public function actionDashboard()
    {
        $courses = CoursesTab::find()->orderBy(['id' => SORT_DESC])->all();
        return $this->render('dashboard', ['courses' => $courses]);
    }

    // ==================== CURSOS ====================

    public function actionCreateCourse()
    {
        $model = new CoursesTab();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Curso creado.');
            return $this->redirect(['dashboard']);
        }
        return $this->render('create-course', ['model' => $model]);
    }

    public function actionUpdateCourse($id)
    {
        $model = CoursesTab::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Curso no encontrado.');
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Curso actualizado.');
            return $this->redirect(['dashboard']);
        }
        return $this->render('update-course', ['model' => $model]);
    }

    public function actionDeleteCourse($id)
    {
        CoursesTab::findOne($id)?->delete();
        Yii::$app->session->setFlash('success', 'Curso eliminado.');
        return $this->redirect(['dashboard']);
    }

    public function actionMetrics($id)
    {
        $course = CoursesTab::findOne($id);
        if ($course === null) {
            throw new \yii\web\NotFoundHttpException('Curso no encontrado.');
        }
        $enrollments = EnrollmentsTab::find()
            ->where(['course_id' => $id])
            ->with('publicUser')
            ->all();
        return $this->render('metrics', [
            'course'      => $course,
            'enrollments' => $enrollments,
            'count'       => count($enrollments),
        ]);
    }

    public function actionDeleteEnrollment($id)
    {
        $enrollment = EnrollmentsTab::findOne($id);
        $enrollment?->delete();
        Yii::$app->session->setFlash('success', 'Inscripción eliminada.');
        return $this->redirect(Yii::$app->request->referrer ?: ['dashboard']);
    }

    // ==================== GESTIÓN DE USUARIOS (MANAGERS Y REVISORES) ====================

    // Listar managers y revisores de pagos
    public function actionManagers($tab = 'managers')
    {
        $managers = ManagerTab::find()->all();
        $payments = PaymentTab::find()->all();
        
        return $this->render('managers', [
            'managers' => $managers,
            'payments' => $payments,
            'model'    => new ManagerTab(),
            'action'   => 'list',
            'tab'      => $tab,
            'type'     => 'manager',
        ]);
    }

    // Crear manager
    public function actionCreateManager()
    {
        $model = new ManagerTab(['scenario' => 'create']);
        if ($model->load(Yii::$app->request->post())) {
            $model->hash_pass = Yii::$app->security->generatePasswordHash($model->hash_pass);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Manager creado.');
                return $this->redirect(['managers']);
            }
        }
        $managers = ManagerTab::find()->all();
        $payments = PaymentTab::find()->all();
        return $this->render('managers', [
            'managers' => $managers,
            'payments' => $payments,
            'model'    => $model,
            'action'   => 'create',
            'tab'      => 'managers',
            'type'     => 'manager',
        ]);
    }

    // Actualizar manager
    public function actionUpdateManager($id)
    {
        $model = ManagerTab::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Manager no encontrado.');
        }
        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->hash_pass)) {
                $model->hash_pass = Yii::$app->security->generatePasswordHash($model->hash_pass);
            } else {
                $model->hash_pass = $model->getOldAttribute('hash_pass');
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Manager actualizado.');
                return $this->redirect(['managers']);
            }
        }
        $managers = ManagerTab::find()->all();
        $payments = PaymentTab::find()->all();
        return $this->render('managers', [
            'managers' => $managers,
            'payments' => $payments,
            'model'    => $model,
            'action'   => 'update',
            'tab'      => 'managers',
            'type'     => 'manager',
        ]);
    }

    // Eliminar manager
    public function actionDeleteManager($id)
    {
        ManagerTab::findOne($id)?->delete();
        Yii::$app->session->setFlash('success', 'Manager eliminado.');
        return $this->redirect(['managers']);
    }

    // Crear revisor de pagos
    public function actionCreatePaymentManager()
    {
        $model = new PaymentTab(['scenario' => 'create']);
        if ($model->load(Yii::$app->request->post())) {
            $model->hash_pass = Yii::$app->security->generatePasswordHash($model->hash_pass);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Revisor de pagos creado.');
                return $this->redirect(['managers', 'tab' => 'payments']);
            }
        }
        $managers = ManagerTab::find()->all();
        $payments = PaymentTab::find()->all();
        return $this->render('managers', [
            'managers' => $managers,
            'payments' => $payments,
            'model'    => $model,
            'action'   => 'create',
            'tab'      => 'payments',
            'type'     => 'payment',
        ]);
    }

    // Actualizar revisor de pagos
    public function actionUpdatePaymentManager($id)
    {
        $model = PaymentTab::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Revisor no encontrado.');
        }
        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->hash_pass)) {
                $model->hash_pass = Yii::$app->security->generatePasswordHash($model->hash_pass);
            } else {
                $model->hash_pass = $model->getOldAttribute('hash_pass');
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Revisor de pagos actualizado.');
                return $this->redirect(['managers', 'tab' => 'payments']);
            }
        }
        $managers = ManagerTab::find()->all();
        $payments = PaymentTab::find()->all();
        return $this->render('managers', [
            'managers' => $managers,
            'payments' => $payments,
            'model'    => $model,
            'action'   => 'update',
            'tab'      => 'payments',
            'type'     => 'payment',
        ]);
    }

    // Eliminar revisor de pagos
    public function actionDeletePaymentManager($id)
    {
        PaymentTab::findOne($id)?->delete();
        Yii::$app->session->setFlash('success', 'Revisor de pagos eliminado.');
        return $this->redirect(['managers', 'tab' => 'payments']);
    }

    // ==================== USUARIOS PÚBLICOS ====================

    public function actionPublicUsers()
    {
        $users = \app\models\PublicUserTab::find()
            ->orderBy(['id' => SORT_DESC])
            ->all();
        
        return $this->render('public-users', ['users' => $users]);
    }

    public function actionDeletePublicUser($id)
    {
        $user = \app\models\PublicUserTab::findOne($id);
        if ($user) {
            \app\models\EnrollmentsTab::deleteAll(['user_id' => $user->id]);
            $user->delete();
            Yii::$app->session->setFlash('success', 'Usuario y sus inscripciones eliminados.');
        }
        return $this->redirect(['public-users']);
    }
}