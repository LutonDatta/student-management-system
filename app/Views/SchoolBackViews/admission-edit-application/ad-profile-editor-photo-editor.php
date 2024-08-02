<?php
    if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
    $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
?>


<?php   
    // Load admission application NAV bar in few application process prat page from a single file.
    echo view('SchoolBackViews/admission-edit-application/ad-admission-process-nav-bar');
?>


<article>
    <div class="row">
        <div class="col-lg-4">
            <div class="ibox border-bottom">
                <div class="ibox-title">
                    <h5>
                        <?=myLang('Photo of Student - 300x300px','শিক্ষার্থীর ছবি - 300x300px');?>
                        <div class="small">Images may take one hour to update across the site. Accepted dimensiont 300x300px</div>
                    </h5>
                    <div class="ibox-tools"><a class="collapse-link" href=""><i class="fa fa-chevron-down"></i></a></div>
                </div>
                <div class="ibox-content">
                    <div class="text-center">
                        <img id="avatar2" src="<?=cdn_url('default-images/sketch-profile-pi-300x300.png');?>" class="rounded img-fluid img-" alt="Profile Picture" width="300" height="300">
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary mt-4" data-ip-modal="#avatarModal"><?=lang('Sa.btn_change_photo');?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox border-bottom">
                <div class="ibox-title">
                    <h5>    
                        <?=myLang('Signature of Student - 300x80px','শিক্ষার্থীর স্বাক্ষর - 300x80px');?>
                        <div class="small">Images may take one hour to update across the site. Accepted dimensiont 300x80px</div>
                    </h5>
                    <div class="ibox-tools"><a class="collapse-link" href=""><i class="fa fa-chevron-down"></i></a></div>
                </div>
                <div class="ibox-content">
                    <div class="text-center">
                        <img id="sign2" src="<?=cdn_url('default-images/sign.png');?>" class="rounded img-fluid img-" alt="Signature Picture" max-width="300" max-height="300">
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary mt-4" data-ip-modal="#signModal"><?=lang('Sa.btn_update_sign');?></button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 animated fadeInRight">
            <div class="widget-text-box text-center">
                <div>
                    <?php $uid = intval(service('request')->getget('student_id')); echo "(UID: $uid)"; ?>
                </div>
                <div>
                    <?=anchor("admin/admission/student/list",'Show Student List',['class'=>'btn btn-success mb-2']);?>
                </div>
                
            </div>
        </div>
        
    </div>
    
</article>

<!-- thumb-taker -->
<link rel="stylesheet" href="<?=cdn_url('thumb-taker/assets/css/bootstrap.css');?>">
<link rel="stylesheet" href="<?=cdn_url('thumb-taker/assets/css/imgpicker.css');?>">
<script src="<?=cdn_url('thumb-taker/assets/js/jquery.Jcrop.min.js');?>" defer="defer"></script>
<script src="<?=cdn_url('thumb-taker/assets/js/jquery.imgpicker.js');?>" defer="defer"></script>


<!-- Avatar Modal -->
<div class="ip-modal" id="avatarModal">
        <div class="ip-modal-dialog">
                <div class="ip-modal-content">
                        <div class="ip-modal-header">
                                <a class="ip-close" title="Close">&times;</a>
                                <h4 class="ip-modal-title">Change avatar (300x300px & 200kb)</h4>
                        </div>
                        <div class="ip-modal-body">
                                <div class="btn btn-primary ip-upload">Upload <input type="file" name="file" class="ip-file"></div>
                                <button type="button" class="btn btn-primary ip-webcam">Webcam</button>
                                <!--<button type="button" class="btn btn-info ip-edit">Edit</button>-->
                                <!--<button type="button" class="btn btn-danger ip-delete">Delete</button>-->

                                <div class="alert ip-alert"></div>
                                <div class="ip-info">To crop this image, drag a region below and then click "Save Image"</div>
                                <div class="ip-preview"></div>
                                <div class="ip-rotate">
                                        <button type="button" class="btn btn-default ip-rotate-ccw" title="Rotate counter-clockwise"><i class="icon-ccw"></i></button>
                                        <button type="button" class="btn btn-default ip-rotate-cw" title="Rotate clockwise"><i class="icon-cw"></i></button>
                                </div>
                                <div class="ip-progress">
                                        <div class="text">Uploading</div>
                                        <div class="progress progress-striped active"><div class="progress-bar"></div></div>
                                </div>
                        </div>
                        <div class="ip-modal-footer">
                                <div class="ip-actions">
                                        <button type="button" class="btn btn-success ip-save">Save Image</button>
                                        <button type="button" class="btn btn-primary ip-capture">Capture</button>
                                        <button type="button" class="btn btn-default ip-cancel">Cancel</button>
                                </div>
                                <button type="button" class="btn btn-default ip-close">Close</button>
                        </div>
                </div>
        </div>
