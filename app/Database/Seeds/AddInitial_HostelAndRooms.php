<?php namespace App\Database\Seeds;

class AddInitial_HostelAndRooms extends \CodeIgniter\Database\Seeder {
    
    public function run(){
        $this->db->disableForeignKeyChecks();
                
        $classes = [ 
            array( 'hos_id' => '1', 'hos_parent' => NULL, 'hos_capacity' => '2', 'hos_title' => 'Boys Hostel',  'hos_excerpt' => 'Hostels for boys.' ),
            array( 'hos_id' => '2', 'hos_parent' => NULL, 'hos_capacity' => '2', 'hos_title' => 'Girls Hostel', 'hos_excerpt' => 'Hostels for girls.' ),
            
            array( 'hos_id' => '3', 'hos_parent' => '1',  'hos_capacity' => '2', 'hos_title' => 'First Floor',  'hos_excerpt' => 'First floor boys hostel.' ),
            array( 'hos_id' => '4', 'hos_parent' => '1',  'hos_capacity' => '2', 'hos_title' => 'Second Floor', 'hos_excerpt' => 'Second floor boys hostel.' ),
            array( 'hos_id' => '5', 'hos_parent' => '2',  'hos_capacity' => '2', 'hos_title' => 'First Floor',  'hos_excerpt' => 'First floor girls hostel.' ),
            array( 'hos_id' => '6', 'hos_parent' => '2',  'hos_capacity' => '2', 'hos_title' => 'Second Floor', 'hos_excerpt' => 'Second floor girls hostel.' ),
            
            array( 'hos_id' => '7', 'hos_parent' => '3',  'hos_capacity' => '4', 'hos_title' => 'BH FF Room A1 - Two Double Bed AC', 'hos_excerpt' => 'Boys Hostel First Floor Room 1' ),
            array( 'hos_id' => '8', 'hos_parent' => '3',  'hos_capacity' => '4', 'hos_title' => 'BH FF Room A2 - Two Double Bed AC', 'hos_excerpt' => 'Boys Hostel First Floor Room 2' ),
            array( 'hos_id' => '9', 'hos_parent' => '4',  'hos_capacity' => '4', 'hos_title' => 'BH SF Room B1 - Four Single Bed', 'hos_excerpt' => 'Boys Hostel Second Floor Room 1' ),
            array( 'hos_id' => '10','hos_parent' => '4',  'hos_capacity' => '4', 'hos_title' => 'BH SF Room B2 - Four Single Bed', 'hos_excerpt' => 'Boys Hostel Second Floor Room 2' ),
            array( 'hos_id' => '11','hos_parent' => '5',  'hos_capacity' => '4', 'hos_title' => 'GH FF Room A1 - Two Double Bed AC', 'hos_excerpt' => 'Girls Hostel First Floor Room 1' ),
            array( 'hos_id' => '12','hos_parent' => '5',  'hos_capacity' => '4', 'hos_title' => 'GH FF Room A2 - Two Double Bed AC', 'hos_excerpt' => 'Girls Hostel First Floor Room 2' ),
            array( 'hos_id' => '13','hos_parent' => '6',  'hos_capacity' => '4', 'hos_title' => 'GH SF Room B1 - Four Single Bed', 'hos_excerpt' => 'Girls Hostel Second Floor Room 2' ),
            array( 'hos_id' => '14','hos_parent' => '6',  'hos_capacity' => '4', 'hos_title' => 'GH SF Room B2 - Four Single Bed', 'hos_excerpt' => 'Girls Hostel Second Floor Room 2' ),
        ];
        
        foreach( $classes as $adClass ){
            service('HostelAndRoomsModel')->insert( $adClass ); 
        }
        
        $this->db->enableForeignKeyChecks();
    } /* EOM */
    
} /* EOC */
