<article class="row">
    <div class="col-lg-8 animated fadeInRight">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        <div class="mail-box">
            <div class="mail-body">
                <div class="slimScrollDiv">
                    <div class="full-height-scroll">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr class="header">
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Code</th>
                                        <th>Excerpt</th>
                                        <th class="text-center">Quantities</th>                
                                        <th class="text-center">Turnover</th>                
                                        <th></th>                
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($items) AND is_array($items) AND count($items) > 0 ): ?>
                                        <?php foreach($items as $row ) : ?>
                                            <tr>
                                                <td><?=esc($row->bk_id);?></td>
                                                <td><?=esc($row->bk_title);?></td>
                                                <td><?=esc($row->bk_code);?></td>
                                                <td><?=esc($row->bk_excerpt);?></td>
                                                <td class="text-center row">
                                                    <?=form_open($altQuUrl,['method'=>'post','class'=>'col-3  float-right m-0 p-0'],['altQuantity' => $row->bk_id, 'typ'=>'up5']);?>
                                                        <?=form_button([
                                                            'type' => 'submit','name' => 'updateBkItemQuantity','value' => 'yes',
                                                            'content' => '<i class="fas fa-arrow-alt-circle-up"></i>',
                                                            'class' => 'btn m-0 p-0', 'title' => 'Increase quantity by 5','data-toggle' => 'tooltip', 'data-placement'=>"left"
                                                        ]);?>
                                                    <?=form_close();?>
                                                    <?=form_open($altQuUrl,['method'=>'post','class'=>'col-2 float-right m-0 p-0'],['altQuantity' => $row->bk_id, 'typ'=>'up1']);?>
                                                        <?=form_button([
                                                            'type' => 'submit','name' => 'updateBkItemQuantity','value' => 'yes',
                                                            'content' => '<i class="fas fa-arrow-up"></i>',
                                                            'class' => 'btn m-0 p-0','title' => 'Increase quantity by 1','data-toggle' => 'tooltip', 'data-placement'=>"left"
                                                        ]);?>
                                                    <?=form_close();?>
                                                    <div class="col-2 m-0 p-0">
                                                        <?=(property_exists($row, 'num_rows') AND intval($row->num_rows) > 0)?sprintf("%02d", $row->num_rows):' &nbsp; &nbsp; ';?>
                                                    </div>
                                                    <?=form_open($altQuUrl,['method'=>'post','class'=>'col-2 float-left m-0 p-0'],['altQuantity' => $row->bk_id, 'typ'=>'down1']);?>
                                                        <?=form_button([
                                                            'type' => 'submit','name' => 'updateBkItemQuantity','value' => 'yes',
                                                            'content' => '<i class="fas fa-arrow-down"></i>',
                                                            'class' => 'btn m-0 p-0','title' => 'Decrease quantity by 1','data-toggle' => 'tooltip' , 'data-placement'=>"left"
                                                        ]);?>
                                                    <?=form_close();?>
                                                    <?=form_open($altQuUrl,['method'=>'post','class'=>'col-3 float-left m-0 p-0'],['altQuantity' => $row->bk_id, 'typ'=>'down5']);?>
                                                        <?=form_button([
                                                            'type' => 'submit','name' => 'updateBkItemQuantity','value' => 'yes',
                                                            'content' => '<i class="fas fa-arrow-alt-circle-down"></i>',
                                                            'class' => 'btn m-0 p-0','title' => 'Decrease quantity by 5','data-toggle' => 'tooltip', 'data-placement'=>"left"
                                                        ]);?>
                                                    <?=form_close();?>                                                    
                                                </td>
                                                <td class="text-center"><?=(property_exists($row, 'qun_turnover') AND intval($row->qun_turnover) > 0) ?sprintf("%02d",$row->qun_turnover):'';?></td>
                                                <td class="text-center">
                                                    <a href="<?=base_url("admin/library?page_library_items_books={$book_list_page}&edtID=".esc($row->bk_id));?>" title="Edit this item" data-toggle="tooltip" data-placement="left">
                                                        <i class="fas fa-edit h5 m-0 p-0"></i>
                                                    </a>
                                                    <a href="<?=base_url("admin/library?urlGoBackPage={$book_list_page}&bkQuantity=".esc($row->bk_id));?>" data-toggle="tooltip" title="Quantity Manager" data-placement="left">
                                                        <i class="fas fa-atlas h5 m-0 p-0"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr><td colspan="7" class="text-center">No book / items found</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="text-center"><?=isset($pager_library_items_books) ? $pager_library_items_books : '';?></div>
    </div>
    
    <div class="col-lg-4 animated fadeInRight">
        <div class="mail-box-header"><h2>Library Items Editor</h2></div>
        <?=form_open($etrSbtUrl,[],['page_library_items_books'=>$book_list_page,'updt_id'=>isset($updt_id)?$updt_id:0]);?>
            <div class="mail-box">
                <div class="mail-body">
                    <div class="mb-3">
                        <?php if(! empty($errors) AND count($errors) > 0) : ?>
                            <div class="alert-danger text-center" role="alert">
                                <ul class="list-unstyled">
                                    <?php foreach ($errors as $error) : ?><li><?= esc($error) ?></li><?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label class="col-form-label" for="book_title">Title / Name</label>
                            <?=form_input(array(
                                        'name' => 'book_title',
                                        'value' => (isset($load) AND is_object($load) AND property_exists($load,'bk_title')) ? $load->bk_title : '',
                                        'required' => 'required',
                                        'class' => 'form-control',
                                    )); ?>
                            <?= !empty($errors) ? get_form_error_msg_from_array($errors,'bk_title') : ''; ?>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="book_code">Code / Identifying mark</label>
                            <?=form_input(array(
                                        'name' => 'book_code',
                                        'value' => (isset($load) AND is_object($load) AND property_exists($load,'bk_code')) ? $load->bk_code : '',
                                        'required' => 'required',
                                        'class' => 'form-control',
                                    )); ?>
                            <?= !empty($errors) ? get_form_error_msg_from_array($errors,'bk_code') : ''; ?>
                            <span class="form-text">You will use this mark to identify books or items. (No space)</span>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="book_excerpt">Excerpt</label>
                            <?=form_input(array(
                                        'name' => 'book_excerpt',
                                        'value' => (isset($load) AND is_object($load) AND property_exists($load,'bk_excerpt')) ? $load->bk_excerpt : '',
                                        'required' => 'required',
                                        'class' => 'form-control',
                                    )); ?>
                            <?= !empty($errors) ? get_form_error_msg_from_array($errors,'bk_excerpt') : ''; ?>
                            <span class="form-text">Add some short description.</span>
                        </div>
                    </div>
                </div>
                <div class="mail-body">
                    <button type="submit" name="lbbkup" value="yes" class="btn btn-sm btn-primary mt-2" ><i class="fa fa-save"></i> <?=$smtBtLbl;?></button>
                    <?=!empty($addNewUrl) ? $addNewUrl: '';?>
                    <?php if(isset($updt_id) AND intval($updt_id) > 0) : ?> 
                        <?=form_hidden('del_bk_id', $updt_id);?>
                        <button type="submit" name="delBookItem" value="yes" class="btn btn-sm btn-danger mt-2" ><i class="fa fa-trash"></i> Delete</button>
                    <?php endif;?>
                </div>
                
                <div class="clearfix"></div>
            </div>
        <?=form_close();?>
    </div>
    
</article>