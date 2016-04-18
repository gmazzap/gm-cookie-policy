<?php
/*
 * This file is part of the elesia package.
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
 * @package elesia
 */
class Message
{
    /**
     * @var \GM\CookiePolicy\Config
     */
    private $config;

    /**
     * @var \GM\CookiePolicy\RendererInterface
     */
    private $renderer;

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
     * @return void
     */
    public function setupAssets()
    {
        $debug = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;
        $name = '/assets/cookie-policy';
        $debug and $name .= '.min';
        $basePath = dirname($this->config['plugin-path']).$name;
        $use = function ($which) {
            $use = filter_var($this->config["use-{$which}"], FILTER_VALIDATE_BOOLEAN);
            return apply_filters("cookie-policy.use-{$which}", $use);
        };

        $use('script') and wp_enqueue_script(
            'cookie-policy',
            plugins_url($name.'.js', $this->config['plugin-path']),
            ['jquery'],
            $debug ? time() : @filemtime($basePath.'.js'),
            true
        );

        if ($use('style')) {
            wp_enqueue_style(
                'cookie-policy',
                plugins_url($name.'.css', $this->config['plugin-path']),
                [],
                $debug ? time() : @filemtime($basePath.'.css'),
                'screen'
            );

            $border = $this->config['show-on'] === 'header' ? 'bottom' : 'top';
            ob_start();
            ?>
            #gm-cookie-policy {
                background-color: <?= esc_attr($this->config['bg-color']) ?>;
                color: <?= esc_attr($this->config['txt-color']) ?>;
                border-<?= $border ?>: <?= esc_attr($this->config['link-color']) ?> 1px solid;
            }
            #gm-cookie-policy .cookie-policy-msg,
            #gm-cookie-policy .cookie-policy-msg table,
            #gm-cookie-policy .cookie-policy-msg table p {
                color: <?= esc_attr($this->config['txt-color']) ?>;
            }
            #gm-cookie-policy .cookie-policy-msg table a {
                color: <?= esc_attr($this->config['link-color']) ?>;
            }
            <?php
            do_action('cookie-policy.print-message-styles', $this->config);
            wp_add_inline_style('cookie-policy', ob_get_clean());
        }
    }

    /**
     * @return void
     */
    public function renderTemplate()
    {
        $message = apply_filters(
            'cookie-policy.message-text',
            do_shortcode($this->config['message']),
            $this->config
        );

        $data = [
            'message'      => $message,
            'closeLabel'   => $this->config['close-label'],
            'moreUrl'      => $this->config['more-url'],
            'moreLabel'    => $this->config['more-label'],
            'closeUrl'     => $this->config['no-cookie-url'],
            'cookieName'   => Cookie::COOKIE,
            'cookieExpire' => Cookie::expiration(),
            'useScript'    => filter_var($this->config['use-script'], FILTER_VALIDATE_BOOLEAN),
            'bgColor'      => $this->config['bg-color'],
            'linkColor'    => $this->config['link-color'],
            'txtColor'     => $this->config['txt-color'],
            'showOn'       => $this->config['show-on'],
        ];

        $template = apply_filters(
            'cookie-policy.message-template',
            dirname(__DIR__).'/templates/message.php'
        );

        echo $this->renderer->render($template, $data);
    }
}
