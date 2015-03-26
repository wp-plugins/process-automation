<?php

/**
 * Plugin Name: Process Automation (WooCommerce Support)
 * Plugin URI: http://www.tidioautomation.com
 * Description:
 * Version: 1.0.2
 * Author: Tidio Ltd.
 * Author URI: http://www.tidioautomation.com
 * License: GPL2
 */
class TidioAutomation {

    private $scriptUrl = '//code.tidio.co/';

    public function __construct() {
        add_action('admin_menu', array($this, 'addAdminMenuLink'));
        add_action('admin_footer', array($this, 'adminJS'));

        self::getPrivateKey();

        if (!is_admin()) {
            add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        }

        add_action('deactivate_' . plugin_basename(__FILE__), array($this, 'uninstall'));

        add_action('wp_ajax_tidio_automation_redirect', array($this, 'ajaxTidioAutomationRedirect'));
    }

    // Ajax - Create New Project

    public function ajaxTidioAutomationRedirect() {

        if (!empty($_GET['access_status']) && !empty($_GET['private_key']) && !empty($_GET['public_key'])) {

            update_option('tidio-automation-external-public-key', $_GET['public_key']);
            update_option('tidio-automation-external-private-key', $_GET['private_key']);

            $view = array(
                'mode' => 'redirect',
                'redirect_url' => self::getRedirectUrl($_GET['private_key'])
            );
        } else {

            $view = array(
                'mode' => 'access_request',
                'access_url' => self::getAccessUrl()
            );
        }
        require "views/ajax-tidio-automation-redirect.php";
        exit;
    }

    // Front End Scripts

    public function enqueueScripts() {
        wp_enqueue_script('tidio-automation', $this->scriptUrl . self::getPublicKey() . '.js', array(), '1.0.0', true);
    }

    // Admin JavaScript

    public function adminJS() {

        $privateKey = self::getPrivateKey();
        $redirectUrl = '';

        if ($privateKey && $privateKey != 'false') {
            $redirectUrl = self::getRedirectUrl($privateKey);
        } else {
            $redirectUrl = admin_url('admin-ajax.php?action=tidio_automation_redirect');
        }

        echo "<script> jQuery('a[href=\"admin.php?page=tidio-automation\"]').attr('href', '" . $redirectUrl . "').attr('target', '_blank') </script>";
    }

    // Menu Pages

    public function addAdminMenuLink() {

        $optionPage = add_menu_page(
                'Automation', 'Automation', 'manage_options', 'tidio-automation', array($this, 'addAdminPage'), plugins_url('media/img/icon.png', __FILE__)
        );
    }

    public function addAdminPage() {
        // Set class property
        $dir = plugin_dir_path(__FILE__);
        include $dir . 'options.php';
    }

    // Uninstall

    public function uninstall() {
        
    }

    // Get Private Key

    public static function getPrivateKey() {

        $privateKey = get_option('tidio-automation-external-private-key');

        if ($privateKey) {
            return $privateKey;
        }

        @$data = file_get_contents(self::getAccessUrl());
        if (!$data) {
            update_option('tidio-automation-external-private-key', 'false');
            return false;
        }

        @$data = json_decode($data, true);
        if (!$data || !$data['status']) {
            update_option('tidio-automation-external-private-key', 'false');
            return false;
        }

        update_option('tidio-automation-external-private-key', $data['value']['private_key']);
        update_option('tidio-automation-external-public-key', $data['value']['public_key']);

        return $data['value']['private_key'];
    }

    // Get Access Url

    public static function getAccessUrl() {

        return 'http://www.tidioautomation.com/access/create?url=' . urlencode(site_url()) . '&platform=wordpress&email=' . urlencode(get_option('admin_email')) . '&_ip=' . $_SERVER['REMOTE_ADDR'];
    }

    public static function getRedirectUrl($privateKey) {

        return 'https://external.tidioautomation.com/access?privateKey=' . $privateKey;
    }

    // Get Public Key

    public static function getPublicKey() {

        $publicKey = get_option('tidio-automation-external-public-key');

        if ($publicKey) {
            return $publicKey;
        }

        self::getPrivateKey();

        return get_option('tidio-automation-external-public-key');
    }

}

$tidioAutomation = new TidioAutomation();

