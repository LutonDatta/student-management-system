<article class="row">
    <div class="col-lg-12 animated fadeInRight">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
    </div>
</article>

<?php   
    // Load admission application NAV bar in few application process prat page from a single file.
    echo view('SchoolBackViews/admission-edit-application/ad-admission-process-nav-bar');
?>



<div class="row">
    <div class="col-lg-6 animated fadeInRight">
        <div class="mail-box-header">
            <h3>
                Select class and session/year
                <?php $uid = intval(service('request')->getget('student_id')); if($uid > 0){ echo "(UID: $uid)"; } ?>
            </h3>
        </div>
        <div class="mail-box">
            <div class="mail-body">
                    <?=form_open(base_url("admin/admission/edit/application/by/admin"),['method'=>'get'],[
                        'InfoPage'  => 'select-courses',
                        'student_id'=>intval(service('request')->getGet('student_id'))
                    ]);?>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="redOnChgSelCls">
                                Select Class
                                <?=anchor('admin/academic/setup','<i class="far fa-edit"></i>',['class'=>'ml-3']);?>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group mb-2">
                                    <?=form_dropdown('class_to_admit_in', $admitAbleClasses,[], ['class'=>'form-control','id'=>'redOnChgSelCls','required'=>'required']);?>
                                </div>
                                <div><?php echo($admitAbleClassesPgr);?></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="sessYear">Input Session/Year</label>
                            <div class="col-sm-9">
                                <div class="input-group mb-2">
                                    <?=form_input('session_to_admit_in','',['class'=>"form-control",'id'=>'selSessYr']);?>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm text-center offset-md-5" name="saveCourse" value="yes" type="submit">
                            Select Class & Session
                        </button>
                    <?=form_close();?>
            </div>
        </div>
    </div>
    
</div>


