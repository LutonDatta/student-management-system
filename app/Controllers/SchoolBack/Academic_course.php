<?php namespace App\Controllers\SchoolBack;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */

use App\Models\Courses_Model;
use App\Models\Classes_and_semesters_Model;
use App\Models\Courses_classes_mapping_Model;

class Academic_course extends BaseController {
    
    /**
     * Add/update sessions, classes, batches, morning/evening shifts etc.
     */
    public function setup(){
        $update_id          = intval($this->request->getGet('edit_id'));
        $data               = $this->data; // We have some pre populated data here
        
        
        $save_form = $this->save_form($update_id);
        if(is_object($save_form)){ return $save_form; }else{ $data = array_merge($data, $save_form); }
        
        $updateId           = intval(isset($data['insertID']) ? $data['insertID'] : $update_id);
        
        $delete_courses = $this->delete_requested_course($updateId);
        if(is_object($delete_courses)){
            return $delete_courses;
        }else{
            $data               = array_merge($data, $delete_courses);
        }
        $data['pageTitle']      = lang('Admin.academic_course');
        $data['title']          = lang('Admin.academic_course');
        $data['formTitle']      = $update_id < 1 ? 'Add New Course' : 'You are updating a Course';
        $data['loadedPage']     = 'academic_course'; // Used to automatically expand submenu and add active class 
        
        $data['submit_form_to'] = base_url('admin/academic/course' . ($updateId > 0 ? '?edit_id=' . $updateId : ''));
        
        $data['allItems']       = service('CoursesModel')->orderBy('courses.co_id DESC')->paginate(15, 'courses');
        $data['pager']          = service('CoursesModel')->pager;
        $data['updateItemData'] = service('CoursesModel')->find($update_id);
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic-course/course-setup', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    }
    
    private function delete_requested_course( int $update_id){
        $data = [];
        // Delete if csrf validation is ok and requested to delete item
        $item = intval($this->request->getPostGet('del_cid')); 
        if($item < 1 ) return $data;
        
        
        if($this->request->getPostGet('acCourses_delete_submit') == 'yes'){
            
            // Is this course being used in students admission applications table? Foreign key check will generate error. So check it.
            service('CoursesClassesStudentsMappingModel')->select('scm_id')
                    ->groupStart()
                        ->where('scm_course_1', $item);
                        for($i = 2; $i <= 15; $i++ ){ 
                            service('CoursesClassesStudentsMappingModel')->orWhere("scm_course_{$i}", $item); 
                        }
                        for($i = 1; $i <= 5; $i++ ){ 
                            service('CoursesClassesStudentsMappingModel')->orWhere("scm_course_op_{$i}", $item);
                        }                    
            $usedInColumn = service('CoursesClassesStudentsMappingModel')->groupEnd()->countAllResults();                
            
            if($usedInColumn > 0){
                $data['display_msg'] = get_display_msg("You can not delete this course because $usedInColumn student(s) applied to a class and selected this course. Please delete applied/admitted students who is using this courses.",'danger w-100');
            }elseif(service('CoursesClassesMappingModel')->where(['ccm_course_id'=>$item])->countAllResults() > 0 ){
                $data['display_msg'] = get_display_msg('You can not delete this course because it is being used by a class.','danger w-100');
            }else{
                $delCo      = service('CoursesModel')->delete($item, true); // false on failure or obj. Object in case of foreign key error, so check erros.
                $delCoErs   = array_filter((array) service('CoursesModel')->errors());
                        
                if( ! (is_bool($delCo) OR count($delCoErs) > 0) ){
                    
                    \CodeIgniter\Events\Events::trigger('course_deleted', $item, service('CoursesModel')->affectedRows());
                    @session_start(); // Reverse session_write_close?
                    return redirect()->to(base_url('admin/academic/course'))->with('display_msg',get_display_msg(lang('Rw.successfully_deleted_courses'),'success w-100'));
                }else{
                    $display_msg = get_display_msg('Unable to delete, please try again. ' . implode(', ', $delCoErs),'danger w-100');
                    @session_start(); // Reverse session_write_close?
                    return redirect()->to(base_url('admin/academic/course'))->with('display_msg',$display_msg);
                }
            }
        }else{
            $display_msg = get_display_msg(lang('Rw.delete_csrf_verification_failed'),'danger w-100');
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url('admin/academic/course'))->with('display_msg',$display_msg);
        }
        return $data;
    }
    
       
    
