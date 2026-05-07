<?php

// services/CourseService.php

namespace app\services;

use Yii;
use app\models\CoursesTab;
use app\models\EnrollmentsTab;
use app\models\PublicUserTab;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class managerCourseService
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
    public function enrollUser($user, $courseId)
    {
        $course = $this->getAvailableCourseById($courseId);

        // Verificar duplicado
        $exists = EnrollmentsTab::find()
            ->where([
                'course_id' => $courseId,
                'user_id'   => $user->id,
            ])
            ->andWhere(['!=', 'status', EnrollmentsTab::STATUS_CANCELLED])
            ->exists();

        if ($exists) {
            throw new \Exception('Ya estas inscrito en este curso.');
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $enrollment = new EnrollmentsTab();
            $enrollment->course_id              = $course->id;
            $enrollment->user_id                = $user->id;
            $enrollment->date_begin_enrollments = $course->date_begin_enrollments;
            $enrollment->date_end_enrollments   = $course->date_end_enrollments;
            $enrollment->teacher_name           = $course->teacher_name;
            $enrollment->counter_enrollments    = 1;
            $enrollment->status                 = EnrollmentsTab::STATUS_PENDING;

            if (!$enrollment->save()) {
                throw new \Exception('Error al registrar la inscripcion: ' . json_encode($enrollment->errors));
            }

            $user->updateCounters(['n_courses_enrollment' => 1]);
            $course->updateCounters(['enrollments_counter' => 1]);

            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza el estado de una inscripcion.
     */
    public function updateEnrollmentStatus($enrollmentId, $newStatus)
    {
        $enrollment = EnrollmentsTab::findOne($enrollmentId);
        if (!$enrollment) {
            throw new \Exception('Inscripcion no encontrada.');
        }

        $enrollment->status = $newStatus;
        if (!$enrollment->save()) {
            throw new \Exception('Error al actualizar: ' . json_encode($enrollment->errors));
        }

        return $enrollment;
    }

    /**
     * Obtiene inscripciones por estado.
     */
    public function getEnrollmentsByStatus($courseId, $status = null)
    {
        $query = EnrollmentsTab::find()
            ->where(['course_id' => $courseId])
            ->with('publicUser');
        
        if ($status) {
            $query->andWhere(['status' => $status]);
        }
        
        return $query->orderBy(['id' => SORT_DESC])->all();
    }
}