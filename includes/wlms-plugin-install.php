<?php

// when plugin activate, it will be call 
function wlms_plugin_install()
{
  wlms_create_table();
  wlms_settings_data();
}

// when plugin deactivate, it will be call 
function wlms_plugin_uninstall()
{
  delete_option('wlms_settings');
}


// plugin tables create 
function wlms_create_table()
{
  global $wpdb;
  $collate = '';

  if ( $wpdb->has_cap( 'collation' ) ) 
  {
    if ( ! empty($wpdb->charset ) ) 
    {
        $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
    }
    if ( ! empty($wpdb->collate ) ) 
    {
        $collate .= " COLLATE $wpdb->collate";
    }
  }

  $wlms_book_categories = "
  CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wlms_book_categories (
  id int(10) NOT NULL auto_increment,
  name VARCHAR(100),
  status int(10),
  PRIMARY KEY  (id)) $collate;
  ";
    
  $wpdb->query( $wlms_book_categories );

  $wlms_books = "
  CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wlms_books_list (
  id int(10) NOT NULL auto_increment,
  book_title TEXT,
  category int(10),
  author TEXT,
  edition VARCHAR(250),
  edition_year VARCHAR(50),
  copy_type VARCHAR(100),
  price VARCHAR(50),
  location TEXT,
  book_copies VARCHAR(30),
  book_pub TEXT,
  publisher_name TEXT,
  isbn VARCHAR(250),
  copyright_year VARCHAR(50),
  date_added DATE,
  status VARCHAR(50),
  short_note TEXT,
  cover_image_url TEXT,
  PRIMARY KEY  (id)) $collate;
  ";
    
  $wpdb->query( $wlms_books );

  $wlms_book_borrow = "
  CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wlms_borrow (
  borrow_id int(11) NOT NULL auto_increment,
  member_id bigint(50),
  date_borrow VARCHAR(100),
  due_date VARCHAR(100),
  filter_date DATE,
  PRIMARY KEY  (borrow_id)) $collate;
  ";
    
  $wpdb->query( $wlms_book_borrow );

  $wlms_book_borrow_details = "
  CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wlms_borrow_details (
  borrow_details_id int(11) NOT NULL auto_increment,
  book_id int(11),
  borrow_id int(11),
  borrow_status VARCHAR(50),
  date_return VARCHAR(100),
  filter_date VARCHAR(100),
  PRIMARY KEY  (borrow_details_id)) $collate;
  ";
    
  $wpdb->query( $wlms_book_borrow_details );

  $wlms_msg_details = "
  CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wlms_msg_details (
  id int(11) NOT NULL auto_increment,
  member_type VARCHAR(50),
  receiver VARCHAR(50),
  subject LONGTEXT,
  msg_details LONGTEXT,
  msg_date VARCHAR(100),
  status int(11),
  PRIMARY KEY  (id)) $collate;
  ";
    
  $wpdb->query( $wlms_msg_details );
  
  $wlms_issues_details = "
  CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wlms_member_issues_settings (
  id int(11) NOT NULL auto_increment,
  member_role LONGTEXT,
  returned_days int(11),
  books_borrow int(11),
  fine int(11),
  PRIMARY KEY  (id)) $collate;
  ";
    
  $wpdb->query( $wlms_issues_details );
  
  $wlms_member_requested_books = "
  CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wlms_member_requested_books (
  id int(11) NOT NULL auto_increment,
  member_id int(11),
  book_id int(11),
  requested_date VARCHAR(100),
  reply_date VARCHAR(100),
  status int(11),
  PRIMARY KEY  (id)) $collate;
  ";
    
  $wpdb->query( $wlms_member_requested_books );
  
  //add default role
  $roles = array('wlms_teachers' => 'Teachers', 'wlms_students' => 'Students', 'wlms_staff' => 'Staff');
  
  foreach($roles as $key => $val)
  {
    add_role(
        $key,
        $val
    );
  }
}


// plugin settings data
function wlms_settings_data()
{
  $save_settings = array(
                        'institute_name' =>              'Your Institute Name',
                        'institute_address' =>           'Institute Address',
                        'phone_number' =>                '02-01245666',
                        'email' =>                       'example@yahoo.com',
                        'currency' =>                    'USD',
                        'terms_and_conditions' =>        'Your Terms and Conditions',
                        'member_roles' =>                array('wlms_teachers', 'wlms_students', 'wlms_staff'),
                        'logo_url' =>                    WLMS_PLUGIN_URL.'/assets/images/sample-logo.png'
                        
  );
  if ( get_option( 'wlms_settings' ) === false ) 
  {
    add_option( 'wlms_settings', $save_settings );
  }
}


// manage admin pages
function wlms_add_admin_page()
{
  add_menu_page( 'Admin Dashboard', 'LMS', 'manage_options','wlms-pages', 'wlms_page_control_content' , WLMS_PLUGIN_URL.'/assets/images/library-icon.png' );
  add_submenu_page( 'wlms-pages', 'Message', 'Message', 'manage_options', 'wlms_message_manage_page', 'wlms_message_manage_page_content' );
  add_submenu_page( 'wlms-pages', 'Reports', 'Reports', 'manage_options', 'wlms_reports_manage_page', 'wlms_reports_manage_page_content' );
  add_submenu_page( 'wlms-pages', 'Settings', 'Settings', 'manage_options', 'wlms-manage-settings-page', 'wlms_settings_manage_page_content' );
}
add_action( 'admin_menu', 'wlms_add_admin_page' );


