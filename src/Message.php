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
    public function render()
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
