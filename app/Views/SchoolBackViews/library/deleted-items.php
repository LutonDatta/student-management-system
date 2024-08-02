<article class="row">
    
    <div class="col-lg-6 animated fadeInRight">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        <div class="mail-box-header">
            <h2>Delete Permanently or Restore</h2>
            <small>Items that have been deleted previously. You can restore them or delete permanently.</small>
        </div>
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
                                        <th class="text-center">Added</th>
                                        <th class="text-center">Restore</th>                
                                        <th class="text-center">Deleted</th>                
                                        <th class="text-center">Remove</th>                
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($dtdItems) AND is_array($dtdItems) AND count($dtdItems) > 0 ): ?>
                                        <?php foreach($dtdItems as $row ) : ?>
                                            <tr>
                                                <td><?=esc($row->bk_id);?></td>
                                                <td><?=esc($row->bk_title);?></td>
                                                <td><?=esc($row->bk_code);?></td>
                                                <td class="text-center"><?php if(! is_null( $row->bk_inserted_at)) echo time_elapsed_string ($row->bk_inserted_at);?></td>
                                                <td class="text-center"><?=anchor($urlUndoSbt."&restore_id=". esc($row->bk_id),'<i class="fas fa-undo"></i>');?></td>
                                                <td class="text-center"><?php if(! is_null( $row->bk_deleted_at)) echo time_elapsed_string ($row->bk_deleted_at);?></td>
                                                <td class="text-center" title="Permanently Delete">
                                                    <?=form_open($urlUndoSbt);?>
                                                        <?=form_hidden('bk_id_to_del', esc($row->bk_id));?>
                                                        <button type="submit" name="delPerSbt" value="yes" class="btn">
                                                            <i class="fas fa-trash text-danger"></i>
                                                        </button>
                                                    <?=form_close();?>
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
        <div class="text-center"><?=(isset($dtdPager) AND is_object($dtdPager)) ? $dtdPager->links('deletedItems') : '';?></div>
    </div>
   
    
    
    
</article>