// manage currency symbol
function get_currency_symbol( $currency = '' )
{
  switch ( $currency ) {
  case 'AED' :
    $currency_symbol = 'د.إ';
    break;
  case 'AUD' :
  case 'ARS' :
  case 'CAD' :
  case 'CLP' :
  case 'COP' :
  case 'HKD' :
  case 'MXN' :
  case 'NZD' :
  case 'SGD' :
  case 'USD' :
    $currency_symbol = '&#36;';
    break;
  case 'BDT':
    $currency_symbol = '&#2547;&nbsp;';
    break;
  case 'BGN' :
    $currency_symbol = '&#1083;&#1074;.';
    break;
  case 'BRL' :
    $currency_symbol = '&#82;&#36;';
    break;
  case 'CHF' :
    $currency_symbol = '&#67;&#72;&#70;';
    break;
  case 'CNY' :
  case 'JPY' :
  case 'RMB' :
    $currency_symbol = '&yen;';
    break;
  case 'CZK' :
    $currency_symbol = '&#75;&#269;';
    break;
  case 'DKK' :
    $currency_symbol = 'DKK';
    break;
  case 'DOP' :
    $currency_symbol = 'RD&#36;';
    break;
  case 'EGP' :
    $currency_symbol = 'EGP';
    break;
  case 'EUR' :
    $currency_symbol = '&euro;';
    break;
  case 'GBP' :
    $currency_symbol = '&pound;';
    break;
  case 'HRK' :
    $currency_symbol = 'Kn';
    break;
  case 'HUF' :
    $currency_symbol = '&#70;&#116;';
    break;
  case 'IDR' :
    $currency_symbol = 'Rp';
    break;
  case 'ILS' :
    $currency_symbol = '&#8362;';
    break;
  case 'INR' :
    $currency_symbol = 'Rs.';
    break;
  case 'ISK' :
    $currency_symbol = 'Kr.';
    break;
  case 'KIP' :
    $currency_symbol = '&#8365;';
    break;
  case 'KRW' :
    $currency_symbol = '&#8361;';
    break;
  case 'MYR' :
    $currency_symbol = '&#82;&#77;';
    break;
  case 'NGN' :
    $currency_symbol = '&#8358;';
    break;
  case 'NOK' :
    $currency_symbol = '&#107;&#114;';
    break;
  case 'NPR' :
    $currency_symbol = 'Rs.';
    break;
  case 'PHP' :
    $currency_symbol = '&#8369;';
    break;
  case 'PLN' :
    $currency_symbol = '&#122;&#322;';
    break;
  case 'PYG' :
    $currency_symbol = '&#8370;';
    break;
  case 'RON' :
    $currency_symbol = 'lei';
    break;
  case 'RUB' :
    $currency_symbol = '&#1088;&#1091;&#1073;.';
    break;
  case 'SEK' :
    $currency_symbol = '&#107;&#114;';
    break;
  case 'THB' :
    $currency_symbol = '&#3647;';
    break;
  case 'TRY' :
    $currency_symbol = '&#8378;';
    break;
  case 'TWD' :
    $currency_symbol = '&#78;&#84;&#36;';
    break;
  case 'UAH' :
    $currency_symbol = '&#8372;';
    break;
  case 'VND' :
    $currency_symbol = '&#8363;';
    break;
  case 'ZAR' :
    $currency_symbol = '&#82;';
    break;
  default :
    $currency_symbol = '';
    break;
}

  return $currency_symbol;
}


// dynamic message set here
function set_message_key($key, $val)
{
  if(!isset($_SESSION[$key]))
  {
    $_SESSION[$key] = $val;
  }
}

// dynamic message return 
function get_message_by_event()
{
  $msg = '';
  
  if(isset($_SESSION['item_saved']))
  {
    $msg =  '<div class="wlms-success-message">Your item have been successfully saved</div>';
    unset($_SESSION['item_saved']);
  }
  elseif(isset($_SESSION['item_updated']))
  {
    $msg = '<div class="wlms-success-message">Your item have been successfully updated</div>';
    unset($_SESSION['item_updated']);
  }
  elseif(isset($_SESSION['check_csv_file']))
  {
    $msg = '<div class="wlms-error-message">Please upload csv file, You can download sample csv file to add bulk books</div>';
    unset($_SESSION['check_csv_file']);
  }
  elseif(isset($_SESSION['check_csv_file_data_format']))
  {
    $msg = '<div class="wlms-error-message">Your csv data are not correct format, You can download sample csv file to add bulk books</div>';
    unset($_SESSION['check_csv_file_data_format']);
  }
  elseif(isset($_SESSION['issues_already_created']))
  {
    $msg = '<div class="wlms-error-message">Issues settings already created for this member type</div>';
    unset($_SESSION['issues_already_created']);
  }
  
  return $msg;
}
?>