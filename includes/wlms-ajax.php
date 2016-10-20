<?php 

// daynamic delete item from the tab list
function wlms_delete_from_list_by_selected_id()
{
  if( isset($_REQUEST['sParms']) )
  {
      global $wpdb;
      $request_id = 0;
      $parseData  = explode(',', $_REQUEST['sParms'] );
      
      check_ajax_referer( 'eLibrary_ajax_call', 'security' );
      if ( is_numeric( $parseData[0] ) ) {
        $request_id = absint( $parseData[0] );
      }
      $id = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}". $parseData[1] ." WHERE id = %d", $request_id ) );
      if( $id == 1 )
      {
          echo 'deleted';
      }
  }
  exit;
}

// update borrow
function wlms_return_by_selected_id()
{
  check_ajax_referer( 'eLibrary_ajax_call', 'security' );
  if( isset($_REQUEST['sParms_id']) && isset($_REQUEST['sParms_borrow_id']) && isset($_REQUEST['sParms_book_id']) )
  {
     global $wpdb; 
     $id = $_REQUEST['sParms_borrow_id'];
     $book_id = $_REQUEST['sParms_book_id'];
     $parseDate = date("Y-m-d");

     $sql = "update {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}wlms_borrow_details on {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id set borrow_status='returned', date_return= NOW(), {$wpdb->prefix}wlms_borrow_details.filter_date='$parseDate' where {$wpdb->prefix}wlms_borrow.borrow_id='$id' and {$wpdb->prefix}wlms_borrow_details.book_id = '$book_id'";

     $ids = $wpdb->query( $sql );
     if( $ids == 1)
     {
         echo "updated";
     }
  }
  exit;
}

// return member type
function wlms_get_option_by_member_type()
{
  check_ajax_referer( 'eLibrary_ajax_call', 'security' );
  if(isset($_REQUEST['sParms_id']))
  {
    $str = "";
    $settings = get_option( 'wlms_settings' );
    $i = 0;
    
    if($_REQUEST['sParms_id'] == -1)
    {
      if(count($settings['member_roles'])>0)
      {
        foreach($settings['member_roles'] as $role)
        {
          $args = array(
           'role' => $role,
           'orderby' => 'user_nicename',
           'order' => 'ASC'
          );
          $authors = get_users($args);

          if(count($authors)>0)
          {
            if($i == 0){
              $str .= '<option value="-1">All</option>';
            }
            foreach ($authors as $user) 
            {
              $str .= '<option value="'. $user->ID .'">'. $user->display_name .'</option>';
              $i ++;
            }  
          }     
        }      
      }
    }
    else
    {
      $args = array(
          'role' => $_REQUEST['sParms_id'],
          'orderby' => 'user_nicename',
          'order' => 'ASC'
      );
      $entries_for_member = get_users($args);

      if( count($entries_for_member)>0 )
      {
        $str .= '<option value="-1">All</option>';
        foreach($entries_for_member as $user)
        {
          $str .= '<option value="'. $user->ID .'">'. $user->display_name .'</option>';
        }
      }
    }
    
    echo $str;
  }
  exit;
}

// return member details by selected member type
function wlms_get_option_by_member_type_for_borrower()
{
  check_ajax_referer( 'eLibrary_ajax_call', 'security' );
  if(isset($_REQUEST['sParms_id']))
  {
    $str = "";
    
    $args = array(
        'role' => $_REQUEST['sParms_id'],
        'orderby' => 'user_nicename',
        'order' => 'ASC'
    );
    $entries_for_member = get_users($args);

    if( count($entries_for_member)>0 )
    {
      $member_data_array = array();

      foreach($entries_for_member as $user)
      {
        $member_data['user_id'] = $user->ID;
        $member_data['display_name'] = $user->display_name;
        array_push($member_data_array, $member_data);
      }
      
      $str = json_encode( $member_data_array );
    }
        
    echo $str;
  }
  exit;
}

