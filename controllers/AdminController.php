<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\AdminTab;
use app\models\ManagerTab;
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
                'only' => ['dashboard', 'create-course', 'update-course', 'delete-course', 'metrics', 'delete-enrollment', 'managers', 'create-manager', 'update-manager', 'delete-manager'],
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
                    'id' => $admin->id,
                    'username' => $admin->user_nickname,
                    'role' => 'admin',
                    'model' => $admin,
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

    public function actionDashboard()
    {
        $courses = CoursesTab::find()->orderBy(['id' => SORT_DESC])->all();
        return $this->render('dashboard', ['courses' => $courses]);
    }

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
        $enrollments = EnrollmentsTab::find()->where(['course_id' => $id])->with('publicUser')->all();
        return $this->render('metrics', [
            'course' => $course,
            'enrollments' => $enrollments,
            'count' => count($enrollments),
        ]);
    }

    public function actionDeleteEnrollment($id)
    {
        $enrollment = EnrollmentsTab::findOne($id);
        $enrollment?->delete();
        Yii::$app->session->setFlash('success', 'Inscripción eliminada.');
        return $this->redirect(Yii::$app->request->referrer ?: ['dashboard']);
    }

    // ----- Gestión de Managers -----
    public function actionManagers()
    {
        $managers = ManagerTab::find()->all();
        return $this->render('managers', [
            'managers' => $managers,
            'model' => new ManagerTab(),
            'action' => 'list',
        ]);
    }

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
        return $this->render('managers', [          // ← cambiar a 'managers'
            'managers' => $managers,
            'model' => $model,
            'action' => 'create',
        ]);
    }

    public function actionUpdateManager($id)
    {
        $model = ManagerTab::findOne($id);
        if (!$model) {
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
        return $this->render('managers', [          // ← cambiar a 'managers'
            'managers' => $managers,
            'model' => $model,
            'action' => 'update',
        ]);
    }

    public function actionDeleteManager($id)
    {
        ManagerTab::findOne($id)?->delete();
        Yii::$app->session->setFlash('success', 'Manager eliminado.');
        return $this->redirect(['managers']);
    }
}