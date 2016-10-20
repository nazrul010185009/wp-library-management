<?php 

//manage pages html content for the tab
function wlms_manage_requested_books_page_content()
{
  wlms_manage_requested_books_page_content_html();
}


// list html content
function wlms_manage_requested_books_page_content_html()
{
  global $wpdb;
  
  $get_request_book_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_requested_books ORDER BY id DESC",  '');
  $get_books_id_from_requested_table = $wpdb->get_results( $get_request_book_sql );
?>
<br>
<table id="viewRequestedBooksTable" class="wlmsTablesorter">
  <thead> 
    <tr> 
         <th>SN.</th>
         <th>MEMBER NAME</th>
         <th>BOOK TITLE</th>
         <th>REQUESTED DATE</th>
         <th>REPLY DATE</th>
         <th>STATUS</th>
         <th>ACTION</th>
    </tr> 
  </thead>
  <tbody>
      <?php 
      $i = 1;
      if( $get_books_id_from_requested_table ){
      foreach( $get_books_id_from_requested_table as $rows ){
        
        $get_books_name_sql = $wpdb->prepare( "SELECT book_title FROM {$wpdb->prefix}wlms_books_list WHERE id = %d ", $rows->book_id );
        $get_books_name = $wpdb->get_row( $get_books_name_sql );
    
        $name           =     $get_books_name->book_title;
        
        $user_data = get_user_by('id', $rows->member_id);
      ?> 
      <tr>
          <td><?php echo $i;?></td>
          <td><?php echo esc_html( $user_data->data->display_name );?></td>
          <td><?php echo esc_html( $name );?></td>
          <td><?php echo $rows->requested_date;?></td>
          <td><?php echo $rows->reply_date;?></td>
          
          <?php if($rows->status == 0){?>
          <td>Pending</td>
          <?php } elseif($rows->status == 1){?>
          <td>Accepted</td>
          <?php } elseif($rows->status == 2){?>
          <td>Rejected</td>
          <?php }?>
          
          <td>
            <?php if($rows->status == 0){?>
            <div class="accepted-requested" data-member="<?php echo $rows->member_id;?>" data-id="<?php echo $rows->id;?>"><button type="button" class="reset button button-primary">Accepted</button></div>
            <div class="rejected-requested" data-member="<?php echo $rows->member_id;?>" data-id="<?php echo $rows->id;?>"><button type="button" class="reset button button-primary">Rejected</button></div>
            <?php } else {?>
            <div class="accepted-requested-disabled" data-member="<?php echo $rows->member_id;?>" data-id="<?php echo $rows->id;?>"><button disabled="disabled" type="button" class="reset button button-primary">Accepted</button></div>
            <div class="rejected-requested-disabled" data-member="<?php echo $rows->member_id;?>" data-id="<?php echo $rows->id;?>"><button disabled="disabled" type="button" class="reset button button-primary">Rejected</button></div>
            <?php }?>
          <div data-id="<?php echo $rows->id;?>" data-name="wlms_member_requested_books" class="wlms_list_delete remove-requested"><button type="button" class="reset button button-primary">Remove</button></div>
          </td>
      </tr>
      <?php $i ++;}}else{?>
          <tr>
              <td colspan="7">No data yet</td>
          </tr>
      <?php }?>
  </tbody>
</table>

<div class="wlms_delete_popup_main">
    <div class="wlms_delete_popup_header_main">
        <div class="wlms_delete_popup_header">Delete Confirmation</div>
        <div class="wlms_delete_popup_close_icon"><button type="button">x</button></div>
    </div>
    <div class="wlms_delete_popup_content">Are you sure to delete this information ?</div>
    <div class="wlms_delete_popup_footer">
        <div class="wlms_btn_delete_popup"><button class="button-primary wlms-delete-btn approved rejected" type="button">Delete</button></div>
        &nbsp;&nbsp;<div class="wlms_btn_delete_popup"><button class="button-primary wlms_cancel" type="button">Cancel</button></div>
        &nbsp;&nbsp;<div class="wlms_loader_delete_panel"></div>      
    </div>
</div>
<div class="wlms_overlay"></div>

<input type="hidden" name="wlms_deleted_str" id="wlms_deleted_str" />
<input type="hidden" name="wlms_selected_button" id="wlms_selected_button" />
<input type="hidden" name="wlms_member" id="wlms_member" />
<?php 
}
?>