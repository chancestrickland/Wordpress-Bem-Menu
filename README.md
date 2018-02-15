# Wordpress BEM Menu

Say goodbye to badly named menus in Wordpress and say hello to Wordpress BEM Menus!

## Usage

1. To get started, clone this repo in whatever directory you keep your theme's functions files and include both files in `functions.php`.
  * If you are using an autoloader and are structuring your class names and filenames according to [WordPress coding standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions), you shouldn't need to add an additional `include` in your functions file for the walker class.
2. Register a navigation menu in your theme: E.g., `register_nav_menu( 'my-menu', 'primary site menu' )`.
3. Create a menu from `wp-admin` and make sure it is assigned a theme location that matches your registered nav menu above.
4. To add a BEM-friendly menu in a template, use the following function. The first argument is the theme location (as defined in `wp-admin`) and the second argument is the class prefix you would like to use for this particular menu. *TODO:* Fix modifier to be BEM compliant, as we shouldn't end up with multiple modifiers.

```php
<?php bem_menu( 'menu-location', 'my-menu', 'mod' ); ?>
```

Please note that these modifier classes are not inherited by descendants. *TODO:* Let's figure out a better way to do this, shall we?

## html output
```html
<ul class="my-menu my-menu--mod">
	<li class="my-menu__item  my-menu__item--active  my-menu__item--id-78"><a class="my-menu__link" href="#">Home</a></li>
	<li class="my-menu__item  my-menu__item--id-79"><a class="my-menu__link" href="#">Page 2</a></li>
	<li class="my-menu__item  my-menu__item--id-84"><a class="my-menu__link" href="#">Page 3</a></li>
</ul>
```

## CSS classes

The syntax is very simple, all menu items are logically grouped by depth to avoid some of the nesting issues of the standard output.

```css
/* Top level items */
.my-menu__item { ... }

/* Specific item (where x = post_id) */
.my-menu__item--id-x { ... }

/* Parent item */
.my-menu__item--parent { ... }

/* Active page item */
.my-menu__item--current { ... }

/* Parent of the current active page */
.my-menu__item--child-is-current { ... }

/* Ancestor of the current active page */
.my-menu__item--parent-is-current { ... }

/* Link */
.my-menu__link { ... }

/* sub menu class */
.my-menu__sub-menu { ... }

/* sub menu item */
.my-menu__item--in-sub-menu { ... }

/* Specific sub menu (where x is the menu depth) */
.my-menu__sub-menu--depth-x { ... }

```

## Modification

BEM syntax is very subjective and different developers use different conventions. If you wish to change or adapt the syntax to go with your own implementation, all the menu suffixes are contained within the `$this->item_css_classes` array:

```php
$this->item_css_classes = array(
	'item'                    => "__{$this->item_pre}",
	'parent_item'             => "__{$this->item_pre}--parent",
	'active_item'             => "__{$this->item_pre}--current",
	'parent_of_active_item'   => "__{$this->item_pre}--child-is-current",
	'ancestor_of_active_item' => "__{$this->item_pre}--parent-is-current",
	'sub_menu'                => "__{$this->sub_menu_pre}",
	'sub_menu_item'           => "__{$this->item_pre}--in-sub-menu",
	'link'                    => "__{$this->link_pre}",
);

```

## Custom CSS classes set in `wp-admin` > Menus

If you add a custom class to a menu item in `wp-admin`, Wordpress-Bem-Menu now adds the class to the item converted to a BEM modifier. For example, if you add a class of `my-class` to a menu item, the output would be `<li class="main-menu main-menu__item main-menu__item--my-class"></li>`.
