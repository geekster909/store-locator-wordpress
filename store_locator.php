<?php
/*
Plugin Name: Store Locator
description: A simple store locator
Version: 1.0.0
Author: Justin Bond
Author URI: http://justin-bond.com
License: GPL v3

Store Locator
Copyright (C) 2018 Justin Bond - bond.justink@gmail.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
  
@package Store_locator
@category Core
@author Justin Bond
*/

if ( !class_exists( 'Store_locator' ) ) {

	class Store_locator {

		/**
		 * Class constructor
		 */
		function __construct()
		{
			if ( method_exists( $this, 'initPostTypes' ) ) {
				add_action( 'admin_init', array($this, 'initPostTypes') );
			}
			$this->initialize();
		}

		/**
		 * Initialize PostType
		 */
		public function initialize()
		{
			add_action('admin_menu', array($this, 'registerManagerMenu'));
		}

		public function initPostTypes()
		{
			$labels = array(
				'name' => __('Manage Stores', 'stores'),
				'singular_name' => __('Store', 'stores'),
				'add_new' => _x('Add New Store', 'stores', 'stores'),
				'add_new_item' => __('Add New Store', 'stores'),
				'edit_item' => __('Edit Store', 'stores'),
				'new_item' => __('New Store', 'stores'),
				'view_item' => __('View Store', 'stores'),
				'search_items' => __('Search Stores', 'stores'),
				'not_found' => __('No Stores found', 'stores'),
				'not_found_in_trash' => __('No Stores found in Trash', 'stores'),
				'parent_item_colon' => __('Parent Store:', 'stores'),
				'menu_name' => __('Manage Stores', 'stores'),
			);

			$args = array(
				'labels' => $labels,
				'hierarchical' => false,
				'description' => 'Manage Stores',
				'taxonomies' => array(),
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => false,
				'show_in_admin_bar' => true,
				'menu_position' => null,
				'menu_icon' => null,
				'show_in_nav_menus' => false,
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'has_archive' => true,
				'query_var' => true,
				'can_export' => true,
				'rewrite' => false,
				'capability_type' => 'post',
				'supports' => array(
					'title',
					'thumbnail',
					'page-attributes'
				)
			);

			register_post_type('stores', $args);

			// adding meta-boxes
			add_action('add_meta_boxes', [$this, 'addStoresFields']);
			add_action('save_post', [$this, 'saveStoresFields'], 10, 2);
		}

		/**
		 * saving Dealers fields
		 */
		public function saveStoresFields($post_id, $post)
		{

			/* Verify the nonce before proceeding. */
			if ( !isset( $_POST['stores_nonce'] ) || !wp_verify_nonce( $_POST['stores_nonce'], 'stores' ) ) {
				return $post_id;
			}

			/* Get the post type object. */
			$post_type = get_post_type_object( $post->post_type );

			/* Check if the current user has permission to edit the post. */
			if (!current_user_can( $post_type->cap->edit_post, $post_id)) {
				return $post_id;
			}

			$columns = ['address_1', 'address_2', 'city', 'state', 'zip', 'country', 'website', 'phone', 'latitude', 'longitude'];
			
			foreach ($columns as $column) {
				$meta_value = get_post_meta($post_id, $column, true);
				$new_meta_value = isset($_POST[$column]) ? $_POST[$column] : '';

				if ( $new_meta_value && $meta_value == '' ){
					add_post_meta( $post_id, $column, $new_meta_value, true );
				}
				elseif ( $new_meta_value && $new_meta_value !== $meta_value ){
					update_post_meta( $post_id, $column, $new_meta_value );
				}
				elseif ( $new_meta_value == '' && $meta_value ){
					delete_post_meta( $post_id, $column, $meta_value );
				}
			}
		}
		public function addStoresFields()
	    {
	        add_meta_box( 'stores-fields', 'Store Fields', [$this, 'displayFields'], 'stores', 'normal');
	    }

	    public function displayFields()
	    {
	        global $post;
	        require_once 'views/store/meta_box.php';
	    }

	    public function add_metabox_classes($classes) {
	        array_push($classes,'acf-postbox');
	        array_push($classes,'seamless');

	        return array_filter($classes);
	    }

		/**
		 * Registers the menu page.
		 */
		public function registerManagerMenu()
		{
			add_menu_page( 'Store Locator', 'Store Locator', 'manage_options', 'store_locator_dashboard', '', '', 2);
			add_submenu_page( 'store_locator_dashboard', 'Manage Stores', 'Manage Stores', 'manage_options', 'edit.php?post_type=stores');
		}

	}

	$GLOBALS['store_locator'] = new Store_locator();
}