<?php

//manage menu pages inside the tab
function wlms_member_issues_settings_page_content()
{
  echo '<div class="pages-content-top">';
  echo '<ul>';
  if(isset($_GET['manage']) && $_GET['manage'] == 'member-issues-settings-list'){
    echo '<li><a class="current" href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=member-issues-settings-list' ) ) .'">Issues List</a></li>';
  }
  else
  {
    echo '<li><a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=member-issues-settings-list' ) ) .'">Issues List</a></li>';
  }
  echo '<li>|</li>';
  
  if(isset($_GET['manage']) && $_GET['manage'] == 'add-member-issues'){
    echo '<li><a class="current" href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=add-member-issues' ) ).'">Add Member Issues</a></li>';
  }
  else
  {
    echo '<li><a href="'. esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=add-member-issues' ) ) .'">Add Member Issues</a></li>';
  }
  
  echo '</ul>';
  echo '</div>';
  
  wlms_member_issues_settings_content();
}


//manage pages html content for the tab
function wlms_member_issues_settings_content()
{
  global $wpdb;
  if( isset($_GET['manage']) && ($_GET['manage'] == 'add-member-issues') )
  {
    wlms_member_issues_settings_content_html();
  }
  else
  {
    wlms_member_issues_settings_list();
  }
}


