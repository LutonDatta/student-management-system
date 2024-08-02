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


class Settings extends BaseController {
    
    /**
     * Edit institution basic data.
     */
    public function institution_edit(){
        $save_resp          = $this->save_settings();
        if(is_object($save_resp)) return $save_resp;      
        
        $data               = $this->data; // We have some pre populated data here
        $data['pageTitle']  = 'Institution Basic Information';
        $data['title']      = 'Institution Basic Information';
        $data['loadedPage'] = 'instBasicInfo';
                        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/settings/institution_edit', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    }
    
    private function save_settings(){
        if( $this->request->getPost('submitBtnInstSett') == 'instInfoBasic'){
            $instTaglineEn = strval( $this->request->getPost('instTaglineEn'));
            $instNameEn = strval( $this->request->getPost('instNameEn'));
                    
            update_option('instTaglineEn', strip_tags($instTaglineEn));
            update_option('instNameEn', strip_tags($instNameEn));
            
            $msg = get_display_msg("Basic information of institution has been updaterd.",'success');
            @session_start();
            return redirect()->to(base_url('admin/institution/edit?showTab=insInfo'));
        }
        
        if( $this->request->getPost('submitBtnInstSett') == 'about'){
            $schEm = strval( $this->request->getPost('schOffEmailAddr'));
            $schPh = strval( $this->request->getPost('schOffPhonNum'));
            $schEi = strval( $this->request->getPost('schOffSchEiin'));
            $count = strval( $this->request->getPost('schOffAddrCountry'));
            $distr = strval( $this->request->getPost('schOffAddrDistrict'));
            $postC = strval( $this->request->getPost('schOffAddrLine1'));
            $zipCo = strval( $this->request->getPost('schOffAddrPostCode'));
                        
            if(filter_var($schEm, FILTER_VALIDATE_EMAIL)){
                update_option('schOffEmailAddr',$schEm);
            }
            update_option('schOffPhonNum', strip_tags($schPh));
            update_option('schOffSchEiin', strip_tags($schEi));
            
            // make sure country/district/address does not have any kind of html tag. Tags will break page
            update_option('schOfficialAddressCountry', strip_tags($count));
            update_option('schOfficialAddressDistrict', strip_tags($distr));
            update_option('schOfficialAddressPost', strip_tags($postC));
            update_option('schOfficialAddressPostCode', strip_tags($zipCo));
            
            $msg = get_display_msg("Information about institution has been updaterd.",'success');
            @session_start();
            return redirect()->to(base_url('admin/institution/edit?showTab=ofAdr'))->with('display_msg',$msg);
        }
        
    } /* EOM */
    
    
} /* EOC */
