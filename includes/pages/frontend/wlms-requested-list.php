<?php 

// member will requeste books for borrow from here
global $wpdb;

$get_books_id_from_requested_table_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_requested_books WHERE member_id = %d ", get_current_user_id() );
$get_books_id_from_requested_table = $wpdb->get_results( $get_books_id_from_requested_table_sql );
?>

<table id="viewRequestedBooksTableByUser" class="wlmsTablesorter">
  <thead> 
      <tr> 
          <th>SN.</th>
          <th>BOOK TITLE</th>
          <th>REQUESTED DATE</th>
          <th>REPLY DATE</th>
          <th>STATUS</th>
      </tr> 
  </thead>
  <tbody>
      <?php 
      $i = 1;

      if( $get_books_id_from_requested_table ){
      foreach( $get_books_id_from_requested_table as $rows ){
        
        $get_books_name_sql = $wpdb->prepare( "SELECT book_title FROM {$wpdb->prefix}wlms_books_list WHERE id = %d ", $rows->book_id );
        $get_books_name     = $wpdb->get_row( $get_books_name_sql );

        $name           =     $get_books_name->book_title;
      ?> 
      <tr>
          <td><?php echo $i;?></td>
          <td><?php echo $name;?></td>
          <td><?php echo $rows->requested_date;?></td>
          <td><?php echo $rows->reply_date;?></td>
          <?php if($rows->status == 0){?>
          <td>Pending</td>
          <?php } elseif($rows->status == 1){?>
          <td>Accepted</td>
          <?php } elseif($rows->status == 2){?>
          <td>Rejected</td>
          <?php }?>
      </tr>
      <?php $i ++; }} else{?>
          <tr>
              <td colspan="5">No data yet</td>
          </tr>
      <?php }?>
  </tbody>
</table>