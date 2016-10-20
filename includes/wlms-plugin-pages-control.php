<?php 

//admin tab control here 
function wlms_page_control_content()
{
  if( isset($_GET['wlms-tab']))
  {
    wlms_tab_control( $_GET['wlms-tab'] );
    
    if( isset($_POST['wlms_save_book_categories']) || isset($_POST['wlms_update_book_categories']) )
    {
      wlms_book_categories_save();
    }
    else if( isset($_POST['wlms_save_book']) || isset($_POST['wlms_update_book']) )
    {
      wlms_books_save();
    }
    else if( isset($_POST['wlms_bulk_save_books']) )
    {
      wlms_bulk_books_save();
    }
    else if( isset($_POST['wlms_borrow_save']) )
    {
      wlms_book_borrow_content_save();
    }
    else if( isset($_POST['wlms_save_member_issues']) || isset($_POST['wlms_update_member_issues']) )
    {
      wlms_member_issues_settings_save();
    }
  }
  else
  {
    wlms_tab_control('wlms-dashboard');
  }
}

function wlms_tab_control( $tab )
{
  echo '<div class="wrap">';
  echo '<h2 class="nav-tab-wrapper">';
  
  if( $tab == 'wlms-dashboard' )
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-dashboard' ) ) .'" title="dashboard" class="nav-tab nav-tab-active">Dashboard</a>';
  }
  else
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-dashboard' ) ) .'" title="dashboard" class="nav-tab">Dashboard</a>';
  }
  
  if( $tab == 'wlms-book-categories' )
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-book-categories&manage=book-categories-lists' ) ) .'" title="book categories" class="nav-tab nav-tab-active">Book Categories</a>';	
  }
  else
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-book-categories&manage=book-categories-lists' ) ) .'" title="book categories" class="nav-tab">Book Categories</a>';
  }
  
  if( $tab == 'wlms-manage-books' )
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=manage-books-lists' ) ) .'" title="manage books" class="nav-tab nav-tab-active">Manage Books</a>';	
  }
  else
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=manage-books-lists' ) ) .'" title="manage books" class="nav-tab">Manage Books</a>';
  }
  
  if( $tab == 'wlms-member-issues-settings' )
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=member-issues-settings-list' ) ) .'" title="member issues settings" class="nav-tab nav-tab-active">Issues Settings</a>';	
  }
  else
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=member-issues-settings-list' ) ) .'" title="member issues settings" class="nav-tab">Issues Settings</a>';
  }
  
  if( $tab == 'wlms-manage-borrow' )
  {
    echo '<a href="'.  esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-borrow&manage=manage-books-borrow' ) ) .'" title="manage books borrow" class="nav-tab nav-tab-active">Borrow</a>';	
  }
  else
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-borrow&manage=manage-books-borrow' ) ) .'" title="manage books borrow" class="nav-tab">Borrow</a>';
  }
  
  if( $tab == 'wlms-manage-borrowed-books' )
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-borrowed-books&manage=manage-borrowed-books' ) ) .'" title="manage borrowed books" class="nav-tab nav-tab-active">Manage Borrowed</a>';
  }
  else
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-borrowed-books&manage=manage-borrowed-books' ) ) .'" title="manage borrowed books" class="nav-tab">Manage Borrowed</a>';
  }
  
  if( $tab == 'wlms-view-returned-books' )
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-view-returned-books&manage=view-returned-books' ) ) .'" title="view returned book" class="nav-tab nav-tab-active">View Returned</a>';
  }
  else
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-view-returned-books&manage=view-returned-books' ) ) .'" title="view returned book" class="nav-tab">View Returned</a>';
  }
  
  if( $tab == 'wlms-manage-requested-books' )
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-requested-books&manage=requested-books' ) ) .'" title="manage requested book" class="nav-tab nav-tab-active">Manage Requested</a>';
  }
  else
  {
    echo '<a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-requested-books&manage=requested-books' ) ) .'" title="manage requested book" class="nav-tab">Manage Requested</a>';
  }
  
  echo '</h2>';
  
  if( $tab == 'wlms-dashboard' )
  {
    wlms_dashboard_page_content();
  }
  else if( $tab == 'wlms-book-categories' )
  {
    wlms_book_categories_page_content();
  }
  else if( $tab == 'wlms-manage-books' )
  {
    wlms_manage_books_page_content();
  }
  else if( $tab == 'wlms-member-issues-settings' )
  {
    wlms_member_issues_settings_page_content();
  }
  else if( $tab == 'wlms-manage-borrow' )
  {
    wlms_manage_books_borrow_page_content();
  }
  else if( $tab == 'wlms-manage-borrowed-books' )
  {
    wlms_manage_borrowed_books_page_content();
  }
  else if( $tab == 'wlms-view-returned-books' )
  {
    wlms_view_returned_books_page_content();
  }
  else if( $tab == 'wlms-manage-requested-books' )
  {
    wlms_manage_requested_books_page_content();
  }
  echo '</div>';
}	
?>