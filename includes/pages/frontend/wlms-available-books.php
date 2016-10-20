<?php 

// The available books from the frontend
global $wpdb;
$issues = '';
$user_role ='';

$get_book_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_books_list", '');
$entries_for_books = $wpdb->get_results( $get_book_sql );
    
$get_issues_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_issues_settings", '');
$entries_for_issues = $wpdb->get_results( $get_issues_sql );

if($entries_for_issues)
{
  $issues = htmlspecialchars(json_encode($entries_for_issues));
}

if ( is_user_logged_in() ) 
{
  $current_user = wp_get_current_user();
  $roles = $current_user->roles;
  $user_role = array_shift( $roles );
}
  
?>
<input type="hidden" name="wlms_issues_data" id="wlms_issues_data" value="<?php echo $issues; ?>" />
<input type="hidden" name="wlms_current_user_role" id="wlms_current_user_role" value="<?php echo $user_role; ?>" />
<div class="wlms_overlay"></div>
<div class="wlms_ajax_loader"></div>

<div class="frontend-error"></div>
<div class="frontend-success"></div>
<div class="send-requested-books"><button style="width: 200px;font-size: 11px;" type="button" name="send_requested_books" id="send_requested_books" class="reset">Send Requested Books</button></div>
<br>
  <table id="availableBooks" class="wlmsTablesorter">
    <thead> 
      <tr> 
        <th>ID</th> 
        <th>BOOK TITLE</th>
        <th>CATEGORY</th>
        <th>AUTHOR</th>
        <th>PUBLISHER NAME</th>
        <th>AVAILABLE COPIES</th>
        <th>VIEW DETAILS</th>
        <th>ADD FOR REQUEST</th>
      </tr> 
    </thead>
    <tbody>
      <?php 
      $i = 1;

      if( count($entries_for_books)>0 ){
      foreach( $entries_for_books as $rowsData ){
        
        $get_book_categories_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_book_categories WHERE id = %d ", $rowsData->category );
        $get_books_cat = $wpdb->get_row( $get_book_categories_sql );
    
        $total         = $wpdb->get_var( "SELECT count({$wpdb->prefix}wlms_borrow_details.book_id) FROM {$wpdb->prefix}wlms_borrow_details WHERE book_id = '". $rowsData->id ."' AND borrow_status = 'pending'" );

        if($total < $rowsData->book_copies){
      ?> 
      <tr>
          <td><?php echo $i;?></td>
          <td><?php echo esc_attr( $rowsData->book_title );?></td>
          <td><?php echo esc_attr( $get_books_cat->name );?></td>
          <td><?php echo esc_attr( $rowsData->author );?></td>
          <td><?php echo esc_attr( $rowsData->publisher_name );?></td>
          <td><?php echo number_format($rowsData->book_copies - $total);?></td>
          <td><a href="<?php echo esc_url_raw(add_query_arg( array('manage' => 'wlms-member-dashboard','page' =>'available-books&book_id='.$rowsData->id ,), home_url() ));?>">More Details</a></td>
          <td><input type="checkbox" class="wlms-check-requested-this-book" name="wlms_check_for_requested_this_book[]" id="wlms_check_for_requested_this_book" value="<?php echo $rowsData->id;?>"></td>
      </tr>
      <?php $i ++; }}} else{?>
        <tr>
            <td colspan="8">No data yet</td>
        </tr>
      <?php }?>
    </tbody>
  </table>