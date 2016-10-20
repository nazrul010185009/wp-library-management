<?php

//manage menu pages inside the tab
function wlms_book_categories_page_content()
{
  echo '<div class="pages-content-top">';
  echo '<ul>';
  if(isset($_GET['manage']) && $_GET['manage'] == 'book-categories-lists'){
    echo '<li><a class="current" href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-book-categories&manage=book-categories-lists' ) ) .'">Book Categories List</a></li>';
  }
  else
  {
    echo '<li><a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-book-categories&manage=book-categories-lists' ) ) .'">Book Categories List</a></li>';
  }
  echo '<li>|</li>';
  
  if(isset($_GET['manage']) && $_GET['manage'] == 'add-book-categories'){
    echo '<li><a class="current" href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-book-categories&manage=add-book-categories' ) ) .'">Add Book Category</a></li>';
  }
  else
  {
    echo '<li><a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-book-categories&manage=add-book-categories' ) ) .'">Add Book Category</a></li>';
  }
  
  echo '</ul>';
  echo '</div>';
  
  wlms_book_categories_manage_page_content();
}

//manage pages html content for the tab
function wlms_book_categories_manage_page_content()
{
  global $wpdb;
  if( isset($_GET['manage']) && ($_GET['manage'] == 'add-book-categories') )
  {
    wlms_book_categories_content();
  }
  else
  {
    wlms_book_categories_list();
  }
}