// savs and update for the tab content
function wlms_member_issues_settings_save()
{
  global $wpdb;
  $member_type = "";
  $returned_limit = "";
  $limit_borrow = "";
  $fine = "";
    
  $member_type =          $_POST['member_roles'];
  $returned_limit =       trim( $_POST['allow_returned_days'] );
  $limit_borrow =         trim( $_POST['allow_books_borrow'] );
  $fine =                 trim( $_POST['fine_per_day'] );
  
  $errors = array();

  if ($member_type == -1)
  {
      array_push( $errors, "*" );
  }
  
  if (strlen($returned_limit) == 0)
  {
      array_push( $errors, "*" );
  }
  
  if (strlen($limit_borrow) == 0)
  {
      array_push( $errors, "*" );
  }
  
  if (strlen($fine) == 0)
  {
      array_push( $errors, "*" );
  }

  if( count($errors) == 0 )
  {
    $nonce = $_REQUEST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'submit_members_issues' ) ) 
    {
      exit; 
    }
    
    $get_issues_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_issues_settings WHERE member_role = %s ", $member_type );
    $issues = $wpdb->get_row( $get_issues_sql, ARRAY_A );
    
    
    if((count($issues) == 0) || ( count($issues) >0 && $issues['id'] == $_GET['update_id'] ))
    {
      
      //save issues
      $id = 0;
      if(isset ($_POST['wlms_save_member_issues']))
      {
        $wpdb->query( $wpdb->prepare( 
          "
            INSERT INTO {$wpdb->prefix}wlms_member_issues_settings
            ( member_role, returned_days, books_borrow, fine )
            VALUES ( %s, %d, %d, %d )
          ", 
          array(
            $member_type, 
            $returned_limit,
            $limit_borrow,
            $fine  
          ) 
        ) );

        $id = $wpdb->insert_id;
        set_message_key('item_saved', 'saved');
      }

      //update issues
      if(isset ($_POST['wlms_update_member_issues']))
      {
        $request_id = 0;
        if ( is_numeric( $_GET['update_id'] ) ) {
          $request_id = absint( $_GET['update_id'] );
          $id         = $request_id;
        }

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->prefix}wlms_member_issues_settings SET member_role = %s, returned_days = %d, books_borrow = %d, fine = %d WHERE id= %d",
                $member_type, $returned_limit, $limit_borrow, $fine, $request_id
            )
        );

        set_message_key('item_updated', 'updated');
      }
      
      wp_redirect( esc_url_raw( add_query_arg( array( 'update_id' => $id  ), admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=add-member-issues' ) ) ) ); exit;
    }
    else
    {
      set_message_key('issues_already_created', 'created');
      wp_redirect( esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=add-member-issues' ) ) ); exit;
    }
  }
  
}


//list html content for the tab
function wlms_member_issues_settings_list()
{
  global $wpdb, $wp_roles;
  
  $get_issues_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_issues_settings ORDER BY id DESC",  '');
  $entries = $wpdb->get_results( $get_issues_sql );
  
  $settings = get_option( 'wlms_settings' );
  ?>
  
  <table id="memberIssues" class="wlmsTablesorter">
      <thead> 
          <tr> 
              <th>MEMBER TYPE</th> 
              <th>ALLOW RETURNED DAYS</th>
              <th>ALLOW BOOKS BORROW</th>
              <th>FINE</th>
              <th class="last-column">ACTION</th>
          </tr> 
      </thead>
      <tbody>
          <?php 
          $i = 1;
          if( $entries ){
          foreach( $entries as $rows ){	
          ?> 
          <tr>
              <td><?php echo esc_html( $wp_roles->roles[$rows->member_role]['name'] ); ?></td>
              <td><?php echo $rows->returned_days;?></td>
              <td><?php echo $rows->books_borrow;?></td>
              <td><?php echo get_currency_symbol($settings['currency']).$rows->fine;?></td>
              <td class="wlms-action">
                  <div class="wlms_list_btn">
                      <a class="wlms_list_update" href="<?php echo esc_url_raw( add_query_arg( array( 'update_id' => $rows->id  ), admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=add-member-issues' ) ) );?>"><div class="wlms_update_icon_from_list"></div></a>
                  </div>&nbsp;&nbsp;&nbsp;
                  <div class="wlms_list_btn">
                      <a data-id="<?php echo $rows->id;?>" data-name="wlms_member_issues_settings" class="wlms_list_delete " href="#"><div class="wlms_delete_icon_from_list"></div></a>
                  </div>
              </td>
          </tr>
          <?php $i ++;}}else{?>
          <tr>
              <td colspan="5">No data yet</td>
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
function wlms_member_issues_settings_content_html()
{
  global $wpdb, $wp_roles;
  
  $member_type = "";
  $returned_limit = "";
  $limit_borrow = "";
  $fine = "";
  $form_action  = esc_url_raw( admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=add-member-issues' ) );
  

  if( isset($_GET['manage']) && ($_GET['manage'] == 'add-member-issues') && isset($_GET['update_id']) )
  {
    if ( is_numeric( $_GET['update_id'] ) ) {
      $request_id = absint( $_GET['update_id'] );
    }
      
    $get_issues_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_member_issues_settings WHERE id = %d ", $request_id );
    $entries = $wpdb->get_row( $get_issues_sql );
    
    $member_type = $entries->member_role;
    $returned_limit = $entries->returned_days;
    $limit_borrow = $entries->books_borrow;
    $fine = $entries->fine;
    
    $form_action  = esc_url_raw( add_query_arg( array( 'update_id' => $request_id  ), admin_url( 'admin.php?page=wlms-pages&wlms-tab=wlms-member-issues-settings&manage=add-member-issues' ) ) );
  }
  
  $settings = get_option( 'wlms_settings' );

  ?>
  <?php echo get_message_by_event();?>
  <div class="wlms_details_box_main">
    <div class="wlms_box_header">
      <span>&#10095; Please Enter Issues Details</span>
    </div>
    <form id="member_issues_form" action="<?php echo $form_action;?>" method="post" enctype="multipart/form-data" >
      <div class="wlms_box_content">
        <table>
          <tbody>
            <tr>
              <th><label>member type:</label></th>
              <td>
                <select name="member_roles" id="member_roles">
                  <option value="-1">Select Member Type</option>
                <?php if(count($settings['member_roles'])>0){ foreach($settings['member_roles'] as $roles){?>
                  <?php if($member_type == $roles){?>
                  <option selected="selected" value="<?php echo $roles;?>"><?php echo $wp_roles->roles[$roles]['name']?></option>
                  <?php } else {?>
                  <option value="<?php echo $roles;?>"><?php echo $wp_roles->roles[$roles]['name']?></option>
                  <?php }?>
                <?php }}?>
                </select>
              </td>
            </tr>
            <tr>
              <th><label>allow maximum returned days:</label></th>
              <td>
                <input type="number" name="allow_returned_days" id="allow_returned_days" value="<?php echo $returned_limit;?>">
              </td>
            </tr>
            <tr>
              <th><label>allow maximum books borrow:</label></th>
              <td>
                <input type="number" name="allow_books_borrow" id="allow_books_borrow" value="<?php echo $limit_borrow;?>">
              </td>
            </tr>
            <tr>
              <th><label>fine/penalty per day :</label></th>
              <td>
                <?php echo get_currency_symbol($settings['currency']);?><input type="number" name="fine_per_day" id="fine_per_day" value="<?php echo $fine;?>">
              </td>
            </tr>
            <tr>
              <td colspan="2" class="submit-form-data">
                  <?php if( isset($_GET['manage']) && ($_GET['manage'] == 'add-member-issues') && isset($_GET['update_id']) ) {?>
                  <input type="submit" class="button-primary" name="wlms_update_member_issues" id="wlms_update_member_issues" value="Update">
                  <?php }else{?>
                  <input type="submit" class="button-primary" name="wlms_save_member_issues" id="wlms_save_member_issues" value="Save">
                  <?php }?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <?php wp_nonce_field( 'submit_members_issues' ); ?>
    </form>
  </div>
  <?php 
}
?>