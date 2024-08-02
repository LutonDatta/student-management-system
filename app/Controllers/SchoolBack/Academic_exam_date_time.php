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


class Academic_exam_date_time extends BaseController {
    
    
    /**
     * Which courses/book is read by which semesters/class is mapped from here.
     */
    public function date_time_setup(){
        $data                   = $this->data;
        $data['pageTitle']      = lang('Menu.exam_date_time');
        $data['title']          = lang('Menu.exam_date_time');
        $data['loadedPage']     = 'exam_date_time'; // Used to automatically expand submenu and add active class 
        
        $atchCrsToCls = $this->save_submitted_exam_date_time();
        if(is_object($atchCrsToCls)){return $atchCrsToCls;}else{$data = array_merge($data, $atchCrsToCls);}
        
        $rmvCrsFrmCls = $this->delete_exam_date_time();
        if(is_object($rmvCrsFrmCls)){return $rmvCrsFrmCls;}else{ $data = array_merge($data, $rmvCrsFrmCls); }
        
        /* Reduce load in few page load, as they are not needed at the time of page submit. */
        $data['allClassItems']  = service('ClassesAndSemestersModel')->get_classes_with_parent_label(false, false); // Allow to admit students to parent class like six if it has section A and B etc
        $data['updateExamDT']   = service('AcademicExamDateTimeModel')->withDeleted()->find(intval($this->request->getGet('updateExamDateTimeID')));
        $data['xmDateTimeList'] = service('AcademicExamDateTimeModel')->withDeleted()->orderBy('axdts_inserted_at DESC')->paginate(10, 'exdtpgr');
        $data['xmDateTimeListPgr'] = service('AcademicExamDateTimeModel')->pager->links('exdtpgr');
        
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic-course/exam/exam-date-time-setup', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } /* EOM */
    
    
    
    private function save_submitted_exam_date_time(){
        if($this->request->getPost('save_exam_date_time') !== 'yes') return [];
        
        $clas = service('ClassesAndSemestersModel')->withDeleted()->whereIn('fcs_id', (array)($this->request->getPost('exam_class_id')) )->findColumn('fcs_id');
        if( !is_array($clas) OR count($clas) < 1 ){
            @session_start();
            return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang('Invalid class ID selected, or you have no authority to use it.','সঠিক ক্লাশ আইডি পাওয়া যায় নি। অথবা আপনি যে ক্লাশ নির্বাচন করেছেন তা সঠিক নয়।'),'danger'));
        }
        
        
        // We will get an object, if user try to update, update based on ID
        $updateExamID   = service('AcademicExamDateTimeModel')->withDeleted()->find(intval($this->request->getPost('updateExamID')));
        
        $exam_session   = strval($this->request->getPost('exam_session'));
        $exam_session   = esc(preg_replace( "/\s+/", "", $exam_session )); // Session shouldn't have any spaces, to prevent errors.
        
        $saveData = [ 
            'axdts_class_id'        => serialize($clas),
            'axdts_session_year'    => $exam_session, 
            'axdts_type'            => strval($this->request->getPost('exam_type')),
            'axdts_exam_starts_at'  => strval($this->request->getPost('exam_starts')),
            'axdts_exam_ends_at'    => strval($this->request->getPost('exam_ends')),
        ];
        
