<?php 

// dynamic manage message for the members
function wlms_message_manage_page_content()
{
  global $wpdb;
  if( isset( $_GET['tab'] ) && $_GET['tab'] === 'view_message' )
  {
    wlms_inbox();
  }
  elseif( (isset( $_GET['tab'] ) && $_GET['tab'] === 'send_message' && isset( $_GET['post'] ) && $_GET['post'] === 'view') || (isset( $_GET['tab'] ) && $_GET['tab'] === 'send_message' &&  isset( $_GET['post'] ) && $_GET['post'] === 'update' && isset($_GET['update_id'])) )
  {
    wlms_send_message();
  }
  elseif( isset( $_GET['tab'] ) && $_GET['tab'] === 'send_message' &&  isset( $_GET['post'] ) && $_GET['post'] === 'add' && (isset($_POST['wlms_submit_msg']) || isset($_POST['wlms_update_msg'])) )
  {
    $type = "";
    $receiver = "";
    $subject = "";
    $mwssage = "";

    $type =           trim( $_POST['wlms_member_type_for_message'] );
    $receiver =       trim( $_POST['wlms_message_receiver'] );
    $subject =        trim( $_POST['wlms_msg_subject'] );
    $mwssage =        trim( $_POST['msg_editor'] );

    $errors = array();

    if (strlen($subject) == 0)
    {
        array_push( $errors, "*" );
    }
    
    if (strlen($mwssage) == 0)
    {
        array_push( $errors, "*" );
    }

    if( count($errors) == 0 )
    {
      $nonce = $_REQUEST['_wpnonce'];
      if ( ! wp_verify_nonce( $nonce, 'submit_messages' ) ) 
      {
        exit; 
      }
        
      //save message
      if(isset($_POST['wlms_submit_msg']) )
      {
        $id = $wpdb->query( $wpdb->prepare( 
          "
            INSERT INTO {$wpdb->prefix}wlms_msg_details
            ( member_type, receiver, subject, msg_details, msg_date, status )
            VALUES ( %s, %s, %s, %s, %s, %d )
          ", 
          array(
            $type,
            $receiver,
            $subject, 
            htmlentities($mwssage),
            date("Y-m-d H:i:s"),
            $_POST['msg_status']  
          ) 
        ) );
          
        if($id == 1)
        {
          echo '<div class="updated"><p>Your message have been successfully saved</p></div>';
          wlms_send_message();
        }
      }
      
      
      //update message
      if(isset($_POST['wlms_update_msg']))
      {
        $request_id = 0;
        if ( is_numeric( $_GET['update_id'] ) ) {
          $request_id = absint( $_GET['update_id'] );
        }
      
        $wpdb->query(
          $wpdb->prepare(
              "UPDATE {$wpdb->prefix}wlms_msg_details SET member_type = %s, receiver = %s, subject = %s, msg_details = %s, msg_date = %s, status = %d WHERE id= %d",
              $type, $receiver, $subject, htmlentities($mwssage), date("Y-m-d H:i:s"), $_POST['msg_status'], $request_id
          )
        );
        
        echo '<div class="updated"><p>Your message have been successfully updated</p></div>';
        wlms_send_message();
      }
      
    }
    else {wlms_send_message();}
  }
  else
  {
      wlms_inbox();
  }
}

function wlms_inbox()
{
  wlms_menu_active_deactive( 'view_message' );
}

function wlms_send_message()
{
  wlms_menu_active_deactive( 'send_message' );
}

