<div class="row">
    <div class="col-lg-6">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        <div class="ibox ">
            <div class="ibox-content text-center p-md">
                <div class="h4 m-b-xxs">
                    <?=$formTitle;?>
                </div>
                <p>Course refers to the various subjects which are studied to the institutions. Such as English, Bangla, Accounting, Finance etc.</p>
                <?=form_open(base_url($submit_form_to));?>
                    <div class="m-t-md">

                            <div class="form-group row"><label class="col-sm-2 col-form-label text-left"><?=lang('Admin.title');?></label>
                                <div class="col-sm-10">
                                    <input required="required" name="ac_title" value="<?=set_value('ac_title',(!empty($updateItemData) AND is_object($updateItemData)) ? $updateItemData->co_title : '');?>" type="text" class="form-control"> 
                                    <?= isset($errors) ? get_form_error_msg_from_array($errors,'ac_title') : '';?>
                                    <span class="form-text m-b-none text-left">Simple title of a course such as English.</span>
                                </div>
                            </div><div class="hr-line-dashed"></div>
                            
                            <div class="form-group row"><label class="col-sm-2 col-form-label text-left">Course Code</label>
                                <div class="col-sm-10">
                                    <input name="ac_code" value="<?=set_value('ac_code',(!empty($updateItemData) AND is_object($updateItemData)) ? $updateItemData->co_code : '');?>" type="text" class="form-control"> 
                                    <?= isset($errors) ? get_form_error_msg_from_array($errors,'ac_code') : '';?>
                                    <span class="form-text m-b-none text-left">Course code can be anything like En101.</span>
                                </div>
                            </div><div class="hr-line-dashed"></div>

                            <div class="form-group row"><label class="col-sm-2 col-form-label text-left"><?=lang('Admin.short_explanation');?></label>
                                <div class="col-sm-10">
                                    <input name="ac_excerpt" value="<?=set_value('ac_excerpt',(!empty($updateItemData) AND is_object($updateItemData)) ? $updateItemData->co_excerpt : '');?>" type="text" class="form-control"> 
                                    <?= isset($errors) ? get_form_error_msg_from_array($errors,'ac_excerpt') : '';?>
                                    <span class="form-text m-b-none text-left">Add short explanation here related to it.</span>
                                </div>
                            </div><div class="hr-line-dashed"></div>

                            <button type="submit" name="acCourses_submit" value="yes" class="btn btn-info"><i class="fas fa-save"></i> Submit and Save</button>
                            <?php 
                                $update_id = intval(service('request')->getGet('edit_id'));
                                $addNuURL = anchor('admin/academic/course','<span class="btn btn-primary mr-1">Add New</span>');
                                if($update_id > 0){ echo $addNuURL; }
                            ?>
                    </div>
                <?=form_close();?>
                
                <?php 
                    if( $update_id > 0 ){
                        echo '<div class="mt-3">';
                            echo form_open(base_url('admin/academic/course'));
                            echo form_hidden('del_cid',$update_id);
                            echo '<button type="submit" id="clsDelCrsHk" name="acCourses_delete_submit" value="yes" class="btn btn-danger"><i class="fas fa-save"></i> Delete This Item</button>';
                            echo form_close();
                        echo '</div>';
                    }
                ?>
            </div>
        </div>
    </div>
    

    <div class="col-lg-6">
        <div class="ibox ">
            <div class="ibox-content text-center p-md">
                <div class="h6 m-b-xxs">Academic Courses</div>
                <div class="h6 m-b-xxs m-1"><span class="label label-primary">Online Admission Safety Active</span></div>
                <p>Available academic courses which can be read by the students from various departments and classes.</p>
                <div class="m-t-md">
                    <div id="jstree" class="text-left">
                        <?php if(count($allItems) > 0) :  ?>
                        <ul>
                            <?php foreach( $allItems as $id => $obj ) : ?>
                                    <li data-jstree='{"icon":"fas fa-book-reader"}' data-itemID="<?=$obj->co_id;?>">
                                        <?php 
                                        $d_title = $obj->co_title . (strlen($obj->co_code) > 0 ? ' ('.$obj->co_code.')' : '') . " [{$obj->co_id}]";
                                        echo esc($d_title);
                                        ?>
                                    </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else : ?>
                                <ul>
                                    <li>Please add new course</li>
                                </ul>
                        <?php endif; ?>
                    </div>
                </div>
                 
            </div>
        </div>
            <?= is_object($pager) ? $pager->links('courses') : ''; ?>
    </div>
</div>



<link href="<?=cdn_url('jstree/dist/themes/default/style.min.css');?>"  rel="stylesheet">
<script src="<?=cdn_url('jstree/dist/jstree.min.js');?>" defer="defer"></script>
<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $('#jstree').jstree({
            "plugins":[
                /*"sort"*//* Do not sort, it will break sorting sent from server based on ID desc*/
            ]});
        $('#jstree').on("changed.jstree", function (e, data) { 
            // redirect edit page when user click to label
            location.href = '<?=base_url('admin/academic/course?edit_id=');?>' + data.node.data.itemid;
        });
        /* Delete confirmation. */
        jQuery('#clsDelCrsHk').on('click',function(){ 
            return confirm('Are you sure to delete it? Other students might not find this if you delete it.');
        });
    });
</script>


<script src="<?= cdn_url('js/show-spinner-on-submit.js');?>"></script>