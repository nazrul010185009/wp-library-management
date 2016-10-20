<?php 
// member borrow list from frontend
global $wpdb;

$get_entries_sql = $wpdb->prepare( "select * from {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}wlms_borrow.member_id = {$wpdb->prefix}users.ID LEFT JOIN {$wpdb->prefix}wlms_borrow_details ON {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id LEFT JOIN {$wpdb->prefix}wlms_books_list on {$wpdb->prefix}wlms_borrow_details.book_id = {$wpdb->prefix}wlms_books_list.id WHERE {$wpdb->prefix}users.ID = ". get_current_user_id() ." ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC",  '');
$_entries = $wpdb->get_results( $get_entries_sql );

$settings = get_option( 'wlms_settings' );
?>

<table id="viewBorrowedBooksTableByUser" class="wlmsTablesorter">
  <thead> 
      <tr> 
          <th>SN.</th>
          <th>BOOK TITLE</th> 
          <th>DATE BORROW</th>
          <th>DUE DATE</th>
          <th>DATE RETURNED</th>
          <th>FINE/PENALTY (<?php echo get_currency_symbol($settings['currency']);?>)</th>
          <th>BORROW STATUS</th>
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
          <td><?php echo esc_attr( $rows->book_title );?></td>
          <td><?php echo $rows->date_borrow;?></td>
          <td><?php echo date("Y-m-d", strtotime($rows->due_date));?></td>
          <td><?php echo $rows->date_return;?></td>
          <td><?php echo $get_fine;?></td>
          <td><?php echo $rows->borrow_status;?></td>
      </tr>
      <?php $i ++;}}else{?>
          <tr>
              <td colspan="7">No data yet</td>
          </tr>
      <?php }?>
  </tbody>
</table>