        if(is_object($updateExamID)){
            if(service('AcademicExamDateTimeModel')->update($updateExamID->axdts_id, $saveData)){
                @session_start();
                return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang('Examination date time determination updated successfully.','পরীক্ষার দিন তারিখ সফলভাবে পরিবর্তন করা হয়েছে।'),'success'));
            }else{
                return ['errors' => service('AcademicExamDateTimeModel')->errors()];
            }
        }else{
            if(service('AcademicExamDateTimeModel')->insert($saveData)){
                @session_start();
                return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang('Examination date time determined successfully.','পরীক্ষার দিন তারিখ সফলভাবে ধ্যার্য করা হয়েছে।'),'success'));
            }else{
                return ['errors' => service('AcademicExamDateTimeModel')->errors()];
            }
        } 
    } // EOM
    
    private function delete_exam_date_time(){
        $delPermanent_ExamDateTimeID    = intval($this->request->getGet('delPermanent_ExamDateTimeID'));
        $recycle_ExamDateTimeID         = intval($this->request->getGet('recycle_ExamDateTimeID'));
        $remTemporarily_ExamDateTimeID  = intval($this->request->getGet('remTemporarily_ExamDateTimeID'));
        
        /* Just a simple verification to reduce unnecessary mySql query to reduce server load. */
        if( ( $delPermanent_ExamDateTimeID + $recycle_ExamDateTimeID + $remTemporarily_ExamDateTimeID ) < 1 ){
            return []; // Simple page load, no request to delete/remove/recycle
        }
        
        
        // If it is object, requested to delete TEMPORARLY
        $delTemporily = service('AcademicExamDateTimeModel')->select('axdts_id')->withDeleted()->find($remTemporarily_ExamDateTimeID);
        if(is_object($delTemporily)){
            if(service('AcademicExamDateTimeModel')->delete($delTemporily->axdts_id)){
                @session_start();
                return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang('Success: Item deleted successfully.','সফলভাবে মুছে ফেলা হয়েছে।'),'success'));
            }else{
                $errors = service('AcademicExamDateTimeModel')->errors();
                $err = is_array($errors) ? implode(', ', $errors) : '';
                @session_start();
                return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang("Error: Failed to remove item. $err","তথ্য মুছা সম্ভব হয় নি। $err"),'danger'));
            }
        }
        
        
        // If it is object, requested to recycle deleted item
        $recycle = service('AcademicExamDateTimeModel')->select('axdts_id')->withDeleted()->find($recycle_ExamDateTimeID);
        if(is_object($recycle)){
            if(service('AcademicExamDateTimeModel')->update($recycle->axdts_id, ['axdts_deleted_at'=>null])){
                @session_start();
                return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang('Success: Item recycled successfully.','সফল: আইটেমটি সফলভাবে পুনরুদ্ধার করা হয়েছে।'),'success'));
            }else{
                $errors = service('AcademicExamDateTimeModel')->errors();
                $err = is_array($errors) ? implode(', ', $errors) : '';
                @session_start();
                return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang("Error: Failed to recycle item. $err","ব্যার্থ: আইটেমটি পুনরুদ্ধার করা সম্ভব হয় নি। $err"),'danger'));
            }
        }
        
        // If it is object, requested to delete permanently
        $delPermanent = service('AcademicExamDateTimeModel')->select('axdts_id,axdts_deleted_at')->onlyDeleted()->find($delPermanent_ExamDateTimeID);
        if(is_object($delPermanent)){
            if($delPermanent->axdts_deleted_at){
                if(service('AcademicExamDateTimeModel')->delete($delPermanent->axdts_id, TRUE)){
                    @session_start();
                    return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang('Success: Item permanently deleted.','স্থায়ীভাবে মুছে ফেলা হয়েছে।'),'success'));
                }else{
                    $errors = service('AcademicExamDateTimeModel')->errors();
                    $err = is_array($errors) ? implode(', ', $errors) : '';
                    @session_start();
                    return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang("Error: Attempt to delete permanently failed. $err","স্থায়ীভাবে মুছে ফেলার জন্য চেষ্টা করা হয়েছে। যা সফল হয়নি। $err"),'danger'));
                }
            }else{
                @session_start();
                return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang('To delete any item permanently, first remove it temporarily.','স্থায়ী ভাবে মুছে ফেলার জন্য আগে অস্থায়ি ভাবে মুছে নিন।'),'success'));
            }
        }
                
        @session_start();
        return redirect()->to(base_url('admin/academic/exam/date/time'))->with('display_msg',get_display_msg(myLang('ID that you have provided is not valid.','আপনি যে আইটেমটি মুছেফেলতে বা পুনরুদ্ধার করতে চেয়েছেন তা সঠিক নয়। '),'success'));
    } // EOM

} // EOC


