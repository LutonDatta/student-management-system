<?php namespace App\Database\Seeds;

class AddInitial_Courses extends \CodeIgniter\Database\Seeder {
    
    public function run(){
        $this->db->disableForeignKeyChecks();
        
        $classes = [ 
            array( 'co_title' => 'English',     'co_code' => 'En101', 'co_excerpt' => 'Preliminary subject for all.'),
            array( 'co_title' => 'Math',        'co_code' => 'Ma101', 'co_excerpt' => 'Preliminary subject for all.'),
            array( 'co_title' => 'Economics',   'co_code' => 'Ec101', 'co_excerpt' => 'Preliminary subject for all.'),
            array( 'co_title' => 'Science',     'co_code' => 'Sc101', 'co_excerpt' => 'Preliminary subject for all.'),
            array( 'co_title' => 'Drawings',    'co_code' => 'Dr101', 'co_excerpt' => 'Preliminary subject for all.'),
            array( 'co_title' => 'Statistics',  'co_code' => 'St101', 'co_excerpt' => 'Preliminary subject for all.'),
        ];
        
        foreach( $classes as $adCourse ){
            service('CoursesModel')->insert( $adCourse ); 
        }
        
        $this->db->enableForeignKeyChecks();
    } /* EOM */
    
} /* EOC */
