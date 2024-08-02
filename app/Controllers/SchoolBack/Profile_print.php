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


class Profile_print extends BaseController {
       
    public function profile_printable_links(){ 
        $data               = $this->data;
        $data['title']      = 'View Profile Printable Links';
        $data['pageTitle']  = myLang('Print Necessary Pages','প্রয়োজনীয় পেজগুলো প্রিন্ট করুন');
        //$data['userProfile']= service('AuthLibrary')->getUserRole();
        $data['loadedPage'] = 'personal_info';
        
        if($this->request->getGet('editinfotype') === 'showPaymentLinks' ){
            $template = 'show-payment-links';
            $classTo = service('ClassesAndSemestersModel')->withDeleted()->find(intval($this->request->getGet('apply_to_class_id')));
            if(is_object($classTo)){
                if(service('AdmissionStartEndSetupModel')->is_admission_going_on(intval($classTo->fcs_id)) ){
                    $classStartEnd              = service('AdmissionStartEndSetupModel')->find(intval($classTo->fcs_id));
                    $data['selectedClass']      = (object) array_merge( (array) $classTo, (array) $classStartEnd);
                }else{
                    $data['display_msg'] = get_display_msg(anchor('admission','Admission of this class is not going on. Click here to visit admission openings page and select correct class.'),'danger');  
                }
            }else{
                $data['display_msg'] = get_display_msg(anchor('admission',myLang('Valid class ID not found. Click here to visit admission openings page and select correct class.','সঠিক ক্লাশ পাওয়া যায়নি। অনুগ্রহ করে এখানে ক্লিক করে ভর্তিযোগ্য ক্লাশ নির্বাচন করুন। ')),'danger');  
            }
            // Show last five transactions to the student, we expect there should be less the 10 application + 10 admission fee payments.
            $data['mainFee'] = service('AdmissionMainFeeRegisterModel')->select('amf_id,amf_class_id,amf_inserted_at,amf_amt_total,amf_is_paid,amf_txn_num,amf_updated_at,amf_paid_by_n_for')->orderBy('amf_id DESC')->withDeleted()->where('amf_paid_by_n_for', service('AuthLibrary')->getLoggedInUserID())->findAll(5,0);
            $data['applFee'] = service('AdmissionApplicationFeeRegisterModel')->select('aaf_id,aaf_class_id,aaf_inserted_at,aaf_amt_total,aaf_is_paid,aaf_txn_num,aaf_updated_at,aaf_paid_by_n_for')->orderBy('aaf_id DESC')->withDeleted()->where('aaf_paid_by_n_for', service('AuthLibrary')->getLoggedInUserID())->findAll(5,0); 
        }else{
            $template = 'profile-printable-pages';
        }
        
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view("SchoolBackViews/personal/$template", $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } /* EOM */
    
    
} /* EOC */
