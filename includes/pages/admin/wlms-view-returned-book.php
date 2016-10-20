<?php 

//manage pages html content for the tab
function wlms_view_returned_books_page_content()
{
  wlms_view_returned_books_manage_page_content();
}

// list html content
function wlms_view_returned_books_manage_page_content()
{
  global $wpdb;
  
  $get_entries_sql = $wpdb->prepare( "select * from {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}wlms_borrow.member_id = {$wpdb->prefix}users.id LEFT JOIN {$wpdb->prefix}wlms_borrow_details ON {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id LEFT JOIN {$wpdb->prefix}wlms_books_list on {$wpdb->prefix}wlms_borrow_details.book_id =  {$wpdb->prefix}wlms_books_list.id where {$wpdb->prefix}wlms_borrow_details.borrow_status = 'returned' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC",  '');
  $_entries = $wpdb->get_results( $get_entries_sql );
?>
<br>
<table id="viewReturnedBooksTable" class="wlmsTablesorter">
  <thead> 
    <tr> 
        <th>SN.</th>
        <th>BOOK TITLE</th> 
        <th>BORROWER</th>
        <th>DATE BORROW</th>
        <th>DUE DATE</th>
        <th>DATE RETURNED</th>
    </tr> 
  </thead>
  <tbody>
      <?php 
      $i = 1;
      if( $_entries ){
      foreach( $_entries as $rows ){
      ?> 
      <tr>
          <td><?php echo $i;?></td>
          <td><?php echo esc_html( $rows->book_title );?></td>
          <td><?php echo esc_html( $rows->display_name );?></td>
          <td><?php echo $rows->date_borrow;?></td>
          <td><?php echo date("l, F j, Y", strtotime($rows->due_date));?></td>
          <td><?php echo $rows->date_return;?></td>
      </tr>
      <?php $i ++;}}else{?>
          <tr>
              <td colspan="6">No data yet</td>
          </tr>
      <?php }?>
  </tbody>
</table>
<?php 
}
?>