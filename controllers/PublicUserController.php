<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\services\PublicCourseService;
use app\models\PublicUserTab;
use app\models\EnrollmentsTab;

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
            
            // Procesar voucher si es curso de pago
            $voucherPath = null;
            if ($course->payment_type === 'pago') {
                $uploadedFile = \yii\web\UploadedFile::getInstance($userModel, 'voucher');
                if ($uploadedFile) {
                    $fileName = 'voucher_' . time() . '_' . Yii::$app->security->generateRandomString(8) . '.' . $uploadedFile->extension;
                    $uploadPath = Yii::getAlias('@webroot/uploads/vouchers/');
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    $uploadedFile->saveAs($uploadPath . $fileName);
                    $voucherPath = 'uploads/vouchers/' . $fileName;
                }
            }

            // Buscar usuario existente
            $existingUser = null;
            if (!empty($userModel->ci)) {
                $existingUser = PublicUserTab::findOne(['ci' => $userModel->ci]);
            }
            if (!$existingUser && !empty($userModel->email)) {
                $existingUser = PublicUserTab::findOne(['email' => $userModel->email]);
            }

            if ($existingUser) {
                $user = $existingUser;
                $user->attributes = $userModel->attributes;
                $user->ci = $existingUser->ci;
            } else {
                $user = $userModel;
            }

            if (!$user->save(false)) {
                Yii::$app->session->setFlash('error', 'Error al guardar datos.');
                return $this->render('enroll', ['model' => $userModel, 'course' => $course]);
            }

            try {
                $status = $service->enrollUser($user, $course->id, $voucherPath);
                if ($status === EnrollmentsTab::STATUS_PAYMENT_PENDING) {
                    Yii::$app->session->setFlash('success', 'Inscripcion recibida. Su comprobante sera revisado por nuestro equipo.');
                } else {
                    Yii::$app->session->setFlash('success', 'Inscripcion realizada con exito!');
                }
                return $this->redirect(['available-courses']);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('enroll', ['model' => $userModel, 'course' => $course]);
    }

    public function actionGetUserByCi($ci)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $user = PublicUserTab::findOne(['ci' => $ci]);
        if ($user) {
            return [
                'success' => true,
                'data' => [
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'age' => $user->age,
                    'public_entity' => $user->public_entity,
                    'nationality' => $user->nationality,
                ]
            ];
        }
        return ['success' => false];
    }

}