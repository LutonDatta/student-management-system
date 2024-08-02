<?php
        if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
        $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
    ?>
<div class="row">
    <div class="col-lg-12 animated fadeInRight">
        <ul class="nav nav-tabs" id="settingsTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?=in_array(service('request')->getGet('showTab'),['pgSet','conMsg','ofAdr'])? '' : 'active';?>" id="institutionalInformation-tab" data-toggle="tab" href="#institutionalInformation" role="tab" aria-controls="institutionalInformation" aria-selected="true">
                    <span class="5">Institutional Information</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?=(service('request')->getGet('showTab') == 'ofAdr')? 'active' : '';?>" id="officialAddress-tab" data-toggle="tab" href="#officialAddress" role="tab" aria-controls="officialAddress" aria-selected="false">
                    <span class="5">About/Official Address</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<?=form_open(base_url('admin/institution/edit'));?>
<div class="row">
    <div class="col-lg-8 animated fadeInRight">
        <div class="tab-content" id="settingsTabContent">
            <div class="tab-pane <?=in_array(service('request')->getGet('showTab'),['pgSet','conMsg','ofAdr'])? '' : 'show active';?>" id="institutionalInformation" role="tabpanel" aria-labelledby="institutionalInformation-tab">
                    <div class="mail-box">
                        <div class="mail-body">
                            <div class="form-group row" data-toggle="tooltip">
                                <label class="col-sm-3 col-form-label">Name of institution (English)</label>
                                <div class="col-sm-9">
                                    <input name="instNameEn" value="<?=esc(get_option('instNameEn'));?>" type="text" class="form-control" >
                                </div>
                            </div>
                            <div class="form-group row"><label class="col-sm-3 col-form-label">Tagline (English)</label>
                                <div class="col-sm-9">
                                    <input name="instTaglineEn" value="<?=esc(get_option('instTaglineEn'));?>" type="text" class="form-control" >
                                </div>
                            </div>
                            
                        </div>
                        <div class="mail-body text-right">
                            <button type="submit" name="submitBtnInstSett" value="instInfoBasic" class="btn btn-sm btn-primary" ><i class="fa fa-save"></i> Save Settings</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
            </div>
            
            <div class="tab-pane <?=(service('request')->getGet('showTab') == 'ofAdr')? 'show active' : '';?>" id="officialAddress" role="tabpanel" aria-labelledby="officialAddress-tab">
                    <div class="mail-box">
                        <div class="mail-body">
                            <div class="form-group row" data-toggle="tooltip">
                                <label class="col-sm-3 col-form-label">Official Email Address</label>
                                <div class="col-sm-9">
                                    <input name="schOffEmailAddr" value="<?=esc(get_option('schOffEmailAddr'));?>" type="text" class="form-control" >
                                </div>
                            </div>
                            
                            <div class="form-group row" data-toggle="tooltip">
                                <label class="col-sm-3 col-form-label">EIIN</label>
                                <div class="col-sm-9">
                                    <input name="schOffSchEiin" value="<?=esc(get_option('schOffSchEiin'));?>" type="text" class="form-control" >
                                </div>
                            </div>
                            
                            <div class="form-group row"><label class="col-sm-3 col-form-label">Official Phone</label>
                                <div class="col-sm-9">
                                    <input name="schOffPhonNum" value="<?=esc(get_option('schOffPhonNum'));?>" type="text" class="form-control" >
                                </div>
                            </div>
                            <div class="form-group row"><label class="col-sm-3 col-form-label">Country</label>
                                <div class="col-sm-9">
                                    <input name="schOffAddrCountry" value="<?=esc(get_option('schOfficialAddressCountry'));?>" type="text" class="form-control" >
                                </div>
                            </div>
                            <div class="form-group row"><label class="col-sm-3 col-form-label">District/City</label>
                                <div class="col-sm-9">
                                    <input name="schOffAddrDistrict" value="<?=esc(get_option('schOfficialAddressDistrict'));?>" type="text" class="form-control" >
                                </div>
                            </div>
                            <div class="form-group row"><label class="col-sm-3 col-form-label">Post/Union</label>
                                <div class="col-sm-9">
                                    <input name="schOffAddrLine1" value="<?=esc(get_option('schOfficialAddressPost'));?>" type="text" class="form-control" >
                                </div>
                            </div>
                            <div class="form-group row"><label class="col-sm-3 col-form-label">Post/Zip Code</label>
                                <div class="col-sm-9">
                                    <input name="schOffAddrPostCode" value="<?=esc(get_option('schOfficialAddressPostCode'));?>" type="text" class="form-control" >
                                </div>
                            </div>
                        </div>
                        <div class="mail-body text-right">
                            <button type="submit" name="submitBtnInstSett" value="about" class="btn btn-sm btn-primary" ><i class="fa fa-save"></i> Save About</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
            </div>
        </div>
    </div>
    
    
</div>


<?=form_close();?>


