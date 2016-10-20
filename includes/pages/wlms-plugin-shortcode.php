<?php 
// manage plugin login shortcode
function wlms_login_details()
{
  if(is_user_logged_in()) 
  {
    return '<a class="user-logout-link" href="'. wp_logout_url( get_permalink() ) .'">Sign out</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="user-dashboard-link" href="'. esc_url_raw( add_query_arg( array('manage' => 'wlms-member-dashboard','page' =>   'available-books',), home_url() ) ).'">User Dashboard</a></tr>';
  }
  elseif (!is_user_logged_in()) 
  {
    return '<a class="user-login-link" href="'. wp_login_url() .'" title="Login">Login</a>';
  }
}

//after login redirect the members dashboard from here
function wlms_login_redirect( $redirect_to, $request, $user ) 
{
  global $user;
  $settings = get_option( 'wlms_settings' );
 
	if ( isset( $user->roles ) && in_array(array_shift($user->roles), $settings['member_roles']) ) 
  {
    return esc_url_raw(add_query_arg( array(
    'manage' => 'wlms-member-dashboard',
    'page' =>   'available-books',
    ), home_url() ));
	} 
  else 
  {
		return $redirect_to;
	}
}
add_filter( 'login_redirect', 'wlms_login_redirect', 10, 3 );
?>