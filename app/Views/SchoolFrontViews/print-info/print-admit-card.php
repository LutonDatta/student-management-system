<!DOCTYPE html>
<html lang="<?=service('request')->getLocale();?>" translate="no" class="notranslate">
<head id="pageA4Head">    
    <meta charset="utf-8">
    <?php $cdn_url = cdn_url();?>
    <title><?=isset($title)?$title:'Welcome';?></title>
    <base href="<?=base_url('/');?>">
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
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>  
        <?=form_open(base_url('print/admission/test/admit/card'),['class'=>'','method'=>'get'],['page_print_view_ao' => intval(service('request')->getGet('page_print_view_ao'))]);?>
        
        
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <button type="button" class="btn btn-danger ml-1" id="printBoxBtn"><i class="fas fa-print"></i> <?=lang('Sa.print_this_page');?></button> 
                </div>
            </div>
        </div>
        <?=form_close();?>
        
    </div>
    

    <div class="page">
        <div class="subpage wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <div class="h4 mt-0 mb-0 pt-0 li"><?=esc(getSchool()->sch_name);?></div>
                    <div class="h6 mt-0 mb-0 pt-0 li"><?=esc(getSchool()->sch_address);?></div>
                    <div class="h6 mt-0 mb-0 pt-0 li">EIIN: <?=esc(getSchool()->sch_eiin);?>, Contact: <?=esc(getSchool()->sch_contact);?></div>
                    <div class="h5">Admit Card</div>
                </div>
                <div class="col-10">
                    <div class="profile-image">
                        <?php 
                            $defaultImth = (is_object($udr) AND $udr->student_u_gender == 'female') ? 'profile-thumb-girl-240x240.jpg' : 'profile-thumb-boy-240x240.jpg';
                            $uthemb = $cdn_url . 'default-images/' . $defaultImth;
                        ?>
                        <img src="<?=$uthemb;?>" width="350" class="rounded mb-4 border" alt="Image">
                    </div>
                    <div class="profile-info mt-2">
                        <div class="h6">Name: <?=is_object($udr) ? esc(trim(get_name_initials($udr->student_u_name_initial) . ' ' . $udr->student_u_name_first . ' ' . $udr->student_u_name_middle . ' ' . $udr->student_u_name_last)) : 'No name found'; ?></div>
                        <div>Father: <?=is_object($udr) ? esc($udr->student_u_father_name) : 'No father name found';?></div>
                        <div>Mother: <?=is_object($udr) ? esc($udr->student_u_mother_name) : 'No mother name found';?></div>
                    </div>
                </div>
                
                <div class="col-12">
                    <h4>Related Information</h4>
                    
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class='text-right'>Test Roll</th>      
                                <td></td>
                            </tr>
                            <tr>
                                <th class='text-right'>Admit To Class</th>
                                <td><?=(isset($applicationClass) AND is_object($applicationClass)) ? esc($applicationClass->title) : '';?></td>
                            </tr>
                            
                            <tr>
                                <th class='text-right'>Admission Session</th>
                                <td><?=(isset($application) AND is_object($application)) ? esc($application->scm_session_year) : '';?></td>
                            </tr>
                            
                            <tr>
                                <th class='text-right'>Admit To Class ID / FCS ID</th>
                                <td><?=(isset($applicationClass) AND is_object($applicationClass)) ? esc($applicationClass->fcs_id) : '';?></td>
                            </tr>
                            <tr>
                                <th class='text-right'>Unique User ID</th>
                                <td><?=is_object($udr) ? esc($udr->student_u_id) : '';?></td>
                            </tr>
                            <tr>
                                <th class='text-right'>SCM ID</th>
                                <td><?=(isset($application) AND is_object($application)) ? esc($application->scm_id) : '';?></td>
                            </tr>
                            <tr>
                                <th class='text-right'>Admission Status</th>
                                <td>
                                    <?php 
                                    $stats = get_student_class_status();
                                    echo (isset($application) AND isset($stats[$application->scm_status])) ? esc($stats[$application->scm_status]) : '';
                                    ?>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                
                <div class="col-12 text-right">
                    <?php  $uthembs = $cdn_url . 'default-images/sign.png'; ?>
                    <table class="float-right">
                        <tr><td class="text-center"><img src="<?=$uthembs;?>" class="rounded" width="180" alt="Signature"></td></tr>
                        <tr><td class="text-center">Signature of Candidate</td></tr>
                    </table>
                </div>
                <div class="col-12 text-center mt-5">
                    This is auto generated admission test admit card. No authority signature required. If you find any issue please contact with us.
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
