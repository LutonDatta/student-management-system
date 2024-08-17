<div class="row">
    <?php
        if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
        $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
    ?>
</div>

<?php service('ShowLinksLibrary')->show_links_on_mobile_devices(isset($loadedPage) ? $loadedPage : (isset($showingPage) ? $showingPage : '')); ?>
           
<div class="row" data-masonry='{"itemSelector": ".col-lg-3", "percentPosition": true}'>
    
    <div class="col-lg-3">
        <div class="widget white-bg no-padding text-center">
            <div class="p-m">
                <h1 class="m-xs" id="stat_total_num_student">
                    <div class="spinner-border text-success d-none" role="status"><span class="sr-only">Loading...</span></div>
                    <div type="button"><i class="fas fa-cloud-download-alt"></i></div>
                </h1>
                <h3 class="font-bold no-margins"><?=lang('Admin.students');?></h3>
                <small><?=lang('Admin.students_excerpt');?></small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3">
        <div class="widget white-bg no-padding text-center">
            <div class="p-m">
                <h1 class="m-xs" id="stat_total_num_books">
                    <div class="spinner-border text-success d-none" role="status"><span class="sr-only">Loading...</span></div>
                    <div type="button"><i class="fas fa-cloud-download-alt"></i></div>
                </h1>
                <h3 class="font-bold no-margins">
                    Total Books [Copies 
                        <span id="stat_total_num_books_q">
                            <div class="spinner-border spinner-border-sm text-success d-none" role="status"><span class="sr-only">Loading...</span></div>
                            <span type="button"><i class="fas fa-cloud-download-alt"></i></span>
                        </span>
                    ]
                </h3>
                <small>Total number of books in library.</small>
            </div>
        </div>
    </div>
    
    
    <div class="col-lg-3">
        <div class="widget lazur-bg no-padding text-center">
            <div class="p-m">
                <h1 class="m-xs" id="stat_total_num_classes">
                    <div class="spinner-border text-success d-none" role="status"><span class="sr-only">Loading...</span></div>
                    <div type="button"><i class="fas fa-cloud-download-alt"></i></div>
                </h1>
                <h3 class="font-bold no-margins">Classes and Faculties</h3>
                <small>Total number of classes added.</small>
            </div>
        </div>
    </div>
    
    
    <div class="col-lg-3">
        <div class="widget lazur-bg no-padding text-center">
            <div class="p-m">
                <h1 class="m-xs" id="stat_total_num_courses">
                    <div class="spinner-border text-success d-none" role="status"><span class="sr-only">Loading...</span></div>
                    <div type="button"><i class="fas fa-cloud-download-alt"></i></div>
                </h1>
                <h3 class="font-bold no-margins"><?=lang('Menu.courses');?>/Subjects</h3>
                <small>Total number of courses/subjects added.</small>
            </div>
        </div>
    </div>
    
    
    <div class="col-lg-3">
        <div class="widget label-success no-padding text-center">
            <div class="p-m">
                <h1 class="m-xs" id="stat_total_invoices_paid_num" <?=tt_title('Number of paid invoices');?>>
                    <div class="spinner-border text-light d-none" role="status"><span class="sr-only">Loading...</span></div>
                    <div type="button"><i class="fas fa-cloud-download-alt"></i></div>
                </h1>
                <h3 class="font-bold no-margins">Paid Hand Cash Invoices</h3>
                <small>Number of Paid hand cash invoices.</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3">
        <div class="widget label-success no-padding text-center">
            <div class="p-m">
                <h1 class="m-xs" id="stat_total_invoices_unpaid_num">
                    <div class="spinner-border text-light d-none" role="status"><span class="sr-only">Loading...</span></div>
                    <div type="button"><i class="fas fa-cloud-download-alt"></i></div>
                </h1>
                <h3 class="font-bold no-margins">Un-Paid Hand Cash Invoices</h3>
                <small>Number of Un-Paid hand cash invoices.</small>
            </div>
        </div>
    </div>
    
