<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT, 
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')){
    require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->setAutoRoute(false);

/**
 *-------------------------------------------------------------------------------------------*
 * ALLOW INSERT OF MIGRATION DATA 
 *-------------------------------------------------------------------------------------------*
 * Allow to create migration tables in development mode. Pass security key to validate 
 * migration request comes from valid source
 */
$routes->environment('development', function($routes){
    $routes->get('migration/migrate/(:any)',    'Migration::migrate/$1');
});


/**
 *-------------------------------------------------------------------------------------------*
 * PLATFORM WISE LANGUAGE CHANGE
 *-------------------------------------------------------------------------------------------*
 * Change language of this whole site using this URL. User can be from inside of this 
 * website or outside unlogged users. Change it based on their clicks.
 */
$routes->get('change/language', 'Platform\Language::change'); // Change language and redirect to source


$routes->get( '/', 'Dashboard::statistics', ['namespace' => 'App\Controllers\SchoolBack', 'filter' => 'auth' ]);


$routes->group('/', ['namespace' => 'App\Controllers\SchoolFront', 'filter' => 'auth'], function($routes){
    $routes->get( 'student/info/print/view',                  'Print_info::print_students_information'); // Print students registration information
//    $routes->get( 'admission/test/roll/numbers/print/view',   'Print_admission::test_roll_numbers'); // Print admission test serial/roll numbers, useful for seat plan
    $routes->get( 'print/admission/test/admit/card',          'Print_admit_card::print_admission_test_admit_card'); // Print admission test admit card
    $routes->get( 'print/student/admission/confirmation/form','Print_student_admission::confirmation_form'); // Print admitted students form
});

$routes->group('user', ['namespace' => 'App\Controllers\User' ], function($routes){
    $routes->get( 'logout', 'Logout::logout');
    $routes->get( 'login',  'Password_login::login'); 
    $routes->post('login',  'Password_login::login'); 
});

$routes->get( 'dashboard',  'Dashboard::statistics', ['namespace' => 'App\Controllers\SchoolBack', 'filter' => 'auth']);  


$routes->group('daily',['namespace' => 'App\Controllers\SchoolBack', 'filter' => 'auth'], function($routes){
    $routes->match(['get','post'],  'attendance/book',      'Daily_attendance::attendance_book' );
    $routes->match(['get','post'],  'attendance/book/view', 'Daily_attendance_viewer::view_attendance' );
});

$routes->group('student/area',['namespace' => 'App\Controllers\SchoolBack', 'filter' => 'auth'], function($routes){
        $routes->get( 'personal/printables',  'Profile_print::profile_printable_links');     
});

// Caution: auth filter will prevent to upload image. // ImagePicker can not support auth token 
$routes->post('admin/auf','Attachment::upload_file',['namespace' => 'App\Controllers\SchoolBack', 'filter' => 'auth']);     // upload images or files & return json object with attachment ID (int)

$routes->group('admin',['namespace' => 'App\Controllers\SchoolBack', 'filter' => 'auth'], function($routes){

    $routes->group('academic', function($routes){
            // Setup class, sessions, batches, morning/evening shifts etc
            $routes->match(['get','post'],  'setup',            'Academic::setup'); 
            $routes->match(['get','post'],  'course',           'Academic_course::setup'); 
            // Attach courses to classes. Which courses is read by which classes/Deartments/semesters
            $routes->match(['get','post'],  'course/distribution', 'Academic_course::distribution'); 

            $routes->match(['get','post'], 'exam/date/time',        'Academic_exam_date_time::date_time_setup'); 
            $routes->match(['get','post'], 'exam/routine',          'Academic_exam_routine::routine_setup'); 
            $routes->match(['get','post'], 'exam/results',          'Academic_exam_results::exam_results_publication'); 
            $routes->match(['get','post'], 'exam/results/publish',  'Academic_exam_results_publish::of_a_student_of_a_year'); 
            $routes->match(['get','post'], 'exam/results/delete',   'Academic_exam_results_delete::delete_exam_result'); 
            $routes->match(['get','post'], 'exam/results/view/own', 'Academic_exam_results_view_own::show_my_own_marksheet'); 
            $routes->match(['get','post'], 'exam/results/viewer',   'Exam_results_viewer::show_exam_results'); 
            $routes->match(['get','post'], 'exam/date/time/viewer', 'Exam_date_time_viewer::show_exam_date_time_to_students'); 
    });
    
    $routes->group('hostel', function($routes){
        $routes->match(['get','post'], 'rooms', 'Hostel::rooms_setup'); 
    });
    
    $routes->group('admission', function($routes){
            $routes->match(['get','post'], 'bulk/action',               'Bulk_action::admission_bulk_actions');
            $routes->match(['get','post'], 'edit/application/by/admin', 'Admission_edit_application::edit_admission_application');
            $routes->match(['get','post'], 'step/up/down',              'Admission_automation::step_up_step_down');
            $routes->match(['get','post'], 'student/list',              'Admitted_student_list::class_wise_list');
    });
    
    $routes->group('institution', function($routes){
            $routes->match(['get','post'],  'edit', 'Settings::institution_edit'); // Edit institution details like address, name, google coordinates etc
    });
    $routes->group('library', function($routes){
            $routes->match(['get','post'], '/',             'Library::show_library_items');
            $routes->match(['get','post'], 'distributions', 'Library::show_item_distributions');
            $routes->match(['get','post'], 'bin',           'Library::show_recycle_bin');
    });
    
    $routes->group('pg', function($routes){
            $routes->match(['get','post'],  'cash/in/hand/collection',              'Cash_collection_on_hand::money_received_by_teachers_from_students_on_hand'); // Schools might collect cash from their students without our need through online system
            $routes->match(['get','post'],  'cash/in/hand/collection/create/inv',   'Cash_collection_on_hand_inv::create_n_edit_cash_invoice'); 
            $routes->match(['get','post'],  'cash/in/hand/collection/mark/as/paid', 'Cash_collection_on_hand_inv_marking::mark_hand_cash_invoice_as_paid'); 
    });
    
});