</div>
<!-- end Modal -->
<!-- Sign Modal -->
<div class="ip-modal" id="signModal">
        <div class="ip-modal-dialog">
                <div class="ip-modal-content">
                        <div class="ip-modal-header">
                                <a class="ip-close" title="Close">&times;</a>
                                <h4 class="ip-modal-title">Change Signature (300x80px & 100kb)</h4>
                        </div>
                        <div class="ip-modal-body">
                                <div class="btn btn-primary ip-upload">Upload <input type="file" name="file" class="ip-file"></div>
                                <button type="button" class="btn btn-primary ip-webcam">Webcam</button>
                                <!--<button type="button" class="btn btn-info ip-edit">Edit</button>-->
                                <!--<button type="button" class="btn btn-danger ip-delete">Delete</button>-->

                                <div class="alert ip-alert"></div>
                                <div class="ip-info">To crop this image, drag a region below and then click "Save Image"</div>
                                <div class="ip-preview"></div>
                                <div class="ip-rotate">
                                        <button type="button" class="btn btn-default ip-rotate-ccw" title="Rotate counter-clockwise"><i class="icon-ccw"></i></button>
                                        <button type="button" class="btn btn-default ip-rotate-cw" title="Rotate clockwise"><i class="icon-cw"></i></button>
                                </div>
                                <div class="ip-progress">
                                        <div class="text">Uploading</div>
                                        <div class="progress progress-striped active"><div class="progress-bar"></div></div>
                                </div>
                        </div>
                        <div class="ip-modal-footer">
                                <div class="ip-actions">
                                        <button type="button" class="btn btn-success ip-save">Save Image</button>
                                        <button type="button" class="btn btn-primary ip-capture">Capture</button>
                                        <button type="button" class="btn btn-default ip-cancel">Cancel</button>
                                </div>
                                <button type="button" class="btn btn-default ip-close">Close</button>
                        </div>
                </div>
        </div>
</div>
<!-- end Modal -->


<script {csp-script-nonce}>
    
    document.addEventListener("DOMContentLoaded", function(){        
        // Fix lightbox modal shadow by moving box in the parent item
        $('#avatarModal').appendTo($('body'));
        $('#signModal').appendTo($('body'));
        
        var time = function(){return'?'+new Date().getTime()};

        // Avatar setup.
        // ?load_avatar_of=4 will be used to show images
        // ?upload_avatar_for=4 will be used to upload avaters for
        $('#avatarModal').imgPicker({
            url: '<?=base_url('api/v1/ip/upload/student/thumb/by/teacher?load_avatar_of='. intval(is_object($updating_student) ? $updating_student->student_u_id : 0));?>',
            aspectRatio: 1,
            swf: '<?=cdn_url('thumb-taker/assets/webcam.swf');?>',
            loadComplete: function(image) {
                <?= ENVIRONMENT !== 'production' ? "console.log('loadComplete,image',image);" : '';?>
                if (image.url) {
                    $('#avatar2').attr('src', image.url +time() );
                    this.setImage(image);
                }
            },
            cropSuccess: function(image) {
                $('#avatar2').attr('src', image.url +time() );
                $('#avatar2prv').attr('src', image.url +time() );
                this.modal('hide');
            }
        });
        $('#signModal').imgPicker({
            url: '<?=base_url('api/v1/ip/upload/student/sign/by/teacher?load_sign_of=' . intval(is_object($updating_student) ? $updating_student->student_u_id : 0));?>',
            aspectRatio: 300/80,
            loadComplete: function(image) {
                <?= ENVIRONMENT !== 'production' ? "console.log('loadComplete,image',image);" : '';?>
                if (image.url) {
                    $('#sign2').attr('src', image.url +time() );
                    this.setImage(image);
                }
            },
            cropSuccess: function(image) {
                $('#sign2').attr('src', image.url +time() );
                $('#sign2prv').attr('src', image.url +time() );
                this.modal('hide');
            }
        });
        
    });
</script>



