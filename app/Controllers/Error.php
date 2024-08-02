<?php namespace App\Controllers;

class Error extends BaseController {
    
    
    /**
    | --------------------------------------------------------------------
    | PLATFORM LEVEL ERROR MESSAGES
    | --------------------------------------------------------------------
    | Using this function we can show several error messages. We can show
    | 404 errors, School not found error. CLOSER function can shows "Can't 
    | find a route for 'x/x/x/x'". So don't use CLOSURE.
    |
    | throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
    | Note: Store error messages in session.
    */
    public function showErrors(){ 
        echo view('errors/html/error_404');
    } // EOM
} // EOC
