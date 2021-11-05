<?php

namespace App\Providers;

use App\Repositories\BloodGroup\BloodGroupRepository;
use App\Repositories\BloodGroup\BloodGroupRepositoryInterface;
use App\Repositories\Classes\ClassesRepository;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\Dorm\DormRepository;
use App\Repositories\Dorm\DormRepositoryInterface;
use App\Repositories\Exam\ExamRepository;
use App\Repositories\Exam\ExamRepositoryInterface;
use App\Repositories\ExamRecord\ExamRecordRepository;
use App\Repositories\ExamRecord\ExamRecordRepositoryInterface;
use App\Repositories\Grade\GradeRepository;
use App\Repositories\Grade\GradeRepositoryInterface;
use App\Repositories\LGA\LGARepository;
use App\Repositories\LGA\LGARepositoryInterface;
use App\Repositories\Major\MajorRepository;
use App\Repositories\Major\MajorRepositoryInterface;
use App\Repositories\Mark\MarkRepository;
use App\Repositories\Mark\MarkRepositoryInterface;
use App\Repositories\MyCourse\MyCourseRepository;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\Nationals\NationalRepository;
use App\Repositories\Nationals\NationalRepositoryInterface;
use App\Repositories\Payment\PaymentRepository;
use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Repositories\PaymentRecord\PaymentRecordRepository;
use App\Repositories\PaymentRecord\PaymentRecordRepositoryInterface;
use App\Repositories\Pin\PinRepository;
use App\Repositories\Pin\PinRepositoryInterface;
use App\Repositories\Promotion\PromotionRepository;
use App\Repositories\Promotion\PromotionRepositoryInterface;
use App\Repositories\Receipt\ReceiptRepository;
use App\Repositories\Receipt\ReceiptRepositoryInterface;
use App\Repositories\Setting\SettingRepository;
use App\Repositories\Setting\SettingRepositoryInterface;
use App\Repositories\Skill\SkillRepository;
use App\Repositories\Skill\SkillRepositoryInterface;
use App\Repositories\StaffRecord\StaffRecordRepository;
use App\Repositories\StaffRecord\StaffRecordRepositoryInterface;
use App\Repositories\State\StateRepository;
use App\Repositories\State\StateRepositoryInterface;
use App\Repositories\Student\StudentRepository;
use App\Repositories\Student\StudentRepositoryInterface;
use App\Repositories\Subject\SubjectRepository;
use App\Repositories\Subject\SubjectRepositoryInterface;
use App\Repositories\TimeSlot\TimeSlotRepository;
use App\Repositories\TimeSlot\TimeSlotRepositoryInterface;
use App\Repositories\TimeTable\TimeTableRepository;
use App\Repositories\TimeTable\TimeTableRepositoryInterface;
use App\Repositories\TimeTableRecord\TimeTableRecordRepository;
use App\Repositories\TimeTableRecord\TimeTableRecordRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserType\UserTypeRepository;
use App\Repositories\UserType\UserTypeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            SettingRepositoryInterface::class,
            SettingRepository::class
        );
        $this->app->bind(
            DormRepositoryInterface::class,
            DormRepository::class
        );
        $this->app->bind(
            PinRepositoryInterface::class,
            PinRepository::class
        );
        $this->app->bind(
            StateRepositoryInterface::class,
            StateRepository::class
        );
        $this->app->bind(
            NationalRepositoryInterface::class,
            NationalRepository::class
        );
        $this->app->bind(
            LGARepositoryInterface::class,
            LGARepository::class
        );
        $this->app->bind(
            MyCourseRepositoryInterface::class,
            MyCourseRepository::class
        );
        $this->app->bind(
            MajorRepositoryInterface::class,
            MajorRepository::class
        );
        $this->app->bind(
            ClassesRepositoryInterface::class,
            ClassesRepository::class
        );
        $this->app->bind(
            SubjectRepositoryInterface::class,
            SubjectRepository::class
        );
        $this->app->bind(
            PromotionRepositoryInterface::class,
            PromotionRepository::class
        );
        $this->app->bind(
            StudentRepositoryInterface::class,
            StudentRepository::class
        );
        $this->app->bind(
            BloodGroupRepositoryInterface::class,
            BloodGroupRepository::class
        );
        $this->app->bind(
            StaffRecordRepositoryInterface::class,
            StaffRecordRepository::class
        );
        $this->app->bind(
            UserTypeRepositoryInterface::class,
            UserTypeRepository::class
        );
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            SkillRepositoryInterface::class,
            SkillRepository::class
        );
        $this->app->bind(
            MarkRepositoryInterface::class,
            MarkRepository::class
        );
        $this->app->bind(
            GradeRepositoryInterface::class,
            GradeRepository::class
        );
        $this->app->bind(
            ExamRecordRepositoryInterface::class,
            ExamRecordRepository::class
        );
        $this->app->bind(
            ExamRepositoryInterface::class,
            ExamRepository::class
        );
        $this->app->bind(
            ReceiptRepositoryInterface::class,
            ReceiptRepository::class
        );
        $this->app->bind(
            PaymentRecordRepositoryInterface::class,
            PaymentRecordRepository::class
        );
        $this->app->bind(
            PaymentRepositoryInterface::class,
            PaymentRepository::class
        );
        $this->app->bind(
            TimeTableRecordRepositoryInterface::class,
            TimeTableRecordRepository::class
        );
        $this->app->bind(
            TimeSlotRepositoryInterface::class,
            TimeSlotRepository::class
        );
        $this->app->bind(
            TimeTableRepositoryInterface::class,
            TimeTableRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
