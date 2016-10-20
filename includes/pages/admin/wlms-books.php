<?php 

//manage menu pages inside the tab
function wlms_manage_books_page_content()
{
  echo '<div class="pages-content-top">';
  echo '<ul>';
  if(isset($_GET['manage']) && $_GET['manage'] == 'manage-books-lists'){
    echo '<li><a class="current" href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=manage-books-lists' ) ) .'">Books List</a></li>';
  }
  else
  {
    echo '<li><a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=manage-books-lists' ) ) .'">Books List</a></li>';
  }
  echo '<li>|</li>';
  
  if(isset($_GET['manage']) && $_GET['manage'] == 'add-books'){
    echo '<li><a class="current" href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-books' ) ) .'">Add New Book</a></li>';
  }
  else
  {
    echo '<li><a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-books' ) ) .'">Add New Book</a></li>';
  }
  
  echo '<li>|</li>';
  
  if(isset($_GET['manage']) && $_GET['manage'] == 'add-bulk-books'){
    echo '<li><a class="current" href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-bulk-books' ) ) .'">Add Bulk Books</a></li>';
  }
  else
  {
    echo '<li><a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-bulk-books' ) ) .'">Add Bulk Books</a></li>';
  }
  
  echo '</ul>';
  echo '</div>';
  
  wlms_books_manage_page_content();
}

//manage pages html content for the tab
function wlms_books_manage_page_content()
{
  global $wpdb;
  if( isset($_GET['manage']) && ($_GET['manage'] == 'add-books') )
  {
    wlms_books_content();
  }
  elseif( isset($_GET['manage']) && ($_GET['manage'] == 'add-bulk-books'))
  {
    wlms_bulk_books_content();
  }   
  else
  {
     wlms_books_list(); 
  }
}


