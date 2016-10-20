<?php

// settings data manage here
function wlms_settings_manage_page_content()
{
  if( isset($_GET['post']) && $_GET['post'] === 'wlms_global_settings' && isset($_POST['wlms_setting_save']) )
  {
    $nonce = $_REQUEST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'submit_settings_data' ) ) 
    {
      exit; 
    }
    
    $save_settings = array(
                          'institute_name' =>              $_POST['wlms_institute_name'],
                          'institute_address' =>           $_POST['wlms_institute_address'],
                          'phone_number' =>                $_POST['wlms_institute_phone'],
                          'email' =>                       $_POST['wlms_institute_email'],
                          'currency' =>                    $_POST['inputCurrency'],
                          'terms_and_conditions' =>        $_POST['terms_and_conditions'],
                          'member_roles' =>                $_POST['member_roles_lists'],
                          'logo_url' =>                    $_POST['wlms_logo_url']

    );
    update_option( 'wlms_settings', $save_settings );
    wlms_settings_page_html();
  }
  else
  {
    wlms_settings_page_html();
  } 
}
 
function wlms_settings_page_html()
{
  $settings = get_option( 'wlms_settings' );
 
  wp_enqueue_media();
?>
<div class="wrap">
  <form method="post" action="<?php echo esc_url_raw( admin_url( 'admin.php?page=wlms-manage-settings-page&post=wlms_global_settings' ) );?>" name="global_settings" enctype="multipart/form-data">
      <div class="wlms_details_box_main">
          <div class="wlms_box_header">
            <span>&#10095; Please Enter Settings Details</span>
          </div>
          <div class="wlms_box_content">
              <table>
                  <tr>
                      <th><label>Institute Name:</label></th>
                      <td><input class="wlms-regular-text" type="text" name="wlms_institute_name" id="wlms_institute_name" value="<?php echo $settings['institute_name'];?>"/></td>
                  </tr>
                  <tr>
                      <th><label>Institute Address:</label></th>
                      <td><input class="wlms-regular-text" type="text" name="wlms_institute_address" id="wlms_institute_address" value="<?php echo $settings['institute_address'];?>"/></td>
                  </tr>
                  <tr>
                      <th><label>Institute Phone Number:</label></th>
                      <td><input class="wlms-regular-text" type="text" name="wlms_institute_phone" id="wlms_institute_phone" value="<?php echo $settings['phone_number']; ?>"/></td>
                  </tr>
                  <tr>
                      <th><label>Institute Email:</label></th>
                      <td><input class="wlms-regular-text" type="text" name="wlms_institute_email" id="wlms_institute_email" value="<?php echo $settings['email'];?>"/></td>
                  </tr>
                  
                  <tr>
                    <th><label>Currency:</label></th>
                    <td>
                      <select  name="inputCurrency">
                        <?php if( $settings['currency'] ==  'AED'){?>
                          <option selected="selected" value="AED">United Arab Emirates Dirham (د.إ)</option>
                        <?php }else {?>
                          <option value="AED">United Arab Emirates Dirham (د.إ)</option>
                        <?php }?>
                          
                        <?php if( $settings['currency'] ==  'ARS'){?>                         
                          <option selected="selected" value="ARS">Argentine Peso ($)</option>
                        <?php }else {?>
                          <option value="ARS">Argentine Peso ($)</option>
                        <?php }?>
                        
                        <?php if( $settings['currency'] ==  'AUD'){?>                            
                          <option selected="selected" value="AUD">Australian Dollars ($)</option>
                        <?php }else {?>
                          <option value="AUD">Australian Dollars ($)</option>
                        <?php }?>
                        
                        <?php if( $settings['currency'] ==  'BDT'){?>    
                          <option selected="selected" value="BDT">Bangladeshi Taka (৳ )</option>
                        <?php }else {?>
                          <option value="BDT">Bangladeshi Taka (৳ )</option>
                        <?php }?>

                        <?php if( $settings['currency'] ==  'BRL'){?>                              
                          <option selected="selected" value="BRL">Brazilian Real (R$)</option>

                        <?php }else {?>

                          <option value="BRL">Brazilian Real (R$)</option>

                       <?php }?>

                        <?php if( $settings['currency'] ==  'BGN'){?>   
                        

                          <option selected="selected" value="BGN">Bulgarian Lev (лв.)</option>

                       <?php }else {?>

                          <option value="BGN">Bulgarian Lev (лв.)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'CAD'){?>     
                        

                          <option selected="selected" value="CAD">Canadian Dollars ($)</option>

                        <?php }else {?>

                          <option value="CAD">Canadian Dollars ($)</option>

                         <?php }?>

                        <?php if( $settings['currency'] ==  'CLP'){?>     
                        

                          <option selected="selected" value="CLP">Chilean Peso ($)</option>

                        <?php }else {?>

                          <option value="CLP">Chilean Peso ($)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'CNY'){?>       
                        

                          <option selected="selected" value="CNY">Chinese Yuan (¥)</option>

                        <?php }else {?>

                          <option value="CNY">Chinese Yuan (¥)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'COP'){?>      
                        

                          <option selected="selected" value="COP">Colombian Peso ($)</option>

                        <?php }else {?>

                          <option value="COP">Colombian Peso ($)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'CZK'){?>       
                        

                          <option selected="selected" value="CZK">Czech Koruna (Kč)</option>

                        <?php }else {?>

                          <option value="CZK">Czech Koruna (Kč)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'DKK'){?>         
                        

                          <option selected="selected" value="DKK">Danish Krone (DKK)</option>

                        <?php }else {?>

                          <option value="DKK">Danish Krone (DKK)</option>

                         <?php }?>

                        <?php if( $settings['currency'] ==  'DOP'){?>       
                        

                          <option selected="selected" value="DOP">Dominican Peso (RD$)</option>

                        <?php }else {?>

                          <option value="DOP">Dominican Peso (RD$)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'EUR'){?>        
                        

                          <option selected="selected" value="EUR">Euros (€)</option>

                        <?php }else {?>

                          <option value="EUR">Euros (€)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'HKD'){?>    
                        

                          <option selected="selected" value="HKD">Hong Kong Dollar ($)</option>

                        <?php }else {?>

                          <option value="HKD">Hong Kong Dollar ($)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'HRK'){?>    
                        

                          <option selected="selected" value="HRK">Croatia kuna (Kn)</option>

                        <?php }else {?>

                          <option value="HRK">Croatia kuna (Kn)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'HUF'){?>    
                        

                          <option selected="selected" value="HUF">Hungarian Forint (Ft)</option>

                        <?php }else {?>

                          <option value="HUF">Hungarian Forint (Ft)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'ISK'){?>    

                          <option selected="selected" value="ISK">Icelandic krona (Kr.)</option>

                        <?php }else {?>

                          <option value="ISK">Icelandic krona (Kr.)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'IDR'){?>      

                          <option selected="selected" value="IDR">Indonesia Rupiah (Rp)</option>

                        <?php }else {?>

                          <option value="IDR">Indonesia Rupiah (Rp)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'INR'){?>   

                          <option selected="selected" value="INR">Indian Rupee (Rs.)</option>

                        <?php }else {?>

                          <option value="INR">Indian Rupee (Rs.)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'NPR'){?>    

                          <option selected="selected" value="NPR">Nepali Rupee (Rs.)</option>

                         <?php }else {?>

                          <option value="NPR">Nepali Rupee (Rs.)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'JPY'){?>      

                          <option selected="selected" value="JPY">Japanese Yen (¥)</option>

                        <?php }else {?>

                          <option value="JPY">Japanese Yen (¥)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'KRW'){?>    

                          <option selected="selected" value="KRW">South Korean Won (₩)</option>

                        <?php }else {?>

                          <option value="KRW">South Korean Won (₩)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'MYR'){?>   

                          <option selected="selected" value="MYR">Malaysian Ringgits (RM)</option>

                        <?php }else {?>

                          <option value="MYR">Malaysian Ringgits (RM)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'MXN'){?>  

                          <option selected="selected" value="MXN">Mexican Peso ($)</option>

                        <?php }else {?>

                          <option value="MXN">Mexican Peso ($)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'NGN'){?>    
                        
                          <option selected="selected" value="NGN">Nigerian Naira (₦)</option>

                        <?php }else {?>

                          <option value="NGN">Nigerian Naira (₦)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'NZD'){?>    

                          <option selected="selected" value="NZD">New Zealand Dollar ($)</option>

                        <?php }else {?>

                          <option value="NZD">New Zealand Dollar ($)</option>

                         <?php }?>

                        <?php if( $settings['currency'] ==  'PHP'){?>      

                          <option selected="selected" value="PHP">Philippine Pesos (₱)</option>

                        <?php }else {?>

                          <option value="PHP">Philippine Pesos (₱)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'GBP'){?>  

                          <option selected="selected" value="GBP">Pounds Sterling (£)</option>

                        <?php }else {?>

                          <option value="GBP">Pounds Sterling (£)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'RON'){?>    

                          <option selected="selected" value="RON">Romanian Leu (lei)</option>

                        <?php }else {?>

                          <option value="RON">Romanian Leu (lei)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'RUB'){?>    

                          <option selected="selected" value="RUB">Russian Ruble (руб.)</option>

                        <?php }else {?>

                          <option value="RUB">Russian Ruble (руб.)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'SGD'){?>   

                          <option selected="selected" value="SGD">Singapore Dollar ($)</option>

                        <?php }else {?>

                          <option value="SGD">Singapore Dollar ($)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'ZAR'){?>    

                          <option selected="selected" value="ZAR">South African rand (R)</option>

                        <?php }else {?>

                          <option value="ZAR">South African rand (R)</option>

                         <?php }?>

                        <?php if( $settings['currency'] ==  'SEK'){?>       

                          <option selected="selected" value="SEK">Swedish Krona (kr)</option>

                        <?php }else {?>

                          <option value="SEK">Swedish Krona (kr)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'CHF'){?>  
                        
                          <option selected="selected" value="CHF">Swiss Franc (CHF)</option>

                        <?php }else {?>

                          <option value="CHF">Swiss Franc (CHF)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'TWD'){?>    

                          <option selected="selected" value="TWD">Taiwan New Dollars (NT$)</option>

                        <?php }else {?>

                          <option value="TWD">Taiwan New Dollars (NT$)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'THB'){?>      

                          <option selected="selected" value="THB">Thai Baht (฿)</option>

                        <?php }else {?>

                          <option value="THB">Thai Baht (฿)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'UAH'){?>  

                          <option selected="selected" value="UAH">Ukrainian Hryvnia (₴)</option>

                        <?php }else {?>

                          <option value="UAH">Ukrainian Hryvnia (₴)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'USD'){?>    

                          <option selected="selected" value="USD">US Dollars ($)</option>

                         <?php }else {?>

                          <option value="USD">US Dollars ($)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'VND'){?>      

                          <option selected="selected" value="VND">Vietnamese Dong (₫)</option>

                        <?php }else {?>

                          <option value="VND">Vietnamese Dong (₫)</option>

                        <?php }?>

                        <?php if( $settings['currency'] ==  'EGP'){?>        

                          <option selected="selected" value="EGP">Egyptian Pound (EGP)</option>

                        <?php }else {?>

                          <option value="EGP">Egyptian Pound (EGP)</option>

                         <?php }?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th>Terms and Conditions </th>
                    <td>
                      <?php echo wp_editor( $settings['terms_and_conditions'], 'terms_and_conditions', array( 'media_buttons' => false ) );?>
                    </td>
                  </tr>
                  <tr class="member-roles-lists">
                    <th>Allow Member Roles </th>
                    <td>
                      <ul>
                      <?php  global $wp_roles; $roles = $wp_roles->get_names();
                        if(count($roles)>0){ foreach($roles as $key => $vals){
                      ?>
                        <?php if(in_array($key, $settings['member_roles'])){?>
                        <li><input type="checkbox" checked="checked" name="member_roles_lists[]" id="member_roles_lists" value="<?php echo $key;?>"><?php echo $vals;?></li>
                        <?php } else {?>
                        <li><input type="checkbox" name="member_roles_lists[]" id="member_roles_lists" value="<?php echo $key;?>"><?php echo $vals;?></li>
                        <?php }?>
                        <?php }}?>
                      </ul>  
                    </td>
                  </tr>
                  <tr>
                      <th><label>Upload Logo:</label></th> 
                      <td class="wlms-logo-image">
                        <div><input style="width:100%;" readonly="true" class="wlms-regular-text" type="text" name="wlms_logo_url" id="wlms_logo_url" value="<?php echo $settings['logo_url'];?>"/></div>  <br>
                        <div><span><input id="wlms_logo_uploader" class="button-primary" name="wlms_logo_uploader" type="button" value="Browse"/></span>&nbsp;&nbsp;<span><img src="<?php echo $settings['logo_url'];?>"></span></div>
                      </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="text-align: right;"><input style="margin-right:5px;" class="button button-primary" type="submit" name="wlms_setting_save" id="wlms_setting_save" value="Save Change" /></td>
                  </tr>
              </table>
          </div>    
      </div>
    <?php wp_nonce_field( 'submit_settings_data' ); ?>
  </form>
</div>  
<?php 	
}
?>