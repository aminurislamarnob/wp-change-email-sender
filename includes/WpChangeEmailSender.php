<?php

namespace WeLabs\WpChangeEmailSender;

/**
 * WpChangeEmailSender class
 *
 * @class WpChangeEmailSender The class that holds the entire WpChangeEmailSender plugin
 */
final class WpChangeEmailSender {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '3.3.0';

    /**
     * Instance of self
     *
     * @var WpChangeEmailSender
     */
    private static $instance = null;

    /**
     * Holds various class instances
     *
     * @since 2.6.10
     *
     * @var array
     */
    private $container = [];

    /**
     * Constructor for the WpChangeEmailSender class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( WP_CHANGE_EMAIL_SENDER_FILE, [ $this, 'activate' ] );
        register_deactivation_hook( WP_CHANGE_EMAIL_SENDER_FILE, [ $this, 'deactivate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
        add_action( 'woocommerce_flush_rewrite_rules', [ $this, 'flush_rewrite_rules' ] );
        add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );
    }

    /**
     * Initializes the WpChangeEmailSender() class
     *
     * Checks for an existing WpChangeEmailSender instance
     * and if it doesn't find one then create a new one.
     *
     * @return WpChangeEmailSender
     */
    public static function init() {
        if ( self::$instance === null ) {
			self::$instance = new self();
		}

        return self::$instance;
    }

