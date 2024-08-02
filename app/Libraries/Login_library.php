<?php namespace App\Libraries;



class Login_library {
    
    public function storeLoginSessionData(){
        @session_start(); // Reopen session to write data in session
        $set = session()->set([
            'logged_in' => TRUE,
            'student_u_id'      => 0,
            'u_email'   => getSSMSAdminEmail(),
        ]);
    } /* EOM */
    
} // EOC
