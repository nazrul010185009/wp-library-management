<?php 

// Each books details here 
global $wpdb;
$cover_img_url = WLMS_PLUGIN_URL.'/assets/images/no-cover.jpg';

$request_id = 0;
if ( isset( $_GET['book_id'] ) && is_numeric( $_GET['book_id'] ) ) {
  $request_id = absint( $_GET['book_id'] );
}
      
$get_book_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_books_list WHERE id = %d ", $request_id );
$entries_for_books = $wpdb->get_row( $get_book_sql );
        
$total             = $wpdb->get_var( "SELECT count({$wpdb->prefix}wlms_borrow_details.book_id) FROM {$wpdb->prefix}wlms_borrow_details WHERE book_id = '". $_GET['book_id'] ."' AND borrow_status = 'pending'" );


$get_book_cat_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_book_categories WHERE id = %d ", $entries_for_books->category );
$get_books_cat = $wpdb->get_row( $get_book_cat_sql );

if($entries_for_books->cover_image_url)
{
  $cover_img_url = $entries_for_books->cover_image_url;
}
?>
<div class="book-details-content">
  <div class="book-details-left-side">
    <div class="cover-image"><img src="<?php echo $cover_img_url;?>" alt="cover image"></div>
    <div class="cover-title"><h3><?php echo esc_attr( $entries_for_books->book_title );?></h3></div>
    <div class="book-author-name"><strong>By - </strong><h4><?php echo esc_attr( $entries_for_books->author );?></h4></div>
    <div class="available-copies"><h4>Available Copies -<?php echo number_format($entries_for_books->book_copies - $total);?></h4></div>
  </div>
  <div class="book-details-right-side">
    <div class="books-details-sub-content">
      
      <div class="details-main">
        <div class="details-top">ISBN</div>
        <div class="details-content"><?php echo esc_attr( $entries_for_books->isbn );?></div>
      </div>
      
      <div class="details-main">
        <div class="details-top">Category</div>
        <div class="details-content"><?php echo esc_attr( $get_books_cat->name );?></div>
      </div>
      
      <div class="details-main">
        <div class="details-top">Edition</div>
        <div class="details-content"><?php echo esc_attr( $entries_for_books->edition );?></div>
      </div>
      
      <div class="details-main">
        <div class="details-top">Edition Year</div>
        <div class="details-content"><?php echo $entries_for_books->edition_year;?></div>
      </div>
      
      <div class="details-main">
        <div class="details-top">Price</div>
        <div class="details-content"><?php echo $entries_for_books->price;?></div>
      </div>
      
      <div class="details-main">
        <div class="details-top">Publication</div>
        <div class="details-content"><?php echo esc_attr( $entries_for_books->book_pub );?></div>
      </div>
      
      <div class="details-main">
        <div class="details-top">Publisher Name</div>
        <div class="details-content"><?php echo esc_attr( $entries_for_books->publisher_name );?></div>
      </div>
      
      <div class="details-main">
        <div class="details-top">Copyright Year</div>
        <div class="details-content"><?php echo $entries_for_books->copyright_year;?></div>
      </div>
      
      <div class="details-main">
        <div class="details-top">Short Note</div>
        <div class="details-content"><?php echo esc_attr( $entries_for_books->short_note );?></div>
      </div>
     
    </div>
  </div>
</div>