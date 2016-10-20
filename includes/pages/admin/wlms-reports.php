<?php 

// manage plugin reports here 
function wlms_reports_manage_page_content()
{
  if( isset( $_GET['tab'] ) && $_GET['tab'] === 'filter_borrow_list_by_date' )
  {
    wlms_filter_borrow();
  }
  elseif( isset( $_GET['tab'] ) && $_GET['tab'] === 'filter_borrow_returned_list_by_date' )
  {
    wlms_filter_borrow_returned();
  }
  elseif( isset( $_GET['tab'] ) && $_GET['tab'] === 'filter_borrow_due_list_by_date' )
  {
    wlms_filter_borrow_due();
  }
  else
  {
    wlms_filter_borrow();
  }
}

function wlms_filter_borrow()
{
  wlms_reports_menu_active_deactive( 'filter_borrow_list_by_date' );
}

function wlms_filter_borrow_content()
{
  global $wpdb;

  $current_date = date("Y-m-d");

  $get_entries_sql = $wpdb->prepare( "select * from {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}wlms_borrow.member_id = {$wpdb->prefix}users.ID LEFT JOIN {$wpdb->prefix}wlms_borrow_details ON {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id LEFT JOIN {$wpdb->prefix}wlms_books_list on {$wpdb->prefix}wlms_borrow_details.book_id =  {$wpdb->prefix}wlms_books_list.id WHERE {$wpdb->prefix}wlms_borrow.filter_date BETWEEN '$current_date' AND '$current_date' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC",  '');
  $_entries = $wpdb->get_results( $get_entries_sql );
?>

  <br>
  <div class="wlms_reports_top">
      <div class="wlms_reports_elements_main">
          <span>Start Date:</span><span><input type="text" name="wlms_borrow_start_date" id="wlms_borrow_start_date" value="<?php echo $current_date;?>"></span>
      </div>
      <div class="wlms_reports_elements_main">
          <span>End Date:</span><span><input type="text" name="wlms_borrow_end_date" id="wlms_borrow_end_date" value="<?php echo $current_date;?>"></span>
      </div>
      <div class="wlms_reports_elements_main">
        <button class="button-primary" type="button" name="wlms_borrow_filter" id="wlms_borrow_filter">Filter</button>
      </div>
  </div>
  <div class="wlms_borrow_filter_result">
    <br>  
    <table id="borrowFilterTable" class="wlmsTablesorter">
        <thead> 
            <tr> 
                <th>SN.</th> 
                <th>BOOK TITLE</th>
                <th>BORROWER</th>
                <th>DATE BORROW</th>
            </tr> 
        </thead>
        <tbody class="wlms_filter_borrow_list">
            <?php 
            $i = 1;
            if( $_entries ){
            foreach( $_entries as $rows ){
            ?> 
            <tr>
                <td><?php echo $i;?></td>
                <td><?php echo $rows->book_title;?></td>
                <td><?php echo $rows->display_name;?></td>
                <td><?php echo $rows->date_borrow;?></td>
            </tr>
            <?php $i ++;}}else{?>
                <tr>
                    <td colspan="4">No data yet</td>
                </tr>
            <?php }?>
        </tbody>
    </table>
  </div>
  <?php 
}

function wlms_filter_borrow_returned()
{
  wlms_reports_menu_active_deactive( 'filter_borrow_returned_list_by_date' );
}

function wlms_filter_borrow_returned_content()
{
  global $wpdb;
  $current_date = date("Y-m-d");

  $get_entries_sql = $wpdb->prepare( "select * from {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}wlms_borrow.member_id = {$wpdb->prefix}users.ID LEFT JOIN {$wpdb->prefix}wlms_borrow_details ON {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id LEFT JOIN {$wpdb->prefix}wlms_books_list on {$wpdb->prefix}wlms_borrow_details.book_id =  {$wpdb->prefix}wlms_books_list.id where {$wpdb->prefix}wlms_borrow_details.filter_date BETWEEN '$current_date' AND '$current_date' AND {$wpdb->prefix}wlms_borrow_details.borrow_status = 'returned' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC",  '');
  $_entries = $wpdb->get_results( $get_entries_sql );

  ?>
  <br>
  <div class="wlms_reports_top">
      <div class="wlms_reports_elements_main">
          <span>Start Date:</span><span><input type="text" name="wlms_borrow_return_start_date" id="wlms_borrow_return_start_date" value="<?php echo $current_date;?>"></span>
      </div>
      <div class="wlms_reports_elements_main">
          <span>End Date:</span><span><input type="text" name="wlms_borrow_return_end_date" id="wlms_borrow_return_end_date" value="<?php echo $current_date;?>"></span>
      </div>
      <div class="wlms_reports_elements_main">
        <button class="button-primary" type="button" name="wlms_borrow_return_filter" id="wlms_borrow_return_filter">Filter</button>
      </div>
  </div>

  <div class="wlms_borrow_filter_result">
    <br>
    <table id="borrowReturnFilterTable" class="wlmsTablesorter">
        <thead> 
            <tr> 
                <th>SN.</th> 
                <th>BOOK TITLE</th>
                <th>BORROWER</th>
                <th>DATE BORROW</th>
                <th>DATE RETURNED</th>
            </tr> 
        </thead>
        <tbody class="wlms_filter_borrow_return_list">
            <?php 
            $i = 1;
            if( $_entries ){
            foreach( $_entries as $rows ){
            ?> 
            <tr>
              <td><?php echo $i;?></td>
              <td><?php echo $rows->book_title;?></td>
              <td><?php echo $rows->display_name;?></td>
              <td><?php echo $rows->date_borrow;?></td>
              <td><?php echo $rows->date_return;?></td>
            </tr>
            <?php $i ++;}}else{?>
                <tr>
                    <td colspan="5">No data yet</td>
                </tr>
            <?php }?>
        </tbody>
    </table>
  </div>
<?php 
}

