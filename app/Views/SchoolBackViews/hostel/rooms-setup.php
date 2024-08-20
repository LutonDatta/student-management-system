<div class="row">
    <div class="col-lg-8">
        
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        <?php 
            if(isset($updateItemData) AND is_object($updateItemData)){
                if($updateItemData->hos_del_at){ 
                    echo get_display_msg('This item has already been deleted. Deleted '. time_elapsed_string($updateItemData->hos_del_at).' at: ' . esc($updateItemData->hos_del_at), 'danger');           
                }
            }
        ?>
        <div class="ibox ">
            <div class="ibox-content text-center p-md">
                <div class="h4 m-b-xxs"><?=$formTitle;?></div>
                <p>
                    Schools do not need to have buildings. They can simply add rooms or sheds.
                </p>
                <div class="m-t-md">
                    <?=form_open(base_url($submit_form_to),['id'=>'academicSetUp_classFaculty']);?>
                            <div class="form-group row"><label class="col-sm-2 col-form-label text-left"><?=lang('Admin.title');?></label>
                                <div class="col-sm-10">
                                    <input name="acSeUp_title" value="<?=set_value('acSeUp_title',(!empty($updateItemData) AND is_object($updateItemData)) ? $updateItemData->hos_title : '');?>" type="text" required="required" class="form-control"> 
                                    <?= isset($errors) ? get_form_error_msg_from_array($errors,'hos_title') : ''; ?>
                                    <span class="form-text m-b-none text-left">Simple title of class like Room 101. It can be name of building, floor, room.</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group row"><label class="col-sm-2 col-form-label text-left"><?=lang('Admin.short_explanation');?></label>
                                <div class="col-sm-10">
                                    <input name="acSeUp_excerpt" value="<?=set_value('acSeUp_excerpt',(!empty($updateItemData) AND is_object($updateItemData)) ? $updateItemData->hos_excerpt : '');?>" type="text" class="form-control"> 
                                    <?= isset($errors) ? get_form_error_msg_from_array($errors,'hos_excerpt') : ''; ?>
                                    <span class="form-text m-b-none text-left">Add short explanation here related to it.</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group row"><label class="col-sm-2 col-form-label text-left">Parent</label>
                                <div class="col-sm-10">
                                    <?php
                                    $parents = ['' => '']; 
                                    $parents = $parents + $parentItemsLabel;
                                    ?>
                                    <?=form_dropdown('acSeUp_parent', $parents,[set_value('acSeUp_parent',(!empty($updateItemData) AND is_object($updateItemData)) ? strval($updateItemData->hos_parent) : '')], ['class'=> "form-control"]); ?>
                                    <?= isset($errors) ? get_form_error_msg_from_array($errors,'hos_parent') : ''; ?>
                                    <span class="form-text m-b-none text-left">Is it has any parent floor or building? </span>
                                    <?=isset($parentItemsLabPg)? $parentItemsLabPg : '';?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            
                            <div class="form-group row"><label class="col-sm-2 col-form-label text-left">Capacity</label>
                                <div class="col-sm-10">
                                    <input name="hos_capacity" value="<?=set_value('hos_capacity',(!empty($updateItemData) AND is_object($updateItemData)) ? $updateItemData->hos_capacity : '1');?>" type="text" required="required" class="form-control"> 
                                    <?= isset($errors) ? get_form_error_msg_from_array($errors,'hos_capacity') : ''; ?>
                                    <span class="form-text m-b-none text-left">In case of building, capacity should be number of floors. In case of floor capacity should be number of rooms in the floor. In case of room capacity should be number of bed/seat inside it.</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            
                            <button type="submit" name="acasetupClsFac_submit" value="yes" class="btn btn-primary">Submit and Save</button>
                    <?=form_close();?>
                    <?php if(is_object($updateItemData)) : ?>  
                        <?=form_open(base_url($submit_form_to),['id'=>'dlClsem']);?>
                            <button type="submit" name="acasetupClsFcl_delete" value="yes" class="btn btn-danger mt-4">Delete this item</button>
                            <?= anchor('admin/hostel/rooms','Add New',['class' => 'btn btn-secondary mt-4']);?>
                        <?=form_close();?>     
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ibox ">
            <div class="ibox-content text-center p-md">
                <div class="h6 m-b-xxs">Hostel, Floor & Room Structure</div>
                <div class="h6 m-b-xxs m-1"><span class="label label-primary">Online Admission Safety Active</span></div>
                <p>
                    Available hostel building, floors, rooms. Students can be admitted 
                    to any of the following sections.
                </p>
                <div class="m-t-md">
                    <div id="jstree2" class="text-left"></div>
                </div>                
            </div>
        </div>
        
        <div class="ibox ">
            <div class="ibox-content text-left p-md">
                <p>All available hostel building, floors, rooms are listed below. Use pagination for different pages.</p>
                <ul class="list-unstyled">
                    <?php foreach($allItems as $rowpItem ) : ?>
                        <li>
                            <i class="fas fa-caret-right"></i>
                            <?= anchor(
                                    base_url('admin/hostel/rooms?edit_id='.$rowpItem->hos_id),
                                    esc($rowpItem->title)
                            ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?=isset($allItemsPager)? $allItemsPager : '';?>
            </div>
            
        </div>
    </div>
</div>


<link href="<?=cdn_url('jstree/dist/themes/default/style.min.css');?>"  rel="stylesheet">
<script src="<?=cdn_url('jstree/dist/jstree.min.js');?>" defer="defer"></script>
<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $('#jstree2').jstree({
            'core' : {
                'data' : {
                    'url' : function (node) {
                        return node.id === '#' ? '<?=base_url('api/v1/hostels?parent_id=0');?>' : '<?=base_url('api/v1/hostels?parent_id=');?>' + node.id;
                    },
                    'data' : function (node) {
                        return { 'id' : node.text };
                    }
                }
            }
        });
        $('#jstree2').jstree({"plugins":["sort"]});
        $('#jstree2').on("changed.jstree",function(e,data){ /* redirect to edit page when user click to label */
            location.href = '<?=base_url('admin/hostel/rooms?edit_id=');?>' + data.node.id;
        });
        /* Delete confirmation. */
        jQuery('#clsDelConfHk').on('click',function(){ 
            return confirm('Are you sure to delete it? Other students might not find this if you delete it.');
        });
    });
</script>
    

<script src="<?= cdn_url('js/show-spinner-on-submit.js');?>"></script>