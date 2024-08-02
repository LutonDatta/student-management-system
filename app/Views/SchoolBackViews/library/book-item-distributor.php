<article class="row">
    
    <div class="col-lg-6 animated fadeInRight">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        <div class="mail-box-header"><h2>Select person and item to distribute</h2></div>
        <div class="mail-box">
            <?=form_open('admin/library/distributions');?>
            <div class="mail-body pt-3 pb-0">
                <div class="form-group row mb-4"><label class="col-sm-4 col-form-label">Select Book / Item</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="selectBk" id="selectBkItem"></select>
                        <span class="small">Select book or other item that you want to distribute to the student.</span>
                    </div>
                </div>
                <div class="form-group row mb-4"><label class="col-sm-4 col-form-label">Specify Version</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="selectBkVersion" id="selectBkVersion"></select>
                        <span class="small">Select specific version of the item. For example your one book may have many copies.</span>
                    </div>
                </div>
                <div class="form-group row mb-4"><label class="col-sm-4 col-form-label">Select Person</label>
                    <div class="col-sm-8">
                        <select class="form-control" name="selectToUser" id="selectToUser"></select>
                        <span class="small">Select student or other registered person who will take this item.</span>
                    </div>
                    
                </div>
            </div>
            <div class="mail-body">
                <button type="submit" name="distBtn" value="yes" class="btn btn-sm btn-primary mt-2"><i class="fa fa-save"></i> Distribute Now</button>
            </div>
            <?=form_close();?>
            <div class="clearfix"></div>
        </div>
    </div>
    
    
    <div class="col-lg-6 animated fadeInRight">
        <div class="mail-box-header">
            <h2>Recent Update</h2>
            <small>List of distributed library items or books.</small>
        </div>
        <div class="mail-box">
            <div class="mail-body">
                <div class="slimScrollDiv">
                    <div class="full-height-scroll">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr class="header">
                                        <th>QID</th>
                                        <th>Title</th>
                                        <th>Code</th>
                                        <th class="text-center">Copy ID</th>
                                        <th class="text-center" title="How many time this book/item is distributed to the students?">Turnover</th>                
                                        <th class="text-center">Distributed</th>                
                                        <th class="text-center"></th>                
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($booksDist) AND is_array($booksDist) AND count($booksDist) > 0 ): ?>
                                        <?php foreach($booksDist as $row ) :  ?>
                                            <tr>
                                                <td><?=esc($row->lq_id);?></td>
                                                <td><?=esc($row->bk_title);?></td>
                                                <td><?=esc($row->bk_code);?>-<?=esc($row->lq_serial_number);?></td>
                                                <td class="text-center"><?=esc($row->lq_serial_number);?></td>
                                                <td class="text-center"><?=(intval($row->lq_turnover) > 0) ? sprintf("%02d", esc($row->lq_turnover)) : '';?></td>
                                                <td class="text-center"><?php if(! is_null( $row->lq_distributed_at)) echo time_elapsed_string ($row->lq_distributed_at);?></td>
                                                <td class="text-center" data-toggle="tooltip" data-placement="left" title="Mark as Collected">
                                                    <?php if( intval($row->lq_is_distributed) > 0): ?>
                                                        <?=anchor("admin/library/distributions?markMeCollected=" . esc($row->lq_id),'<i class="fab fa-get-pocket h6"></i>');?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr><td colspan="7" class="text-center">No items found</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>           
            <div class="clearfix"></div>
        </div>
        <div class="text-center"><?=(isset($subPager) AND is_object($subPager)) ? $subPager->links('booksDistributed') : '';?></div>
    </div>
    
    
    
</article>



<script {csp-script-nonce}>
    window.addEventListener('load', function(event){
        jQuery.ajaxSetup({ headers: { '<?=csrf_header();?>': '<?=csrf_hash();?>' } });
        
        $('#selectBkItem').select2({ minimumInputLength: 1,
            ajax: { url: "<?=base_url('api/v1/select2/view/books')?>",
                dataType: 'json', method: 'POST', delay: 250, cache: true,
                data: function (params) { return { inputTxt: params.term, pageNumber: params.page || 1 }; },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
                    return { results: data.results, pagination: { more: (params.page * 10) < data.total } };
                },
            },
            data: [<?=isset($fillBook) ? $fillBook : '';?>],
        });

        $('#selectBkVersion').select2({ minimumInputLength: 1,
            ajax: { url: "<?=base_url('api/v1/select2/view/books/qu')?>",
                dataType: 'json', method: 'POST', delay: 250, cache: true,
                data: function (params) {
                    return { inputTxt: params.term, inputItemID: $('#selectBkItem').val(), pageNumber: params.page || 1 }; },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return { results: data.results, pagination: { more: (params.page * 10) < data.total } };
                },
            },
            data: [<?=isset($fillQuantity) ? $fillQuantity : '';?>],
        });
    });
    
    window.addEventListener('load', function(event){
        $('#selectToUser').select2({ minimumInputLength: 1,
            templateResult: dropdown_show_search_result,
            templateSelection: selected_content_template,
            ajax: { url: "<?=base_url('api/v1/users')?>",dataType: 'json', method: 'POST', delay: 250, cache: true,
                data: function (params) { return { inputTxt: params.term }; },
                processResults: function (data, params) {  return { results: data }; },
            },
            data: [<?=isset($fillUser) ? $fillUser : '';?>],
        });
    });
    function dropdown_show_search_result (repo) {
        if (repo.loading) { return repo.text; }
        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__title'></div>" +
                    "<div class='select2-result-repository__statistics'>" +
                      "<div class='select2-result-repository__forks'><i class='fa fa-arrow-rightx'></i> </div>" +
                      "<div class='select2-result-repository__stargazers'><i class='fa fa-eyex'></i> </div>" +
                    "</div>" +
                "</div>" +
            "</div>"
        );
        $container.find(".select2-result-repository__title").text(repo.name + ' [' + repo.id + ']');
        $container.find(".select2-result-repository__forks").append(" Father: " + repo.fat);
        $container.find(".select2-result-repository__stargazers").append( " Mother: " + repo.mot);
        return $container;
    }
    /* When user select one of options, it shows in the selected field. */
    function selected_content_template (r) {
        return r.name +' ['+ r.id + ']';
    }
</script>
