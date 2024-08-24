<?php
    if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
    $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
?>

<div class="row">
    <div class="col-lg-12">
        <div class="row">
                <div class="col-lg-6">
                    <div class="ibox">
                        <div class="ibox-title p-0 border">
                            <table class="table table-hover table-bordered bg-white m-0">
                                <thead class="thead-light">
                                    <tr class="text-center"><th scope="col">Selected Student</th></tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <?php if(isset($selectedStudent) AND is_object($selectedStudent)) : ?>
                                            <td>
                                                <?php
                                                $bookings = service('HostelRoomsBookingModel')->select('hrb_seat_no,hos_title,hrb_hos_id')->join('hostel_and_rooms','hostel_and_rooms.hos_id = hostel_rooms_booking.hrb_hos_id','LEFT')->where('hrb_student_id',$selectedStudent->student_u_id)->find();
                                                echo '<strong>'.esc(service('AuthLibrary')->getUserFullName_fromObj($selectedStudent,'No name')).'</strong>';
                                                echo '<br>Student ID (SID): ' . $selectedStudent->student_u_id;
                                                echo '<br>Father: ' . $selectedStudent->student_u_father_name .' & Mother: ' . $selectedStudent->student_u_mother_name;
                                                echo '<br>Bookings: ' . ((count($bookings) > 0) ? count($bookings) : '0'); 
                                                ?>
                                                <?php if(count($bookings) > 0): ?>
                                                    <div class="row justify-content-center">
                                                        <div class="col-auto">
                                                            <table class="table table-responsive">
                                                                <tr><th>Seat #</th><th>Room Name</th><th>Booking</th></tr>
                                                                <?php 
                                                                foreach($bookings as $seatB){
                                                                    echo "<tr><td>{$seatB->hrb_seat_no}</td><td>".esc($seatB->hos_title)."</td><td class='p-0'>";
                                                                    ?>
                                                                    <?=form_open('admin/hostel/bed/distribution',['method'=>'post'],[
                                                                                'student_id'        => $selectedStudent->student_u_id,
                                                                                'hostel_room_id'    => $seatB->hrb_hos_id,
                                                                                'hostel_room_id_showing_in_page'    => service('request')->getGet('hostel_room_id'),
                                                                                'hostel_seat_no'    => $seatB->hrb_seat_no,
                                                                                'hostel_cancel_sid_seat'=> 'yes'
                                                                            ]);?>
                                                                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Cancel </button>
                                                                    <?=form_close();?>
                                                                    <?php 
                                                                    echo "</td></tr>";
                                                                }
                                                                ?>
                                                            </table>
                                                        </div>
                                                    </div>
                                                <?php endif;?>
                                            </td>
                                        <?php else:?>
                                            <td colspan="6">No Student Selected.</td>
                                        <?php endif;?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            

                <div class="col-lg-6">
                    <div class="ibox">
                        <div class="ibox-title p-0 border">
                            <table class="table table-hover table-bordered bg-white m-0">
                                <thead class="thead-light">
                                    <tr class="text-center"><th scope="col">Selected Hostel Room</th></tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <?php if(isset($selectedRoom) AND is_object($selectedRoom)) : ?>
                                            <td>
                                                <?php
                                                echo '<strong>'.esc($selectedRoom->title).'</strong>';
                                                echo '<br>Hostel Room ID: ' . $selectedRoom->hos_id;
                                                echo '<br>Capacity: ' . $selectedRoom->hos_capacity;
                                                echo '<br>Occupied: ' . esc(service('HostelRoomsBookingModel')->where('hrb_hos_id',$selectedRoom->hos_id)->countAllResults());
                                                $seats = service('HostelRoomsBookingModel')->where('hrb_hos_id',$selectedRoom->hos_id)->findColumn('hrb_seat_no');
                                                if(is_array($seats) AND count($seats) > 0 ){
                                                    sort($seats);
                                                    echo ' ('. implode(', ', $seats) . ')';
                                                }
                                                
                                                $seatsBookings = service('HostelRoomsBookingModel')->select('hrb_student_id,hrb_seat_no,hrb_hos_id,student_u_name_initial,student_u_name_middle,student_u_name_first,student_u_name_last,student_u_father_name,student_u_mother_name,student_u_gender')->join('students','students.student_u_id = hostel_rooms_booking.hrb_student_id','LEFT')->where('hrb_hos_id',$selectedRoom->hos_id)->limit(100)->find();
                                                ?>
                                                <?php if(count($seatsBookings) > 0): ?>
                                                    <div class="row justify-content-center">
                                                        <div class="col-auto">
                                                            <table class="table table-responsive">
                                                                <tr><th>Seat #</th><th>Student Name</th><th>Booking</th></tr>
                                                                <?php 
                                                                foreach($seatsBookings as $seatB){
                                                                    $fntm = implode(' & ', array_filter([trim($seatB->student_u_father_name),trim($seatB->student_u_mother_name)]));
                                                                    $stdxname = service('AuthLibrary')->getUserFullName_fromObj($seatB,'No name');
                                                                    $sondot = ($seatB->student_u_gender == 'male') ? 'son of' : ( ($seatB->student_u_gender == 'female') ? 'daughter of' : '');
                                                                    if(strlen($fntm) > 0){ $stdxname .= " ($sondot ".$fntm.')';}

                                                                    echo "<tr><td>{$seatB->hrb_seat_no}</td><td>".esc($stdxname)."</td><td class='p-0'>";
                                                                    ?>
                                                                    <?=form_open('admin/hostel/bed/distribution',['method'=>'post'],[
                                                                                'student_id'        => $seatB->hrb_student_id,
                                                                                'hostel_room_id'    => $seatB->hrb_hos_id,
                                                                                'hostel_room_id_showing_in_page'    => service('request')->getGet('hostel_room_id'),
                                                                                'hostel_seat_no'    => $seatB->hrb_seat_no,
                                                                                'hostel_cancel_room_seat'=> 'yes'
                                                                            ]);?>
                                                                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Cancel </button>
                                                                    <?=form_close();?>
                                                                    <?php 
                                                                    echo "</td></tr>";
                                                                }
                                                                ?>
                                                            </table>
                                                        </div>
                                                    </div>
                                                <?php endif;?>
                                            </td>
                                        <?php else:?>
                                            <td colspan="5">No Hostel Book Selected.</td>
                                        <?php endif;?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-title p-2 border">
                            <?php if(isset($selectedRoom) AND is_object($selectedRoom)) : ?>
                                <?php if(isset($selectedStudent) AND is_object($selectedStudent)) : ?>
                                    <?=form_open('admin/hostel/bed/distribution',['method'=>'post'],[
                                                'student_id'        => $selectedStudent->student_u_id,
                                                'hostel_room_id'    => $selectedRoom->hos_id,
                                                'hostel_room_book'  => 'yes'
                                            ]);?>
                                            <div class="row">
                                                <div class="col-lg-4 text-right">
                                                    <label for="seat_number" class="m-2">Select Seat Number: </label>
                                                </div>
                                                <div class="col-lg-4">
                                                    <?= form_dropdown('seat_number',range(0,intval($selectedRoom->hos_capacity),1),'',['class'=>'form-control','id'=>'seat_number']); ?>
                                                </div>
                                                <div class="col-lg-4">
                                                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Book Seat </button>
                                                </div>
                                            </div>
                                    <?=form_close();?>
                                <?php endif;?>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title p-2 border">
                        <?php $neededOtherValues = service('request')->getGet(); unset($neededOtherValues['student_id']);?>
                        <?=form_open('admin/hostel/bed/distribution',['method'=>'get'],$neededOtherValues);?>
                            <div class="form-row align-items-center">
                                <div class="col-auto"><label for="student_id">Student ID:</label></div>
                                <div class="col-auto"><?=form_input('student_id',intval(service('request')->getGet('student_id')),['class'=>'form-control', 'id'=>'student_id']);?></div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search Student</button>
                                </div>
                            </div>
                        <?=form_close();?>
                    </div>
                </div>
            </div>
            
            
            
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered bg-white">
                        <?php 
                        $tr_thead_tfoot = '<tr class="text-center">';
                            $tr_thead_tfoot .= '<th scope="col">Roll</th>';
                            $tr_thead_tfoot .= '<th scope="col">Name</th>';
                            $tr_thead_tfoot .= '<th scope="col">Class</th>';
                            $tr_thead_tfoot .= '<th scope="col">Session/Status</th>';
                            $tr_thead_tfoot .= '<th scope="col">SID</th>';
                            $tr_thead_tfoot .= '<th scope="col">Room</th>';
                        $tr_thead_tfoot .= '</tr>';
                        ?>
                        <thead class="thead-light"><?php echo $tr_thead_tfoot; ?></thead>
                        <tfoot class="thead-light"><?php echo $tr_thead_tfoot; ?></tfoot>
                        <tbody>
                            <?php if(isset($students_list) AND is_array($students_list) AND count($students_list) > 0 ): ?>
                                <?php foreach($students_list as $stdDta): ?>
                                    <tr class="text-center">
                                        <td><?=esc($stdDta->scm_c_roll);?></td>
                                        <td>
                                            <?php
                                                $fnm = implode(' & ', array_filter([trim($stdDta->student_u_father_name),trim($stdDta->student_u_mother_name)]));
                                                $stdname = service('AuthLibrary')->getUserFullName_fromObj($stdDta,'No name');
                                                $sondot = ($stdDta->student_u_gender == 'male') ? 'son of' : ( ($stdDta->student_u_gender == 'female') ? 'daughter of' : '');
                                                if(strlen($fnm) > 0){ $stdname .= " ($sondot ".$fnm.')';}
                                                echo anchor(
                                                        "admin/hostel/bed/distribution?student_id={$stdDta->student_u_id}&hostel_room_id=" . service('request')->getGet('hostel_room_id'),
                                                        esc($stdname),['class'=>'']);
                                            ?>
                                            <?php 
                                                if($stdDta->scm_deleted_at){
                                                    echo '<span class="btn label label-warning">Deleted ' . time_elapsed_string($stdDta->scm_deleted_at) . '</span>';
                                                }
                                            ?>
                                            
                                        </td>
                                        <td><?=service('ClassesAndSemestersModel')->get_single_class_with_parent_label($stdDta->scm_class_id)->title ." [".intval($stdDta->scm_class_id)."]";?></td>
                                        <td>
                                            <?=esc($stdDta->scm_session_year);?> /
                                            <?=isset(get_student_class_status()[$stdDta->scm_status]) ? get_student_class_status()[$stdDta->scm_status]: '';?>
                                        </td>
                                        <td><?=esc($stdDta->student_u_id);?></td>
                                        <td><?=esc(service('HostelRoomsBookingModel')->where('hrb_student_id',$stdDta->student_u_id)->countAllResults());?></td>  
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="text-center"><td colspan="20">No student found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?= isset($studentsLstPgr) ? $studentsLstPgr : '';?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title p-2 border">
                        <?php $neededOtherValue2 = service('request')->getGet(); unset($neededOtherValue2['hostel_room_id']);?>
                        <?=form_open('admin/hostel/bed/distribution',['method'=>'get'],$neededOtherValue2);?>
                            <div class="form-row align-items-center">
                                <div class="col-auto"><label for="hostel_room_id">Hostel Room ID:</label></div>
                                <div class="col-auto"><?=form_input('hostel_room_id',intval(service('request')->getGet('hostel_room_id')),['class'=>'form-control', 'id'=>'hostel_room_id']);?></div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search Room</button>
                                </div>
                            </div>
                        <?=form_close();?>
                    </div>
                </div>
            </div>
            
            
            
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
