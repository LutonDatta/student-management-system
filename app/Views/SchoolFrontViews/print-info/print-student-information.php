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
    <?php
        if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
        $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
    ?>
    <?php $scm_u_id = (isset($classApplication) AND is_object($classApplication) AND property_exists($classApplication,'scm_u_id')) ? $classApplication->scm_u_id : ''; ?>
    <div class="d-print-none pt-4">
        <?=form_open(base_url('student/info/print/view'),['class'=>'container','method'=>'get'],['user_id'=>service('request')->getGet('user_id')]);?>
        <div class="row">
            <div class="col-lg-12 text-center">
                <button type="button" class="btn btn-danger" id="printBoxBtn"><i class="fas fa-print"></i> <?=lang('Sa.print_this_page');?></button>
                
            </div>
        </div>   
        <?=form_close();?>
    </div>
    

    <div class="page">
        <div class="subpage wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-12 text-center mb-0">
                    <div class="h4 mt-0 pt-0 li mb-0">
                        <div class="h4 mt-0 mb-0 pt-0 li"><?=esc(getSchool()->sch_name);?></div>
                        <div class="h6 mt-0 mb-0 pt-0 li"><?=esc(getSchool()->sch_address);?></div>
                        <div class="h6 mt-0 mb-0 pt-0 li">EIIN: <?=esc(getSchool()->sch_eiin);?>, Contact: <?=esc(getSchool()->sch_contact);?></div>

                    </div>
                    <div class="h4 mb-0">Student Information Form</div>
                </div>
                <div class="col-12">
                    <div class="profile-image">
                        <?php 
                            $defaultImth = (!empty($classApplication) AND $classApplication->student_u_gender == 'female') ? 'profile-thumb-girl-240x240.jpg' : 'profile-thumb-boy-240x240.jpg';
                            $uthemb = cdn_url('default-images/' . $defaultImth); 
                        ?>
                        <img src="<?=$uthemb;?>" width="350" class="rounded mb-4 border" alt="Image">
                    </div>
                    <div class="profile-info">
                        <div class="">
                            <div>
                                <h3 class="mt-4">
                                    <?=empty($classApplication) ? '' : trim(get_name_initials($classApplication->student_u_name_initial) . ' ' . esc($classApplication->student_u_name_first) . ' ' . esc($classApplication->student_u_name_middle) . ' ' . esc($classApplication->student_u_name_last)); ?>
                                </h3>
                                
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-6">
                    <h4>Basic & Identity Information</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th class='text-right'>Father's Name</th>      <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_father_name);?></td></tr>
                            <tr><th class='text-right'>Mother's Name</th>      <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_mother_name);?></td></tr>
                            <tr><th class='text-right'>Gender</th>             <td><?=empty($classApplication) ? '' : esc(get_gender_list($classApplication->student_u_gender ? $classApplication->student_u_gender : 'dummy-text-to-prevent-error'));?></td></tr>
                            <tr><th class='text-right'>Religion</th>           <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_religion);?></td></tr>
                            <tr><th class='text-right'>Date of Birth</th>      <td><?=empty($classApplication) ? '' : date('jS F Y',strtotime($classApplication->student_u_date_of_birth));?></td></tr>
                            <tr><th class='text-right'>NID Number</th>         <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_nid_no);?></td></tr>
                            <tr><th class='text-right'>Birth Reg Number</th>   <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_birth_reg_no);?></td></tr>
                            <tr><th class='text-right'>Email</th>              <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_email_own);?></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <h4>Address & Contact Information</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th class="text-right">Country</th>                 <td><?=empty($classApplication) ? '' : esc(get_country_list(strlen($classApplication->student_u_addr_country) < 3 ? 'BGD' : $classApplication->student_u_addr_country ));?></td></tr>
                            <tr><th class="text-right">Division/District</th>       <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_addr_state);?> / <?=empty($classApplication) ? '' : esc($classApplication->student_u_addr_district);?></td></tr>
                            <tr><th class="text-right">Post Office/Code</th>        <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_addr_post_office);?> / <?=empty($classApplication) ? '' : esc($classApplication->student_u_addr_zip_code);?></td></tr>
                            <tr><th class="text-right">Village/Area</th>            <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_addr_village);?></td></tr>
                            <tr><th class="text-right">Road/House</th>              <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_addr_road_house_no);?></td></tr>
                            <tr><th class="text-right">Mobile</th>                  <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_mobile_own);?></td></tr>
                            <tr><th class="text-right">Father's Mobile</th>         <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_mobile_father);?></td></tr>
                            <tr><th class="text-right">Mother's Mobile</th>         <td><?=empty($classApplication) ? '' : esc($classApplication->student_u_mobile_mother);?></td></tr>
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
                                        <td><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_1'; echo (!empty($classApplication) AND property_exists($classApplication, $p)) ? esc($classApplication->$p) : '-'; ?></td> 
                                        <td>
                                            <?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_2'; echo (!empty($classApplication) AND property_exists($classApplication, $p)) ? esc($classApplication->$p) : '-'; ?>
                                            /
                                            <?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_3'; echo (!empty($classApplication) AND property_exists($classApplication, $p)) ? esc($classApplication->$p) : '-'; ?>
                                        </td> 
                                        <td class="text-break"><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_4'; echo (!empty($classApplication) AND property_exists($classApplication, $p)) ? esc($classApplication->$p) : '-'; ?></td> 
                                        <td><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_5'; echo (!empty($classApplication) AND property_exists($classApplication, $p)) ? esc($classApplication->$p) : '-'; ?></td> 
                                        <td><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_6'; echo (!empty($classApplication) AND property_exists($classApplication, $p)) ? esc($classApplication->$p) : '-'; ?></td> 
                                        <td><?php $p = 'u_aq_'.($sl==1?'a':($sl==2?'b':'c')).'_7'; echo (!empty($classApplication) AND property_exists($classApplication, $p)) ? esc($classApplication->$p) : '-'; ?></td> 
                                    </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                
                
                <div class="col-12">
                    <h4>Application to admit</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr><th scope="col">Class ID</th><th scope="col">Class Name</th><th scope="col">Status</th><th scope="col">Payment</th></tr>
                        </thead>
                        <tbody>
                            <?php if(isset($classApplication) AND isset($classCoursesStd)) : ?>
                                <tr>
                                    <th scope="row"><?=$classApplication->scm_class_id;?></th>
                                    <td><?=is_object($classData) ? esc($classData->title) : 'Invalid class. It might be deleted.'; ?></td>
                                    <td>
                                        <?php $stats = get_student_class_status(); echo isset($stats[$classApplication->scm_status]) ? esc($stats[$classApplication->scm_status]) : '';?>
                                        <div class="small">
                                            Applied <?=time_elapsed_string($classApplication->scm_inserted_at);?>
                                            <?=($classApplication->scm_deleted_at)? '<div class="label label-warning">Deleted ' .time_elapsed_string ($classApplication->scm_deleted_at).'</div>':'';?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            
                                        </div>
                                        <div>
                                            
                                        </div>
                                  </td>
                                </tr>
                                <tr><th colspan="2">Mandatory Courses</th><th colspan="2">Optional Courses</th></tr>
                                <tr>
                                    <td colspan="2">
                                        <?php if( is_array($classCoursesMan) AND count($classCoursesMan) > 0 ) : ?>
                                            <ul class="mb-0">
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
                                    <td colspan="2">
                                        <?php if( is_array($classCoursesOpt) AND count($classCoursesOpt) > 0 ) : ?>
                                            <ul class="mb-0">
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
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No application Found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
                
                <div class="col-12 text-right">
                    <?php  
                    $uthembs = cdn_url('default-images/sign.png'); ?>
                    <table class="float-right">
                        <tr><td class="text-center"><img src="<?=$uthembs;?>" class="rounded" width="180" alt="Signature"></td></tr>
                        <tr><td class="text-center">Signature of Candidate</td></tr>
                    </table>
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

    <?php // if(service('request')->getLocale() === 'bn'): ?>
        <style {csp-style-nonce}>
                @font-face {font-family: 'nikoshf';src: url('<?=cdn_url('fonts/SolaimanLipi_20-04-07.ttf');?>') format('truetype');}
                .font-nikosh,h1,h2,h3,h4,h5,h6,p,a,li,span:not(.fa),strong,td,.card-header,div,.post-title{ font-family: nikoshf !important; }
        </style>
    <?php // endif; ?>

</body>
</html>
