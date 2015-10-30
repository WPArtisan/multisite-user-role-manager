<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ozthegreat.io
 * @since      1.0.0
 *
 * @package    Multisite_User_Role_Manager
 * @subpackage Multisite_User_Role_Manager/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Multisite_User_Role_Manager
 * @subpackage Multisite_User_Role_Manager/includes
 * @author     OzTheGreat <oz@ozthegreat.io>
 */
class Multisite_User_Role_Manager {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Multisite_User_Role_Manager_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'multisite-user-role-manager';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Multisite_User_Role_Manager_Loader. Orchestrates the hooks of the plugin.
	 * - Multisite_User_Role_Manager_i18n. Defines internationalization functionality.
	 * - Multisite_User_Role_Manager_Admin. Defines all hooks for the admin area.
	 * - Multisite_User_Role_Manager_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-multisite-user-role-manager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-multisite-user-role-manager-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-multisite-user-role-manager-admin.php';

		$this->loader = new Multisite_User_Role_Manager_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Multisite_User_Role_Manager_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Multisite_User_Role_Manager_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Multisite_User_Role_Manager_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Ajax endpoints
		$this->loader->add_action( 'wp_ajax_get_user_blogs', $plugin_admin, 'ajax_get_user_blogs' );
		$this->loader->add_action( 'wp_ajax_reassign_user_blog_posts', $plugin_admin, 'ajax_reassign_user_blog_posts' );
		$this->loader->add_action( 'wp_ajax_set_user_blog_roles', $plugin_admin, 'ajax_set_user_blog_roles' );
		$this->loader->add_action( 'wp_ajax_remove_user_from_blog', $plugin_admin, 'ajax_remove_user_from_blog' );
		$this->loader->add_action( 'wp_ajax_get_blogs_wo_user', $plugin_admin, 'ajax_get_blogs_wo_user' );
		$this->loader->add_action( 'wp_ajax_get_blog_roles', $plugin_admin, 'ajax_get_blog_roles' );
		$this->loader->add_action( 'wp_ajax_add_user_to_blog', $plugin_admin, 'ajax_add_user_to_blog' );

		// User profile HTML
		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'template_manage_user_roles', 1 );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'template_manage_user_roles', 1 );

		// Ajax template
		$this->loader->add_action( 'admin_footer-user-edit.php', $plugin_admin, 'template_manage_user_blogs_roles_popup' );
		$this->loader->add_action( 'admin_footer-user-edit.php', $plugin_admin, 'template_user_blogs_roles_row' );
		$this->loader->add_action( 'admin_footer-user-edit.php', $plugin_admin, 'template_add_user_to_blog' );
		$this->loader->add_action( 'admin_footer-user-edit.php', $plugin_admin, 'template_user_blog_remove_confirm' );
		$this->loader->add_action( 'admin_footer-user-edit.php', $plugin_admin, 'template_blog_roles_options' );
		$this->loader->add_action( 'admin_footer-user-edit.php', $plugin_admin, 'template_notification' );
		$this->loader->add_action( 'admin_footer-user-edit.php', $plugin_admin, 'template_ajax_spinner' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Multisite_User_Role_Manager_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
