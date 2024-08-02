<?php namespace App\Controllers\SchoolBack;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */


class Library extends BaseController {
    
    public function show_library_items(){
        $data                                   = $this->data; // We have some pre populated data here        
        $data['title']          = 'Library Items (Books)';
        $data['pageTitle']      = 'Library Items (Books)';
        $data['loadedPage']     = 'lib_books';
        $data                   = array_merge( $data,service('LibraryItemsQuantitiesModel')->mark_book_as_collected($this->request));
        
        $subPro = $this->increase_or_decrease_quantity_n_get_url();
        if(is_object($subPro)){ return $subPro;}else{ $data = array_merge( $data, $subPro); }
        
        $data['itemForQ']       = service('LibraryItemsModel')->find(intval($this->request->getGet('bkQuantity')));
        if( is_object($data['itemForQ'])){
            $loadTemplate       = 'book-item-quantity-manager';
            $data               = array_merge( $data, $this->process_quantities_of_item($data['itemForQ']));
        }else{
            $loadTemplate       = 'book-list';
            $updateSave = $this->process_submits_add_or_update_item();
            if(is_object($updateSave)){ return $updateSave; } // return redirect()->to() triggered
            $data               = array_merge( $data, $updateSave);
            $data               = array_merge( $data, $this->get_all_items_with_pagination());
        }
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view("SchoolBackViews/library/$loadTemplate", $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    }
    
    private function get_all_items_with_pagination(){
        $perPage        = 15;
        $pagerGroup     = 'library_items_books';
        $page           = intval( $this->request->getGet("page_$pagerGroup")); $page = $page < 1 ? 1 : $page; // Page number must be 1 or more
        $offset         = $page * $perPage - $perPage;
        
        $map            = service('LibraryItemsQuantitiesModel')->prefixTable('library_items_quantities');
        $data['items']  = service('LibraryItemsModel')
                ->select(implode(',',array(
                    "bk_id, MIN(bk_title) AS bk_title, MIN(bk_code) AS bk_code, MIN(bk_excerpt) AS bk_excerpt, MIN(bk_deleted_at) AS bk_deleted_at, MIN(bk_updated_at) AS bk_updated_at, MIN(bk_inserted_at) AS bk_inserted_at",
                    "COUNT(lq_id) AS num_rows, SUM(lq_turnover) AS qun_turnover",
                )))
                ->join($map,"bk_id = lq_bk_id", 'left')
                ->groupBy("bk_id")
                ->orderBy("bk_id DESC")
                ->findAll($perPage,$offset);
        
        // Paginate do not work with groupBy, paginate again to overwrite previous invalid links
        service('LibraryItemsModel')->paginate($perPage,$pagerGroup);
        $data["pager_library_items_books"]  = service('LibraryItemsModel')->pager->links($pagerGroup);
        $data["book_list_page"]  = $page;
        return $data;
    }
    
    /*
     * Increase or decrease quantity of the items.
     */
    private function increase_or_decrease_quantity_n_get_url(){
        $mrk_to = trim($this->request->getPost('typ'));
        $item   = service('LibraryItemsModel')
                ->find( intval($this->request->getPost('altQuantity')) ); // Book Object
        if( in_array( $mrk_to, ['up5','down5','up1','down1']) AND is_object($item) ){
            $current_rows = service('LibraryItemsQuantitiesModel')
                    ->withDeleted()
                    ->where('lq_bk_id',$item->bk_id)
                    ->countAllResults(); // Rows in DB now for specific item
            $cuFst = $current_rows + 1;
            $insert = [ 'lq_bk_id' => $item->bk_id, 'lq_is_distributed' => '0','lq_distributed_to'=>NULL,'lq_returned_by'=>NULL ];
            if( $mrk_to === 'up1' ){
                $insert['lq_serial_number'] = $cuFst;
                $ins = service('LibraryItemsQuantitiesModel')->insert( $insert );
                if( ! $ins){
                    @session_start(); // Reverse session_write_close?
                    return redirect()
                            ->to(base_url('admin/library?page_library_items_books='.intval($this->request->getGet('page_library_items_books'))))
                            ->with('display_msg', get_display_msg('Failed to create item. ' . implode(', ',service('LibraryItemsQuantitiesModel')->errors()),'danger'));
                }
            }
            if( $mrk_to === 'up5' ) for( $i=$cuFst; $i <= ($cuFst + 4); $i++ ){
                $insert['lq_serial_number'] = $i;
                $ins = service('LibraryItemsQuantitiesModel')->insert( $insert );
                if( ! $ins){
                    // Do not process loop, when problem made on first attempt
                    @session_start(); // Reverse session_write_close?
                    return redirect()
                            ->to(base_url('admin/library?page_library_items_books='.intval($this->request->getGet('page_library_items_books'))))
                            ->with('display_msg', get_display_msg('Failed to create item. ' . implode(', ',service('LibraryItemsQuantitiesModel')->errors()),'danger'));
                }
            }
            if( $mrk_to === 'down1' AND $current_rows > 0 ){
                $keep_rows      = $current_rows - 1;
                $delete_row_ids = service('LibraryItemsQuantitiesModel')->where('lq_bk_id', $item->bk_id)->limit(1,$keep_rows)->findColumn('lq_id');
                service('LibraryItemsQuantitiesModel')->delete( $delete_row_ids, TRUE );
            }
            if( $mrk_to === 'down5' AND $current_rows > 4 ){
                $keep_rows      = $current_rows - 5;
                $delete_row_ids = service('LibraryItemsQuantitiesModel')->where('lq_bk_id', $item->bk_id)->limit(5,$keep_rows)->findColumn('lq_id');
                service('LibraryItemsQuantitiesModel')->delete( $delete_row_ids, TRUE);
            }
        }
        $page = intval($this->request->getGet('page_library_items_books')); // We are viewin items in this page
        return ['altQuUrl' => base_url("admin/library?page_library_items_books=$page")];
    }
    
    private function process_quantities_of_item( object $book ){
        $fromItemsPage          = intval($this->request->getGet('urlGoBackPage'));
        $return                 = [];
        $return['qRows']        = service('LibraryItemsQuantitiesModel')->where('lq_bk_id',$book->bk_id)->paginate(10, 'item_quantities');
        $return['qPager']       = service('LibraryItemsQuantitiesModel')->pager->links('item_quantities');
        $return['urlRefresh']   = base_url("admin/library?urlGoBackPage=$fromItemsPage&bkQuantity={$book->bk_id}");
        $return['urlGoBack']    = base_url("admin/library?page_library_items_books=$fromItemsPage" ); // With Pagination 
        $return['urlMarkRead']  = base_url("admin/library?urlGoBackPage=$fromItemsPage"); // With Pagination 
        return $return;
    }
    
    
    
     /* Editor: Add or Updated library item */
    private function process_submits_add_or_update_item(){
        $currentPage       = intval($this->request->getPost('page_library_items_books'));
        if($this->request->getPost('delBookItem') === 'yes' ){
            if(service('LibraryItemsModel')->delete( intval($this->request->getPost('del_bk_id')) )){
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/library?page_library_items_books=$currentPage"))->with('display_msg', get_display_msg('Item moved to trush successfully.','success')); /* Delete and refresh page */
            }else{
                $data['display_msg'] = get_display_msg('Unable to delete.','danger');
            }
        }
        
        // ----------------------- Form submitted ------ UPDATE if update ID exists or Insert --------------
        if($this->request->getPost('lbbkup') === 'yes'){
            $updateMe       = service('LibraryItemsModel')->find(intval($this->request->getPost('updt_id')));
            $values = [
                'bk_title'  => $this->request->getPost('book_title'),
                'bk_excerpt'=> $this->request->getPost('book_excerpt'),
                'bk_code'   => $this->request->getPost('book_code'),
            ];
            if( is_object($updateMe) ){
                if(service('LibraryItemsModel')->update($updateMe->bk_id, $values )){
                    @session_start(); // Reverse session_write_close?
                    return redirect()->to(base_url("admin/library?page_library_items_books=$currentPage"))->with('display_msg',get_display_msg('Item has been updated successfully.','success'));
                }else{
                    $data['display_msg'] = get_display_msg('Failed to update item.','danger');
                }
            }else{
                if(service('LibraryItemsModel')->insert( $values )){                    
                    @session_start(); // Reverse session_write_close?
                    return redirect()->to(base_url("admin/library"))->with('display_msg',get_display_msg('New item has been added successfully.','success'));
                }else{
                    $data['display_msg'] = get_display_msg('Failed to add new item.','danger');
                }
            }
            $data['errors'] = service('LibraryItemsModel')->errors();
        }
        
        // ------------------------- Get request found to update , populate form to update --------------
        $data['load']       = service('LibraryItemsModel')->find(intval($this->request->getGet('edtID'))); // Load item to update if update id provided
        if(is_object($data['load'])){
            $data['smtBtLbl']   = 'Update';
            $data['updt_id']    = $data['load']->bk_id; // We need to get it from post request
            $data['addNewUrl']  = anchor('admin/library','<i class="fa fa-edit"></i> Add New',['class'=>'btn btn-sm btn-info mt-2']);
            $data['etrSbtUrl']  = base_url("admin/library?page_library_items_books={$this->request->getGet('library_items_books')}"); // Update URL so show page=respective_page
        }else{
            $data['smtBtLbl']   = 'Save';
            $data['etrSbtUrl']  = base_url("admin/library"); // Insert Data first time so show page=1
        }        
        return $data;
    }
    
    
    
    public function show_item_distributions(){        
        $data = $this->data; // We have some pre populated data here
        $data['title']      = 'Distributions of Library Items (Books)';
        $data['pageTitle']  = 'Distributions of Library Items (Books)';
        $data['loadedPage'] = 'lib_books_distributed';
        $data               = array_merge( $data, $this->book_distribute_to_people());
        $data               = array_merge( $data, service('LibraryItemsQuantitiesModel')->mark_book_as_collected($this->request));
        
        $data['booksDist']  = service('LibraryItemsQuantitiesModel')->join('library_items','library_items.bk_id = library_items_quantities.lq_bk_id','LEFT')->orderBy('lq_updated_at DESC')->paginate(5, 'item_quantities');
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/library/book-item-distributor', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    }
    
    
    private function book_distribute_to_people(){
        $return = [];
        // Populate field if data requested from items quantity page
        $item = intval($this->request->getGet('item'));         // Book/item ID
        $vers = intval($this->request->getGet('distributeQu')); // Quantity ID
        $user = intval($this->request->getGet('to'));           // User ID
        
        if($user > 0){
            $usr = service('UserStudentsModel')->find($user);
            if( is_object( $usr ) ){
                $obj = new \stdClass();
                $obj->id = $usr->student_u_id;
                $nm = trim(get_name_initials($usr->student_u_name_initial) . ' ' . $usr->student_u_name_first . ' ' . $usr->student_u_name_middle . ' ' .$usr->student_u_name_last);
                $obj->name = strlen($nm) > 0 ? esc($nm) : 'User has not set his name';
                $return['fillUser'] = json_encode( $obj );
            }
        }
        if($vers > 0){
            $q = 'library_items_quantities';$b = 'library_items';
            $bld = service('LibraryItemsQuantitiesModel')
                    ->join($b,"$b.bk_id = $q.lq_bk_id")
                    ->select("$q.lq_id,$q.lq_serial_number,$b.bk_code,$b.bk_id,$b.bk_title")
                    ->where('lq_id', $vers)
                    ->get()->getFirstRow();
            if( is_object( $bld ) ){
                $bk = new \stdClass();
                $bk->id = $bld->lq_id;
                $bk->text = $bld->bk_code .'-' .$bld->lq_serial_number . '['.esc($bld->bk_title).']';
                $return['fillQuantity'] = json_encode( $bk );
                $lq = new \stdClass();
                $lq->id = $bld->bk_id;
                $lq->text = esc($bld->bk_title) . ' ('.$bld->bk_code .'-' .$bld->lq_serial_number . ')['.$bld->lq_id.']';
                $return['fillBook'] = json_encode( $lq );
            }
        }
        // Distribute book upon submit
        if($this->request->getPost('distBtn') === 'yes'){
            $book = intval($this->request->getPost('selectBk'));
            $vers = intval($this->request->getPost('selectBkVersion'));
            $user = intval($this->request->getPost('selectToUser'));
            if( $user > 0 AND $vers > 0 ){
                $user_obj   = service('UserStudentsModel')->find($user);
                $present    = service('LibraryItemsQuantitiesModel')->find( $vers );
                if(is_object($present) AND is_object($user_obj)){
                    if($present->lq_is_distributed){
                        $return['display_msg'] = get_display_msg('It is already distributed, so you can not distribute is without collecting it.','danger');
                    }elseif( $book !== intval($present->lq_bk_id)){
                        $return['display_msg'] = get_display_msg('Invalid version selected for item. Select item first.','danger');
                    }else{
                        $save = service('LibraryItemsQuantitiesModel')->update($vers,[
                            'lq_is_distributed' => '1',
                            'lq_distributed_to' => $user,
                            'lq_distributed_at' => date('Y-m-d H:i:s'),
                            'lq_turnover'       => $present->lq_turnover + 1,
                        ]);
                        if( $save ) $return['display_msg'] = get_display_msg('You have successfully distributed this item.','success');
                        else $return['display_msg'] = get_display_msg('Failed to distribute this item.','danger');
                    }
                }else{
                    $return['display_msg'] = get_display_msg('Invalid User selection or Item version selection.','danger');
                }
            }else{
                $return['display_msg'] = get_display_msg('User selection or Item version selection is wrong.','danger');
            }
            
        }
        return $return;
    }
    
    public function show_recycle_bin(){        
        $data               = $this->data; 
        $data               = array_merge( $data,$this->delete_permanently_or_restore());
        $data['title']      = 'Recycle Bin of Library Items (Books)';
        $data['pageTitle']  = 'Recycle Bin of Library Items (Books)';
        $data['loadedPage'] = 'lib_book_bin';
        
        $data['dtdItems']   = service('LibraryItemsModel')->onlyDeleted()->paginate(10,'deletedItems');
        $data['dtdPager']   = service('LibraryItemsModel')->pager;
        
        $page               = intval($this->request->getGet('page_deletedItems'));
        $data['urlUndoSbt'] = base_url("admin/library/bin?page_deletedItems=$page");
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/library/deleted-items', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    }
    
    private function delete_permanently_or_restore(){
        $return = [];
        $restoreMe = intval($this->request->getGet('restore_id'));
        if( $restoreMe > 0 ){
            $obj = service('LibraryItemsModel')->onlyDeleted()->find( $restoreMe );
            if( is_object($obj) ){
                if(service('LibraryItemsModel')->update( $restoreMe, [ 'bk_deleted_at' => null ] )){
                    $return['display_msg'] = get_display_msg('Restoration successful.','success');
                }else{
                    $return['display_msg'] = get_display_msg('Failed to restore.','danger');
                }
            }
        }
        
        if( $this->request->getPost('delPerSbt') === 'yes'){
            $delete_me = intval($this->request->getPost('bk_id_to_del'));
            if( $delete_me > 0 ){
                $findMe = service('LibraryItemsModel')->onlyDeleted()->find( $delete_me );
                if( is_object( $findMe ) ){
                    service('LibraryItemsQuantitiesModel')->delete_all_copies_of_a_book($delete_me);
                    $delLibItm = service('LibraryItemsModel')->delete_permanently($delete_me);
                    $return['display_msg'] = get_display_msg('Permanently deleted: ' . esc($findMe->bk_title),'success');
                }
            }
        }
        return $return;
    }
}
