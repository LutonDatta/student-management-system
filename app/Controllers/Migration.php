<?php namespace App\Controllers;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */

use CodeIgniter\Controller;

class Migration extends Controller {
    /**
     * We are not using baseController. Unlogged users can not access BaseController. 
     * It might create redirect loop. So we are using CodeIgniter\Controller;
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger){
        parent::initController($request, $response, $logger);
    }
    
    public function migrate( $key ){
        // Apply simple password before action. It is safe because migration will not work twice.
        if( (string)$key === (string) env('migrationSaftyKey')){
            $migrate = \Config\Services::migrations();
            try{
                $migrate->setNamespace('App')->latest();
            }catch (\CodeIgniter\Database\Exceptions\DatabaseException $e){
                $dbxName = db_connect()->getDatabase();
                return view('migration/migrate-error', [ 'message'=> get_display_msg("Have you created database <strong>'{$dbxName}'</strong> already? Please create database if you have not.<br> Error: (".$e->getCode().') ' . $e->getMessage() . ' <br>'.$e->getTraceAsString(),'danger') ] );
            }catch (\Exception $e){
                return view('migration/migrate-error', ['message'=> '('. $e->getCode() . ') ' . $e->getMessage()] );
            }
             $anchor = anchor(base_url(),lang('Student.go_back'));
            return view('migration/migrate-success', ['message'=> "Table Might already created. Or Successful in this time. <strong>$anchor</strong>"] );
        }else{
            return view('migration/migrate-error', ['message'=> 'Sorry, Security validation failed.'] );
        }
    }


}