// return borrow by date filter
function wlms_get_borrow_filter_data_by_date()
{
  check_ajax_referer( 'eLibrary_ajax_call', 'security' );
  if( isset($_REQUEST['sParms_start_date']) && isset($_REQUEST['sParms_end_date']) )
  {
    global $wpdb;
    $startDate = $_REQUEST['sParms_start_date'];
    $endDate =   $_REQUEST['sParms_end_date'];
    
    $get_entries_sql = $wpdb->prepare( "select * from {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}wlms_borrow.member_id = {$wpdb->prefix}users.ID LEFT JOIN {$wpdb->prefix}wlms_borrow_details ON {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id LEFT JOIN {$wpdb->prefix}wlms_books_list on {$wpdb->prefix}wlms_borrow_details.book_id =  {$wpdb->prefix}wlms_books_list.id WHERE {$wpdb->prefix}wlms_borrow.filter_date BETWEEN '$startDate' AND '$endDate' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC",  '');
    $_entries = $wpdb->get_results( $get_entries_sql );
    
   $i = 1;
   if( $_entries )
   {
     foreach( $_entries as $rows )
     {
       echo '<tr>';
       echo '<td>'. $i .'</td>';
       echo '<td>'. $rows->book_title .'</td>';
       echo '<td>'. $rows->display_name .'</td>';
       echo '<td>'. $rows->date_borrow .'</td>';   
       echo '</tr>';
       $i++;
     }
   }
   else {
     echo '<tr><td colspan="4">No data yet</td></tr>';
   }
  }
  exit;
}

// borrow return data filter by date
function wlms_get_borrow_return_filter_data_by_date()
{
  check_ajax_referer( 'eLibrary_ajax_call', 'security' );
  if( isset($_REQUEST['sParms_start_date']) && isset($_REQUEST['sParms_end_date']) )
  {
    global $wpdb;
    $startDate = $_REQUEST['sParms_start_date'];
    $endDate =   $_REQUEST['sParms_end_date'];
    
    $get_entries_sql = $wpdb->prepare( "select * from {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}wlms_borrow.member_id = {$wpdb->prefix}users.ID LEFT JOIN {$wpdb->prefix}wlms_borrow_details ON {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id LEFT JOIN {$wpdb->prefix}wlms_books_list on {$wpdb->prefix}wlms_borrow_details.book_id =  {$wpdb->prefix}wlms_books_list.id where {$wpdb->prefix}wlms_borrow_details.filter_date BETWEEN '$startDate' AND '$endDate' AND {$wpdb->prefix}wlms_borrow_details.borrow_status = 'returned' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC",  '');
  $_entries = $wpdb->get_results( $get_entries_sql );
    
    $i = 1;
    
    if( $_entries )
    {
     foreach( $_entries as $rows )
     {
       echo '<tr>';
       echo '<td>'. $i .'</td>';
       echo '<td>'. $rows->book_title .'</td>';
       echo '<td>'. $rows->display_name .'</td>';
       echo '<td>'. $rows->date_borrow .'</td>';
       echo '<td>'. $rows->date_return .'</td>';
       echo '</tr>';
       $i++;
     }
    }
    else {
     echo '<tr><td colspan="5">No data yet</td></tr>';
    }
    }
  exit;
}

