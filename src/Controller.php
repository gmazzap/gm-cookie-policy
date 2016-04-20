<?php
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
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package gm-cookie-policy
 */
class Controller
{
    /**
     * @var bool
     */
    private $isAdmin;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->isAdmin = is_admin();
    }

    /**
     * When JS is disabled cookie have to be set in PHP.
     * This methods looks at query string to see if user accepted policy and set cookie if necessary.
     *
     * @param \GM\CookiePolicy\Cookie $cookie
     * @return bool
     */
    public function maybePolicyAccepted(Cookie $cookie = null)
    {
        if (
            ! $this->isAdmin
            && filter_input(INPUT_GET, Cookie::ACCEPTED_QUERY, FILTER_VALIDATE_INT) > 0
        ) {
            $cookie or $cookie = new Cookie();
            $cookie->save();

            return true;
        }

        return false;
    }

    /**
     * Setup backend hooks.
     * Instantiate necessary objects if necessary.
     *
     * @param \GM\CookiePolicy\Config                 $config
     * @param \GM\CookiePolicy\SettingsPage|null      $settings
     * @param \GM\CookiePolicy\RendererInterface|null $renderer
     */
    public function setupBackendActions(
        Config $config,
        SettingsPage $settings = null,
        RendererInterface $renderer = null
    ) {
        if (! $this->isAdmin) {
            return;
        }

        $this->loadTextDomain();

        // Setup settings page
        add_action('admin_menu', function () use ($config, $settings, $renderer) {
            $renderer or $renderer = new SimpleRenderer();
            $settings or $settings = new SettingsPage($config, $renderer);
            $settings->setup();
        });

        // Save setting page form when submitted
        add_action('admin_post_'.SettingsPage::ACTION,
            function () use ($config, $settings, $renderer) {
                $renderer or $renderer = new SimpleRenderer();
                $settings or $settings = new SettingsPage($config, $renderer);
                $settings->save();
                exit();
            });
    }

    /**
     * Setup frontend hooks.
     * Instantiate necessary objects if necessary.
     *
     * @param \GM\CookiePolicy\Config       $config
     * @param \GM\CookiePolicy\Cookie|null  $cookie
     * @param \GM\CookiePolicy\Message|null $message
     * @param \GM\CookiePolicy\Assets       $assets
     */
    public function setupFrontendActions(
        Config $config,
        Cookie $cookie = null,
        Message $message = null,
        Assets $assets = null
    ) {
        if ($this->isAdmin || ! filter_var($config['enabled'], FILTER_VALIDATE_BOOLEAN)) {
            return;
        }

        $this->loadTextDomain();

        add_action('template_redirect', function () use ($config, $cookie, $message, $assets) {
            $cookie or $cookie = new Cookie();
            $assets or $assets = new Assets($config);
            $exists = $cookie->exists();
            /** @var callable $setupScripts */
            $setupScripts = $exists ? [$assets, 'setupApiScripts'] : [$assets, 'setupScripts'];

            // Add necessary script according to config
            add_action('wp_enqueue_scripts', $setupScripts);

            // If the cookie exists, user accepted policy, nothing else to do
            if ($exists) {
                return;
            }

            // Avoid show message when in the "Read More" page
            $moreUrl = rtrim($config['more-url'], '/');
            if (!$moreUrl || $moreUrl !== rtrim(esc_url(home_url(add_query_arg([]))), '/')) {
                $message or $message = new Message($config, $this->createRenderer($config));

                // Add necessary assets according to config
                add_action('wp_enqueue_scripts', [$assets, 'setupStyles']);
                // Render message content
                add_action('wp_footer', [$message, 'render']);
            }
        });
    }

    /**
     * This factory gives users possibility to use a different renderer object
     * for the rendering of message.
     *
     * @param \GM\CookiePolicy\Config $config
     * @return \GM\CookiePolicy\RendererInterface
     */
    private function createRenderer(Config $config)
    {
        $renderer = apply_filters('cookie-policy.renderer-instance', null, $config);
        $renderer instanceof RendererInterface or $renderer = new SimpleRenderer();

        return $renderer;
    }

    /**
     * Load text domain.
     */
    private function loadTextDomain()
    {
        // Load text domain
        $pathArr = explode(DIRECTORY_SEPARATOR, dirname(__DIR__));
        load_plugin_textdomain('gm-cookie-policy', false, end($pathArr).'/lang');
    }
}