function send_message_content()
{
  global $wpdb, $wp_roles; 
  $roles = $wp_roles->get_names();
  $settings = get_option( 'wlms_settings' );
  
  $editor_args = array(
      'textarea_rows' =>10,
      'media_buttons' => false,
      'teeny' => true,
      'quicktags' =>true
  );
  
  $member_type = "";
  $receiver = "";
  $subject = "";
  $details = "";
  $status = "";
  $form_action  = esc_url_raw( admin_url( 'admin.php?page=wlms_message_manage_page&tab=send_message&post=add' ) );

  if( isset($_GET['tab']) && $_GET['tab'] == 'send_message' && isset($_GET['update_id']) )
  {
    global $wpdb;
    if ( is_numeric( $_GET['update_id'] ) ) {
      $request_id = absint( $_GET['update_id'] );
    }
    
    $get_notification_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_msg_details WHERE id = %d ", $request_id );
    $get_notifications_by_member = $wpdb->get_row( $get_notification_sql );
    
    
    $member_type = $get_notifications_by_member->member_type;
    $receiver    = $get_notifications_by_member->receiver;
    $subject     = $get_notifications_by_member->subject;
    $details     = $get_notifications_by_member->msg_details;
    $status      = $get_notifications_by_member->status;
    
    $form_action  = esc_url_raw( add_query_arg( array( 'update_id' => $request_id  ), admin_url( 'admin.php?page=wlms_message_manage_page&tab=send_message&post=add' ) ) );
  }
  
?>
<br>
  <div class="wlms_details_box_main">
    <div class="wlms_box_header">
      
      <?php if( isset($_GET['tab']) && $_GET['tab'] == 'send_message' && isset($_GET['update_id']) ) {?>
        <span>&#10095; Update Your Message</span>
      <?php } elseif ( isset($_GET['tab']) && $_GET['tab'] == 'send_message' && !isset($_GET['update_id']) ) {?>
        <span>&#10095; Send Your Message</span>
      <?php }?>
      
    </div>
    <form id="message_form" action="<?php echo $form_action;?>" method="post" enctype="multipart/form-data" >
      <div class="wlms_box_content">
        <table>
          <tbody>
            <tr>
                <th><label>member type:</label></th>
                <td>
                    <select class="wlms-regular-select" name="wlms_member_type_for_message" id="wlms_member_type_for_message">
                      <?php if($member_type == -1){?>
                      <option selected="selected" value="-1">All</option>
                      <?php } else {?>
                      <option value="-1">All</option>
                      <?php }?>
                        <?php  if(count($roles)>0){ foreach($roles as $key => $vals){
                          if(in_array($key, $settings['member_roles'])){
                        ?>
                        <?php if($member_type == $key){?>
                        <option selected="selected" value="<?php echo $key;?>"><?php echo $vals;?></option>
                        <?php } else {?>
                        <option value="<?php echo $key;?>"><?php echo $vals;?></option>
                        <?php }?>
                          <?php }}}?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label>receiver:</label></th>
                <td>
                    <select class="wlms-regular-select" name="wlms_message_receiver" id="wlms_message_receiver">
                      <?php if($receiver == -1){?>
                      <option selected="selected" value="-1">All</option>
                      <?php } else {?>
                      <option value="-1">All</option>
                      <?php }?>
                      
                      <?php if(count($settings['member_roles'])>0){
                        foreach($settings['member_roles'] as $role){
                          $args = array(
                           'role' => $role,
                           'orderby' => 'user_nicename',
                           'order' => 'ASC'
                          );
                          $authors = get_users($args);

                          if(count($authors)>0){
                             foreach ($authors as $user) {
                      ?>
                      <?php if($receiver == $user->ID ){?>
                      <option selected="selected" value="<?php echo $user->ID;?>"><?php echo $user->display_name;?></option>
                      <?php } else {?>
                      <option value="<?php echo $user->ID;?>"><?php echo $user->display_name;?></option>
                      <?php }?>
                             <?php }}}}?>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label>subject:</label></th>
                <td><input class="wlms-regular-text" type="text" placeholder="Subject" name="wlms_msg_subject" id="wlms_msg_subject" value="<?php echo $subject;?>"></td>
            </tr>
            <tr>
                <th valign="top"><label>notice or massage:</label></th>
                <td><?php echo wp_editor(html_entity_decode($details), 'msg_editor', $editor_args );?></td>
            </tr>
            <tr>
                <th><label>status:</label></th>
                <td>
                  <select name="msg_status" id="msg_status">
                    <?php if($status == 1){?>
                    <option selected="selected" value="1">Active</option>
                    <?php } else {?>
                    <option value="1">Active</option>
                    <?php }?>
                    
                    <?php if($status == 0){?>
                    <option selected="selected" value="0">Inactive</option>
                    <?php } else {?>
                    <option value="0">Inactive</option>
                    <?php }?>
                    
                  </select>
                </td>
            </tr>
            
            <tr>
              <td colspan="2" style="text-align: right;">
                
                <?php if( isset($_GET['tab']) && $_GET['tab'] == 'send_message' && isset($_GET['update_id']) ) {?>
                  <input class="button-primary" type="submit" name="wlms_update_msg" id="wlms_update_msg" value="Update">
                <?php } elseif ( isset($_GET['tab']) && $_GET['tab'] == 'send_message' && !isset($_GET['update_id']) ) {?>
                  <input class="button-primary" type="submit" name="wlms_submit_msg" id="wlms_submit_msg" value="Send">
                <?php }?>
                 
              </td>
            </tr>
          </tbody>
        </table>
      </div>
       <?php wp_nonce_field( 'submit_messages' ); ?>
    </form>
  </div>
<?php
}