// borrow due data filter by date
function wlms_get_borrow_due_filter_data_by_date()
{
  check_ajax_referer( 'eLibrary_ajax_call', 'security' );
  if( isset($_REQUEST['sParms_start_date']) && isset($_REQUEST['sParms_end_date']) )
  {
    global $wpdb;
    $startDate = $_REQUEST['sParms_start_date'];
    $endDate =   $_REQUEST['sParms_end_date'];
    
   
    $get_entries_sql = $wpdb->prepare( "select * from {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}wlms_borrow.member_id = {$wpdb->prefix}users.ID LEFT JOIN {$wpdb->prefix}wlms_borrow_details ON {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id LEFT JOIN {$wpdb->prefix}wlms_books_list on {$wpdb->prefix}wlms_borrow_details.book_id =  {$wpdb->prefix}wlms_books_list.id WHERE {$wpdb->prefix}wlms_borrow.filter_date BETWEEN '$startDate' AND '$endDate' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC",  '');
    $_entries = $wpdb->get_results( $get_entries_sql );
    
    $i = 1;
    if( $_entries )
    {
      foreach( $_entries as $rows )
      {
        echo '<tr>';
        echo '<td>'. $i .'</td>';
        echo '<td>'. $rows->book_title .'</td>';
        echo '<td>'. $rows->display_name .'</td>';
        echo '<td>'. $rows->date_borrow .'</td>';
        echo '<td>'. $rows->due_date .'</td>';
        echo '</tr>';
        $i++;
      }
    }
    else {
        echo '<tr><td colspan="5">No data yet</td></tr>';
    }
  }
  exit;
}

// requested book add by the member from frontend
function wlms_add_requested_books_for_member()
{
  check_ajax_referer( 'eLibrary_ajax_call', 'security' );
  if(isset($_REQUEST['sParms_id']))
  {
    global $wpdb; 
    $str = '';
    $array = array();
    
    $book_array = array();
    
    $get_total_pending_sql = $wpdb->prepare( "SELECT {$wpdb->prefix}wlms_borrow_details.book_id FROM {$wpdb->prefix}wlms_borrow_details INNER JOIN {$wpdb->prefix}wlms_borrow ON {$wpdb->prefix}wlms_borrow_details.borrow_id = {$wpdb->prefix}wlms_borrow.borrow_id WHERE {$wpdb->prefix}wlms_borrow.member_id = '". get_current_user_id() ."' AND {$wpdb->prefix}wlms_borrow_details.borrow_status = 'pending'",  '');
    $total_pending = $wpdb->get_results( $get_total_pending_sql, ARRAY_A );
 
    if(count($total_pending)>0)
    {
      foreach($total_pending as $row)
      {
        array_push($book_array, $row['book_id']);
      }
    }
    
    $get_same_values = array_intersect($book_array, $_REQUEST['sParms_id']);
    $get_diffrence_values = array_diff($_REQUEST['sParms_id'], $book_array);
    
    
    $get_books_id_from_requested_table_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_requested_books WHERE member_id = %d AND status = %d", absint( get_current_user_id() ), 0);
    $get_books_id_from_requested_table = $wpdb->get_results( $get_books_id_from_requested_table_sql );
    
   
    $requested_book_array = array();
    if(count($get_books_id_from_requested_table)>0)
    {
      foreach($get_books_id_from_requested_table as $val)
      {
        array_push($requested_book_array, $val->book_id);
      }
    }
    
    
    if(count($get_diffrence_values)>0)
    {
      foreach($get_diffrence_values as $vals)
      {
        if(!in_array($vals, $requested_book_array))
        {
          $wpdb->query( $wpdb->prepare( 
            "
              INSERT INTO {$wpdb->prefix}wlms_member_requested_books
              ( member_id, book_id, requested_date, reply_date, status )
              VALUES ( %d, %d, %s, %s, %d )
            ", 
            array(
              get_current_user_id(),
              $vals,
              date("Y-m-d H:i:s"),
              '',
              0  
            ) 
          ) );
        }
      }
    }
    
    if(count($get_same_values)>0)
    {
      foreach($get_same_values as $book_row)
      {
        $get_book_name_sql = $wpdb->prepare( "SELECT book_title FROM {$wpdb->prefix}wlms_books_list WHERE id = %d ", $book_row );
        $get_books_name    = $wpdb->get_row( $get_book_name_sql );
    
        $name           =     $get_books_name->book_title;

        $str .= '<div><i><strong>'. $name .'</i></strong> already pending this book </div>';
      }
    }
    
    
    $get_requested_same_values = array_intersect($requested_book_array, $_REQUEST['sParms_id']);
    if(count($get_requested_same_values)>0)
    {
      foreach($get_requested_same_values as $requested_book_row)
      {
        $get_book_name_sql = $wpdb->prepare( "SELECT book_title FROM {$wpdb->prefix}wlms_books_list WHERE id = %d ", $requested_book_row );
        $get_books_name    = $wpdb->get_row( $get_book_name_sql );
        
        $name           =     $get_books_name->book_title;

        $str .= '<div>Already you have a request for <strong><i>'. $name .'</i></strong></div>';
      }
    }
    
    
    if($str)
    {
      $array = array('status' => 'exist', 'message' => $str);
    }
    else
    {
      $array = array('status' => 'success', 'message' => 'Successfully has sent your requested books');
    }
    
    echo json_encode($array);
  }
  exit;
}

