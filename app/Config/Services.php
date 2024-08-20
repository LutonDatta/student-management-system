<?php namespace Config;

use CodeIgniter\Config\Services as CoreServices;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends CoreServices
{
    public static function BanglaDateLibrary($getShared = true){
        if (! $getShared){
            return new \App\Libraries\BanglaDate_library( time(), 0 );
        }
        return static::getSharedInstance('BanglaDateLibrary');
    } /* EOM */
    
    public static function AuthLibrary($getShared = true){
        if (! $getShared){
            return new \App\Libraries\Auth_library();
        }
        return static::getSharedInstance('AuthLibrary');
    } /* EOM */
    
    public static function ShowLinksLibrary($getShared = true){
        if (! $getShared){return new \App\Libraries\Show_links_library(); }
        return static::getSharedInstance('ShowLinksLibrary');
    } /* EOM */
    
    public static function LibraryItemsModel($getShared = true){
        if (! $getShared){
            return new \App\Models\Library_items_Model();
        }
        return static::getSharedInstance('LibraryItemsModel');
    } /* EOM */
    
    public static function LibraryItemsQuantitiesModel($getShared = true){
        if (! $getShared){
            return new \App\Models\Library_items_quantities_Model();
        }
        return static::getSharedInstance('LibraryItemsQuantitiesModel');
    } /* EOM */
    
    
    public static function StudentsModel($getShared = true){
        if (! $getShared) return new \App\Models\Students_Model();
        return static::getSharedInstance('StudentsModel');
    } /* EOM */
    
    public static function HandCashCollectionsModel($getShared = true){
        if (! $getShared) return new \App\Models\Hand_cash_collections_Model();
        return static::getSharedInstance('HandCashCollectionsModel');
    } /* EOM */
    
    
    public static function AcademicExamDateTimeModel($getShared = true){
        if (! $getShared) return new \App\Models\Academic_exam_date_time_Model();
        return static::getSharedInstance('AcademicExamDateTimeModel');
    } /* EOM */
    
    
    
    public static function CoursesClassesStudentsMappingModel($getShared = true){
        if (! $getShared) return new \App\Models\Courses_classes_students_mapping_Model();
        return static::getSharedInstance('CoursesClassesStudentsMappingModel');
    } /* EOM */
    
    public static function CoursesClassesMappingModel($getShared = true){
        if (! $getShared) return new \App\Models\Courses_classes_mapping_Model();
        return static::getSharedInstance('CoursesClassesMappingModel');
    } /* EOM */
    
    public static function CoursesModel($getShared = true){
        if (! $getShared) return new \App\Models\Courses_Model();
        return static::getSharedInstance('CoursesModel');
    } /* EOM */
    
    public static function ClassesAndSemestersModel($getShared = true){
        if (! $getShared) return new \App\Models\Classes_and_semesters_Model();
        return static::getSharedInstance('ClassesAndSemestersModel');
    } /* EOM */
    
    
    public static function OptionsModel($getShared = true){
        if (! $getShared) return new \App\Models\Options_Model();
        return static::getSharedInstance('OptionsModel');
    } /* EOM */
    
    
    public static function DailyAttendanceModel($getShared = true){
        if (! $getShared) return new \App\Models\Daily_attendance_Model();
        return static::getSharedInstance('DailyAttendanceModel');
    } /* EOM */
    
    public static function image(string $handler = null, $config = null, bool $getShared = true){
        if ($getShared){
            return static::getSharedInstance('image', $handler, $config);
        }
        if (empty($config)){
            $config = new \Config\Images();
        }
        $handler = is_null($handler) ? $config->defaultHandler : $handler;
        $class = $config->handlers[$handler];
        return new $class($config);       
    } /* EOM */
    
    
    public static function ExamResultsModel($getShared = true){
        if (! $getShared) return new \App\Models\Exam_results_Model();
        return static::getSharedInstance('ExamResultsModel');
    } /* EOM */
    
    public static function HostelAndRoomsModel($getShared = true){
        if (! $getShared){ return new \App\Models\Hostel_and_rooms_Model(); }
        return static::getSharedInstance('HostelAndRoomsModel');
    } /* EOM */
    
    
} //EOC