    /**
     * Magic getter to bypass referencing objects
     *
     * @since 2.6.10
     *
     * @param string $prop
     *
     * @return Class Instance
     */
    public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
		}
    }

    /**
     * Placeholder for activation function
     *
     * Nothing is being called here yet.
     */
    public function activate() {
        // Rewrite rules during wp_change_email_sender activation
        if ( $this->has_woocommerce() ) {
            $this->flush_rewrite_rules();
        }
    }

    /**
	 * Register plugin REST routes
	 *
	 * @return void
	 */
	public function register_rest_route() {
        $this->container['admin_settings_rest']->register_routes();
        $this->container['test_email_rest']->register_routes();
	}

    /**
     * Flush rewrite rules after wp_change_email_sender is activated or woocommerce is activated
     *
     * @since 3.2.8
     */
    public function flush_rewrite_rules() {
        // fix rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {     }

    /**
     * Define all constants
     *
     * @return void
     */
    public function define_constants() {
        defined( 'WP_CHANGE_EMAIL_SENDER_PLUGIN_VERSION' ) || define( 'WP_CHANGE_EMAIL_SENDER_PLUGIN_VERSION', $this->version );
        defined( 'WP_CHANGE_EMAIL_SENDER_DIR' ) || define( 'WP_CHANGE_EMAIL_SENDER_DIR', dirname( WP_CHANGE_EMAIL_SENDER_FILE ) );
        defined( 'WP_CHANGE_EMAIL_SENDER_INC_DIR' ) || define( 'WP_CHANGE_EMAIL_SENDER_INC_DIR', WP_CHANGE_EMAIL_SENDER_DIR . '/includes' );
        defined( 'WP_CHANGE_EMAIL_SENDER_TEMPLATE_DIR' ) || define( 'WP_CHANGE_EMAIL_SENDER_TEMPLATE_DIR', WP_CHANGE_EMAIL_SENDER_DIR . '/templates' );
        defined( 'WP_CHANGE_EMAIL_SENDER_PLUGIN_ASSET' ) || define( 'WP_CHANGE_EMAIL_SENDER_PLUGIN_ASSET', plugins_url( 'assets', WP_CHANGE_EMAIL_SENDER_FILE ) );
        defined( 'WP_CHANGE_EMAIL_SENDER_PLUGIN_ADMIN_ASSET' ) || define( 'WP_CHANGE_EMAIL_SENDER_PLUGIN_ADMIN_ASSET', WP_CHANGE_EMAIL_SENDER_PLUGIN_ASSET . '/admin' );
        defined( 'WP_CHANGE_EMAIL_SENDER_PLUGIN_PUBLIC_ASSET' ) || define( 'WP_CHANGE_EMAIL_SENDER_PLUGIN_PUBLIC_ASSET', WP_CHANGE_EMAIL_SENDER_PLUGIN_ASSET . '/public' );

        // give a way to turn off loading styles and scripts from parent theme
        defined( 'WP_CHANGE_EMAIL_SENDER_LOAD_STYLE' ) || define( 'WP_CHANGE_EMAIL_SENDER_LOAD_STYLE', true );
        defined( 'WP_CHANGE_EMAIL_SENDER_LOAD_SCRIPTS' ) || define( 'WP_CHANGE_EMAIL_SENDER_LOAD_SCRIPTS', true );
    }

    /**
     * Load the plugin after WP User Frontend is loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->load_textdomain();
        ( new Upgrader() )->maybe_upgrade();

        $this->includes();
        $this->init_hooks();

        do_action( 'wp_change_email_sender_loaded' );
    }

    /**
     * Load plugin text domain for translations.
     *
     * @return void
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'wp-change-email-sender', false, dirname( WP_CHANGE_EMAIL_SENDER_BASENAME ) . '/languages' );
    }

    /**
     * Initialize the actions
     *
     * @return void
     */
    public function init_hooks() {
        // initialize the classes
        add_action( 'init', [ $this, 'init_classes' ], 4 );
        add_action( 'plugins_loaded', [ $this, 'after_plugins_loaded' ] );
    }

    /**
     * Include all the required files
     *
     * @return void
     */
    public function includes() {
        // include_once STUB_PLUGIN_DIR . '/functions.php';
    }

    /**
     * Init all the classes
     *
     * @return void
     */
    public function init_classes() {
        $this->container['scripts'] = new Assets();
        $this->container['admin_settings'] = new Admin\Settings();
		$this->container['admin_settings_rest'] = new Admin\REST\SettingsController();
		$this->container['test_email_rest'] = new Admin\REST\TestEmailController();
		$this->container['email_sender'] = new OverrideEmailSender();
    }

    /**
     * Executed after all plugins are loaded
     *
     * At this point wp_change_email_sender Pro is loaded
     *
     * @since 2.8.7
     *
     * @return void
     */
    public function after_plugins_loaded() {
        // Initiate background processes and other tasks
    }

    /**
     * Check whether woocommerce is installed and active
     *
     * @since 2.9.16
     *
     * @return bool
     */
    public function has_woocommerce() {
        return class_exists( 'WooCommerce' );
    }

    /**
     * Check whether woocommerce is installed
     *
     * @since 3.2.8
     *
     * @return bool
     */
    public function is_woocommerce_installed() {
        return in_array( 'woocommerce/woocommerce.php', array_keys( get_plugins() ), true );
    }

    /**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', WP_CHANGE_EMAIL_SENDER_FILE ) );
	}

    /**
     * Get the template file path to require or include.
     *
     * @param string $name
     * @return string
     */
    public function get_template_path( $name ) {
        $template = untrailingslashit( WP_CHANGE_EMAIL_SENDER_TEMPLATE_DIR ) . '/' . untrailingslashit( $name );

        return apply_filters( 'wp_change_email_sender_template', $template, $name );
    }

    /**
     * Get templates passing attributes and including the file.
     * You can use this method to load php template file by following:
     * Example-1: welabs_wp_change_email_sender()->get_template( 'admin/custom-meta-fields.php' );
     * Example-2: welabs_wp_change_email_sender()->get_template( 'admin/custom-meta-fields.php', [
			'loop' => $loop,
			'variation_data' => $variation_data,
			'variation' => $variation
		] );
     *
     * @param mixed  $template_name
     * @param array  $args          (default: array())
     * @param string $template_path (default: '')
     * @param string $default_path  (default: '')
     *
     * @return void
     */
    public function get_template( $template_name, $args = [] ) {
        if ( $args && is_array( $args ) ) {
            extract( $args ); // phpcs:ignore
        }

        $template_path = $this->get_template_path( $template_name );

        if ( ! file_exists( $template_path ) ) {
            _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', esc_html( $template_path ) ), WP_CHANGE_EMAIL_SENDER_PLUGIN_VERSION );

            return;
        }

        do_action( 'wp_change_email_sender_before_template_part', $template_name, $args );

        include $this->get_template_path( $template_name );

        do_action( 'wp_change_email_sender_after_template_part', $template_name, $args );
    }
}
