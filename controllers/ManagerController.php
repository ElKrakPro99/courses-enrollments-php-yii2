<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\ManagerTab;
use app\models\CoursesTab;
use app\models\EnrollmentsTab;
use app\models\UserIdentity;

class ManagerController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['dashboard', 'create-course', 'update-course', 'metrics'],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isManager();
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

        $model = new ManagerTab(['scenario' => 'login']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $manager = ManagerTab::findByUsername($model->user_nickname);
            if ($manager && $manager->validatePassword($model->hash_pass)) {
                $identity = new UserIdentity([
                    'id' => $manager->id,
                    'username' => $manager->user_nickname,
                    'role' => 'manager',
                    'model' => $manager,
                ]);
                Yii::$app->user->login($identity, 86400);
                Yii::$app->session->setFlash('success', 'Bienvenido Manager.');
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
}