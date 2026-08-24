<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package OTHello
 */

declare(strict_types=1);

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('ot_hello_settings');
delete_option('ot_hello_license');
delete_option('ot_hello_world_settings');
delete_option('ot_hello_world_license');
delete_transient('ot_hello_update');
delete_transient('ot_hello_world_update');
