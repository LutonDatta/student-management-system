</div>
        <script src="<?=cdn_url().'jquery/3.4.1/jquery-3.4.1.min.js';?>"></script>
        <script src="<?=cdn_url().'popper.min.js';?>"></script>
        <script src="<?=cdn_url().'bootstrap-4.3.1-dist/js/bootstrap.min.js';?>"></script>    
        <script src="<?=cdn_url().'jquery.metisMenu.js';?>"></script>
        <script src="<?=cdn_url().'dashboard.js';?>"></script>
        <script src="<?=cdn_url().'slimscroll/jquery.slimscroll.min.js';?>"></script>
        
        <script src="<?=cdn_url().'select2-4.0.13/dist/js/select2.min.js';?>"></script>
        <script src="<?=cdn_url('select2-4.0.13/dist/js/i18n/bn.js');?>"></script>
                
        <script src="<?=cdn_url().'bs-custom-file-input.min.js';?>" defer="defer"></script>
        <script {csp-script-nonce}>$(function(){$('[data-toggle="tooltip"]').tooltip();});</script>
        <script src="<?=cdn_url().'jquery-ui-1.12.1.custom/jquery-ui.min.js';?>" defer="defer"></script>
        
        <script src="<?=cdn_url('js/dual-listbox-master-dist/dual-listbox.js');?>"></script>
        <link href="<?=cdn_url('js/dual-listbox-master-dist/dual-listbox.css');?>" rel="stylesheet" type="text/css" >
        
        <link href="<?=cdn_url().'select2-4.0.13/dist/css/select2.min.css'; ?>" rel="stylesheet">
        <link href="<?=cdn_url().'jquery-ui-1.12.1.custom/jquery-ui.min.css';?>" rel="stylesheet">
        
        <?php if(service('request')->getUserAgent()->isMobile()): ?>
            <!-- In mobile devices, some contents were not visible, fix it. -->
            <style {csp-style-nonce}>
                #page-wrapper > div.wrapper.wrapper-content{ padding-bottom: 400px !important; }
            </style>
        <?php endif; ?>
</body>
</html>
