<?php 

//manage pages html content for the tab
function wlms_manage_borrowed_books_page_content()
{
  wlms_view_borrowed_books_manage_page_content();
}

//list html content
function wlms_view_borrowed_books_manage_page_content()
{
  global $wpdb;
 
  $get_entries_sql = $wpdb->prepare( "select * from {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}wlms_borrow.member_id = {$wpdb->prefix}users.ID LEFT JOIN {$wpdb->prefix}wlms_borrow_details ON {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id LEFT JOIN {$wpdb->prefix}wlms_books_list on {$wpdb->prefix}wlms_borrow_details.book_id = {$wpdb->prefix}wlms_books_list.id ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC",  '');
  $_entries = $wpdb->get_results( $get_entries_sql );
  
  $settings = get_option( 'wlms_settings' );
  
?>
<br>
  <table id="viewBorrowedBooksTable" class="wlmsTablesorter">
    <thead> 
        <tr> 
            <th>SN.</th>
            <th>BOOK TITLE</th> 
            <th>BORROWER</th>
            <th>DATE BORROW</th>
            <th>DUE DATE</th>
            <th>DATE RETURNED</th>
            <th>FINE/PENALTY (<?php echo get_currency_symbol($settings['currency']);?>)</th>
            <th>BORROW STATUS</th>
            <th>ACTION</th>
        </tr> 
    </thead>
    <tbody>
        <?php 
        $i = 1;
        
        if( $_entries ){
        foreach( $_entries as $rows ){
          $get_fine = 0;
          if($rows->borrow_status == 'pending')
          {
            $due_date     = date("Y-m-d");
            $borrow_date  = date("Y-m-d", strtotime($rows->date_borrow));  
            $days = (strtotime($due_date) - strtotime($borrow_date)) / (60 * 60 * 24);

            $get_fine = get_fine_by_member($rows->ID, $days);
          }
        ?> 
        <tr>
            <td><?php echo $i;?></td>
            <td><?php echo esc_html( $rows->book_title );?></td>
            <td><?php echo esc_html( $rows->display_name );?></td>
            <td><?php echo $rows->date_borrow;?></td>
            <td><?php echo date("l, F j, Y", strtotime($rows->due_date));?></td>
            <td><?php echo $rows->date_return;?></td>
            <td><?php echo $get_fine;?></td>
            <td><?php echo $rows->borrow_status;?></td>
            <?php if($rows->borrow_status == 'pending'){?>
            <td> <button data-borrow_id="<?php echo $rows->borrow_id; ?>" data-book_id="<?php echo $rows->book_id; ?>" id="<?php echo $rows->borrow_details_id;?>" class="button-primary wlms_is_return" type="button">Return</button> </td>
            <?php } else {?>
            <td> <button disabled="disabled" data-borrow_id="<?php echo $rows->borrow_id; ?>" data-book_id="<?php echo $rows->book_id; ?>" id="<?php echo $rows->borrow_details_id;?>" class="button-primary" type="button">Return</button> </td>
            <?php }?>
        </tr>
        <?php $i ++;}}else{?>
            <tr>
                <td colspan="9">No data yet</td>
            </tr>
        <?php }?>
    </tbody>
  </table>
  
  <div class="wlms_delete_popup_main">
    <div class="wlms_delete_popup_header_main">
      <div class="wlms_delete_popup_header">Return Confirmation</div>
      <div class="wlms_delete_popup_close_icon"><button type="button">x</button></div>
    </div>
    <div class="wlms_delete_popup_content">Do you want to Return this Book?</div>
    <div class="wlms_delete_popup_footer">
      <div class="wlms_btn_delete_popup"><button class="button-primary wlms-return-btn" type="button">Yes</button></div>
      &nbsp;&nbsp;<div class="wlms_btn_delete_popup"><button class="button-primary wlms_cancel" type="button">Cancel</button></div>
      &nbsp;&nbsp;<div class="wlms_loader_delete_panel"></div>      
    </div>
  </div>
  <div class="wlms_overlay"></div>
  
  <input type="hidden" name="wlms_return_id" id="wlms_return_id" />
  <input type="hidden" name="wlms_borrow_id" id="wlms_borrow_id" />
  <input type="hidden" name="wlms_book_id" id="wlms_book_id" />
<?php 
}

function get_fine_by_member($member_id, $days)
{
  global $wpdb;
  $fine = 0;
  
  $caps = get_user_meta($member_id, 'wp_capabilities', true);
  $roles = array_keys((array)$caps);
  
  $get_role_issues_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_issues_settings WHERE member_role = %s ", $roles[0] );
  $role_issues = $wpdb->get_row( $get_role_issues_sql );
  
  if($role_issues)
  {
    if($days > $role_issues->returned_days)
    {
      $fine = $role_issues->fine;
    }
  }
  
  return $fine;
}
?>