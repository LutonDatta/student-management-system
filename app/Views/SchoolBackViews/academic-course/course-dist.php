<div class="row">
<?php 
    // We may have errors, show at the beginning in small characters
    $formErrors = ( ! empty($validation) ) ? $validation->getErrors() : []; 
    if(is_array($formErrors) AND count($formErrors) > 0){
        echo '<div class="alert-danger small text-center p-2 w-100" role="alert">';
        foreach($formErrors as $name => $errStr ) echo esc($errStr).'<br/>';
        echo '</div>';
    }
?>  
    <div class="col-lg-6 animated fadeInRight">
        <div class="ibox ">
            <div class="ibox-title pr-3">
                <?=anchor('admin/academic/course','Update',['class'=>'float-right label-info p-1 btn']);?>
                <h5>
                    Select Course for: 
                    <b>
                        <?= (isset($selectedClass) AND is_object($selectedClass) AND property_exists($selectedClass, 'fcs_title')) ? esc($selectedClass->fcs_title) : '';?>
                    </b>
                </h5>
            </div>
            <div class="ibox-content">

                <?=form_open($submit_form_to,['method'=>'get'],['class_id'=>$loaded_class,'cls_wise_class_session'=>$loaded_session]);?>
                    <div class="form-group  row">
                        <label class="col-sm-2 col-form-label">Search Courses</label>
                        <div class="col-sm-8">
                            <input type="text" name="searchCourseTxt" class="form-control form-control-sm" value="<?= isset($searchCourseTxt) ? esc($searchCourseTxt) : '';?>">
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-secondary btn-sm" type="submit"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </div>
                <?=form_close();?>

                <?=form_open($submit_form_to,['id'=>'uPrmForm'],['acCouAtt_submit'=>'yes']);?>
                    <div class="form-group  row">
                        <label class="col-sm-2 col-form-label">Select courses for this class</label>
                        <div class="col-sm-10">    
                            <?php if(isset($allCourseItems)) : ?>
                                <?php foreach( $allCourseItems as $coObj  ): ?>
                                    <div>
                                        <label> 
                                            <input type="checkbox" name="coClasses[]" value="<?=esc($coObj->co_id);?>" <?=in_array($coObj->co_id,$currentCourseItems) ? 'disabled' : '';?>> 
                                            <?=esc($coObj->co_title);?>
                                            <?= (strlen($coObj->co_code) > 0) ?  ' ('.$coObj->co_code.')'  :  ''; ?>
                                            [<?=esc($coObj->co_id);?>]
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                    <div class="form-group  row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-10"><?= isset($allCourseItems_pgr) ? $allCourseItems_pgr : ''; ?></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group row">
                        <div class="col-sm-12">
                            <button class="btn btn-primary btn-sm mb-1" type="submit" name="course_type" value="mandatory"><i class="fa fa-save"></i> Add as mandatory course</button>
                            <button class="btn btn-primary btn-sm mb-1" type="submit" name="course_type" value="optional"><i class="fa fa-save"></i> Add as optional course</button>
                            <a class="btn btn-primary btn-sm mb-1" href="<?= base_url('admin/academic/course/distribution?class_id='.urlencode(service('request')->getGet('class_id')).'&cls_wise_class_session='.urlencode(service('request')->getGet('cls_wise_class_session')));?>"><i class="fa fa-undo"></i> Refresh </a>
                        </div>
                    </div>
                <?=form_close();?>
            </div>
        </div>
    </div>
    
    
    
    <div class="col-lg-3 animated fadeInRight">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Currently selected Courses</h5>
            </div>
            <div class="ibox-content">
                <?=form_open($submit_form_to,['id'=>'alreadySectdForm'],['acCxd_sub'=>'yes']);?>
                    <div class="form-group  row">
                        <div class="col-sm-12">
                            <?php if(isset($oldAttachedItems) AND is_array($oldAttachedItems)) : ?>
                                <?php foreach( $oldAttachedItems as $coObjStd  ):  ?>
                                <div>
                                    <label> 
                                        <input type="checkbox" name="coClsDelete[]" value="<?=esc($coObjStd->co_id);?>"> 
                                        <?=esc($coObjStd->co_title);?>
                                        <?= (strlen($coObjStd->co_code) > 0) ?  ' ('.$coObjStd->co_code.')'  :  ''; ?>
                                        [<?=esc($coObjStd->co_id);?>] 
                                        <?= (intval($coObjStd->ccm_is_compulsory) === 0) ? '<i data-toggle="tooltip" title="This is optional course" class="far fa-bell-slash text-danger"></i> -' . myLang('(Optional)','(ঐচ্ছিক)') : '';?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                                <?php 
                                $numManCrs = array_filter(array_map(function($ar){return (intval($ar->ccm_is_compulsory) === 0) ? false: true;},$oldAttachedItems));
                                if(count($numManCrs) < 3){
                                    if(! function_exists('counted')) helper('inflector');
                                    echo get_display_msg("Warning: Must have at least 3 mandatory courses. " . counted(count($numManCrs), 'course') . ' found.','danger');
                                }
                                ?>
                            <?php endif; ?>
                            <div class=""><?= isset($allCoursePager) ? $allCoursePager : ''; ?></div>
                            <div>Students can select maximum 15 mandatory and 5 optional courses.</div>
                        </div>
                    </div>
                    
                    <div class="hr-line-dashed"></div>

                    <div class="form-group row">
                        <div class="col-sm-12">
                            <button class="btn btn-primary btn-sm" type="submit" name="submit_type" value="delete"><i class="fa fa-trash"></i> Remove selected courses</button>
                        </div>
                    </div>
                <?=form_close();?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 animated fadeInRight">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Already saved</h5>
            </div>
            <div class="ibox-content">
                <?php if(isset($already_saved_class_wise_courses) AND isset($already_saved_class_wise_courses['data']) AND is_array($already_saved_class_wise_courses['data']) AND count($already_saved_class_wise_courses['data']) > 0): ?>
                <ol>
                    <?php foreach($already_saved_class_wise_courses['data'] as $xv) : ?>
                    <li>
                        <a href="<?=base_url("admin/academic/course/distribution?class_id=$xv->ccm_class_id&cls_wise_class_session=".esc($xv->ccm_year_session));?>">
                            <?=esc($xv->simple_title);?>[<?=esc($xv->ccm_year_session);?>]
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <?=service('pager')->makeLinks(
                            intval(service('request')->getGet('page_saved_class_edit')),
                            20,
                            $already_saved_class_wise_courses['count'],
                            'default_full',
                            0,
                            'saved_class_edit'
                            ); ?>
                </ol>
                <?php else: ?>
                    You do not have any saved data. Please use correct session your your while saving data.
                <?php endif;?>
            </div>
        </div>
    </div>
</div>