    private function save_form( int $update_id = 0){
        $data = [];
        if($this->request->getPost('acCourses_submit') !== 'yes') return $data;
        
        $values = [
            'co_title'    => $this->request->getPost('ac_title'), 
            'co_code'     => $this->request->getPost('ac_code'),
            'co_excerpt'  => $this->request->getPost('ac_excerpt'), 
        ];
        if($update_id > 0 ){
            if(service('CoursesModel')->update($update_id, $values )){
                $display_msg = get_display_msg(lang('Rw.item_updated_successfully'),'success');
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/academic/course?edit_id=$update_id"))->with('display_msg',$display_msg);
            }else{
                $display_msg = get_display_msg('Failed to update item: ' . implode(',',service('CoursesModel')->errors()),'danger');
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url('admin/academic/course'))->with('display_msg',$display_msg);
            }
        }else{
            if(service('CoursesModel')->insert( $values )){
                $insertID = service('CoursesModel')->insertID();
                
                \CodeIgniter\Events\Events::trigger('course_inserted', $insertID, $values);
                
                $insMsg = myLang(
                        "Course item added. New Course item has been added successfully. New ID: $insertID",
                        "কোর্স যুক্ত হয়েছে। নতুন কোর্স সফলভাবে সংযুক্ত করা হয়েছে। নতুন কর্সের আইডি নম্বর হল: " . esc($insertID)
                );
                $display_msg = get_display_msg( $insMsg, 'success');
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url('admin/academic/course'))->with('display_msg',$display_msg);
            }else{
                $errors = esc(implode(' ',service('CoursesModel')->errors()));
                $display_msg = get_display_msg("Failed to add new item. $errors" ,'danger');
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url('admin/academic/course'))->with('display_msg',$display_msg);
            }
        }
        $data['errors'] = service('CoursesModel')->errors();
        return $data; 
    }
    
    
    /**
     * Which courses/book is read by which semesters/class is mapped from here.
     */
    public function distribution(){
        $data                   = $this->data;
        $class_id               = intval($this->request->getPostGet('class_id'));
        $sessi_id               = strval($this->request->getPostGet('cls_wise_class_session'));
        $sessi_id               = esc(preg_replace( "/\s+/", "", $sessi_id )); // Session shouldn't have any spaces, to prevent errors.
        
        $atchCrsToCls = $this->attach_courses_to_classes($class_id, $sessi_id);
        if(is_object($atchCrsToCls)){return $atchCrsToCls;}else{$data = array_merge($data, $atchCrsToCls);}
        
        $rmvCrsFrmCls = $this->remove_courses_from_classes($class_id, $sessi_id);
        if(is_object($rmvCrsFrmCls)){return $rmvCrsFrmCls;}else{ $data = array_merge($data, $rmvCrsFrmCls); }
        
        $data['pageTitle']      = lang('Admin.academic_course_selection');
        $data['title']          = lang('Admin.academic_course_selection');
        $data['formTitle']      = $class_id < 1 ? 'Select a Class' : 'You are updating class courses';
        $data['loadedPage']     = 'academic_course_dist'; // Used to automatically expand submenu and add active class 
        $data['allClassItems']  = service('ClassesAndSemestersModel')->get_classes_with_parent_label(false, false); // Allow to admit students to parent class like six if it has section A and B etc
        $data['submit_form_to'] = base_url("admin/academic/course/distribution?class_id={$class_id}&cls_wise_class_session=$sessi_id");
        $data['loaded_class']   = $class_id;
        $data['loaded_session'] = $sessi_id;
        $data['searchCourseTxt']= $this->request->getGet('searchCourseTxt');
        
        if($class_id > 0 ){
            // Allow users to search course
            if(strlen($data['searchCourseTxt']) > 0 ){
                service('CoursesModel')
                        ->like('co_title', $data['searchCourseTxt'])
                        ->orLike('co_code',$data['searchCourseTxt'])
                        ->orLike('co_excerpt',$data['searchCourseTxt']);
            }
            service('CoursesModel');
            
            $data['allCourseItems']     = service('CoursesModel')->orderBy('courses.co_id DESC')->paginate(15,'courses_selector');
            $data['allCourseItems_pgr'] = service('CoursesModel')->pager->links('courses_selector');
            // Allow to admit students to class six if it has section A and B etc, we need to allow to add subject to any class either it is parent or not.
            //$data['selectedClass']      = service('ClassesAndSemestersModel')->has_child_item_of_faculty($class_id) ? 0 : service('ClassesAndSemestersModel')->find($class_id);
            $data['selectedClass']      = service('ClassesAndSemestersModel')->find($class_id);
            if( ! is_object($data['selectedClass'])){
                $data['display_msg']    = get_display_msg( 'Invalid class ID selected. Please check your URL or try again.', 'danger');
            }
            $data['currentCourseItems'] = [];
            $old = service('CoursesClassesMappingModel')
                    ->select('ccm_course_id')
                    ->where([ 'ccm_class_id' => $class_id, 'ccm_year_session'  => $sessi_id ])
                    ->findAll(50,0);
            foreach( $old as $o ) $data['currentCourseItems'][] = $o->ccm_course_id;
            if(is_array($data['currentCourseItems']) AND count($data['currentCourseItems']) > 0){
                // Only query if there is courses selected we can retrieve
                $data['oldAttachedItems'] = service('CoursesModel')
                        ->where([ 'ccm_class_id'=>$class_id, 'ccm_year_session'  => $sessi_id ])
                        ->whereIn('co_id', $data['currentCourseItems'])
                        ->join('courses_classes_mapping','courses_classes_mapping.ccm_course_id = courses.co_id')
                        ->paginate(35,'old_selected_courses'); // Admin can select many with pagination but student can only select 15 courses with optionals
            }
            $data['allCoursePager']     = service('CoursesModel')->pager->links('old_selected_courses');
        }else{
            if(strlen($data['searchCourseTxt']) > 0 ){
                $data['display_msg']    = get_display_msg( 'You are trting to search but have not selected any class. Please select a class and search again.','danger');
            }
        }
        
        // Show already saved class mapped with courses, to edit easilty
        $data['already_saved_class_wise_courses'] = $this->get_classes_names_with_parent_label();
        
                
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic-course/course-dist-select-class', $data);
        echo view('SchoolBackViews/academic-course/course-dist', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } // EOM
    
    private function get_classes_names_with_parent_label(){
        $to     = service('CoursesClassesMappingModel')->db->DBPrefix . 'courses_classes_mapping';
        $t      = service('CoursesClassesMappingModel')->db->DBPrefix . 'classes_and_semesters';
        $sql    =  "SELECT
                    MAX(ti.ccm_class_id) AS ccm_class_id, 
                    MAX(ti.ccm_year_session) AS ccm_year_session,
                    MIN(t.fcs_id), 
                    MIN(t.fcs_title) AS fcs_title, 
                    MIN(t.fcs_parent) AS fcs_parent, 
                    MIN(t4.fcs_title) AS title_4, 
                    MIN(t3.fcs_title) AS title_3, 
                    MIN(t2.fcs_title) AS title_2, 
                    MIN(t1.fcs_title) AS title_1
                FROM $to AS ti
                LEFT JOIN $t AS t  ON ti.ccm_class_id = t.fcs_id
                LEFT JOIN $t AS t4 ON t.fcs_parent = t4.fcs_id
                LEFT JOIN $t AS t3 ON t4.fcs_parent = t3.fcs_id
                LEFT JOIN $t AS t2 ON t3.fcs_parent = t2.fcs_id
                LEFT JOIN $t AS t1 ON t2.fcs_parent = t1.fcs_id
                ";
        $sql .= " GROUP BY ti.ccm_class_id, ti.ccm_year_session "; // Get distinct rows based on these two column
        
        /**
         * Generally a school needs only 50 classes max and 200 max including parent items.
         * But they may add may classes. If thousands of rows added to the database, as 
         * we are loading all of them, it may cause memory limit error. To prevent memory
         * leak error we should add LIMIT to the query. Limit should be 1000 to 3000 is good.
         */
        //$sql .= " ORDER BY ti.ccm_id DESC ";
        
        $page = intval(service('request')->getGet('page_saved_class_edit')); 
        $page = $page < 1 ? 1 : $page;
        $perP = 20;
        $offs = ($page * $perP) - $perP;
        
        $sql .= " LIMIT $offs, $perP ";
        
        $classes = service('CoursesClassesMappingModel')->db->query($sql)->getResult();
        $simplified_class_name = [];
        foreach( $classes as $cls ){
            $title = (is_string($cls->title_1) AND strlen($cls->title_1) > 0) ? $cls->title_1 . ' -> ' : '';
            $title .= (is_string($cls->title_2) AND strlen($cls->title_2) > 0) ? $cls->title_2 . ' -> ' : '';
            $title .= (is_string($cls->title_3) AND strlen($cls->title_3) > 0) ? $cls->title_3 . ' -> ' : '';
            $title .= (is_string($cls->title_4) AND strlen($cls->title_4) > 0) ? $cls->title_4 . ' -> ' : '';
            $title .= $cls->fcs_title;
            $cls->simple_title = $title;
            $simplified_class_name[] = $cls;
        }
        return [
            'data' => $simplified_class_name,
            'count'=> service('CoursesClassesMappingModel')
                ->select('GROUP_CONCAT(ccm_id)')
                ->groupBy(['ccm_class_id','ccm_year_session'])
                ->countAllResults(),
        ];
    }

    
    /**
     * Add courses to class. One class might have many course. At the time of up-gradation, we need to remove cows at we had 
     * already. And add newly added courses. Never delete all rows. It will increase number in auto incremental field.
     * @param int $class_id 
     * @return array Array or error or success message.
     */
    private function attach_courses_to_classes( int $class_id, string $session_year = '' ){
        $r = [];
        if($this->request->getPost('acCouAtt_submit') !== 'yes') return $r;
            
        $new_courses = $this->request->getPost('coClasses');
        if( ! is_array( $new_courses ) OR count( $new_courses ) < 1 ){
            $msgx = get_display_msg('No course submitted. You must select at least one course','danger'); // Atleast one course must be attached to a class
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/course/distribution?class_id={$class_id}&cls_wise_class_session=" . esc($session_year)))->with('display_msg', $msgx);
        }
        
        $course_type = ($this->request->getPost('course_type') === 'mandatory') ? '1' : '0';
        if( count( $new_courses) < 1 ){
            return ['display_msg' => get_display_msg('No new course to add.','danger')];
        } 

        // New courses
        $saveErros = [];
        foreach( $new_courses as $index => $cs ){
            $svDtta = [
                'ccm_is_compulsory' => $course_type,
                'ccm_class_id'      => $class_id,
                'ccm_course_id'     => $cs,
                'ccm_year_session'  => $session_year,
            ];
            // We are in loop, but don't worry. It will never happen until hacker trys to do it. Return first time when found.
            if(service('CoursesClassesMappingModel')->where($svDtta)->countAllResults()){
                return ['display_msg' => get_display_msg('Duplicate: This course is already added.','danger')];
            }
            $saveClsMps = service('CoursesClassesMappingModel')->save($svDtta);
            if( ! $saveClsMps){
                $saveErros = service('CoursesClassesMappingModel')->errors();
            }
        }
        if(count($saveErros) >0){
            $r['display_msg'] = get_display_msg(implode(',',$saveErros),'danger');
        }else{
            $msgx = get_display_msg('Course added successfully.','success');
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/course/distribution?class_id={$class_id}&cls_wise_class_session=" . esc($session_year)))->with('display_msg', $msgx);
        }
        return $r;
    } // EOM
    
    private function remove_courses_from_classes( int $class_id, string $session_year = '' ){
        $r = [];
        if($this->request->getPost('acCxd_sub') !== 'yes') return $r;
            
        $new_courses = $this->request->getPost('coClsDelete');
        if( ! is_array( $new_courses ) OR count( $new_courses ) < 1 ){
            // Atleast one course must be selected to delete
            $msgx = get_display_msg('No course submitted. You must select at least one course to remove from the class.','danger');
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/course/distribution?class_id={$class_id}&cls_wise_class_session=" . esc($session_year)))->with('display_msg', $msgx);
        }
        // A class can not have more then 15 courses based on our table column number
        service('CoursesClassesMappingModel')
                ->where([
                    'ccm_class_id'      => $class_id,
                    'ccm_year_session'  => $session_year,
                ])
                ->whereIn('ccm_course_id', $new_courses)
                ->delete_permanently();
        
        $msgx = get_display_msg('Course removed successfully.','success');
        @session_start(); // Reverse session_write_close?
        return redirect()->to(base_url("admin/academic/course/distribution?class_id={$class_id}&cls_wise_class_session=" . esc($session_year)))->with('display_msg', $msgx);
    } // EOM

} // EOC


