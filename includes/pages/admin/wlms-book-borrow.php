<?php

// manage books borrow for the members
function wlms_manage_books_borrow_page_content()
{
  wlms_book_borrow_manage_page_content();
}

//manage pages html content for the tab
function wlms_book_borrow_manage_page_content()
{
  if( isset($_GET['manage']) && ($_GET['manage'] == 'manage-books-borrow') )
  {
    wlms_book_borrow_content();
  }
}


//content save
function wlms_book_borrow_content_save()
{
  global $wpdb;    
  $id =         $_POST['wlms_check_for_borrow_this_book'];
  $member_id  = $_POST['wlms_select_member'];
  $due_date  =  $_POST['wlms_due_date'];
  
  $caps = get_user_meta($member_id, 'wp_capabilities', true);
  $roles = array_keys((array)$caps);
  
  if(count($id)>0)
  {
    if(isset($_POST['wlms_select_member_type']) && $_POST['wlms_select_member_type'] != -1 && !empty($member_id) && !empty($due_date))
    {
      $nonce = $_REQUEST['_wpnonce'];
      if ( ! wp_verify_nonce( $nonce, 'submit_books_borrow' ) ) 
      {
        exit; 
      }
    
      $today = date("Y-m-d");

      if(date("Y-m-d", strtotime($due_date)) > $today)
      {
        $book_array = array();
        
        $get_total_pending_sql = $wpdb->prepare( "SELECT {$wpdb->prefix}wlms_borrow_details.book_id FROM {$wpdb->prefix}wlms_borrow_details INNER JOIN {$wpdb->prefix}wlms_borrow ON {$wpdb->prefix}wlms_borrow_details.borrow_id = {$wpdb->prefix}wlms_borrow.borrow_id WHERE {$wpdb->prefix}wlms_borrow.member_id = '". $member_id ."' AND {$wpdb->prefix}wlms_borrow_details.borrow_status = 'pending'",  '');
        
        $total_pending = $wpdb->get_results( $get_total_pending_sql, ARRAY_A );

        if(count($total_pending)>0)
        {
          foreach($total_pending as $row)
          {
            array_push($book_array, $row['book_id']);
          }
        }
        
        $data = get_allocated_books_total(json_decode( stripslashes($_POST['wlms_issues_data'])), $roles[0]);
        
        if(count($data)>0)
        {
          if(count($total_pending) < $data['books_borrow'])
          {
            $get_same_values = array();
            $get_diffrence_values = array();

            $get_same_values = array_intersect($book_array, $id);
            //$get_diffrence_values = array_merge(array_diff($book_array, $id), array_diff($id, $book_array));
            $get_diffrence_values = array_diff($id, $book_array);

            if(count($get_diffrence_values)>0)
            {
              //save borrow books
              $wpdb->query( $wpdb->prepare( 
                "
                  INSERT INTO {$wpdb->prefix}wlms_borrow
                  ( member_id, date_borrow, due_date, filter_date )
                  VALUES ( %d, %s, %s, %s )
                ", 
                array(
                  $member_id, 
                  date("Y-m-d H:i:s"),
                  date("Y-m-d", strtotime($due_date)),
                  date("Y-m-d")  
                ) 
              ) );
          
              
              $get_borrow_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_borrow ORDER BY borrow_id DESC LIMIT 1",  '');
              $get_borrow = $wpdb->get_row( $get_borrow_sql );
              $borrow_id  = $get_borrow->borrow_id;

              $insert_count =0;
              
              foreach($get_diffrence_values as $vals)
              {
                $insert_count = $wpdb->query( $wpdb->prepare( 
                  "
                    INSERT INTO {$wpdb->prefix}wlms_borrow_details
                    ( book_id, borrow_id, borrow_status )
                    VALUES ( %d, %d, %s )
                  ", 
                  array(
                    $vals,
                    $borrow_id,
                    'pending'  
                  ) 
                ) );
              }
              echo '<div class="updated"><p>Book(s) are successfully added for your selected member</p></div>';
            }  

            if(count($get_same_values)>0)
            {
              $str = '';
              $str .= '<p><strong>There are some error - </strong></p>';

              foreach($get_same_values as $book_row)
              {
                $get_books_name_sql = $wpdb->prepare( "SELECT book_title FROM {$wpdb->prefix}wlms_books_list WHERE id = %d ",  $book_row);
                $get_books_name = $wpdb->get_row( $get_books_name_sql );
              
                $name           =     $get_books_name->book_title;

                $str .= '<p><i><strong>'. $name .'</strong></i> already pending this book</p>';
              }

              if($str)
              {
                echo '<div class="error">'. $str .'</div>';
              }
            }
          }
          else
          {
            echo '<div class="error"><p>There are '. $data['books_borrow'] .' books pending of this member, already exceeded your borrow limit</p></div>';
          }
        }
        else
        {
          echo '<div class="error"><p>How many book(s) will be allow for this member type, please configure from "Member Issues Settings"</p></div>';
        }
        
      }
      else {
        echo '<div class="error"><p>You can not select previous date from today</p></div>';
      }
    }
    else
    {
      echo '<div class="error">'
      . '<p><b>ERROR:</b> Member Type Required</p>'
      . '<p><b>ERROR:</b> Borrower Required</p>'
      . '<p><b>ERROR:</b> Due Date Required</p>'
      . '</div>';
    }
  }
  else {
    echo '<div class="error"><p>Please select book name from right side book list</p></div>';
  }
}

