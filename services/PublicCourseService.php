<?php

// services/CourseService.php

namespace app\services;

use Yii;
use app\models\CoursesTab;
use app\models\EnrollmentsTab;
use app\models\PublicUserTab;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class PublicCourseService
{
    /**
     * Obtiene los cursos cuyo periodo de inscripcion esta activo hoy.
     * @return CoursesTab[]
     */
    public function getAvailableCourses()
    {
        $today = date('Y-m-d');

        return CoursesTab::find()
            ->where(['<=', 'date_begin_enrollments', $today])
            ->andWhere(['>=', 'date_end_enrollments', $today])
            ->orderBy(['date_begin_enrollments' => SORT_ASC])
            ->all();
    }

    /**
     * Obtiene un curso por ID solo si esta dentro del periodo de inscripcion.
     * @param int $id
     * @return CoursesTab
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function getAvailableCourseById($id)
    {
        $course = CoursesTab::findOne($id);

        if ($course === null) {
            throw new NotFoundHttpException('El curso solicitado no existe.');
        }

        $today = date('Y-m-d');
        if ($today < $course->date_begin_enrollments || $today > $course->date_end_enrollments) {
            throw new ForbiddenHttpException(
                'Este curso no esta disponible para inscripcion en este momento. ' .
                'Periodo: ' . $course->date_begin_enrollments . ' al ' . $course->date_end_enrollments
            );
        }

        return $course;
    }

    /**
     * Registra la inscripcion de un usuario en un curso.
     * @param PublicUserTab $user
     * @param int $courseId
     * @return bool
     * @throws \Exception
     */
    public function enrollUser($user, $courseId, $voucherPath = null)
    {
        $course = $this->getAvailableCourseById($courseId);

        // Determinar estado segun tipo de pago
        if ($course->payment_type === CoursesTab::PAYMENT_PAID) {
            $status = EnrollmentsTab::STATUS_PAYMENT_PENDING;
        } else {
            $status = EnrollmentsTab::STATUS_PENDING;
        }

        $exists = EnrollmentsTab::find()
            ->where(['course_id' => $courseId, 'user_id' => $user->id])
            ->andWhere(['!=', 'status', EnrollmentsTab::STATUS_CANCELLED])
            ->exists();

        if ($exists) {
            throw new \Exception('Ya estas inscrito en este curso.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $enrollment = new EnrollmentsTab();
            $enrollment->course_id = $course->id;
            $enrollment->user_id = $user->id;
            $enrollment->date_begin_enrollments = $course->date_begin_enrollments;
            $enrollment->date_end_enrollments = $course->date_end_enrollments;
            $enrollment->teacher_name = $course->teacher_name;
            $enrollment->counter_enrollments = 1;
            $enrollment->status = $status;
            $enrollment->voucher_path = $voucherPath;  // ← NUEVO

            if (!$enrollment->save()) {
                throw new \Exception('Error: ' . json_encode($enrollment->errors));
            }

            if ($status === EnrollmentsTab::STATUS_PENDING) {
                $user->updateCounters(['n_courses_enrollment' => 1]);
                $course->updateCounters(['enrollments_counter' => 1]);
            }

            $transaction->commit();
            return $status;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

}