$routes->group('api/v1', ['namespace' => 'App\Controllers\API\v1\SchoolFront', 'filter' => 'auth'], function($routes){
    $routes->match(['get','post'],  'users',                'Users::render_users');         // Renders list of users, generally search users by id or other user data
    $routes->match(['get','post'],  'classes',              'Academic::render_classes');   // Renders list of classes of root, or sub classes under parent class if parent id provide
    $routes->match(['get','post'],  'hostels',              'Hostel::render_rooms');   // Renders list of hostel rooms in jstree
    $routes->match(['get','post'],  'courses',              'Courses::render_courses');   // Pbulicly Renders list of courses
    $routes->match(['get','post'],  'viewable/sessions/yrs','Sessions_years::render_sessions_years');   // Publicly Renders list of available sessions/years
    $routes->post(                  'select2/view/books',   'Library::view_public_book_data');   
    $routes->post(                  'select2/view/books/qu','Library::view_public_book_quantity'); 
    $routes->post(                  'select2/view/users',   'Select2::view_public_user_data'); 
});


$routes->group('api/v1', ['namespace' => 'App\Controllers\API\v1\SchoolBack', 'filter' => 'auth' ], function($routes){
    // From students list page, admin can change roll number
    $routes->post('update/class/roll', 'Change_roll::update_roll_by_admin');
    
    // auth not supported. Check Auth: Teachers can take attendance. 
    $routes->group('daily/class/attendance', ['filter' => 'auth', 'namespace' => 'App\Controllers\API\v1\SchoolBack'], function($routes){
            $routes->post('show/students',    'Daily_attendance::load_class_student_rolls');
            $routes->post('change/status',    'Daily_attendance_update::change_student_attendance_status');
            $routes->post('show/history',     'Daily_attendance_history::attendance_history_viewer'); // Admin can show history of attendance
    });

    /**
    |-------------------------------------------------------------------------------------------*
    | UPLOAD AVATAR, SIGN, COVER PHOTO - BY LOGGED IN USERS (SCHOOL ID NOT REQUIRED)
    |-------------------------------------------------------------------------------------------*
    | ImagePicker is being used in this site globally as Avatar management software. 
    | Show/upload/edit user avatar using three library. Users must be logged in to do that.
    |
    | CAUTION: auth filter will prevent to upload image. // ImagePicker can not support auth token
    */
    $routes->group('ip', ['namespace' => 'App\Controllers\User', 'filter' => 'auth' ], function($routes){
       /** For admission, teachers need to upload thumb/sign for students, directly upload, no table needed. **/
       $routes->match(['get','post'],  'upload/student/thumb/by/teacher',   'Admission_pictures::upload_student_thumb_by_teacher');  
       $routes->match(['get','post'],  'upload/student/sign/by/teacher',    'Admission_pictures::upload_student_sign_by_teacher');  
    });


    /**
     |-------------------------------------------------------------------------------------------*
     | DASHBOARD STATISTICS INFO
     |-------------------------------------------------------------------------------------------*
     | Primarily these info is not so critical. So no need to add any security. But later we will implement
     | filter to show only to admins. We will apply Auth Filter here to show only logged in admins only)
     */
     $routes->group('admin', function($routes){
        $routes->post('dashboard/statistics/books/count',           'Dashboard_statistics_info::total_books_count');   
        $routes->post('dashboard/statistics/books/quantity/count',  'Dashboard_statistics_info::total_books_quantity_count');
        $routes->post('dashboard/statistics/classes/count',                 'Dashboard_statistics_info::total_classes_count');   
        $routes->post('dashboard/statistics/courses/count',                 'Dashboard_statistics_info::total_courses_count');   
        $routes->post('dashboard/statistics/total/student/count',       'Dashboard_statistics_info::total_total_student_count');   
        $routes->post('dashboard/statistics/invoice/paid/count',        'Dashboard_statistics_info::total_invoice_paid_count');   
        $routes->post('dashboard/statistics/invoice/unpaid/count',        'Dashboard_statistics_info::total_invoice_un_paid_count');   
     });
});


