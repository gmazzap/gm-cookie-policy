<?php
/*
Plugin Name: EU Cookie Policy
Description: A simple plugin to show a message for compliance with EU cookie law.
Author:      Giuseppe Mazzapica
License:     MIT
License URI: http://opensource.org/licenses/MIT
*/

/*
 * This file is part of the gm-cookie-policy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\CookiePolicy;

/**
 * Load Composer autoload if available, otherwise register a simple autoload callback.
 *
 * @return void
 */
function autoload()
{
    static $done;
    if (! $done && ! class_exists('GM\CookiePolicy\Config', true)) {
        $done = true;
        file_exists(__DIR__.'/vendor/autoload.php')
            ? require_once __DIR__.'/vendor/autoload.php'
            : spl_autoload_register(function ($class) {
                if (strpos($class, __NAMESPACE__) === 0) {
                    $name = str_replace('\\', '/', substr($class, strlen(__NAMESPACE__)));
                    /** @noinspection PhpIncludeInspection */
                    require_once __DIR__."/src{$name}.php";
                }
            });
    }
}

// A filter that can be used to check if the user accepted cookies or not.
// Being a filter, checks can be done without relying on specific plugin class or functions.
add_filter('cookie-policy-accepted', function () {
    autoload();
    return (new Cookie())->exists();
});

/**
 * Nothing more to do on AJAX requests
 */
if (defined('DOING_AJAX') && DOING_AJAX) {
    return;
};


// The plugin "main" routine
add_action('wp_loaded', function () {

    autoload();

    $isAdmin = is_admin();

    // User accepted policy, let's save a cookie to don't show message again and then return.
    // This is needed to ensure plugin works when js is disabled.
    if (
        ! $isAdmin
        && filter_input(INPUT_GET, Cookie::ACCEPTED_QUERY, FILTER_VALIDATE_INT) > 0
    ) {
        $cookie = new Cookie();
        $cookie->save();
        return;
    }

    // Load text domain
    $pathArr = explode(DIRECTORY_SEPARATOR, __DIR__);
    load_plugin_textdomain('gm-cookie-policy', false, end($pathArr).'/lang');

    // Instantiate config class
    $config = new Config(
        [
            'plugin-path'   => __FILE__,
            'no-cookie-url' => esc_url(add_query_arg([Cookie::ACCEPTED_QUERY => time()])),
        ],
        SettingsPage::defaults()
    );

    // Now let's attach routines to proper hooks...

    // Setup and show setting page
    $isAdmin and add_action('admin_menu', function () use ($config) {
        $settings = new SettingsPage($config, new SimpleRenderer());
        $settings->setup();
    });

    // Save setting page form when submitted
    $isAdmin and add_action('admin_post_'.SettingsPage::ACTION, function () use ($config) {
        $settings = new SettingsPage($config, new SimpleRenderer());
        $settings->save();
        exit();
    });

    // Show cookie policy message when it was not already shown
    $isAdmin or add_action('template_redirect', function () use ($config) {
        $cookie = new Cookie();
        // If the cookie exists, user accepted policy, nothing else to do
        if ($cookie->exists()) {
            return;
        }

        // Avoid show message when in the "Read More" page
        $moreUrl = rtrim($config['more-url'], '/');
        if ($moreUrl && $moreUrl === rtrim(esc_url(home_url(add_query_arg([]))), '/')) {
            return;
        }

        $message = new Message($config, new SimpleRenderer());
        // Adding necessary assets according to config
        add_action('wp_enqueue_scripts', [$message, 'setupAssets']);
        // Render message content
        add_action('wp_footer', [$message, 'renderTemplate']);
    });
});
