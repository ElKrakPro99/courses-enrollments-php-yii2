<?php

// controllers/PaymentController.php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\PaymentTab;
use app\models\EnrollmentsTab;
use app\models\CoursesTab;
use app\models\UserIdentity;

class PaymentController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['dashboard', 'approve', 'reject', 'view-course'],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isPayment();
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

        $model = new PaymentTab(['scenario' => 'login']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $payment = PaymentTab::findByUsername($model->user_nickname);
            if ($payment && $payment->validatePassword($model->hash_pass)) {
                $identity = new UserIdentity([
                    'id'       => 'payment-' . $payment->id,
                    'username' => $payment->user_nickname,
                    'role'     => 'payment',
                    'model'    => $payment,
                    'realId'   => $payment->id,
                ]);
                Yii::$app->user->login($identity, 86400);
                return $this->redirect(['dashboard']);
            }
            Yii::$app->session->setFlash('error', 'Credenciales invalidas.');
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
        // Todos los cursos que son de pago (tengan o no pendientes)
        $courses = CoursesTab::find()
            ->where(['payment_type' => CoursesTab::PAYMENT_PAID])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->render('dashboard', ['courses' => $courses]);
    }

    // Ver inscripciones de un curso especifico
    public function actionViewCourse($id)
    {
        $course = CoursesTab::findOne($id);
        if (!$course) {
            throw new \yii\web\NotFoundHttpException('Curso no encontrado.');
        }

        // Todas las inscripciones de este curso (pendientes, aprobadas, rechazadas)
        $enrollments = EnrollmentsTab::find()
            ->where(['course_id' => $id])
            ->with(['publicUser'])
            ->orderBy(['status' => SORT_ASC, 'id' => SORT_DESC])
            ->all();

        $countPending = EnrollmentsTab::find()
            ->where(['course_id' => $id, 'status' => EnrollmentsTab::STATUS_PAYMENT_PENDING])
            ->count();

        return $this->render('view-course', [
            'course' => $course,
            'enrollments' => $enrollments,
            'countPending' => $countPending,
        ]);
    }

    public function actionApprove($id)
    {
        $enrollment = EnrollmentsTab::findOne($id);
        if ($enrollment) {
            $enrollment->status = EnrollmentsTab::STATUS_CONFIRMED;
            $enrollment->payment_verified_by = Yii::$app->user->identity->realId;
            $enrollment->payment_verified_at = date('Y-m-d H:i:s');
            $enrollment->save();
            
            $enrollment->publicUser->updateCounters(['n_courses_enrollment' => 1]);
            $enrollment->course->updateCounters(['enrollments_counter' => 1]);
            
            Yii::$app->session->setFlash('success', 'Pago aprobado. Inscripcion confirmada.');
        }
        return $this->redirect(['view-course', 'id' => $enrollment->course_id]);
    }

    public function actionReject($id)
    {
        $enrollment = EnrollmentsTab::findOne($id);
        if ($enrollment) {
            $enrollment->status = EnrollmentsTab::STATUS_CANCELLED;
            $enrollment->payment_verified_by = Yii::$app->user->identity->realId;
            $enrollment->payment_verified_at = date('Y-m-d H:i:s');
            $enrollment->save();
            Yii::$app->session->setFlash('info', 'Pago rechazado.');
        }
        return $this->redirect(['view-course', 'id' => $enrollment->course_id]);
    }
}