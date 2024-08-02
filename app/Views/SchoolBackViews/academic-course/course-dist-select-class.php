<div class="row">
    <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
    
    
    <div class="col-lg-12 animated fadeInRight">
        <div class="ibox ">
            <div class="ibox-title pr-3">
                <?=anchor('admin/academic/setup','Update',['class'=>'float-right label-info p-1 btn']);?>
                <h5>Course Combination with Classes</h5>
            </div>
            <div class="ibox-content">
                <?=form_open(base_url("admin/academic/course/distribution"),['method'=>'get']);?>
                <div class="row">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label class="col-form-label">Select Class</label>
                            <?php 
                                if( count($allClassItems) < 1) $allClassItems = [];
                                $allClassItems = [''=>'Select class'] + $allClassItems;
                                $class_id_sel = (isset($selectedClass) AND is_object( $selectedClass)) ? $selectedClass->fcs_id : '';
                                echo form_dropdown('class_id', $allClassItems, $class_id_sel, ['class'=>'form-control','required'=>'required']);
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label class="col-form-label">Enter Session/Year</label>
                            <input type="text" name="cls_wise_class_session" required="required" class="form-control" 
                                   value="<?=esc(preg_replace( "/\s+/", "", service('request')->getGet('cls_wise_class_session')));?>">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-primary btn-sm mt-4" type="submit"><i class="fa fa-save"></i> Select</button>
                    </div>
                </div>
                <?=form_close();?>
            </div>
        </div>
    </div>
</div>

<script src="<?= cdn_url('js/show-spinner-on-submit.js');?>"></script>