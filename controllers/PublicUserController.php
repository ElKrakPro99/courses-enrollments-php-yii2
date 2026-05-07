<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\services\PublicCourseService;
use app\models\PublicUserTab;

class PublicUserController extends Controller
{
    public function actionAvailableCourses()
    {
        $service = new PublicCourseService();
        $courses = $service->getAvailableCourses();

        return $this->render('available', [
            'courses' => $courses,
        ]);
    }

    public function actionCourseData($id)
    {
        $service = new PublicCourseService();

        try {
            $course = $service->getAvailableCourseById($id);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['available-courses']);
        }

        return $this->render('course-data', [
            'course' => $course,
        ]);
    }

    public function actionEnroll($course_id = null)
    {
        $service = new PublicCourseService();
        $courseId = $course_id ?? Yii::$app->request->post('course_id');

        try {
            $course = $service->getAvailableCourseById($courseId);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['available-courses']);
        }

        $userModel = new PublicUserTab();

        if ($userModel->load(Yii::$app->request->post())) {
            if (!$userModel->validate()) {
                return $this->render('enroll', [
                    'model'  => $userModel,
                    'course' => $course,
                ]);
            }

            $existingUser = PublicUserTab::findOne(['email' => $userModel->email]);
            if ($existingUser) {
                $user = $existingUser;
                $user->attributes = $userModel->attributes;
            } else {
                $user = $userModel;
            }

            if (!$user->save()) {
                Yii::$app->session->setFlash('error', 'Error al guardar los datos personales.');
                return $this->render('enroll', [
                    'model'  => $userModel,
                    'course' => $course,
                ]);
            }

            try {
                $service->enrollUser($user, $course->id);
                Yii::$app->session->setFlash('success', '¡Inscripción realizada con éxito!');
                return $this->redirect(['available-courses']);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('enroll', [
            'model'  => $userModel,
            'course' => $course,
        ]);
    }
}