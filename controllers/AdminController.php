<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\AdminTab;
use app\models\CoursesTab;
use app\models\EnrollmentsTab;

class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['dashboard', 'create-course', 'update-course', 'delete-course', 'metrics', 'delete-enrollment'],
                'rules' => [
                    [
                        'allow'   => true,
                        'roles'   => ['@'],
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
                // Login exitoso - usar el modelo encontrado en BD, no el del formulario
                Yii::$app->user->login($admin, 86400);
                Yii::$app->session->setFlash('success', 'Bienvenido al panel de administración.');
                return $this->redirect(['dashboard']);
            }
            // Error de autenticación
            Yii::$app->session->setFlash('error', 'Usuario o contraseña incorrectos.');
        }

        // Limpiar campo de contraseña por seguridad
        $model->hash_pass = '';
        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->setFlash('info', 'Sesión cerrada correctamente.');
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
            Yii::$app->session->setFlash('success', 'Curso creado exitosamente.');
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
            Yii::$app->session->setFlash('success', 'Curso actualizado correctamente.');
            return $this->redirect(['dashboard']);
        }

        return $this->render('update-course', ['model' => $model]);
    }

    public function actionDeleteCourse($id)
    {
        $model = CoursesTab::findOne($id);
        if ($model !== null) {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Curso eliminado correctamente.');
        }
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
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $count = count($enrollments);

        return $this->render('metrics', [
            'course'      => $course,
            'enrollments' => $enrollments,
            'count'       => $count,
        ]);
    }

    public function actionDeleteEnrollment($id)
    {
        $enrollment = EnrollmentsTab::findOne($id);
        if ($enrollment !== null) {
            $courseId = $enrollment->course_id;
            $enrollment->delete();
            Yii::$app->session->setFlash('success', 'Inscripción eliminada.');
            return $this->redirect(['metrics', 'id' => $courseId]);
        }
        return $this->redirect(['dashboard']);
    }
}