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
    <div class="col-lg-12 animated fadeInRight">
        <div class="ibox ">           
            <div class="ibox-title">
                <div class="text-center h6">
                    Class: <?=esc($selectedClass->title);?> [<?=esc($selectedClass->fcs_id);?>], 
                    Session/Year: <?=esc($selectedSession);?>,
                    <?php $uid = intval(service('request')->getget('student_id')); if($uid > 0){ echo "(UID: $uid)"; } ?>
                </div> 
            </div>
            <?php if(isset($coursesUnderClassM) AND is_array($coursesUnderClassM)) : ?>
                <div class="ibox-content">
                    <div class="text-center h6">
                        <?=myLang('Select from Available Courses','নির্বাচনযোগ্য কোর্সসমূহ থেকে প্রয়োজনীয় কোর্সসমূহ নির্বাচন করুন');?>
                        <?= anchor("admin/academic/course/distribution?class_id={$selectedClass->fcs_id}&cls_wise_class_session=" . urlencode($selectedSession),myLang('Update','হালনাগাদ করুন'),"class='btn btn-info btn-sm'");?>
                    </div>
                    <?=form_open(base_url("admin/admission/edit/application/by/admin?InfoPage=select-courses&class_to_admit_in={$selectedClass->fcs_id}&session_to_admit_in=".urlencode($selectedSession)."&student_id=".intval(service('request')->getGet('student_id'))),['method'=>'post']);?>
                    <?php $isUpdatingApplication = (isset($coursesUnderClassAlreadySaved) AND is_array($coursesUnderClassAlreadySaved) AND count($coursesUnderClassAlreadySaved) > 0); ?>
                        <select id="selectCourseDuelBx" name="admissionCourseSelection[]" multiple="multiple">
                            <!-- CAUTION: When user is updating applications show selected courses as SELECTED. When user is sending application first time, show mandatory courses as SELECTED. -->
                            <?php foreach($coursesUnderClassM as $clsObj ): ?>
                                <option value="<?=$clsObj->co_id;?>"
                                        <?=( $isUpdatingApplication AND in_array($clsObj->co_id,$coursesUnderClassAlreadySaved)) ? 'selected="selected"' : '' ?>
                                        <?=( ! $isUpdatingApplication ) ? 'selected="selected"' : '' ?>
                                        ><?=esc($clsObj->co_title);?> [<?=$clsObj->co_id;?>]</option>
                            <?php endforeach;?>
                            <?php if(isset($coursesUnderClassO) AND is_array($coursesUnderClassO)) foreach($coursesUnderClassO as $clsObj ): ?>
                                <option value="<?=$clsObj->co_id;?>" 
                                    <?=( $isUpdatingApplication AND in_array($clsObj->co_id,$coursesUnderClassAlreadySaved)) ? 'selected="selected"' : '' ?>d
                                        ><?=esc($clsObj->co_title);?> [<?=$clsObj->co_id;?>] - Optional</option>
                            <?php endforeach;?>
                        </select>
                        <?=form_hidden('class_to_admit_in', $selectedClass->fcs_id);?>
                        <?=form_hidden('session_to_admit_in', esc($selectedSession));?>
                        <button class="btn btn-primary btn-sm mt-4 text-center offset-md-5" name="saveCourse" value="yes" type="submit">
                            <i class="fa fa-save"></i> 
                            <?= $isUpdatingApplication ? 'Update Record' : 'Save Record & Complete Admission';?>
                        </button>
                    <?=form_close();?>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
    
</div>



<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        var dlb2 = new DualListbox("#selectCourseDuelBx",{
            availableTitle: "<?=myLang('Available courses','নির্বাচনযোগ্য বিষয়সমূহ');?>",
            selectedTitle: "<?=myLang('Selected courses','নির্বাচিত বিষয়সমূহ');?>",
            addButtonText: "Add",
            removeButtonText: "Remove",
            addAllButtonText: "Add All",
            removeAllButtonText: "Remove All",
            enableDoubleClick: true,
            showAddButton: true,
            showAddAllButton: true,
            showRemoveButton: true,
            showRemoveAllButton: true,
            showSortButtons: false,
            searchPlaceholder: '<?=lang('Menu.search');?>',
        });
    });
</script>


<style {csp-style-nonce}> 
    /* Keep dual box in the center of the page. */
    .dual-listbox__container{ margin:auto;} 
</style>