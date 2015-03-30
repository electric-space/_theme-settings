<?php 	//use in templates
		global $options;
		$options = get_option( 'sa_options' );?>


<?php

// Default options values
$sa_options = array(
	'dropdown_menu'	=> 'no',
	'footer_copyright' => '&copy; ' . date('Y') . ' ' . get_bloginfo('name'),
	'intro_text' => '',
	'layout_view' => 'fixed'
);

if ( is_admin() ) : // Load only if we are viewing an admin page

function sa_register_settings() {
	// Register settings and call sanitation functions
	register_setting( 'sa_theme_options', 'sa_options', 'sa_validate_options' );
}

add_action( 'admin_init', 'sa_register_settings' );




// Store layouts views in array
$sa_dropdown = array(
	'no' => array(
		'value' => 'no',
		'label' => 'No dropdown menu'
	),
	'yes' => array(
		'value' => 'yes',
		'label' => 'Add dropdown menu'
	),
);

// Store layouts views in array
$sa_layouts = array(
	'fixed' => array(
		'value' => 'fixed',
		'label' => 'Fixed Layout'
	),
	'responsive' => array(
		'value' => 'responsive',
		'label' => 'Responsive Layout'
	),
);


function sa_theme_options() {
	// Add theme options page to the addmin menu
	add_theme_page( 'Theme Options', 'Theme Options', 'edit_theme_options', 'theme_options', 'sa_theme_options_page' );
}

add_action( 'admin_menu', 'sa_theme_options' );

// Function to generate options page
function sa_theme_options_page() {
	global $sa_options, $sa_layouts, $sa_dropdown;

	
	if ( !isset( $_REQUEST['updated'] ) )
		
		
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>

	<div class="wrap">

	<?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options' ) . "</h2>";
	// This shows the page's name and an icon if one has been provided ?>

	<?php if ( $_REQUEST['updated'] == true ) : ?>
	
	
	<div class="updated fade">
		<p><strong><?php _e( 'Options saved' ); ?></strong></p>
	</div>
	
	<?php endif; // If the form has just been submitted, this shows the notification ?>

	
	
	<form method="post" action="options.php">

	<?php $settings = get_option( 'sa_options', $sa_options ); ?>
	
	<?php settings_fields( 'sa_theme_options' );
	
	/* This function outputs some hidden fields required by the form,
	including a nonce, a unique number used to ensure the form has been submitted from the admin page
	and not somewhere else, very important for security */ ?>
	
	
	<style>
		input, textarea{width:50%}
		input[type="checkbox"],input[type="radio"], input[type="submit"] {width:auto}
		.description{font-size:0.8em; font-style:italic; color:#ccc; display:block}
	</style>
	
	
	<table class="form-table"><!-- Grab a hot cup of coffee, yes we're using tables! -->
	
	
	<tr valign="top"><th scope="row">Add dropdown menu?</th>
	<td>
	<?php foreach( $sa_dropdown as $dd ) : ?>
	<input type="radio" id="<?php echo $dd['value']; ?>" name="sa_options[dropdown_menu]" value="<?php esc_attr_e( $dd['value'] ); ?>" <?php checked( $settings['dropdown_menu'], $dd['value'] ); ?> />
	<label for="<?php echo $dd['value']; ?>"><?php echo $dd['label']; ?></label><br />
	<?php endforeach; ?>
	</td>
	</tr>
	
	<tr valign="top"><th scope="row">Layout View</th>
	<td>
	<?php foreach( $sa_layouts as $layout ) : ?>
	<input type="radio" id="<?php echo $layout['value']; ?>" name="sa_options[layout_view]" value="<?php esc_attr_e( $layout['value'] ); ?>" <?php checked( $settings['layout_view'], $layout['value'] ); ?> />
	<label for="<?php echo $layout['value']; ?>"><?php echo $layout['label']; ?></label><br />
	<?php endforeach; ?>
	</td>
	</tr>
	
	<tr valign="top"><th scope="row"><label for="intro_text">Intro Text</label></th>
	<td>
	<textarea id="intro_text" name="sa_options[intro_text]" rows="5" cols="30"><?php echo stripslashes($settings['intro_text']); ?></textarea>
	</td>
	</tr>
	
	<tr valign="top"><th scope="row"><label for="phone">Phone Number</label></th>
	<td>
	<input id="twitter" name="sa_options[phone]" type="text" value="<?php  esc_attr_e($settings['phone']); ?>" />
	</td>
	</tr>
	
	<tr valign="top"><th scope="row"><label for="address">Address</label></th>
	<td>
	<textarea id="intro_text" name="sa_options[address]" rows="5" cols="30"><?php echo stripslashes($settings['address']); ?></textarea>
	<label class="description" for="new_theme_theme_options[sometextarea]"><?php _e( $theme .'Comma seperated.', $theme ); ?></label>
	</td>
	</tr>
	
	<tr valign="top"><th scope="row"><label for="social">Social</label></th>
	<td>
	<textarea  id="social" name="sa_options[social]" rows="5" cols="30"><?php  esc_attr_e($settings['social']); ?></textarea>
	<label class="description" for="new_theme_theme_options[social]"><?php _e( $theme .'Each account URL on a new line', $theme ); ?></label>
	</td>
	</tr>
	
	<tr valign="top"><th scope="row"><label for="footer_copyright">Footer Copyright Notice</label></th>
	<td>
	<input id="footer_copyright" name="sa_options[footer_copyright]"  type="text" value="<?php  esc_attr_e($settings['footer_copyright']); ?>" />
	</td>
	</tr>

	</table>

	<p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>

	</form>

	</div>

	<?php
}

function sa_validate_options( $input ) {
	global $sa_options, $sa_categories, $sa_layouts;

	$settings = get_option( 'sa_options', $sa_options );
	
	// We strip all tags from the text field, to avoid vulnerablilties like XSS
	$input['footer_copyright'] = wp_filter_nohtml_kses( $input['footer_copyright'] );
	
	// We strip all tags from the text field, to avoid vulnerablilties like XSS
	$input['intro_text'] = wp_filter_post_kses( $input['intro_text'] );
	
	// We select the previous value of the field, to restore it in case an invalid entry has been given
	$prev = $settings['featured_cat'];
	// We verify if the given value exists in the categories array
	if ( !array_key_exists( $input['featured_cat'], $sa_categories ) )
		$input['featured_cat'] = $prev;
	
	// We select the previous value of the field, to restore it in case an invalid entry has been given
	$prev = $settings['layout_view'];
	// We verify if the given value exists in the layouts array
	if ( !array_key_exists( $input['layout_view'], $sa_layouts ) )
		$input['layout_view'] = $prev;
	
	// If the checkbox has not been checked, we void it
	if ( ! isset( $input['author_credits'] ) )
		$input['author_credits'] = null;
	// We verify if the input is a boolean value
	$input['author_credits'] = ( $input['author_credits'] == 1 ? 1 : 0 );
	
	return $input;
}

endif;  // EndIf is_admin()