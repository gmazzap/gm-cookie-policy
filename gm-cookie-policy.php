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

// Nothing more to do on AJAX requests
(defined('DOING_AJAX') && DOING_AJAX) or add_action('wp_loaded', function () {

    autoload();

    // Controller class is responsible to instantiate objects and attach their methods to proper hooks.
    $controller = new Controller();

    // User accepted policy, let's save a cookie to don't show message again and then return.
    // This is needed to ensure plugin works when js is disabled so cookie have to be set in PHP.
    $accepted = $controller->maybePolicyAccepted();
    if ($accepted) {
        return;
    }

    // Instantiate config class
    $config = new Config(
        [
            'plugin-path'   => __FILE__,
            'no-cookie-url' => esc_url(add_query_arg([Cookie::ACCEPTED_QUERY => time()])),
        ],
        SettingsPage::defaults()
    );

    // Setup backend actions
    $controller->setupBackendActions($config);

    // Setup frontend action
    $controller->setupFrontendActions($config);
});
