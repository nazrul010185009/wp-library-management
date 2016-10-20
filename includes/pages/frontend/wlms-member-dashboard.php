<?php	
// member dashbord from frontend
get_header();

$is_available_books =   'class="wlms-inactive-menu"';
$is_borrow_list     =   'class="wlms-inactive-menu"';
$is_requested       =   'class="wlms-inactive-menu"';
$is_message         =   'class="wlms-inactive-menu"';

if((isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' && $_GET['page'] == 'available-books') || (isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' && $_GET['page'] == 'available-books' && isset($_GET['book_id']))){
  $is_available_books =   'class="wlms-active-menu"';
}
elseif(isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' && $_GET['page'] == 'borrow-books'){
  $is_borrow_list =   'class="wlms-active-menu"';
}
elseif(isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' && $_GET['page'] == 'requested-books'){
  $is_requested =   'class="wlms-active-menu"';
}
elseif(isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' && $_GET['page'] == 'message'){
  $is_message =   'class="wlms-active-menu"';
}

$settings = get_option( 'wlms_settings' );
?>
<div class="dashboard-top-content">
  <div class="top-content-left-side">
    <div class="logo-content"><img src="<?php echo $settings['logo_url'];?>" alt="logo"></div>
    <div class="name-content"><h2><?php echo $settings['institute_name'];?></h2></div>
  </div>
  <div class="top-content-right-side">
    <div class="content-address"><strong>Address:</strong>&nbsp;<?php echo $settings['institute_address'];?></div>
    <div class="content-phone"><strong>Contact:</strong>&nbsp;<?php echo $settings['phone_number'];?></div>
    <div class="content-email"><strong>Email:</strong>&nbsp;<?php echo $settings['email'];?></div>
  </div>
</div>
<br>
<div class="wlms-member-dashboard-content">
  <div class="wlms-tabbed-content-main">
    <div class="wlms-tabbed">
      <div class="wlms-tabbed-menu"><div <?php echo $is_available_books;?>></div><a href="<?php echo esc_url_raw(add_query_arg( array('manage' => 'wlms-member-dashboard','page' =>'available-books',), home_url() ));?>">Available Books</a></div>
      <div class="wlms-tabbed-menu"><div <?php echo $is_borrow_list;?>></div><a href="<?php echo esc_url_raw(add_query_arg( array('manage' => 'wlms-member-dashboard','page' =>'borrow-books',), home_url() ));?>">My Borrow List</a></div>
      <div class="wlms-tabbed-menu"><div <?php echo $is_requested;?>></div><a href="<?php echo esc_url_raw(add_query_arg( array('manage' => 'wlms-member-dashboard','page' =>'requested-books',), home_url() ));?>">My Requested Books</a></div>
      <div class="wlms-tabbed-menu"><div <?php echo $is_message;?>></div><a href="<?php echo esc_url_raw(add_query_arg( array('manage' => 'wlms-member-dashboard','page' =>'message',), home_url() ));?>">My Notifications</a></div>
      <div class="wlms-tabbed-menu"><div class="wlms-inactive-menu"></div><a href="<?php echo wp_logout_url( get_permalink() );?>">Logout</a></div>
    </div>
    <div class="wlms-tabbed-content">
      <?php if(isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' && $_GET['page'] == 'available-books' && !isset($_GET['book_id'])){ ?>
        <?php require_once( WLMS_PLUGIN_DIR . '/includes/pages/frontend/wlms-available-books.php' ); ?>
      <?php } elseif(isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' && $_GET['page'] == 'available-books' && isset($_GET['book_id'])){ ?>
        <?php require_once( WLMS_PLUGIN_DIR . '/includes/pages/frontend/wlms-books-details.php' ); ?>
      <?php }  elseif(isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' && $_GET['page'] == 'borrow-books'){?>
        <?php require_once( WLMS_PLUGIN_DIR . '/includes/pages/frontend/wlms-borrow-list.php' ); ?>
      <?php } elseif(isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' && $_GET['page'] == 'requested-books'){?>
        <?php require_once( WLMS_PLUGIN_DIR . '/includes/pages/frontend/wlms-requested-list.php' ); ?>
      <?php } elseif(isset($_GET['manage']) && isset($_GET['page']) && $_GET['manage'] == 'wlms-member-dashboard' && $_GET['page'] == 'message'){?>
        <?php require_once( WLMS_PLUGIN_DIR . '/includes/pages/frontend/wlms-notifications.php' ); ?>
      <?php }?>
    </div>
  </div>
</div>
<?php
get_footer();
?>