function wlms_filter_borrow_due()
{
  wlms_reports_menu_active_deactive( 'filter_borrow_due_list_by_date' );
}

function wlms_filter_borrow_due_content()
{
  global $wpdb;
  $current_date = date("Y-m-d");

  $get_entries_sql = $wpdb->prepare( "select * from {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}wlms_borrow.member_id = {$wpdb->prefix}users.ID LEFT JOIN {$wpdb->prefix}wlms_borrow_details ON {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id LEFT JOIN {$wpdb->prefix}wlms_books_list on {$wpdb->prefix}wlms_borrow_details.book_id =  {$wpdb->prefix}wlms_books_list.id where {$wpdb->prefix}wlms_borrow_details.filter_date BETWEEN '$current_date' AND '$current_date' AND {$wpdb->prefix}wlms_borrow_details.borrow_status = 'returned' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC",  '');
  $_entries = $wpdb->get_results( $get_entries_sql );

  ?>
  <br>
  <div class="wlms_reports_top">
      <div class="wlms_reports_elements_main">
          <span>Start Date:</span><span><input type="text" name="wlms_borrow_due_start_date" id="wlms_borrow_due_start_date" value="<?php echo $current_date;?>"></span>
      </div>
      <div class="wlms_reports_elements_main">
          <span>End Date:</span><span><input type="text" name="wlms_borrow_due_end_date" id="wlms_borrow_due_end_date" value="<?php echo $current_date;?>"></span>
      </div>
      <div class="wlms_reports_elements_main">
        <button class="button-primary" type="button" name="wlms_borrow_due_filter" id="wlms_borrow_due_filter">Filter</button>
      </div>
  </div>

  <div class="wlms_borrow_filter_result">
    <br>   
    <table id="borrowDueFilterTable" class="wlmsTablesorter">
        <thead> 
            <tr> 
                <th>SN.</th> 
                <th>BOOK TITLE</th>
                <th>BORROWER</th>
                <th>DATE BORROW</th>
                <th>DUE DATE</th>
            </tr> 
        </thead>
        <tbody class="wlms_filter_borrow_due_list">
            <?php 
            $i = 1;
            if( $_entries ){
            foreach( $_entries as $rows ){
            ?> 
            <tr>
              <td><?php echo $i;?></td>
              <td><?php echo $rows->book_title;?></td>
              <td><?php echo $rows->display_name;?></td>
              <td><?php echo $rows->date_borrow;?></td>
              <td><?php echo $rows->due_date;?></td>
            </tr>
            <?php $i ++;}}else{?>
                <tr>
                    <td colspan="5">No data yet</td>
                </tr>
            <?php }?>
        </tbody>
    </table>
  </div>
  <?php 
}

function wlms_reports_menu_active_deactive( $whichTab )
{
  echo '<div class="wrap">';
    echo '<h2 class="nav-tab-wrapper">';
    if( $whichTab == 'filter_borrow_list_by_date' )
    {
        echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms_reports_manage_page&tab=filter_borrow_list_by_date' ) ) .'" title="Borrow List" class="nav-tab nav-tab-active">Borrow List</a>';	
    }
    else
    {
        echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms_reports_manage_page&tab=filter_borrow_list_by_date' ) ) .'" title="Borrow List" class="nav-tab">Borrow List</a>';
    }

    if( $whichTab == 'filter_borrow_returned_list_by_date' )
    { 
        echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms_reports_manage_page&tab=filter_borrow_returned_list_by_date' ) ) .'" title="Returned List" class="nav-tab nav-tab-active">Returned List</a>';	
    }
    else
    {
        echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms_reports_manage_page&tab=filter_borrow_returned_list_by_date' ) ) .'" title="Returned List" class="nav-tab">Returned List</a>';
    }
    
    if( $whichTab == 'filter_borrow_due_list_by_date' )
    {
       
        echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms_reports_manage_page&tab=filter_borrow_due_list_by_date' ) ) .'" title="Due List" class="nav-tab nav-tab-active">Due List</a>';	
    }
    else
    {
        echo '<a href="'.  esc_url_raw( admin_url( 'admin.php?page=wlms_reports_manage_page&tab=filter_borrow_due_list_by_date' ) ) .'" title="Due List" class="nav-tab">Due List</a>';
    }
    echo '</h2>';
    
    if( $whichTab == 'filter_borrow_list_by_date' )
    {
      wlms_filter_borrow_content();
    }
    
    if( $whichTab == 'filter_borrow_returned_list_by_date' )
    {
      wlms_filter_borrow_returned_content();
    }
    
    if( $whichTab == 'filter_borrow_due_list_by_date' )
    {
      wlms_filter_borrow_due_content();
    }
    
  echo '</div>';  
}	
?>