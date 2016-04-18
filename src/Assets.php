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
class Assets
{
    const HANDLE = 'cookie-policy';

    /**
     * @var array
     */
    private $info;

    /**
     * @var array
     */
    private $use;

    /**
     * @var array
     */
    private $colors;

    /**
     * Assets constructor.
     *
     * @param \GM\CookiePolicy\Config $config
     */
    public function __construct(Config $config)
    {
        $debug = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;
        $name = '/assets/cookie-policy';
        $debug and $name .= '.min';
        $path = dirname($config['plugin-path']).$name;
        $this->info = compact('name', 'path', 'debug');
        $this->info['plugin'] = $config['plugin-path'];
        $this->info['position'] = $config['show-on'];
        $this->use = ['scripts' => $config['use-script'], 'styles' => $config['use-style']];
        $this->colors = [
            'txt'  => $config['txt-color'],
            'bg'   => $config['bg-color'],
            'link' => $config['link-color']
        ];
    }

    /**
     * @return bool
     */
    public function setupApiScripts()
    {
        return $this->shouldUse('scripts-api') ? $this->setupScripts() : false;
    }

    /**
     * @return bool
     */
    public function setupScripts()
    {
        if ($this->shouldUse('scripts')) {
            wp_enqueue_script(
                self::HANDLE,
                plugins_url($this->info['name'].'.js', $this->info['plugin']),
                ['jquery'],
                $this->info['debug'] ? time() : @filemtime($this->info['path'].'.js'),
                true
            );

            wp_localize_script(self::HANDLE, 'cookiePolicy', [
                'accepted' => false,
                'cookie'   => ['name' => Cookie::COOKIE, 'expire' => Cookie::expiration()]
            ]);

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function setupStyles()
    {
        if ($this->shouldUse('styles')) {
            wp_enqueue_style(
                self::HANDLE,
                plugins_url($this->info['name'].'.css', $this->info['plugin']),
                [],
                $this->info['debug'] ? time() : @filemtime($this->info['path'].'.css'),
                'screen'
            );

            wp_add_inline_style('cookie-policy', $this->inlineStyle());

            return true;
        }

        return false;
    }

    /**
     * @param string $which
     * @return bool
     */
    private function shouldUse($which)
    {
        $key = explode('-', $which);
        $use = filter_var($this->use[$key[0]], FILTER_VALIDATE_BOOLEAN);

        return (bool) apply_filters("cookie-policy.use-{$which}", $use);
    }

    /**
     * @return mixed
     */
    private function inlineStyle()
    {
        $border = $this->info['position'] === 'header' ? 'bottom' : 'top';
        ob_start();
        ?>
        #gm-cookie-policy {
            background-color: <?= esc_attr($this->colors['bg']) ?>;
            color: <?= esc_attr($this->colors['txt']) ?>;
            border-<?= $border ?>: <?= esc_attr($this->colors['link']) ?> 1px solid;
        }
        #gm-cookie-policy p,
        #gm-cookie-policy .cookie-policy-msg,
        #gm-cookie-policy .cookie-policy-msg p,
        #gm-cookie-policy .cookie-policy-msg table,
        #gm-cookie-policy .cookie-policy-msg table p {
            color: <?= esc_attr($this->colors['txt']) ?>;
        }
        #gm-cookie-policy a,
        #gm-cookie-policy .cookie-policy-msg a,
        #gm-cookie-policy .cookie-policy-msg table a {
            color: <?= esc_attr($this->colors['link']) ?>;
        }
        <?php
        $css = ob_get_clean();
        $context = array_merge($this->colors, $this->info);

        return apply_filters('cookie-policy.message-inline-css', $css, $context);
    }
}
