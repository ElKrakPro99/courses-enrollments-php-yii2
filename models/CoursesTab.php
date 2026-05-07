<?php

// models/CoursesTab.php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "courses_tab".
 *
 * @property int $id
 * @property string $course_name
 * @property string $category
 * @property int $enrollments_counter
 * @property string $date_begin_enrollments
 * @property string $date_end_enrollments
 * @property string $date_begin_course
 * @property string $date_end_course
 * @property string $class_days
 * @property string $payment_type
 * @property string $modality
 * @property string $teacher_name
 *
 * @property EnrollmentsTab[] $enrollmentsTabs
 */
class CoursesTab extends ActiveRecord
{
    const CATEGORY_CURSO = 'curso';
    const CATEGORY_DIPLOMADO = 'diplomado';
    const CATEGORY_TALLER = 'taller';
    const CATEGORY_SEMINARIO = 'seminario';

    const MODALITY_PRESENCIAL = 'presencial';
    const MODALITY_ONLINE = 'online';
    const MODALITY_MIXTO = 'mixto';

    const PAYMENT_FREE = 'libre';
    const PAYMENT_PAID = 'pago';

    public static function tableName()
    {
        return 'courses_tab';
    }

    public function rules()
    {
        return [
            [['course_name', 'category', 'date_begin_enrollments', 'date_end_enrollments', 
              'date_begin_course', 'date_end_course', 'payment_type', 'modality', 'teacher_name'], 'required'],
            [['enrollments_counter'], 'integer'],
            [['date_begin_enrollments', 'date_end_enrollments', 'date_begin_course', 'date_end_course'], 'date', 'format' => 'php:Y-m-d'],
            [['course_name', 'teacher_name'], 'string', 'max' => 100],
            [['category'], 'string', 'max' => 50],
            [['category'], 'in', 'range' => [self::CATEGORY_CURSO, self::CATEGORY_DIPLOMADO, self::CATEGORY_TALLER, self::CATEGORY_SEMINARIO]],
            [['payment_type'], 'string', 'max' => 20],
            [['payment_type'], 'in', 'range' => [self::PAYMENT_FREE, self::PAYMENT_PAID]],
            [['modality'], 'string', 'max' => 20],
            [['modality'], 'in', 'range' => [self::MODALITY_PRESENCIAL, self::MODALITY_ONLINE, self::MODALITY_MIXTO]],
            [['class_days'], 'string', 'max' => 100],
            [['date_end_enrollments', 'date_begin_enrollments'], 'validateEnrollmentDates'],
            [['date_end_course', 'date_begin_course'], 'validateCourseDates'],
        ];
    }

    public function validateEnrollmentDates($attribute, $params)
    {
        if ($this->date_begin_enrollments && $this->date_end_enrollments) {
            if (strtotime($this->date_end_enrollments) < strtotime($this->date_begin_enrollments)) {
                $this->addError('date_end_enrollments', 'La fecha fin de inscripcion no puede ser anterior a la fecha de inicio.');
            }
        }
    }

    public function validateCourseDates($attribute, $params)
    {
        if ($this->date_begin_course && $this->date_end_course) {
            if (strtotime($this->date_end_course) < strtotime($this->date_begin_course)) {
                $this->addError('date_end_course', 'La fecha fin del curso no puede ser anterior a la fecha de inicio.');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'id'                      => 'ID',
            'course_name'             => 'Nombre del curso',
            'category'                => 'Categoria',
            'enrollments_counter'     => 'Total de inscritos',
            'date_begin_enrollments'  => 'Inicio de inscripciones',
            'date_end_enrollments'    => 'Fin de inscripciones',
            'date_begin_course'       => 'Inicio del curso',
            'date_end_course'         => 'Fin del curso',
            'class_days'              => 'Dias de clase',
            'payment_type'            => 'Tipo de pago',
            'modality'                => 'Modalidad',
            'teacher_name'            => 'Docente',
        ];
    }

    public function getCategoryLabel()
    {
        $labels = [
            self::CATEGORY_CURSO => 'Curso',
            self::CATEGORY_DIPLOMADO => 'Diplomado',
            self::CATEGORY_TALLER => 'Taller',
            self::CATEGORY_SEMINARIO => 'Seminario',
        ];
        return $labels[$this->category] ?? $this->category;
    }

    public function getModalityLabel()
    {
        $labels = [
            self::MODALITY_PRESENCIAL => 'Presencial',
            self::MODALITY_ONLINE => 'Online',
            self::MODALITY_MIXTO => 'Mixto',
        ];
        return $labels[$this->modality] ?? $this->modality;
    }

    public function getPaymentTypeLabel()
    {
        return $this->payment_type === self::PAYMENT_FREE ? 'Libre' : 'Pago';
    }

    public function getEnrollmentsTabs()
    {
        return $this->hasMany(EnrollmentsTab::class, ['course_id' => 'id']);
    }
}