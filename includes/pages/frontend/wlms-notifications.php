<?php 

// all notifications message show here
global $wpdb;

$get_notifications_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_msg_details WHERE receiver = %d AND status = %d", get_current_user_id(), 1 );
$get_notifications_by_member = $wpdb->get_results( $get_notifications_sql, ARRAY_A );
    

$get_all_notifications_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_msg_details WHERE member_type = %d AND receiver = %d AND status = %d", -1, -1, 1 );
$get_notifications_for_all = $wpdb->get_results( $get_all_notifications_sql, ARRAY_A );


$all_notifications =  array_merge($get_notifications_by_member, $get_notifications_for_all);

?>

<table id="viewNotificationsTableByUser" class="wlmsTablesorter">
  <thead> 
      <tr> 
          <th>SN.</th>
          <th>DATE</th>
          <th>SUBJECT</th>
          <th>DETAILS</th>
      </tr> 
  </thead>
  <tbody>
      <?php 
      $i = 1;

      if( $all_notifications ){
      foreach( $all_notifications as $rows ){
      ?> 
      <tr>
          <td><?php echo $i;?></td>
          <td><?php echo esc_attr( $rows['msg_date'] );?></td>
          <td><?php echo esc_attr( $rows['subject'] );?></td>
          <td><?php echo esc_attr( $rows['msg_details'] );?></td>
      </tr>
      <?php $i ++; }} else{?>
          <tr>
              <td colspan="4">No data yet</td>
          </tr>
      <?php }?>
  </tbody>
</table>