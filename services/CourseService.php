<?php

namespace app\services;

use Yii;
use app\models\CoursesTab;
use app\models\EnrollmentsTab;
use app\models\PublicUserTab;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class CourseService
{
    /**
     * Obtiene los cursos cuyo período de inscripción está activo hoy.
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
     * Obtiene un curso por ID solo si está dentro del período de inscripción.
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
                'Este curso no está disponible para inscripción en este momento. ' .
                'Período: ' . $course->date_begin_enrollments . ' al ' . $course->date_end_enrollments
            );
        }

        return $course;
    }

    /**
     * Registra la inscripción de un usuario en un curso.
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
            ->exists();

        if ($exists) {
            throw new \Exception('Ya estás inscrito en este curso. No puedes inscribirte dos veces.');
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

            if (!$enrollment->save()) {
                throw new \Exception('Error al registrar la inscripción: ' . json_encode($enrollment->errors));
            }

            // Actualizar contadores
            $user->updateCounters(['n_courses_enrollment' => 1]);
            $course->updateCounters(['enrollments_counter' => 1]);

            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}