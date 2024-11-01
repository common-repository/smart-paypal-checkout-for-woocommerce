<?php

/**
 * @since      1.0.0
 * @package    Paypal_Checkout_For_Woocommerce
 * @subpackage Paypal_Checkout_For_Woocommerce/includes
 * @author     PayPal <paypal@mbjtechnolabs.com>
 */
class Paypal_Checkout_For_Woocommerce {

    protected $loader;
    protected $plugin_name;
    protected $version;
    public $button_manager;

    public function __construct() {
        if (defined('SMART_PAYPAL_CHECKOUT_FOR_WOOCOMMERCE_VERSION')) {
            $this->version = SMART_PAYPAL_CHECKOUT_FOR_WOOCOMMERCE_VERSION;
        } else {
            $this->version = '3.1.0';
        }
        $this->plugin_name = 'smart-paypal-checkout-for-woocommerce';
        add_filter('woocommerce_payment_gateways', array($this, 'psb_woocommerce_payment_gateways'), 99999999);
        $prefix = is_network_admin() ? 'network_admin_' : '';
        add_filter("{$prefix}plugin_action_links_" . SMART_PAYPAL_CHECKOUT_FOR_WOOCOMMERCE_BASENAME, array($this, 'psb_plugin_action_links'), 10, 1);
        $this->load_dependencies();
        $this->set_locale();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/smart-paypal-checkout-for-woocommerce-function.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-smart-paypal-checkout-for-woocommerce-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-smart-paypal-checkout-for-woocommerce-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/smart-paypal-checkout-for-woocommerce-function.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-smart-paypal-checkout-for-woocommerce-button-manager.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-smart-paypal-checkout-for-woocommerce-product.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-smart-paypal-checkout-for-woocommerce-pay-later-messaging.php';
        $this->loader = new Paypal_Checkout_For_Woocommerce_Loader();
    }

    private function set_locale() {
        $plugin_i18n = new Paypal_Checkout_For_Woocommerce_i18n();
        $this->loader->add_action('init', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_public_hooks() {
        $this->button_manager = new Paypal_Checkout_For_Woocommerce_Button_Manager($this->get_plugin_name(), $this->get_version());
        Paypal_Checkout_For_Woocommerce_Pay_Later::instance();
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

    public function psb_woocommerce_payment_gateways($methods) {
        include_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-smart-paypal-checkout-for-woocommerce-gateway.php';
        $methods[] = 'Paypal_Checkout_For_Woocommerce_Gateway';
        return $methods;
    }

    public function psb_plugin_action_links($actions) {
        $custom_actions = array(
            'configure' => sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wc-settings&tab=checkout&section=paypal_smart_checkout'), __('Configure', 'smart-paypal-checkout-for-woocommerce')),
            'support' => '<a href="' . esc_url('https://wordpress.org/support/plugin/smart-paypal-checkout-for-woocommerce/') . '" aria-label="' . esc_attr__('Support', 'smart-paypal-checkout-for-woocommerce') . '">' . esc_html__('Support', 'smart-paypal-checkout-for-woocommerce') . '</a>',
            'reviews' => '<a href="' . esc_url('https://wordpress.org/support/plugin/smart-paypal-checkout-for-woocommerce/reviews/#new-post') . '" aria-label="' . esc_attr__('Write a Review', 'smart-paypal-checkout-for-woocommerce') . '">' . esc_html__('Write a Review', 'smart-paypal-checkout-for-woocommerce') . '</a>',
        );
        return array_merge($custom_actions, $actions);
    }
}
