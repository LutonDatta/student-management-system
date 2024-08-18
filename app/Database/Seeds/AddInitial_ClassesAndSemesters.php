<?php namespace App\Database\Seeds;

class AddInitial_ClassesAndSemesters extends \CodeIgniter\Database\Seeder {
    
    public function run(){
        $this->db->disableForeignKeyChecks();
        
        $classes = [ 
            array( 'fcs_title' => 'Class One',  'fcs_excerpt' => 'Students of age 6 can get admitted here.',                            'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class Two',  'fcs_excerpt' => 'Students of age 7 and passed from class One can get admitted here.',  'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class III',  'fcs_excerpt' => 'Students of age 8 and passed from class Two can get admitted here.',  'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class Four', 'fcs_excerpt' => 'Students of age 9 and passed from class Three can get admitted here.','fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class Five', 'fcs_excerpt' => 'Students of age 10 and passed from class Four can get admitted here.','fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class VI',   'fcs_excerpt' => 'Students of age 11 and passed from class Five can get admitted here.','fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class VII',  'fcs_excerpt' => 'Students of age 12 and passed from class Six can get admitted here.', 'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class VIII', 'fcs_excerpt' => 'Students of age 13 and passed from class VII can get admitted here.', 'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class IX',   'fcs_excerpt' => 'Students of age 14 and passed from class VIII can get admitted here.','fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class X',    'fcs_excerpt' => 'Students of age 15 and passed from class Nine can get admitted here.','fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class XI',   'fcs_excerpt' => 'Students of age 16 and passed from class Ten can get admitted here.', 'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
            array( 'fcs_title' => 'Class XII',  'fcs_excerpt' => 'Students of age 17, intermediate 1st year done can get admitted.',    'fcs_session_starts' => 'Jan', 'fcs_session_ends' => 'Dec' ),
        ];
        
        foreach( $classes as $adClass ){
            service('ClassesAndSemestersModel')->insert( $adClass ); 
        }
        
        $this->db->enableForeignKeyChecks();
    } /* EOM */
    
} /* EOC */
