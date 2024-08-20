<!DOCTYPE html>
<html lang="<?=service('request')->getLocale();?>" translate="no" class="notranslate">
<head id="pageA4Head">    
    <meta charset="utf-8">
    <title><?=isset($title)?$title:'Welcome';?></title>
    <base href="<?=base_url('/');?>">
    <?php $cdn_url = cdn_url();?>
    <meta name="google" content="notranslate">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="<?php echo base_url('favicon.ico');?>">
    <meta name="author" content="Luton Datta">
    <link href="<?=$cdn_url.'bootstrap-4.3.1-dist/css/bootstrap.min.css';?>" rel="stylesheet">
    <link href="<?=$cdn_url.'style-inst-admin.css';?>" rel="stylesheet">
</head>
<body>
    <div class="d-print-none pt-4">
        <?=form_open(base_url('print/student/admission/confirmation/form'),['class'=>'container','method'=>'get']);?>
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>    
                
        <div class="row">
            <div class="col-lg-12 text-center">
                <button type="button" class="btn btn-primary" id="printBoxBtn"><i class="fas fa-print"></i> <?=lang('Sa.print_this_page');?></button>
            </div>
        </div>   
        <?=form_close();?>
    </div>
    

    <div class="page">
        <div class="subpage wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <div class="h4 mt-0 pt-0 li"><?=(strlen(getSchool()->sch_name) < 2) ? 'No institution name set' : '';?></div>
                    <div class="h6">Student Admission Confirmation Form</div>
                </div>
                <div class="col-10">
                    <div class="profile-image">
                        <?php 
                            $defaultImth = $udr->student_u_gender == 'female' ? 'profile-thumb-girl-240x240.jpg' : 'profile-thumb-boy-240x240.jpg';
                            $uthemb = $cdn_url . 'default-images/' . $defaultImth;
                        ?>
                        <img src="<?=$uthemb;?>" width="350" class="rounded mb-4 border" alt="Image">
                    </div>
                    <div class="profile-info">
                        <div class="">
                            <div>
                                <!-- Keep name of the stdent little below when no tagline and about me found giving extra h tag-->
                                <h3 class=" mt-4">
                                    <?=trim(get_name_initials($udr->student_u_name_initial) . ' ' . esc($udr->student_u_name_first) . ' ' . esc($udr->student_u_name_middle) . ' ' . esc($udr->student_u_name_last)); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-6">
                    <h4>Basic & Identity Information</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th class='text-right'>Father's Name</th>      <td><?=esc($udr->student_u_father_name);?></td></tr>
                            <tr><th class='text-right'>Mother's Name</th>      <td><?=esc($udr->student_u_mother_name);?></td></tr>
                            <tr><th class='text-right'>Gender</th>             <td><?=esc(get_gender_list($udr->student_u_gender ? $udr->student_u_gender : 'dummy-text-to-prevent-error'));?></td></tr>
                            <tr><th class='text-right'>Religion</th>           <td><?=esc($udr->student_u_religion);?></td></tr>
                            <tr><th class='text-right'>Date of Birth</th>      <td><?=date('jS F Y',strtotime($udr->student_u_date_of_birth));?></td></tr>
                            <tr><th class='text-right'>NID Number</th>         <td><?=esc($udr->student_u_nid_no);?></td></tr>
                            <tr><th class='text-right'>Birth Reg Number</th>   <td><?=esc($udr->student_u_birth_reg_no);?></td></tr>
                            <tr><th class='text-right'>Email</th>              <td><?=esc($udr->student_u_email_own);?></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <h4>Address & Contact Information</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th class="text-right">Country</th>                <td><?=esc(get_country_list(strlen($udr->student_u_addr_country) < 3 ? 'BGD' : $udr->student_u_addr_country ));?></td></tr>
                            <tr><th class="text-right">Division/District</th>         <td><?=esc($udr->student_u_addr_state);?> / <?=esc($udr->student_u_addr_district);?></td></tr>
                            <tr><th class="text-right">Post Office/Code</th>       <td><?=esc($udr->student_u_addr_post_office);?> / <?=esc($udr->student_u_addr_zip_code);?></td></tr>
                            <tr><th class="text-right">Village/Area</th>            <td><?=esc($udr->student_u_addr_village);?></td></tr>
                            <tr><th class="text-right">Road/House</th>              <td><?=esc($udr->student_u_addr_road_house_no);?></td></tr>
                            <tr><th class="text-right">Mobile</th>                 <td><?=esc($udr->student_u_mobile_own);?></td></tr>
                            <tr><th class="text-right">Father's Mobile</th>        <td><?=esc($udr->student_u_mobile_father);?></td></tr>
                            <tr><th class="text-right">Mother's Mobile</th>        <td><?=esc($udr->student_u_mobile_mother);?></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <h4>Academic Qualifications</h4>
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr><th>#</th><th>Examination</th><th>GPA</th><th>Institution Name</th><th>Year/Session</th><th>Roll</th><th>Reg</th></tr>
                        </thead>
                        <tbody>
                            <?php for($sl=1; $sl <=3; $sl++) : ?>
                                    <tr>
                                        <td><?=$sl;?></td>
                                        <td><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_1'; echo property_exists($udr, $p) ? esc($udr->$p) : '-'; ?></td> 
                                        <td><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_2'; echo property_exists($udr, $p) ? esc($udr->$p) : '-'; ?>/<?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_3'; echo property_exists($udr, $p) ? esc($udr->$p) : '-'; ?></td> 
                                        <td><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_4'; echo property_exists($udr, $p) ? esc($udr->$p) : '-'; ?></td> 
                                        <td><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_5'; echo property_exists($udr, $p) ? esc($udr->$p) : '-'; ?></td> 
                                        <td><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_6'; echo property_exists($udr, $p) ? esc($udr->$p) : '-'; ?></td> 
                                        <td><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_7'; echo property_exists($udr, $p) ? esc($udr->$p) : '-'; ?></td> 
                                    </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                
                
                <div class="col-12">
                    <?php if(isset($oneApplication) AND is_array($oneApplication)) : ?>
                        <h4>Admission Confirmation</h4>
                        <?php
                            $classApplication   = $oneApplication['cls_appi']; // We must have it
                            $classData          = $oneApplication['cls_data']; // Class data might be deleted, so it might be null
                            $classCoursesMan    = $oneApplication['cls_mand'];
                            $classCoursesOpt    = $oneApplication['cls_opti'];
                            $classCoursesStd    = $oneApplication['cls_sltd'];
                        ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Class ID</th>
                                    <th scope="col">Class Name</th>
                                    <th scope="col">Session</th>
                                    <th scope="col" class="text-center" <?=tt_title('Class Roll');?>>Class Roll</th>
                                    <th scope="col" class="text-center">SCM ID</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <th scope="row"><?=$classApplication->scm_class_id;?></th>
                                  <td><?=is_object($classData) ? esc($classData->title) : 'Invalid class. It might be deleted.'; ?></td>
                                  <td class="text-center"><?=$classApplication->scm_session_year;?></td>
                                  <td class="text-center"><?=$classApplication->scm_c_roll;?></td>
                                  <td class="text-center"><?=$classApplication->scm_id;?></td>
                                  <td>
                                      <?php $stats = get_student_class_status(); echo isset($stats[$classApplication->scm_status]) ? esc($stats[$classApplication->scm_status]) : '';?>
                                      <div class="small">
                                          <span class="d-none d-print-block">(Updated at <?=esc($classApplication->scm_updated_at);?>)</span>
                                          <span class="d-print-none">(Updated <?=time_elapsed_string($classApplication->scm_updated_at);?>)</span>
                                          <?=($classApplication->scm_deleted_at)? '<div class="label label-warning">Deleted ' .time_elapsed_string ($classApplication->scm_deleted_at).'</div>':'';?>
                                      </div>
                                  </td>
                                </tr>
                                <tr>
                                    <th colspan="3">Mandatory Courses</th>
                                    <th colspan="4">Optional Courses</th>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                      
                                        <?php if( is_array($classCoursesMan) AND count($classCoursesMan) > 0 ) : ?>
                                            <ul>
                                                <?php foreach($classCoursesMan as $mcr): ?>
                                                <li>
                                                    <?php echo ( ! in_array($mcr->co_id,$classCoursesStd) ) ? '<del '.tt_title('This course is not selected.').'>' : '';?>
                                                        <?=esc($mcr->co_title);?> [<?=esc($mcr->co_id);?>]
                                                    <?php echo ( ! in_array($mcr->co_id,$classCoursesStd) ) ? '</del>' : '';?>                                                    
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </td>
                                    <td colspan="4">
                                        <?php if( is_array($classCoursesOpt) AND count($classCoursesOpt) > 0 ) : ?>
                                            <ul>
                                                <?php foreach($classCoursesOpt as $ocr): ?>
                                                <li>
                                                    <?php echo ( ! in_array($ocr->co_id,$classCoursesStd) ) ? '<del '.tt_title('This course is not selected.').'>' : '';?>
                                                                <?=esc($ocr->co_title);?> [<?=esc($ocr->co_id);?>]
                                                    <?php echo ( ! in_array($ocr->co_id,$classCoursesStd) ) ? '</del>' : '';?>                                                
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php 
                                    // If courses removed in (class wise course setup) after student admission, will not see course list here
                                    // So we need to reverse check and show list here.
                                    $classWiseCourseNowM = array_map(function($objC){ return $objC->co_id; },$classCoursesMan);
                                    $classWiseCourseNowO = array_map(function($objC){ return $objC->co_id; },$classCoursesOpt);
                                    // Student was admitted to these course, but now course was removed from the class now, just show it was added
                                    $notShowinCourseList = array_diff($classCoursesStd,array_merge($classWiseCourseNowM,$classWiseCourseNowO)); 
                                    if( count($notShowinCourseList) > 0 ){
                                        $xt = service('CoursesModel')->select('co_id,co_title')->whereIn('co_id', $notShowinCourseList)->limit(5,0)->findAll();
                                        if(count($xt) > 0){
                                            echo "<tr><td colspan='20' class='text-center'>";
                                            echo "Currently not in course list. But the student was admitted to these courses.";
                                            echo "<ul class='list-inline m-0'>";
                                            foreach($xt as $xtc){
                                                echo "<li> &nbsp; &rarr; ".esc($xtc->co_title) . ' ['.esc($xtc->co_id).'] </li>';
                                            }
                                            echo '</ul></td></tr>';
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <div class="col-12">
                    <div class="text-center">Printed at: <?=date('jS M Y h:i:s a P');?> GMT</div>
                    <div class="text-center">Printed info of SID: <?=$udr->student_u_id;?></div>
                </div>
        </div>
    </div>

    <script src="<?=$cdn_url.'self-xss-attack-warn.js';?>"></script>
    <script {csp-script-nonce}>document.addEventListener('DOMContentLoaded',function(){document.getElementById('printBoxBtn').addEventListener('click',function(){window.print();});});</script>
    <style {csp-style-nonce}>    
        *{box-sizing: border-box;-moz-box-sizing: border-box;}
        body{
            margin: 0;
            padding: 0;
            background-color: rgb(240,240,240);
            font: 10pt "Tahoma";
        }
        .page {
          width: 21cm;
          min-height: 29.7cm;
          padding: 2cm;
          margin: 1cm auto;
          border: 1px lightgray solid;
          border-radius: 5px;
          background: white;
          box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .subpage {
            padding: 0;
            border: 1px rgb(240, 240, 240) dotted;
            height: 333mm;
            outline: 2cm #FFFFFF solid;
        }
        @page {size: A4;margin: 0;}
        @media print {
            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }
    </style>
    <link href="<?= $cdn_url.'fontawesome-free-5.9.0-web/css/all.min.css'; ?>" rel="stylesheet">
</body>
</html>
