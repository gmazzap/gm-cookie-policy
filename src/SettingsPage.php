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
class SettingsPage
{
    const SLUG         = 'gm-cookie-policy';
    const ACTION       = 'gm-cookie-policy-save';
    const NONCE_ACTION = 'gm-cookie-policy-nonce';
    const NONCE_KEY    = '_gm-cookie-policy';
    const ERR_KEY      = 'err';
    const EDITOR_ID    = 'cookie-policy-message';

    /**
     * @var \GM\CookiePolicy\Config
     */
    private $config;

    /**
     * @var \GM\CookiePolicy\RendererInterface
     */
    private $renderer;

    /**
     * @return array
     */
    public static function defaults()
    {
        return [
            'use-style'   => 'yes',
            'use-script'  => 'yes',
            'message'     => '',
            'bg-color'    => '#FFFFFF',
            'link-color'  => '#337ab7',
            'txt-color'   => '#555555',
            'show-on'     => 'footer',
            'close-label' => esc_html_x('Close.', 'policy message link text', 'gm-cookie-policy'),
            'more-url'    => '',
            'more-label'  => esc_html_x('Read more.', 'policy message link text', 'gm-cookie-policy'),
        ];
    }

    /**
     * @param \GM\CookiePolicy\Config            $config
     * @param \GM\CookiePolicy\RendererInterface $renderer
     */
    public function __construct(Config $config, RendererInterface $renderer)
    {
        $this->config = $config;
        $this->renderer = $renderer;
    }

    /**
     * @return \GM\CookiePolicy\SettingsPage
     */
    public function setup()
    {
        if (! current_user_can($this->config['capability'])) {
            return;
        }

        // Setup assets and markup for settings page

        add_action('admin_enqueue_scripts', function ($page) {
            if ($page === 'tools_page_'.self::SLUG) {
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker');
            }
        });

        add_submenu_page(
            'tools.php',
            esc_html_x('Cookie Policy Settings', 'setting title', 'gm-cookie-policy'),
            esc_html__('Cookie Policy', 'gm-cookie-policy'),
            $this->config['capability'],
            self::SLUG,
            function () {
                echo $this->renderer->render(
                    dirname(__DIR__).'/templates/settings.php',
                    $this->settingsContext()
                );
            }
        );

        add_filter('teeny_mce_buttons', function ($buttons, $editor) {
            if ($editor === self::EDITOR_ID) {
                $buttons = ['bold', 'italic', 'bullist', 'numlist', 'hr', 'link', 'unlink'];
            }

            return $buttons;
        }, 10, 2);

        // Show error notices if needed
        add_action('current_screen', function (\WP_Screen $screen) {
            $err = filter_input(INPUT_GET, self::ERR_KEY, FILTER_VALIDATE_INT);
            if ($err && ($screen->id === 'tools_page_'.self::SLUG)) {
                add_action('admin_notices', function () use ($err) {
                    $msg = $this->errorMessage($err);
                    $msg and printf('<div class="error"><p>%s</p></div>', $msg);
                });
            }
        });

        return $this;
    }

    /**
     * @return bool|\WP_Error
     */
    public function save()
    {
        if (! current_user_can($this->config['capability'])) {
            return false;
        }

        $url = add_query_arg(['page' => self::SLUG], admin_url('tools.php'));

        if (! current_user_can($this->config['capability'])) {
            wp_safe_redirect(add_query_arg(['err' => 10], $url));

            return false;
        }

        $data = $this->inputData();
        if (is_wp_error($data)) {
            wp_safe_redirect(add_query_arg(['err' => 10], $url));

            return false;
        }

        $config = Config::newInstanceFrom($this->config, $data);

        if (! $config->save()) {
            wp_safe_redirect(add_query_arg(['err' => 20], $url));

            return false;
        }

        wp_safe_redirect($url);

        return true;
    }

