<?php namespace App\Controllers\SchoolFront;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */



/**
 * Admin can print students admit card.
 * Student can print his own admit card.
 */
class Print_admit_card extends BaseController {
    
    public function print_admission_test_admit_card(){
        session_write_close(); 
        
        $data                   = $this->data;
        $data['title']          = "Print Admit Card";
        $data['metaDescription']= "Print Admit Card for Online admission ";
        $data['metaKeywords']   = 'Print Admit Card for Online admission powered by Ultra School';
        $user_id                = intval($this->request->getGet('admit_card_of')); /* Print information of this user */
        $apply_to_class_id      = intval($this->request->getGet('apply_to_class_id')); /* Admit card of this Admission Opening */
        
        
        $data['udr'] = service('UserStudentsModel')->find($user_id);
        
        
        $data['adOpenings']     = service('ClassesAndSemestersModel')->paginate(20, 'print_view_ao');
        $data['adOpeningsPgr']  = service('ClassesAndSemestersModel')->pager->links('print_view_ao');
        
        $data['printingCls']     = service('ClassesAndSemestersModel')->withDeleted()->withDeleted()->find($apply_to_class_id);
        $data['adOpenings'][]   = $data['printingCls']; // Our selected AO might not be in the retrieved list. Add it here to show properly in the dropdown box.
        
        
        if( ! is_object($data['udr'])){
            $data['display_msg'] = get_display_msg('Invalid User ID. We have not find any user associated with this ID.','danger');
        }elseif( ! is_object($data['printingCls'])){
            $data['display_msg'] = get_display_msg(lang('Extra.extra_ao_not_provided_select_from_below'),'danger');
        }else{
            $data['application']= service('CoursesClassesStudentsMappingModel')
                        //->withDeleted() // groupBy error 
                        ->where([ 
                            'scm_u_id'          => $data['udr']->student_u_id, 
                            'scm_class_id'      => $data['printingCls']->fcs_id,
                            'scm_session_year'  => urldecode(service('request')->getGet('sess_year'))
                        ])->first();
            if( ! is_object($data['application'])){
                $erEn = 'No valid matched application found. May be you have not applied to this class. It indicate that the user ID: ' . esc(service('request')->getGet('admit_card_of')) . ' has no application to the class ID: ' . service('request')->getGet('apply_to_class_id');
                $data['display_msg'] = get_display_msg($erEn,'danger');
            }else{
                $data['applicationClass'] = service('ClassesAndSemestersModel')->get_single_class_with_parent_label($data['application']->scm_class_id);
            }
        }
        
        return view('SchoolFrontViews/print-info/print-admit-card', $data );
    } /* EOM */
   
} /* EOC */