// manage member requested books accepted
function wlms_accepted_requested_books_by_selected_id()
{
  check_ajax_referer( 'eLibrary_ajax_call', 'security' );
  if(isset($_REQUEST['sParms']) && isset($_REQUEST['member']))
  {
    global $wpdb;
    $book_array = array();
    $issues = '';
    
    $caps = get_user_meta($_REQUEST['member'], 'wp_capabilities', true);
    $roles = array_keys((array)$caps);
    
    $get_issues_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_issues_settings",  '');
    $entries_for_issues = $wpdb->get_results( $get_issues_sql );
  
    $get_requested_books_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_requested_books WHERE id = %d ", $_REQUEST['sParms'] );
    $entries_for_request_book = $wpdb->get_row( $get_requested_books_sql );
    
    
  
    if($entries_for_issues)
    {
      $issues = $entries_for_issues;
    }
  

    $get_total_pending_sql = $wpdb->prepare( "SELECT {$wpdb->prefix}wlms_borrow_details.book_id FROM {$wpdb->prefix}wlms_borrow_details INNER JOIN {$wpdb->prefix}wlms_borrow ON {$wpdb->prefix}wlms_borrow_details.borrow_id = {$wpdb->prefix}wlms_borrow.borrow_id WHERE {$wpdb->prefix}wlms_borrow.member_id = '". $_REQUEST['member'] ."' AND {$wpdb->prefix}wlms_borrow_details.borrow_status = 'pending'",  '');
    $total_pending = $wpdb->get_results( $get_total_pending_sql, ARRAY_A );
    

    if(count($total_pending)>0)
    {
      foreach($total_pending as $row)
      {
        array_push($book_array, $row['book_id']);
      }
    }
    
    
    $data = get_allocated_books_total($issues, $roles[0]);
    
    
    if(count($total_pending) < $data['books_borrow'])
    {
      if(!in_array($entries_for_request_book->book_id, $book_array))
      {
        $wpdb->query( $wpdb->prepare( 
          "
            INSERT INTO {$wpdb->prefix}wlms_borrow
            ( member_id, date_borrow, due_date, filter_date )
            VALUES ( %d, %s, %s, %s )
          ", 
          array(
            $_REQUEST['member'], 
            date("Y-m-d H:i:s"),
            date('Y-m-d', strtotime('+'. $data['returned_days'] .' days') ),
            date("Y-m-d")
          ) 
        ) );
                  
        $get_borrow_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_borrow ORDER BY borrow_id DESC LIMIT 1",  '');
        $get_borrow = $wpdb->get_row( $get_borrow_sql );
        $borrow_id  = $get_borrow->borrow_id;
        
        
        $wpdb->query( $wpdb->prepare( 
          "
            INSERT INTO {$wpdb->prefix}wlms_borrow_details
            ( book_id, borrow_id, borrow_status )
            VALUES ( %d, %d, %s )
          ", 
          array(
            $entries_for_request_book->book_id,
            $borrow_id,
            'pending'  
          ) 
        ) );
        
        $wpdb->query(
          $wpdb->prepare(
              "UPDATE {$wpdb->prefix}wlms_member_requested_books SET reply_date = %s, status = %d WHERE id= %d",
              date("Y-m-d"), 1, absint($_REQUEST['sParms'])
          )
        );
        
        echo 'Successfully added for your selected member';
      
      }
      else {
        echo 'Already pending this book, please try another';
      }
    }
    else
    {
      echo 'There are '. $data['books_borrow'] .' books pending of this member, already exceeded the limit';
    }
    
  }
  exit;
}