</div>


<script src="<?=cdn_url('js/masonry/masonry.pkgd.min.js');?>"></script>


<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        /* Pass CSRF token with all AJAX requests */
        $.ajaxSetup({headers: { '<?=csrf_header();?>': '<?=csrf_hash();?>' }});
        
        $('#stat_total_num_student').click(function(){
            $(this).children(":nth-child(1)").removeClass('d-none');$(this).children(":nth-child(2)").addClass('d-none');
            $.post('<?=base_url('api/v1/admin/dashboard/statistics/total/student/count');?>',function(data){
                $('#stat_total_num_student').html(data.count);
            }).fail(function(){$('#stat_total_num_student').html('Failed...');});
        });

        $('#stat_total_num_books').click(function(){
            $(this).children(":nth-child(1)").removeClass('d-none');$(this).children(":nth-child(2)").addClass('d-none');
            $.post('<?=base_url('api/v1/admin/dashboard/statistics/books/count');?>',function(data){
                $('#stat_total_num_books').html(data.count);
            }).fail(function(){$('#stat_total_num_books').html('Failed...');});
        });

        $('#stat_total_num_books_q').click(function(){
            $(this).children(":nth-child(1)").removeClass('d-none');$(this).children(":nth-child(2)").addClass('d-none');
            $.post('<?=base_url('api/v1/admin/dashboard/statistics/books/quantity/count');?>',function(data){
                $('#stat_total_num_books_q').html(data.count);
            }).fail(function(){$('#stat_total_num_books_q').html('Failed...');});
        });

        $('#stat_total_num_classes').click(function(){
            $(this).children(":nth-child(1)").removeClass('d-none');$(this).children(":nth-child(2)").addClass('d-none');
            $.post('<?=base_url('api/v1/admin/dashboard/statistics/classes/count');?>',function(data){
                $('#stat_total_num_classes').html(data.count);
            }).fail(function(){$('#stat_total_num_classes').html('Failed...');});
        });

        $('#stat_total_num_courses').click(function(){
            $(this).children(":nth-child(1)").removeClass('d-none');$(this).children(":nth-child(2)").addClass('d-none');
            $.post('<?=base_url('api/v1/admin/dashboard/statistics/courses/count');?>',function(data){
                $('#stat_total_num_courses').html(data.count);
            }).fail(function(){$('#stat_total_num_courses').html('Failed...');});
        });

        $('#stat_total_invoices_paid_num').click(function(){
            $(this).children(":nth-child(1)").removeClass('d-none');$(this).children(":nth-child(2)").addClass('d-none');
            $.post('<?=base_url('api/v1/admin/dashboard/statistics/invoice/paid/count');?>',function(data){
                $('#stat_total_invoices_paid_num').html(data.count);
            }).fail(function(){$('#stat_total_invoices_paid_num').html('Failed...');});
        });
        
        $('#stat_total_invoices_unpaid_num').click(function(){
            $(this).children(":nth-child(1)").removeClass('d-none');$(this).children(":nth-child(2)").addClass('d-none');
            $.post('<?=base_url('api/v1/admin/dashboard/statistics/invoice/unpaid/count');?>',function(data){
                $('#stat_total_invoices_unpaid_num').html(data.count);
            }).fail(function(){$('#stat_total_invoices_unpaid_num').html('Failed...');});
        });
        
        
        // Trigger 429 too many request error, so trigger request on a base of intervel of 1 second
        var idToTriggerClickEventOnIt = [
            "stat_total_num_student", "stat_total_num_books","stat_total_num_books_q",
            "stat_total_num_classes", "stat_total_num_courses",  "stat_total_invoices_paid_num", "stat_total_invoices_unpaid_num"
        ];
        for (var i = 0; i < idToTriggerClickEventOnIt.length; i++){
            (function (i) {
                setTimeout(function(){
                    $('#' + idToTriggerClickEventOnIt[i] ).click();
                }, 10 * i);
            })(i);
        };
    });
</script>

