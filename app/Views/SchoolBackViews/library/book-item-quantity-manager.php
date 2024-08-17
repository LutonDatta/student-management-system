<article class="row">
    <div class="col-lg-12 animated fadeInRight">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        <div class="mail-box">
            <div class="mail-body pt-3 pb-0">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr title="<?=isset($itemForQ)?esc($itemForQ->bk_excerpt):'';?>">
                            <td>
                                <a href="<?=isset($urlGoBack)?$urlGoBack:'#';?>"><button type="button" class="btn btn-white btn-xs"><i class="fa fa-refresh"></i> Go Back</button></a>
                                <a href="<?=isset($urlRefresh)?$urlRefresh:'#';?>"><button type="button" class="btn btn-white btn-xs"><i class="fa fa-refresh"></i> Refresh Page</button></a>
                            </td>
                            <td>Item ID: <?=isset($itemForQ)?esc($itemForQ->bk_id):'';?></td>
                            <td>Item Code: <?=isset($itemForQ)?esc($itemForQ->bk_code):'';?></td>
                            <td>Added: <?=isset($itemForQ)? time_elapsed_string($itemForQ->bk_inserted_at):'';?></td>
                            <td>Title: <?=isset($itemForQ)?esc($itemForQ->bk_title):'';?></td>
                        </tr>                                                                                                                       
                    </tbody>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-lg-12 animated fadeInRight">
        <div class="mail-box">
            <div class="mail-body">
                <div class="slimScrollDiv">
                    <div class="full-height-scroll">
                        <div class="table-responsive">
                            
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr class="header">
                                        <th>QID</th>
                                        <th>Item Code</th>
                                        <th>Version</th>
                                        <th data-toggle="tooltip" title="How many time this book/item is distributed to the students?">Turnover</th>                
                                        <th>Returned By</th>                
                                        <th>Requested By</th>                
                                        <th>Distributed To</th>
                                        <th>Status</th>
                                        <th>Distribute</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($qRows) AND is_array($qRows) AND count($qRows) > 0 ): ?>
                                        <?php foreach($qRows as $row ) : ?>
                                            <tr>
                                                <td><?=esc($row->lq_id);?></td>
                                                <td><?=isset($itemForQ)?esc($itemForQ->bk_code):'';?>-<?=esc($row->lq_serial_number);?></td>
                                                <td><?=esc($row->lq_serial_number);?></td>
                                                <td><?=(property_exists($row, 'lq_turnover') AND intval($row->lq_turnover) > 0)?sprintf("%02d", $row->lq_turnover):'';?></td>
                                                <td>
                                                    <?php
                                                    $returned_by = intval($row->lq_returned_by);
                                                    if( $returned_by > 0 ){
                                                        $usr = service('StudentsModel')->select(implode(',',['student_u_id','student_u_name_first','student_u_name_middle','student_u_name_last']))->find($returned_by);
                                                        if(is_object($usr)){
                                                            $name = trim($usr->student_u_name_first . ' ' . $usr->student_u_name_middle . ' ' . $usr->student_u_name_last);
                                                            if(strlen($name) < 1 ) $name = 'No name';
                                                            echo esc( $name ) .' ('.$returned_by.')';
                                                            if(! is_null( $row->lq_returned_at)) echo '<br>'. time_elapsed_string ($row->lq_returned_at);
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                   
                                                </td>
                                                <td>
                                                    <?php
                                                    $returned_by = intval($row->lq_distributed_to);
                                                    if( $returned_by > 0 ){
                                                        $usr = service('StudentsModel')->select(implode(',',['student_u_id','student_u_name_first','student_u_name_middle','student_u_name_last']))->find($returned_by);
                                                        if(is_object($usr)){
                                                            $name = trim($usr->student_u_name_first . ' ' . $usr->student_u_name_middle . ' ' . $usr->student_u_name_last);
                                                            if(strlen($name) < 1 ) $name = 'No name';
                                                            echo esc( $name ) .' ('.$returned_by.')';
                                                            if(! is_null( $row->lq_distributed_at)) echo '<br>'. time_elapsed_string ($row->lq_distributed_at);
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <?php 
                                                    if( ! $row->lq_is_distributed ){ ?>
                                                        <td class="text-center">
                                                            <span class="btn btn-success btn-sm m-0 pt-0 pb-0">Available</span>
                                                        </td>
                                                        <td <?=tt_title('Distribute this item.','left');?>>
                                                            <?=anchor("admin/library/distributions?distributeQu={$row->lq_id}&to=&item={$itemForQ->bk_id}",'<i class="fas fa-share-square h6 m-0 p-0"></i>'); ?>
                                                        </td>
                                                    <?php }else{ ?>
                                                        <td class="text-center">
                                                            <span class="btn btn-info btn-sm m-0 pt-0 pb-0">Distributed</span>
                                                        </td>
                                                        <td  class="text-left" data-toggle="tooltip" title="Mark this item as collected.">
                                                            <i class="fas fa-check h6" data-toggle="tooltip" title="This item has been already distributed."></i>
                                                            <?=anchor($urlMarkRead . "&markMeCollected={$row->lq_id}&distributeQu={$row->lq_id}",'<i class="fab fa-get-pocket h6 m-0 p-0"></i>'); ?>
                                                        </td>
                                                    <?php } ?>                                                
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr><td colspan="9">No quantity added for this book / item.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="text-center"><?=isset($qPager) ? $qPager : '';?></div>
    </div>
    
    
    
</article>