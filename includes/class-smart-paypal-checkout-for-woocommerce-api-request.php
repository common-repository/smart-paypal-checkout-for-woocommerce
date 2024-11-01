<?php

defined('ABSPATH') || exit;

class Paypal_Checkout_For_Woocommerce_API_Request {

    public $is_sandbox;
    public $token_url;
    public $paypal_oauth_api;
    public $generate_token_url;
    public $client_id;
    public $secret_key;
    public $basicAuth;
    public $api_response;
    public $result;
    public $settings;
    public $api_request;
    protected static $_instance = null;
    public $api_log;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->psb_load_class();
        $this->is_sandbox = 'yes' === $this->settings->get('testmode', 'no');
        $this->paymentaction = $this->settings->get('paymentaction', 'capture');
        if ($this->is_sandbox) {
            $this->token_url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
            $this->paypal_oauth_api = 'https://api-m.sandbox.paypal.com/v1/oauth2/token/';
            $this->generate_token_url = 'https://api-m.sandbox.paypal.com/v1/identity/generate-token';
            $this->client_id = $this->settings->get('sandbox_client_id');
            $this->secret_key = $this->settings->get('sandbox_secret_id');
            $this->basicAuth = base64_encode($this->client_id . ":" . $this->secret_key);
        } else {
            $this->token_url = 'https://api-m.paypal.com/v1/oauth2/token';
            $this->paypal_oauth_api = 'https://api-m.paypal.com/v1/oauth2/token/';
            $this->generate_token_url = 'https://api-m.paypal.com/v1/identity/generate-token';
            $this->client_id = $this->settings->get('api_client_id');
            $this->secret_key = $this->settings->get('api_secret');
            $this->basicAuth = base64_encode($this->client_id . ":" . $this->secret_key);
        }
    }

    public function request($url, $args, $action_name = 'default') {
        try {
            $this->result = wp_remote_get($url, $args);
            return $this->api_response->parse_response($this->result, $url, $args, $action_name);
        } catch (Exception $ex) {
            $this->api_log->log("The exception was created on line: " . $ex->getLine(), 'error');
            $this->api_log->log($ex->getMessage(), 'error');
        }
    }

    public function psb_load_class() {
        try {
            if (!class_exists('Paypal_Checkout_For_Woocommerce_Response')) {
                include_once SMART_PAYPAL_CHECKOUT_FOR_WOOCOMMERCE_DIR . '/includes/class-smart-paypal-checkout-for-woocommerce-response.php';
            }
            if (!class_exists('Paypal_Checkout_For_Woocommerce_Settings')) {
                include_once SMART_PAYPAL_CHECKOUT_FOR_WOOCOMMERCE_DIR . '/includes/class-smart-paypal-checkout-for-woocommerce-settings.php';
            }
            if (!class_exists('Paypal_Checkout_For_Woocommerce_Log')) {
                include_once SMART_PAYPAL_CHECKOUT_FOR_WOOCOMMERCE_DIR . '/includes/class-smart-paypal-checkout-for-woocommerce-log.php';
            }
            $this->api_log = Paypal_Checkout_For_Woocommerce_Log::instance();
            $this->settings = Paypal_Checkout_For_Woocommerce_Settings::instance();
            $this->api_response = Paypal_Checkout_For_Woocommerce_Response::instance();
        } catch (Exception $ex) {
            $this->api_log->log("The exception was created on line: " . $ex->getLine(), 'error');
            $this->api_log->log($ex->getMessage(), 'error');
        }
    }

}