function manage_messages_content()
{
  global $wpdb;
  
  $get_notifications_sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wlms_msg_details ORDER BY id DESC",  '');
  $get_notifications_by_member = $wpdb->get_results( $get_notifications_sql );
  
  ?>
  <br>
  <table id="viewNotificationsTableByUser" class="wlmsTablesorter">
    <thead> 
        <tr> 
            <th>SN.</th>
            <th>RECEIVER</th>
            <th>DATE</th>
            <th>SUBJECT</th>
            <th>DETAILS</th>
            <th>STATUS</th>
            <th>ACTION</th>
        </tr> 
    </thead>
    <tbody>
        <?php 
        $i = 1;

        if( $get_notifications_by_member ){
        foreach( $get_notifications_by_member as $rows ){
          
          $receiver = '';
          if( $rows->receiver == -1)
          {
            $receiver = All;
          }
          else if($rows->receiver != -1)
          {
            $get_user = get_userdata( $rows->receiver ); 
            $receiver = $get_user->data->display_name;
          }
          
        ?> 
        <tr>
            <td><?php echo $i;?></td>
            <td><?php echo esc_attr( $receiver );?></td>
            <td><?php echo $rows->msg_date;?></td>
            <td><?php echo esc_attr( $rows->subject );?></td>
            <td><?php echo esc_attr( $rows->msg_details );?></td>
            <?php if($rows->status == 0){?>
            <td>Inactive</td>
            <?php }?>
            <?php if($rows->status == 1){?>
            <td>Active</td>
            <?php }?>
            <td class="wlms-action">
                <div class="wlms_list_btn">
                    <a class="wlms_list_update" href="<?php echo esc_url_raw( add_query_arg( array( 'update_id' => $rows->id  ), admin_url( 'admin.php?page=wlms_message_manage_page&tab=send_message&post=update' ) ) );?>"><div class="wlms_update_icon_from_list"></div></a>
                </div>&nbsp;&nbsp;&nbsp;
                <div class="wlms_list_btn">
                    <a data-id="<?php echo $rows->id;?>" data-name="wlms_msg_details" class="wlms_list_delete " href=""><div class="wlms_delete_icon_from_list"></div></a>
                </div>
            </td>
        </tr>
        <?php $i ++; }} else{?>
            <tr>
                <td colspan="7">No data yet</td>
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

function wlms_menu_active_deactive( $whichTab )
{
  echo '<div class="wrap">';
  echo '<h2 class="nav-tab-wrapper">';
  if( $whichTab == 'view_message' )
  {
    echo '<a href="?page=wlms_message_manage_page&tab=view_message" title="Inbox" class="nav-tab nav-tab-active">Manage Messages</a>';	
  }
  else
  {
    echo '<a href="?page=wlms_message_manage_page&tab=view_message" title="Inbox" class="nav-tab">Manage Messages</a>';
  }

  if( $whichTab == 'send_message' )
  {
    echo '<a href="?page=wlms_message_manage_page&tab=send_message&post=view" title="Send Message" class="nav-tab nav-tab-active">Send Message</a>';	
  }
  else
  {
    echo '<a href="?page=wlms_message_manage_page&tab=send_message&post=view" title="Send Message" class="nav-tab">Send Message</a>';
  }
  echo '</h2>';
  
  if( $whichTab == 'send_message' )
  {
    send_message_content();
  }
  
  elseif( $whichTab == 'view_message' )
  {
    manage_messages_content();
  }
  
  echo '</div>';
}	
?>