    /**
     * @return array
     */
    private function settingsContext()
    {
        global $title;

        return [
            'title'      => $title,
            'message'    => $this->config['message'],
            'actionUrl'  => admin_url('admin-post.php'),
            'actionName' => self::ACTION,
            'nonceName'  => self::NONCE_KEY,
            'nonceValue' => wp_create_nonce(self::NONCE_ACTION),
            'values'     => [
                'use-style'   => $this->config['use-style'],
                'use-script'  => $this->config['use-script'],
                'bg-color'    => $this->config['bg-color'],
                'link-color'  => $this->config['link-color'],
                'txt-color'   => $this->config['txt-color'],
                'show-on'     => $this->config['show-on'],
                'close-label' => $this->config['close-label'],
                'more-url'    => $this->config['more-url'],
                'more-label'  => $this->config['more-label'],
            ],
            'labels'     => [
                'message'     => _x('Message', 'form label', 'gm-cookie-policy'),
                'use-style'   => _x('Use Styles', 'form label', 'gm-cookie-policy'),
                'use-script'  => _x('Use Script', 'form label', 'gm-cookie-policy'),
                'bg-color'    => _x('Background Color', 'form label', 'gm-cookie-policy'),
                'link-color'  => _x('Link Color', 'form label', 'gm-cookie-policy'),
                'txt-color'   => _x('Text Color', 'form label', 'gm-cookie-policy'),
                'show-on'     => _x('Position', 'form label', 'gm-cookie-policy'),
                'header'      => _x('Header', 'form label', 'gm-cookie-policy'),
                'footer'      => _x('Footer', 'form label', 'gm-cookie-policy'),
                'close-label' => _x('"Close" Link Text', 'form label', 'gm-cookie-policy'),
                'more-url'    => _x('"Read More" URL', 'form label', 'gm-cookie-policy'),
                'more-label'  => _x('"Read More" Link Text', 'form label', 'gm-cookie-policy'),
            ],
            'editorId'   => self::EDITOR_ID,
            'editorArgs' => [
                'wpautop'       => false,
                'media_buttons' => false,
                'teeny'         => true,
                'quicktags'     => true,
                'textarea_rows' => 5
            ]
        ];
    }

    /**
     * @return \WP_Error|array
     */
    private function inputData()
    {
        $color = function ($val) {
            $val = (is_string($val) && preg_match('~^#?[ABCDEF0-9]{3,6}$~i', $val)) ? $val : '';

            return (empty($val) || strpos($val, '#') === 0) ? $val : '#'.$val;
        };

        $yesNo = function ($val) {
            $val = is_string($val) ? filter_var($val, FILTER_SANITIZE_STRING) : '';

            return (empty($val) || in_array($val, ['yes', 'no'], true)) ? $val : 'yes';
        };

        $showOn = function ($val) {
            $val = is_string($val) ? strtolower(filter_var($val, FILTER_SANITIZE_STRING)) : '';

            return in_array($val, ['footer', 'header'], true) ? $val : $val = 'footer';
        };

        $message = function ($val) {
            is_string($val) or $val = '';
            if ($val && ! current_user_can('unfiltered_html')) {
                $allow = array_fill_keys(['br', 'em', 'strong', 'ul', 'ol', 'li'], true);
                $allow['a'] = ['href' => true];
                $allow['img'] = ['src' => true, 'width' => true, 'height' => true];
                $val = wp_kses($val, $allow);
            }

            return $val;
        };

        $data = filter_input_array(INPUT_POST, [
            self::NONCE_KEY => FILTER_SANITIZE_STRING,
            'use-style'     => ['filter' => FILTER_CALLBACK, 'options' => $yesNo],
            'use-script'    => ['filter' => FILTER_CALLBACK, 'options' => $yesNo],
            'close-label'   => FILTER_SANITIZE_STRING,
            'more-url'      => FILTER_VALIDATE_URL,
            'more-label'    => FILTER_SANITIZE_STRING,
            self::EDITOR_ID => ['filter' => FILTER_CALLBACK, 'options' => $message],
            'show-on'       => ['filter' => FILTER_CALLBACK, 'options' => $showOn],
            'bg-color'      => ['filter' => FILTER_CALLBACK, 'options' => $color],
            'link-color'    => ['filter' => FILTER_CALLBACK, 'options' => $color],
            'txt-color'     => ['filter' => FILTER_CALLBACK, 'options' => $color],
        ]);

        if (! wp_verify_nonce($data[self::NONCE_KEY], self::NONCE_ACTION)) {
            return new \WP_Error(__CLASS__, 'Invalid data.');
        }

        $data['message'] = $data[self::EDITOR_ID];
        unset($data[self::NONCE_KEY], $data[self::EDITOR_ID]);

        return array_filter($data);
    }

    /**
     * @param  int $error
     * @return string
     */
    private function errorMessage($error)
    {
        $errors = [
            10 => esc_html__('User not allowed.', 'gm-cookie-policy'),
            20 => esc_html__('An error occurred while saving configuration.', 'gm-cookie-policy'),
        ];

        return isset($errors[$error]) ? $errors[$error] : '';
    }
}
