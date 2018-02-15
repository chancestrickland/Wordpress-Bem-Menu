<?php
/**
 * Navigation related functions.
 *
 * @package xx
 */

/**
 * Returns an instance of the xx_Walker_BEM class.
 *
 * @param  string     $location            This must be the same as what is set
 *                                         in wp-admin/settings/menus for menu
 *                                         location.
 * @param  string     $css_class_prefix    This string will prefix all of the
 *                                         menu's classes, BEM syntax friendly.
 * @param  arr/string $css_class_modifiers Provide either a string or array of
 *                                         values to apply extra classes to the
 *                                         <ul> but not the <li>'s.
 */
function xx_bem_menu( $location = 'main_menu', $css_class_prefix = 'main-menu', $css_class_modifiers = null ) {
	// Check to see if any css modifiers were supplied.
	if ( $css_class_modifiers ) {
		if ( is_array( $css_class_modifiers ) ) {
			$modifiers = implode( ' ', $css_class_modifiers );
		} elseif ( is_string( $css_class_modifiers ) ) {
			$modifiers = $css_class_modifiers;
		}
	} else {
		$modifiers = '';
	}
	$args = array(
		'theme_location' => $location,
		'container'      => false,
		'items_wrap'     => '<ul class="' . $css_class_prefix . ' ' . $modifiers . '">%3$s</ul>',
		'walker'         => new \XX\Walker_Bem( $css_class_prefix, true ),
	);
	if ( has_nav_menu( $location ) ) {
		return wp_nav_menu( $args );
	} else {
		/* translators: Name of the WordPress dashboard page. */
		printf( esc_html__( 'You need to first define a menu in %s.', 'xx' ), 'wp-admin' );
	}
}
