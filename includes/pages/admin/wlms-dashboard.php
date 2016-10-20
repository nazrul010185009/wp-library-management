<?php 

// This is the plugin dashboard
function wlms_dashboard_page_content()
{
  wlms_dashboard_html();
}

function wlms_dashboard_html()
{
  global $wpdb;
  $total_borrow   = 0;
  $total_returned = 0;
  $total_pending  = 0;
  $total_books    = 0;
  $current_date   = date("Y-m-d");
  
  $total_borrow                           =   get_totals_for_reports ("ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC", 'borrow_list');
  
  $total_returned                         =   get_totals_for_reports ("where {$wpdb->prefix}wlms_borrow_details.borrow_status = 'returned' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC", 'borrow_list'); 
  
  
  $total_pending                          =   get_totals_for_reports ("where {$wpdb->prefix}wlms_borrow_details.borrow_status = 'pending' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC", 'borrow_list'); 
  
  
  
  $total_borrow_by_current_day            =   get_totals_for_reports ("where DATE({$wpdb->prefix}wlms_borrow.filter_date) = '". $current_date."' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC", 'borrow_list'); 
  
  
  
  $total_borrow_by_current_month          =   get_totals_for_reports ("where {$wpdb->prefix}wlms_borrow.filter_date >= LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL 1 MONTH AND {$wpdb->prefix}wlms_borrow.filter_date < LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC", 'borrow_list');
  
  
  
  $total_returned_by_current_day          =   get_totals_for_reports ("where DATE({$wpdb->prefix}wlms_borrow.filter_date) = '". $current_date."' and {$wpdb->prefix}wlms_borrow_details.borrow_status = 'returned' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC", 'borrow_list');
          
          
  
  $total_returned_by_current_month        =   get_totals_for_reports ("where {$wpdb->prefix}wlms_borrow.filter_date >= LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL 1 MONTH AND {$wpdb->prefix}wlms_borrow.filter_date < LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY and {$wpdb->prefix}wlms_borrow_details.borrow_status = 'returned' ORDER BY {$wpdb->prefix}wlms_borrow.borrow_id DESC", 'borrow_list');
  
  
  
  
  $total_books                           =    get_totals_for_reports ("ORDER BY {$wpdb->prefix}wlms_books_list.id DESC", 'book_list');
  
  $total_books_current_date              =    get_totals_for_reports ("where DATE({$wpdb->prefix}wlms_books_list.date_added) = '". $current_date."' ORDER BY {$wpdb->prefix}wlms_books_list.id DESC", 'book_list');
  
  
  
  $total_books_current_month             =    get_totals_for_reports ("where {$wpdb->prefix}wlms_books_list.date_added >= LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL 1 MONTH AND {$wpdb->prefix}wlms_books_list.date_added < LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY ORDER BY {$wpdb->prefix}wlms_books_list.id DESC", 'book_list');
  
  
  
  $settings = get_option( 'wlms_settings' );
  
  
  if($total_borrow > 0)
  {
    $total_borrow = $total_borrow;
  }
  
  if($total_returned > 0)
  {
    $total_returned = $total_returned;
  }
  
  if($total_pending > 0)
  {
    $total_pending = $total_pending;
  }
  
  $array = array('Total Borrowed' => $total_borrow, 'Total Returned' => $total_returned, 'Total Pending' => $total_pending);
  
  $_data_arry = array();
  
  if(count($array)>0)
  {
    foreach($array as $key => $val)
    {
      $dataObj = new stdClass();
      $dataObj->name  = $key;
      $dataObj->total = $val;
      $_data_arry[] = $dataObj;
    }
  }
  
?>
<div class="wlms-reports-graph">
  <div class="chart" id="books_issued_chart"></div>
</div>
<br>

<div class="wlms-overall-report">
  <h2>Overall Report</h2>
  <div class="wlms-small-box wlms-bg-1">
    <div class="inner">
      <h3><?php echo $total_books;?></h3>
      <p>Total Number of Books</p>
    </div>
    <div class="icon">
      <i class="ion ion-bag"></i>
    </div>
    <a class="wlms-small-box-footer" target="_blank" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=manage-books-lists' ) );?>">More Information <i class="fa fa-arrow-circle-right"></i></a>
  </div>
  <div class="wlms-small-box wlms-bg-2">
    <div class="inner">
      <h3><?php echo $total_borrow;?></h3>
      <p>Total Number of Borrowed Books</p>
    </div>
    <div class="icon">
      <i class="ion ion-bag"></i>
    </div>
    <a class="wlms-small-box-footer" target="_blank" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-borrowed-books&manage=manage-borrowed-books' ) );?>">More Information <i class="fa fa-arrow-circle-right"></i></a>
  </div>
  <div class="wlms-small-box wlms-bg-3">
    <div class="inner">
      <h3><?php echo count($settings['member_roles']);?></h3>
      <p>Total Number of Allow Member Roles</p>
    </div>
    <div class="icon">
      <i class="ion ion-bag"></i>
    </div>
    <a class="wlms-small-box-footer" target="_blank" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms-manage-settings-page' ) );?>">More Information <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<br>

<div class="wlms-todays-report">
  <h2>Today's Report</h2>
  <div class="wlms-small-box wlms-bg-4">
    <div class="inner">
      <h3><?php echo $total_books_current_date;?></h3>
      <p>Today's Added Books</p>
    </div>
    <div class="icon">
      <i class="ion ion-bag"></i>
    </div>
    <a class="wlms-small-box-footer" target="_blank" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=manage-books-lists' ) );?>">More Information <i class="fa fa-arrow-circle-right"></i></a>
  </div>
  <div class="wlms-small-box wlms-bg-2">
    <div class="inner">
      <h3><?php echo $total_borrow_by_current_day;?></h3>
      <p>Today's Borrowed Books</p>
    </div>
    <div class="icon">
      <i class="ion ion-bag"></i>
    </div>
    <a class="wlms-small-box-footer" target="_blank" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms_reports_manage_page' ) );?>">More Information <i class="fa fa-arrow-circle-right"></i></a>
  </div>
  <div class="wlms-small-box wlms-bg-5">
    <div class="inner">
      <h3><?php echo $total_returned_by_current_day;?></h3>
      <p>Today's Returned Books</p>
    </div>
    <div class="icon">
      <i class="ion ion-bag"></i>
    </div>
    <a class="wlms-small-box-footer" target="_blank" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms_reports_manage_page&tab=filter_borrow_returned_list_by_date' ) );?>">More Information <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<br>

<div class="wlms-current-month-report">
  <h2>Current Month's Report</h2>
  <div class="wlms-small-box wlms-bg-4">
    <div class="inner">
      <h3><?php echo $total_books_current_month;?></h3>
      <p>This Month's Added Books</p>
    </div>
    <div class="icon">
      <i class="ion ion-bag"></i>
    </div>
    <a class="wlms-small-box-footer" target="_blank" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=manage-books-lists' ) );?>">More Information <i class="fa fa-arrow-circle-right"></i></a>
  </div>
  <div class="wlms-small-box wlms-bg-2">
    <div class="inner">
      <h3><?php echo $total_borrow_by_current_month;?></h3>
      <p>This Month's Borrowed Books</p>
    </div>
    <div class="icon">
      <i class="ion ion-bag"></i>
    </div>
    <a class="wlms-small-box-footer" target="_blank" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms_reports_manage_page' ) );?>">More Information <i class="fa fa-arrow-circle-right"></i></a>
  </div>
  <div class="wlms-small-box wlms-bg-5">
    <div class="inner">
      <h3><?php echo $total_returned_by_current_month;?></h3>
      <p>This Month's Returned Books</p>
    </div>
    <div class="icon">
      <i class="ion ion-bag"></i>
    </div>
    <a class="wlms-small-box-footer" target="_blank" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms_reports_manage_page&tab=filter_borrow_returned_list_by_date' ) );?>">More Information <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<input type="hidden" id="reports_json_data" name="reports_json_data" value="<?php echo esc_attr( json_encode( $_data_arry ) );?>">
<?php 
}

function get_totals_for_reports($sql, $track)
{
  global $wpdb;
  $total = 0;
  
  if($track == 'borrow_list')
  {
    $total = $wpdb->get_var( "select count({$wpdb->prefix}wlms_borrow_details.borrow_details_id) from {$wpdb->prefix}wlms_borrow LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}wlms_borrow.member_id = {$wpdb->prefix}users.ID LEFT JOIN {$wpdb->prefix}wlms_borrow_details ON {$wpdb->prefix}wlms_borrow.borrow_id = {$wpdb->prefix}wlms_borrow_details.borrow_id LEFT JOIN {$wpdb->prefix}wlms_books_list on {$wpdb->prefix}wlms_borrow_details.book_id = {$wpdb->prefix}wlms_books_list.id ".$sql );
  }
  elseif($track == 'book_list')
  {
    $total = $wpdb->get_var("select count({$wpdb->prefix}wlms_books_list.id) from {$wpdb->prefix}wlms_books_list ".$sql);
  }
  
  return $total;
}
?>