// savs and update for the tab content
function wlms_book_categories_save()
{
  global $wpdb;
  $name = "";
    
  $name =         trim( $_POST['wlms_book_categories_name'] );
  $errors = array();

  if (strlen($name) == 0)
  {
    array_push( $errors, "book_cat" );
  }


  if( count($errors) == 0 )
  {
    $nonce = $_REQUEST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'submit_books_categories' ) ) 
    {
      exit; 
    }

    // books categories save
    $id = 0;
    if(isset ($_POST['wlms_save_book_categories']))
    {
      $wpdb->query( $wpdb->prepare( 
        "
          INSERT INTO {$wpdb->prefix}wlms_book_categories
          ( name, status )
          VALUES ( %s, %d )
        ", 
        array(
          $_POST['wlms_book_categories_name'], 
          $_POST['wlms_book_cat_status']
        ) 
      ) );
          
      $id = $wpdb->insert_id;
      set_message_key('item_saved', 'saved');
    }
    
    
    //update books categories
    if(isset ($_POST['wlms_update_book_categories']))
    {
      $request_id = 0;
      if ( is_numeric( $_GET['update_id'] ) ) {
        $request_id = absint( $_GET['update_id'] );
        $id         = $request_id;
      }
      
      $wpdb->query(
          $wpdb->prepare(
              "UPDATE {$wpdb->prefix}wlms_book_categories SET name = %s, status = %d WHERE id= %d",
              $_POST['wlms_book_categories_name'], $_POST['wlms_book_cat_status'], $request_id
          )
      );
      
      set_message_key('item_updated', 'updated');
    }
    
    
    wp_redirect( esc_url_raw( add_query_arg( array( 'update_id' => $id  ), admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-book-categories&manage=add-book-categories' ) ) ) ); exit;
  }
  
}


//list html content for the tab
function wlms_book_categories_list()
{
  global $wpdb;
  
  $get_book_categories_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_book_categories ORDER BY id DESC",  '');
  $entries_for_book_categories = $wpdb->get_results( $get_book_categories_sql );
  
  ?>
  <table id="bookCategoriesTable" class="wlmsTablesorter">
      <thead> 
          <tr> 
              <th>SN.</th> 
              <th>NAME</th>
              <th>STATUS</th>
              <th class="last-column">ACTION</th>
          </tr> 
      </thead>
      <tbody>
          <?php 
          $i = 1;
          if( $entries_for_book_categories ){
          foreach( $entries_for_book_categories as $rows ){	
          ?> 
          <tr>
              <td><?php echo $i;?></td>
              <td><?php echo esc_html( $rows->name );?></td>
              <?php if($rows->status == 1){?>
              <td>Active</td>
              <?php } else {?>
               <td>Banned</td>
              <?php }?>
              <td class="wlms-action">
                  <div class="wlms_list_btn">
                      <a class="wlms_list_update" href="<?php echo esc_url_raw( add_query_arg( array( 'update_id' => $rows->id  ), admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-book-categories&manage=add-book-categories' ) ) );?>"><div class="wlms_update_icon_from_list"></div></a>
                  </div>&nbsp;&nbsp;&nbsp;
                  <div class="wlms_list_btn">
                      <a data-id="<?php echo $rows->id;?>" data-name="wlms_book_categories" class="wlms_list_delete" href="#"><div class="wlms_delete_icon_from_list"></div></a>
                  </div>
              </td>
          </tr>
          <?php $i ++;}}else{?>
          <tr>
              <td colspan="4">No data yet</td>
          </tr>
          <?php }?>
      </tbody>
  </table>

  <div class="wlms_delete_popup_main">
      <div class="wlms_delete_popup_header_main">
          <div class="wlms_delete_popup_header">Delete Confirmation</div>
          <div class="wlms_delete_popup_close_icon"><button type="button">x</button></div>
      </div>
      <div class="wlms_delete_popup_content">Are you sure to delete this information ?</div>
      <div class="wlms_delete_popup_footer">
          <div class="wlms_btn_delete_popup"><button class="button-primary wlms-delete-btn" type="button">Delete</button></div>
          &nbsp;&nbsp;<div class="wlms_btn_delete_popup"><button class="button-primary wlms_cancel" type="button">Cancel</button></div>
          &nbsp;&nbsp;<div class="wlms_loader_delete_panel"></div>      
      </div>
  </div>
  <div class="wlms_overlay"></div>
  
  <input type="hidden" name="wlms_deleted_str" id="wlms_deleted_str" />
  <input type="hidden" name="wlms_selected_button" id="wlms_selected_button" />
  <?php 
}

//page html content
function wlms_book_categories_content()
{
  $name         = "";
  $form_action  = esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-book-categories&manage=add-book-categories' ) );
  $status       = '';

  if( isset($_GET['manage']) && $_GET['manage'] == 'add-book-categories' && isset($_GET['update_id']) )
  {
    global $wpdb;
    if ( is_numeric( $_GET['update_id'] ) ) {
      $request_id = absint( $_GET['update_id'] );
    }
      
    $get_book_categories_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_book_categories WHERE id = %d ", $request_id );
    $entries_for_book_categories = $wpdb->get_row( $get_book_categories_sql );
    
    $name         = $entries_for_book_categories->name;
    $status       = $entries_for_book_categories->status;
    
    $form_action  = esc_url_raw( add_query_arg( array( 'update_id' => $request_id  ), admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-book-categories&manage=add-book-categories' ) ) );
  }
 
  ?>
  <?php echo get_message_by_event();?>
  <div class="wlms_details_box_main">
    <div class="wlms_box_header">
      <span>&#10095; Please Enter Book Category Details</span>
    </div>
    <form id="book_categories_form" action="<?php echo $form_action;?>" method="post" enctype="multipart/form-data" >
      <div class="wlms_box_content">
        <table>
          <tbody>
            <tr>
              <th><label>categories name:</label></th>
              <td><input class="wlms-regular-text" type="text" placeholder="Enter Category Name" name="wlms_book_categories_name" id="wlms_book_categories_name" value="<?php echo esc_html( $name );?>"></td>
            </tr>
            <tr>
              <th><label>status:</label></th>
              <td>
                  <select class="wlms-regular-select" name="wlms_book_cat_status" id="wlms_book_cat_status">
                    <?php if($status == 1){?>
                    <option selected="selected" value="1">Active</option>
                    <?php }else{?>
                    <option value="1">Active</option>
                    <?php } if($status == 0){?>
                    <option selected="selected" value="0">Banned</option>
                    <?php }else{?>
                    <option value="0">Banned</option>
                    <?php }?>
                  </select>
              </td>
            </tr>
            <tr>
              <td colspan="2" class="submit-form-data">
                  <?php if( isset($_GET['manage']) && $_GET['manage'] == 'add-book-categories' && isset($_GET['update_id']) ) {?>
                  <input type="submit" class="button-primary" name="wlms_update_book_categories" id="wlms_update_book_categories" value="Update">
                  <?php }else{?>
                  <input type="submit" class="button-primary" name="wlms_save_book_categories" id="wlms_save_book_categories" value="Save">
                  <?php }?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <?php wp_nonce_field( 'submit_books_categories' ); ?>
    </form>
  </div>
  <?php 
}
?>