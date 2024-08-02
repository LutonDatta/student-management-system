<?php namespace App\Database\Seeds;

class AddInitial_ClassesAndSemesters extends \CodeIgniter\Database\Seeder {
    
    public function run(){
        $this->db->disableForeignKeyChecks();
        
        $classes = [ 
            array( 'fcs_title' => 'Class One',  'fcs_excerpt' => 'Students of age 6 can get admitted here.', 'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class Two',  'fcs_excerpt' => 'Students of age 7 and passed from class one can get admitted here.', 'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class Three', 'fcs_excerpt' => 'Students of age 8 and passed from class Two can get admitted here.', 'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class Four',  'fcs_excerpt' => 'Students of age 9 and passed from class Three can get admitted here.', 'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
        ];
        
        foreach( $classes as $adClass ){
            service('ClassesAndSemestersModel')->insert( $adClass ); 
        }
        
        $this->db->enableForeignKeyChecks();
    } /* EOM */
    
} /* EOC */