// manage member requested book rejected
function wlms_rejected_requested_books_by_selected_id()
{
  check_ajax_referer( 'eLibrary_ajax_call', 'security' );
  if(isset($_REQUEST['sParms']))
  {
    global $wpdb;
    
    $wpdb->query(
      $wpdb->prepare(
          "UPDATE {$wpdb->prefix}wlms_member_requested_books SET reply_date = %s, status = %d WHERE id= %d",
          date("Y-m-d"), 2, absint( $_REQUEST['sParms'] )
      )
    );

    echo 'Your selected item have been rejected';
  }
    
  exit;
}


add_action( 'wp_ajax_nopriv_wlms_delete_from_list_by_selected_id','wlms_delete_from_list_by_selected_id' );
add_action( 'wp_ajax_wlms_delete_from_list_by_selected_id', 'wlms_delete_from_list_by_selected_id' );
add_action( 'wp_ajax_nopriv_wlms_return_by_selected_id','wlms_return_by_selected_id' );
add_action( 'wp_ajax_wlms_return_by_selected_id', 'wlms_return_by_selected_id' );
add_action( 'wp_ajax_nopriv_wlms_get_option_by_member_type','wlms_get_option_by_member_type' );
add_action( 'wp_ajax_wlms_get_option_by_member_type', 'wlms_get_option_by_member_type' );
add_action( 'wp_ajax_nopriv_wlms_get_borrow_filter_data_by_date','wlms_get_borrow_filter_data_by_date' );
add_action( 'wp_ajax_wlms_get_borrow_filter_data_by_date', 'wlms_get_borrow_filter_data_by_date' );
add_action( 'wp_ajax_nopriv_wlms_get_borrow_return_filter_data_by_date','wlms_get_borrow_return_filter_data_by_date' );
add_action( 'wp_ajax_wlms_get_borrow_return_filter_data_by_date', 'wlms_get_borrow_return_filter_data_by_date' );
add_action( 'wp_ajax_nopriv_wlms_get_borrow_due_filter_data_by_date','wlms_get_borrow_due_filter_data_by_date' );
add_action( 'wp_ajax_wlms_get_borrow_due_filter_data_by_date', 'wlms_get_borrow_due_filter_data_by_date' );
add_action( 'wp_ajax_nopriv_wlms_get_option_by_member_type_for_borrower','wlms_get_option_by_member_type_for_borrower' );
add_action( 'wp_ajax_wlms_get_option_by_member_type_for_borrower', 'wlms_get_option_by_member_type_for_borrower' );
add_action( 'wp_ajax_nopriv_wlms_add_requested_books_for_member','wlms_add_requested_books_for_member' );
add_action( 'wp_ajax_wlms_add_requested_books_for_member', 'wlms_add_requested_books_for_member' );
add_action( 'wp_ajax_nopriv_wlms_accepted_requested_books_by_selected_id','wlms_accepted_requested_books_by_selected_id' );
add_action( 'wp_ajax_wlms_accepted_requested_books_by_selected_id', 'wlms_accepted_requested_books_by_selected_id' );
add_action( 'wp_ajax_nopriv_wlms_rejected_requested_books_by_selected_id','wlms_rejected_requested_books_by_selected_id' );
add_action( 'wp_ajax_wlms_rejected_requested_books_by_selected_id', 'wlms_rejected_requested_books_by_selected_id' );
?>