// savs and update for the tab content
function wlms_books_save()
{
  global $wpdb;
  $title  = "";
  $cat    = "";
  $author = "";
  $copies = "";
  $status = "";

  $title =                  trim( $_POST['wlms_book_title'] );
  $cat =                    trim( $_POST['wlms_category'] );
  $author =                 trim( $_POST['wlms_book_author'] );
  $edition =                trim( $_POST['wlms_book_edition'] );
  $store_copy =             trim( $_POST['wlms_store_type'] );
  $price =                  trim( $_POST['wlms_book_price'] );
  $location =               trim( $_POST['wlms_book_location'] );
  $copies =                 trim( $_POST['wlms_book_copies'] );
  $isbn =                   trim( $_POST['wlms_Isbn'] );
  $edition_year =           trim( $_POST['wlms_book_edition_year'] );
  $copy_right_year =        trim( $_POST['wlms_copyright_year'] );
  $status =                 trim( $_POST['wlms_book_status'] );

  $errors = array();

  if (strlen($title) == 0)
  {
      array_push( $errors, "*" );
  }

  if ($cat == -1)
  {
      array_push( $errors, "*" );
  }

  if (strlen($author) == 0)
  {
      array_push( $errors, "*" );
  }
  
  if (strlen($edition) == 0)
  {
      array_push( $errors, "*" );
  }
  
  if (strlen($store_copy) == 0)
  {
      array_push( $errors, "*" );
  }
  
  if (strlen($price) == 0)
  {
      array_push( $errors, "*" );
  }
  
  if (strlen($location) == 0)
  {
      array_push( $errors, "*" );
  }
 
  if (strlen($copies) == 0)
  {
      array_push( $errors, "*" );
  }
  
  if (strlen($isbn) == 0)
  {
      array_push( $errors, "*" );
  }
  
  if (strlen($status) == 0)
  {
      array_push( $errors, "*" );
  }

  $date = new DateTime();
  $ts = $date->format("Y-m-d");
  
  $edition_date = $date->format("Y");
  $copy_right_date = $date->format("Y");
  
  if (strlen($edition_year) != 0)
  {
    $edition_date = $_POST['wlms_book_edition_year'];
  }
  
  if (strlen($copy_right_year) != 0)
  {
    $copy_right_date = $_POST['wlms_copyright_year'];
  }
  
  if( count($errors) == 0 )
  {
    $nonce = $_REQUEST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'submit_books' ) ) 
    {
      exit; 
    }
    
    
    // books save
    $id = 0;
    if(isset ($_POST['wlms_save_book']))
    {
      $wpdb->query( $wpdb->prepare( 
        "
          INSERT INTO {$wpdb->prefix}wlms_books_list
          ( book_title, category, author, edition, edition_year, copy_type, price, location, book_copies, book_pub, publisher_name, isbn, copyright_year, date_added, status, short_note, cover_image_url)
          VALUES ( %s, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )
        ", 
        array(
          $_POST['wlms_book_title'],
          $_POST['wlms_category'],
          $_POST['wlms_book_author'],
          $_POST['wlms_book_edition'],
          $edition_date,
          $_POST['wlms_store_type'],
          $_POST['wlms_book_price'], 
          $_POST['wlms_book_location'], 
          $_POST['wlms_book_copies'],
          $_POST['wlms_book_publication'], 
          $_POST['wlms_publisher_name'],  
          $_POST['wlms_Isbn'],  
          $copy_right_date,  
          $ts, 
          $_POST['wlms_book_status'], 
          $_POST['short_note'],
          $_POST['wlms_cover_image_url']  
        ) 
      ) );
          
      
      $id = $wpdb->insert_id;
      set_message_key('item_saved', 'saved');
    }
    
    
    //update books
    if(isset ($_POST['wlms_update_book']))
    {
      $request_id = 0;
      if ( is_numeric( $_GET['update_id'] ) ) {
        $request_id = absint( $_GET['update_id'] );
        $id         = $request_id;
      }
      
      $wpdb->query(
          $wpdb->prepare(
              "UPDATE {$wpdb->prefix}wlms_books_list SET book_title = %s, category = %d, author = %s, edition = %s, edition_year = %s, copy_type = %s, price = %s, location = %s, book_copies = %s, book_pub = %s, publisher_name = %s, isbn = %s, copyright_year = %s, status = %s, short_note = %s, cover_image_url = %s WHERE id= %d",
              $_POST['wlms_book_title'],
              $_POST['wlms_category'],
              $_POST['wlms_book_author'],
              $_POST['wlms_book_edition'],
              $edition_date,
              $_POST['wlms_store_type'],
              $_POST['wlms_book_price'], 
              $_POST['wlms_book_location'], 
              $_POST['wlms_book_copies'],
              $_POST['wlms_book_publication'], 
              $_POST['wlms_publisher_name'],  
              $_POST['wlms_Isbn'],  
              $copy_right_date,
              $_POST['wlms_book_status'], 
              $_POST['short_note'],
              $_POST['wlms_cover_image_url'], 
              $request_id
          )
      );
              
      set_message_key('item_updated', 'updated');
    }
    
    wp_redirect( esc_url_raw( add_query_arg( array( 'update_id' => $id  ), admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-books' ) ) ) ); exit;
  }
}


//bulk books save
function wlms_bulk_books_save()
{
  global $wpdb;
  $FileType = pathinfo($_FILES["booksFileToUpload"]["name"], PATHINFO_EXTENSION);

  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'submit_bulk_books' ) ) 
  {
    exit; 
  }
    
  if( $FileType == 'csv' )
  {
    if( $_FILES["booksFileToUpload"]["size"] > 0 )
    {
        $header_fields = array('BookTitle', 'Author', 'Edition', 'EditionYear', 'Price', 'Location', 'BookCopies', 'BookPublication', 'PublisherName', 'Isbn', 'CopyrightYear', 'Status', 'ShortNote');

        $file = fopen($_FILES["booksFileToUpload"]["tmp_name"], "r");
        $csv_fields = fgetcsv($file, 10000, ",", '"');

        if( serialize($header_fields) == serialize($csv_fields) && count($csv_fields)>0 )
        {
          $rowCount = 1;
          while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
          {
            $rowCount++;

            $date = new DateTime();
            $nts = $date->format("Y-m-d");

            if($rowCount>1)				
            {
              $wpdb->query( $wpdb->prepare( 
                "
                  INSERT INTO {$wpdb->prefix}wlms_books_list
                  ( book_title, author, edition, edition_year, price, location, book_copies, book_pub, publisher_name, isbn, copyright_year, date_added, status, short_note)
                  VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )
                ", 
                array(
                  $emapData[0],
                  $emapData[1],
                  $emapData[2],
                  $emapData[3],
                  $emapData[4],
                  $emapData[5],
                  $emapData[6],
                  $emapData[7],
                  $emapData[8],
                  $emapData[9],
                  $emapData[10],
                  $nts,
                  $emapData[11],
                  $emapData[12]
                ) 
              ) );
            }
          }
          
          wp_redirect( esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=manage-books-lists' ) ) ); exit;
        }
        else
        {
          set_message_key('check_csv_file_data_format', 'not_correct_format');
          wp_redirect( esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-bulk-books' ) ) ); exit;
        }
        fclose($file);
    }
  }
  else
  {
    set_message_key('check_csv_file', 'not_csv');
    wp_redirect( esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-bulk-books' ) ) ); exit;
  }
}

//list html content for the tab
function wlms_books_list()
{
  global $wpdb;
  
  $get_books_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_books_list ORDER BY id DESC",  '');
  $entries_for_books = $wpdb->get_results( $get_books_sql );
  
  $settings = get_option( 'wlms_settings' );
  ?>
  <table id="booksTable" class="wlmsTablesorter">
      <thead> 
          <tr> 
              <th>SN.</th> 
              <th>BOOK TITLE</th> 
              <th>CATEGORY</th> 
              <th>AUTHOR</th> 
              <th>PUBLISHER NAME</th>
              <th>ISBN</th>
              <th>COPYRIGHT YEAR</th>
              <th>COPIES</th>
              <th>PRICE</th>
              <th>DATE ADDED</th>
              <th>STATUS</th>
              <th class="last-column">ACTION</th>
          </tr> 
      </thead>
      <tbody>
          <?php if( $entries_for_books ){
          $i = 1;    
          foreach( $entries_for_books as $rows ){
              $get_book_categories_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_book_categories WHERE id = %d ", $rows->category );
              $get_books_cat = $wpdb->get_row( $get_book_categories_sql );
          ?> 
          <tr>
              <td><?php echo $i;?></td>
              <td><?php echo esc_html( $rows->book_title );?></td>
              <td><?php echo esc_html( $get_books_cat->name );?></td>
              <td><?php echo esc_html( $rows->author );?></td>
              <td><?php echo esc_html( $rows->publisher_name );?></td>
              <td><?php echo esc_html( $rows->isbn );?></td>
              <td><?php echo esc_html( $rows->copyright_year );?></td>
              <td><?php echo esc_html( $rows->book_copies );?></td>
              <td><?php echo get_currency_symbol($settings['currency']).$rows->price;?></td>
              <td><?php echo $rows->date_added;?></td>
              <td><?php echo esc_html( $rows->status );?></td>
              <td class="wlms-action">
                  <div class="wlms_list_btn">
                      <a class="wlms_list_update" href="<?php echo esc_url_raw( add_query_arg( array( 'update_id' => $rows->id  ), admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-books' ) ) );?>"><div class="wlms_update_icon_from_list"></div></a>
                  </div>&nbsp;&nbsp;&nbsp;
                  <div class="wlms_list_btn">
                      <a data-id="<?php echo $rows->id;?>" data-name="wlms_books_list" class="wlms_list_delete " href=""><div class="wlms_delete_icon_from_list"></div></a>
                  </div>
              </td>
          </tr>
          <?php $i++;}}else{?>
              <tr>
                  <td colspan="12">No data yet</td>
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
function wlms_books_content()
{
  global $wpdb;
  $book_title = '';
  $cat_name = "";
  $author = '';
  $edition = '';
  $edition_year = '';
  $copy_type = '';
  $price = '';
  $location = '';
  $book_copies = '';
  $book_pub = '';
  $publisher_name = '';
  $isbn = '';
  $copyright_year = '';
  $status = '';
  $short_note = '';
  $cover_image_url = '';
  $form_action  = esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-books' ) );

  if( isset($_GET['manage']) && $_GET['manage'] == 'add-books' && isset($_GET['update_id']) )
  {
    if ( is_numeric( $_GET['update_id'] ) ) {
      $request_id = absint( $_GET['update_id'] );
    }
    
    
    $get_books_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_books_list WHERE id = %d ", $request_id );
    $entries_for_book = $wpdb->get_row( $get_books_sql );
    
    $book_title = $entries_for_book->book_title;
    $cat_name = $entries_for_book->category;
    $author = $entries_for_book->author;
    $edition = $entries_for_book->edition;
    $edition_year = $entries_for_book->edition_year;
    $copy_type = $entries_for_book->copy_type;
    $price = $entries_for_book->price;
    $location = $entries_for_book->location;
    $book_copies = $entries_for_book->book_copies;
    $book_pub = $entries_for_book->book_pub;
    $publisher_name = $entries_for_book->publisher_name;
    $isbn = $entries_for_book->isbn;
    $copyright_year = $entries_for_book->copyright_year;
    $status = $entries_for_book->status;
    $short_note = $entries_for_book->short_note;
    $cover_image_url = $entries_for_book->cover_image_url;
    
    $form_action  = esc_url_raw( add_query_arg( array( 'update_id' => $request_id  ), admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-books' ) ) );
  }

  
  $get_book_categories_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_book_categories WHERE status = %d ", 1 );
  $entries_for_cat = $wpdb->get_results( $get_book_categories_sql );
    
  wp_enqueue_media();
  ?>
  <style type="text/css">
    .ui-datepicker-calendar 
    {
      display: none;
    }
  </style>
  
  <?php echo get_message_by_event();?>
  <div class="wlms_details_box_main">
      <div class="wlms_box_header">
        <span>&#10095; Please Enter Book Details</span>
      </div>
    <form id="books_form" action="<?php echo $form_action;?>" method="post" enctype="multipart/form-data" >
          <div class="wlms_box_content">
              <table>
                  <tbody>
                    <tr>
                      <th><label>book title:</label></th>
                      <td><input class="wlms-regular-text" type="text" placeholder="book title" name="wlms_book_title" id="wlms_book_title" value="<?php echo esc_html( $book_title );?>"></td>
                    </tr>
                    <tr>
                      <th><label>category:</label></th>
                      <td>
                          <select class="wlms-regular-select" name="wlms_category" id="wlms_category">
                              <option value="-1">select category</option>
                              <?php if(count($entries_for_cat)>0){
                              foreach($entries_for_cat as $Data){
                                if($Data->id == $cat_name){
                              ?>
                              <option selected="selected" value="<?php echo $Data->id;?>"><?php echo $Data->name;?></option>
                                <?php }else{?>
                              <option value="<?php echo $Data->id;?>"><?php echo $Data->name;?></option>
                                <?php }}}?>
                          </select>
                      </td>
                    </tr>
                    <tr>
                      <th><label>author:</label></th>
                      <td><input class="wlms-regular-text" type="text" placeholder="author name" name="wlms_book_author" id="wlms_book_author" value="<?php echo esc_html( $author );?>"></td>
                    </tr>
                    <tr>
                      <th><label>edition:</label></th>
                      <td><input class="wlms-regular-text" type="text" placeholder="edition" name="wlms_book_edition" id="wlms_book_edition" value="<?php echo esc_html( $edition );?>"></td>
                    </tr>
                    <tr>
                      <th><label>edition year:</label></th>
                      <td><input style="width:92%;" class="wlms-regular-text" type="text" placeholder="edition year" name="wlms_book_edition_year" id="wlms_book_edition_year" value="<?php echo $edition_year;?>"></td>
                    </tr>
                    <tr>
                      <th><label>price:</label></th>
                      <td><input class="wlms-regular-text" type="number" placeholder="price" name="wlms_book_price" id="wlms_book_price" value="<?php echo $price;?>"></td>
                    </tr>
                     <tr>
                      <th><label>location:</label></th>
                      <td><input class="wlms-regular-text" type="text" placeholder="location" name="wlms_book_location" id="wlms_book_location" value="<?php echo esc_html( $location );?>"></td>
                    </tr>
                    <tr>
                      <th><label>book copies:</label></th>
                      <td><input class="wlms-regular-text" type="number" placeholder="amount of book" name="wlms_book_copies" id="wlms_book_copies" value="<?php echo esc_html( $book_copies );?>"></td>
                    </tr>
                    <tr>
                      <th><label>book publication:</label></th>
                      <td><input class="wlms-regular-text" type="text" placeholder="book publication name" name="wlms_book_publication" id="wlms_book_publication" value="<?php echo esc_html( $book_pub );?>"></td>
                    </tr>
                    <tr>
                      <th><label>publisher name:</label></th>
                      <td><input class="wlms-regular-text" type="text" placeholder="publisher name" name="wlms_publisher_name" id="wlms_publisher_name" value="<?php echo esc_html( $publisher_name );?>"></td>
                    </tr>
                    <tr>
                      <th><label>Isbn:</label></th>
                      <td><input class="wlms-regular-text" type="text" placeholder="ISBN" name="wlms_Isbn" id="wlms_Isbn" value="<?php echo esc_html( $isbn );?>"></td>
                    </tr>
                    <tr>
                      <th><label>copyright year:</label></th>
                      <td><input style="width:92%;" class="wlms-regular-text" type="text" placeholder="copyright year" name="wlms_copyright_year" id="wlms_copyright_year" value="<?php echo $copyright_year;?>"></td>
                    </tr>
                    <tr>
                      <th><label>status:</label></th>
                      <td>
                          <select class="wlms-regular-select" name="wlms_book_status" id="wlms_book_status">
                              <?php if($status == 'New'){?>
                              <option selected="selected" value="New">New</option>
                              <?php }else{?>
                              <option value="New">New</option>
                              <?php } if($status == 'Old'){?>
                              <option selected="selected" value="Old">Old</option>
                              <?php }else{?>
                              <option value="Old">Old</option>
                              <?php } if($status == 'Lost'){?>
                              <option selected="selected" value="Lost">Lost</option>
                              <?php }else{?>
                              <option value="Lost">Lost</option>
                              <?php }if($status == 'Damage'){?>
                              <option selected="selected" value="Damage">Damage</option>
                              <?php }else{?>
                              <option value="Damage">Damage</option>
                              <?php }?>
                          </select>
                      </td>
                    </tr>
                    <tr>
                      <th><label>cover image:</label></th> 
                      <td class="wlms-logo-image">
                          <div><input class="wlms-regular-text" type="text" readonly="true" name="wlms_cover_image_url" id="wlms_cover_image_url" value="<?php echo $cover_image_url;?>"/></div><br>
                          <div><span><input id="wlms_cover_image_uploader" class="button-primary" name="wlms_cover_image_uploader" type="button" value="Browse"/></span><?php if($cover_image_url){?>&nbsp;&nbsp;&nbsp;<span><img src="<?php echo $cover_image_url;?>"></span><?php }?></div>
                      </td>
                    </tr>
                    <tr>
                      <th><label>book store type:</label></th>
                      <td>
                          <select class="wlms-regular-select" name="wlms_store_type" id="wlms_store_type">
                              <?php if($copy_type == 'physical'){?>
                              <option selected="selected" value="physical">Hard Copy</option>
                              <?php } else {?>
                              <option value="physical">Hard Copy</option>
                              <?php }?>
                              
                              <?php if($copy_type == 'soft'){?>
                              <option selected="selected" value="soft">Soft Copy</option>
                              <?php } else {?>
                              <option value="soft">Soft Copy</option>
                              <?php }?>
                              
                              <?php if($copy_type == 'others'){?>
                              <option selected="selected" value="others">Others</option>
                              <?php } else {?>
                              <option value="others">Others</option>
                              <?php }?>
                              
                              
                          </select>
                      </td>
                    </tr>                                    
                    <tr>
                      <th><label>short note:</label></th>
                      <td>
                        <?php echo wp_editor( $short_note, 'short_note', array( 'media_buttons' => false ) );?>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" class="submit-form-data">
                          <?php if( isset($_GET['manage']) && $_GET['manage'] == 'add-books' && isset($_GET['update_id']) ){?>
                          <input type="submit" class="button-primary" name="wlms_update_book" id="wlms_update_book" value="Update">
                          <?php }else{?>
                          <input type="submit" class="button-primary" name="wlms_save_book" id="wlms_save_book" value="Save">
                          <?php }?>
                      </td>
                    </tr>
                  </tbody>
                </table>
          </div>
      <?php wp_nonce_field( 'submit_books' ); ?>
      </form>
  </div>
  <?php 
}


//page bulk html content
function wlms_bulk_books_content()
{
  $form_action  = esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-manage-books&manage=add-bulk-books' ) );
?>
  <?php echo get_message_by_event();?>
  <div class="wlms_details_box_main">
      <div class="wlms_box_header">
          <span>&#10095; Add bulk books</span>
      </div>
    <form id="bulk_book_form" action="<?php echo $form_action;?>" method="post" enctype="multipart/form-data" >
          <div class="wlms_box_content">
              <table>
                  <tbody>
                      <tr>
                          <th>Select csv to upload</th>
                          <td>
                              <input type="file" name="booksFileToUpload" id="booksFileToUpload">
                          </td>
                      </tr>
                      <tr>
                          <th></th>
                          <td>
                              <a class="button-primary" download href="<?php echo esc_url_raw( WLMS_PLUGIN_URL.'/includes/bulk-csv/bulk-books-sample.csv' );?>">Download Sample Csv File</a>
                              <input type="submit" class="button-primary" name="wlms_bulk_save_books" id="wlms_bulk_save_books" value="Upload And Import">
                          </td>
                      </tr>
                  </tbody>
              </table>
          </div>
      <?php wp_nonce_field( 'submit_bulk_books' ); ?>
      </form>
  </div>
  <?php 
}
?>