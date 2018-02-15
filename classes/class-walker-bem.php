<?php
/**
 * XX\Walker_Bem class
 *
 * @package xx
 */

namespace XX;
use       XX;

/**
 * Inserts some BEM naming sensibility into WordPress menus.
 *
 * @see Walker_Nav_Menu
 */
class Walker_Bem extends \Walker_Nav_Menu {
	/**
	 * BEM prefix for items.
	 *
	 * @var string
	 */
	private $item_pre = 'item';

	/**
	 * BEM prefix for sub-menus.
	 *
	 * @var string
	 */
	private $sub_menu_pre = 'sub-menu';

	/**
	 * BEM prefix for links.
	 *
	 * @var string
	 */
	private $link_pre = 'link';

	/**
	 * Constructor.
	 *
	 * @param string $css_class_prefix Block-level prefix.
	 */
	function __construct( $css_class_prefix ) {
		$this->css_class_prefix = $css_class_prefix;

		// Define menu item names appropriately.
		$this->item_css_class_suffixes = array(
			'item'                    => "__{$this->item_pre}",
			'parent_item'             => "__{$this->item_pre}--parent",
			'active_item'             => "__{$this->item_pre}--current",
			'parent_of_active_item'   => "__{$this->item_pre}--child-is-current",
			'ancestor_of_active_item' => "__{$this->item_pre}--parent-is-current",
			'sub_menu'                => "__{$this->sub_menu_pre}",
			'sub_menu_item'           => "__{$this->item_pre}--in-sub-menu",
			'link'                    => "__{$this->link_pre}",
		);
	}

	/**
	 * Check for children.
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing (passed by reference).
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Used to append additional content (passed by reference).
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {

		$id_field = $this->db_fields['id'];

		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
		}

		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * The $args parameter holds additional values that may be used with the
	 * child class methods. This method finishes the list at the end of output
	 * of the elements.
	 *
	 * @abstract
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int    $depth  Depth of the item.
	 * @param array  $args   An array of additional arguments.
	 */
	function start_lvl( &$output, $depth = 1, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent      = str_repeat( $t, $depth );
		$prefix      = $this->css_class_prefix;
		$suffix      = $this->item_css_class_suffixes;
		$real_depth  = $depth + 1;
		$classes     = array(
			$prefix . $suffix['sub_menu'],
			$prefix . $suffix['sub_menu'] . '--depth-' . $real_depth,
		);
		$class_names = implode( ' ', $classes );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		// Add a ul wrapper to sub nav.
		$output .= "{$n}{$indent}<ul{$class_names} data-depth='{$real_depth}'>{$n}";
	}

	/**
	 * Start the element output.
	 *
	 * The $args parameter holds additional values that may be used with the
	 * child class methods. Includes the element output also.
	 *
	 * @abstract
	 * @param string $output            Used to append additional content (passed by reference).
	 * @param object $item              The menu item.
	 * @param int    $depth             Depth of the item.
	 * @param array  $args              An array of additional arguments.
	 * @param int    $id                ID of the current item.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // Code indent.
		$prefix = $this->css_class_prefix;
		$suffix = $this->item_css_class_suffixes;

		// Depth class.
		$depth_class = $prefix . $suffix['item'] . '--depth-' . ( $depth + 1 ); // Use real depth.

		// Item classes. TODO: Make this more readable.
		$item_classes = array(
			'item_class'            => "{$prefix}{$suffix['item']}",
			'parent_class'          => $args->has_children ? $parent_class = "{$prefix}{$suffix['parent_item']}" : '',
			'active_page_class'     => in_array( 'current-menu-item', $item->classes, true ) ? "{$prefix}{$suffix['active_item']}" : '',
			'active_parent_class'   => in_array( 'current-menu-parent', $item->classes, true ) ? "{$prefix}{$suffix['parent_of_active_item']}" : '',
			'active_ancestor_class' => in_array( 'current-menu-ancestor', $item->classes, true ) ? "{$prefix}{$suffix['ancestor_of_active_item']}" : '',
			'depth_class'           => $depth >= 1 ? "{$depth_class} {$prefix}{$suffix['sub_menu_item']}" : $depth_class,
			'item_id_class'         => "{$prefix}{$suffix['item']}--object-id-{$item->object_id} {$prefix}{$suffix['item']}--id-{$id}",
			'user_class'            => ! empty( $item->classes[0] ) ? "{$prefix}__item--{$item->classes[0]}" : '',
		);

		// Convert array to string excluding any empty values.
		$class_string = implode( '  ', array_filter( $item_classes ) );

		// Add the classes to the wrapping <li>.
		$output .= $indent . '<li class="' . $class_string . '">';

		// Depth class for links.
		$depth_link_class = $prefix . $suffix['link'] . '--depth-' . ( $depth + 1 ); // Use real depth.

		// Link classes. TODO: Make this more readable.
		$link_classes = array(
			'item_link'   => $prefix . $suffix['link'],
			'depth_class' => $depth >= 1 ? $depth_link_class . ' ' . $prefix . $suffix['link'] . '--in-sub-menu' : $depth_link_class,
		);

		$link_class_string = implode( '  ', array_filter( $link_classes ) );
		$link_class_output = 'class="' . $link_class_string . '"';

		// Link attributes.
		// phpcs:disable
		// (WPCS doesn't like our spacing here TODO: update WPCS config for ternary operater spacing like this)
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target )     . '"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn )        . '"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url )        . '"' : '';
		// phpcs:enable

		// Creatre link markup.
		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . ' ' . $link_class_output . '>';
		$item_output .= $args->link_before;
		$item_output .= apply_filters( 'the_title', $item->title, $item->ID ); // WPCS: prefix ok.
		$item_output .= $args->link_after;
		$item_output .= $args->after;
		$item_output .= '</a>';

		// Filter <li>.
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args ); // WPCS: prefix ok.
	}
}