function wlms_book_borrow_content()
{
  global $wpdb, $wp_roles;
  $issues = '';
  
  $roles = $wp_roles->get_names();
  $settings = get_option( 'wlms_settings' );
  
  $get_book_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_books_list",  '');
  $entries_for_books = $wpdb->get_results( $get_book_sql );
  
  $get_issues_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_issues_settings",  '');
  $entries_for_issues = $wpdb->get_results( $get_issues_sql );
  
  if($entries_for_issues)
  {
    $issues = esc_attr(json_encode($entries_for_issues));
  }
  
?>
<br>
<form id="borrow_form" action="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-borrow&manage=manage-books-borrow' ) );?>" method="post" enctype="multipart/form-data">
  <div class="wlms_borrow_content">
    <input type="hidden" name="wlms_issues_data" id="wlms_issues_data" value="<?php echo $issues; ?>" />
    <div class="wlms_borrow_content_left">
      <div class="wlms_member_option">
        <div>Select Member Type</div>
        <div>
          <select name="wlms_select_member_type" id="wlms_select_member_type">
            <option value="-1">Member Type</option>
         
            <?php  if(count($roles)>0){ foreach($roles as $key => $vals){
              if(in_array($key, $settings['member_roles'])){
            ?>
            <option value="<?php echo $key;?>"><?php echo $vals;?></option>
            <?php }}}?>
          </select>
        </div>
      </div>
      <div class="wlms_member_option">
        <div>Select Borrower Name</div>
        <div>
          <select name="wlms_select_member" id="wlms_select_member">
          </select>
        </div>
      </div>
      <div class="wlms_due_option">
        <div>Due Date</div>
        <div><input type="text" name="wlms_due_date" id="wlms_due_date"></div>
      </div>
        <div><input class="reset button-primary" type="submit" name="wlms_borrow_save" id="wlms_borrow_save" value="Borrow"></div>  
    </div>
    <div class="wlms_borrow_content_right">
      <div class="wlms_select_book_top">
        <p>Select Book For Borrow</p>
      </div>
      
      <table id="borrowTable" class="wlmsTablesorter">
          <thead> 
              <tr> 
                  <th>ID</th> 
                  <th>BOOK TITLE</th>
                  <th>CATEGORY</th>
                  <th>AUTHOR</th>
                  <th>PUBLISHER NAME</th>
                  <th>AVAILABLE COPIES</th>
                  <th>STATUS</th>
                  <th>ADD</th>
              </tr> 
          </thead>
          <tbody>
              <?php 
              $i = 1;
              if( $entries_for_books ){
              foreach( $entries_for_books as $rowsData ){
                $get_book_cat_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_book_categories WHERE id = %d ", $rowsData->category );
                $get_books_cat = $wpdb->get_row( $get_book_cat_sql );
    
                $total         = $wpdb->get_var( "SELECT count({$wpdb->prefix}wlms_borrow_details.book_id) FROM {$wpdb->prefix}wlms_borrow_details WHERE book_id = '". $rowsData->id ."' AND borrow_status = 'pending'" );
                
                if($total < $rowsData->book_copies){
              ?> 
              <tr>
                  <td><?php echo $i;?></td>
                  <td><?php echo esc_html( $rowsData->book_title );?></td>
                  <td><?php echo esc_html( $get_books_cat->name );?></td>
                  <td><?php echo esc_html( $rowsData->author );?></td>
                  <td><?php echo esc_html( $rowsData->publisher_name );?></td>
                  <td><?php echo number_format($rowsData->book_copies - $total);?></td>
                  <td><?php echo $rowsData->status;?></td>
                  <td><input class="add_selected_book" type="checkbox" name="wlms_check_for_borrow_this_book[]" id="wlms_check_for_borrow_this_book" value="<?php echo $rowsData->id;?>"></td>
              </tr>
              <?php $i ++; }}} else{?>
                  <tr>
                      <td colspan="7">No data yet</td>
                  </tr>
              <?php }?>
          </tbody>
      </table>
    </div>
  </div>
  <?php wp_nonce_field( 'submit_books_borrow' ); ?>
</form>    
<?php 
}

function get_allocated_books_total($array = array(), $roles)
{
  $allocated_data = array();
  
  if(count($array)>0)
  {
    foreach($array as $vals)
    {
      if($vals->member_role == $roles)
      {
        $allocated_data['id'] = $vals->id;
        $allocated_data['member_role'] = $vals->member_role;
        $allocated_data['returned_days'] = $vals->returned_days;
        $allocated_data['books_borrow'] = $vals->books_borrow;
        $allocated_data['fine'] = $vals->fine;
      }
    }
  }
  
  return $allocated_data;
}
?>