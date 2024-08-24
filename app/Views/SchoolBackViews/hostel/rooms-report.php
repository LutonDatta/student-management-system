<?php
    if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
    $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
?>

<div class="row">
    <div class="col-lg-12">
        <div class="row">
           
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered bg-white">
                        <?php 
                            $tr_thead_tfoot = '<tr class="text-center">';
                                $tr_thead_tfoot .= '<th scope="col">Room ID</th>';
                                $tr_thead_tfoot .= '<th scope="col">Title</th>';
                                $tr_thead_tfoot .= '<th scope="col">Capacity</th>';
                                $tr_thead_tfoot .= '<th scope="col">Occupied</th>';
                            $tr_thead_tfoot .= '</tr>';
                        ?>
                        <thead class="thead-light"><?php echo $tr_thead_tfoot; ?></thead>
                        <tfoot class="thead-light"><?php echo $tr_thead_tfoot; ?></tfoot>
                        <tbody>
                            <?php if(isset($hostelRooms) AND is_array($hostelRooms) AND count($hostelRooms) > 0 ): ?>
                                <?php foreach($hostelRooms as $stdDta): ?>
                                    <tr class="text-center">
                                        <td><?=esc($stdDta->hos_id);?></td>                                
                                        <td class="text-left">
                                            <?=anchor(
                                                "admin/hostel/bed/distribution?hostel_room_id={$stdDta->hos_id}&student_id=".service('request')->getGet('student_id'),
                                                    esc($stdDta->title),
                                                    ['class'=>'']);?></td>                                
                                        <td><?=esc($stdDta->hos_capacity);?></td>                                
                                        <td><?=esc(service('HostelRoomsBookingModel')->where('hrb_hos_id',$stdDta->hos_id)->countAllResults());?></td>                                
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="text-center"><td colspan="20">No room found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?= isset($hostelRoomsPgr) ? $hostelRoomsPgr : '';?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <?php if(isset($hostelRooms) AND is_array($hostelRooms) AND count($hostelRooms) > 0 ): ?>
            <?php foreach($hostelRooms as $stdDta): ?>
                <?php 
                    $seatsBookings = service('HostelRoomsBookingModel')->select('hrb_student_id,hrb_seat_no,hrb_hos_id,student_u_name_initial,student_u_name_middle,student_u_name_first,student_u_name_last,student_u_father_name,student_u_mother_name,student_u_gender')->join('students','students.student_u_id = hostel_rooms_booking.hrb_student_id','LEFT')->where('hrb_hos_id',$stdDta->hos_id)->limit(100)->find(); 
                    if( count($seatsBookings) < 1 ) continue; // Do not show empty table
                ?>
                <div class="ibox">
                    <div class="ibox-title p-0 border">
                        <table class="table table-bordered bg-white m-0">
                            <thead class="thead-light">
                                <tr class="text-center"><th scope="col"><?=esc($stdDta->title . ' ['.$stdDta->hos_id.']');?>, Capacity: <?=esc($stdDta->hos_capacity);?>, Occupied: <?=count($seatsBookings);?></th></tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td>
                                        <?php if(count($seatsBookings) > 0): ?>
                                            <div class="row justify-content-center">
                                                <div class="col-auto">
                                                    <table class="table table-responsive">
                                                        <tr><th>Seat #</th><th>Student Name</th><th>Student ID</th><th>Father</th><th>Mother</th></tr>
                                                        <?php 
                                                        foreach($seatsBookings as $seatB){
                                                            echo "<tr><td>{$seatB->hrb_seat_no}</td><td>".esc(service('AuthLibrary')->getUserFullName_fromObj($seatB,'No name'))."</td><td>".esc($seatB->hrb_student_id)."</td><td>".esc($seatB->student_u_father_name)."</td><td>".esc($seatB->student_u_mother_name)."</td></tr>";
                                                        }
                                                        ?>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php endif;?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach;?>
        <?php endif;?>        
    </div>
</div>
