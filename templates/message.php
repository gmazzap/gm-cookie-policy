<div id="gm-cookie-policy" class="cookie-policy-<?= sanitize_html_class($this->showOn) ?>">

    <?php do_action('cookie-policy.before-message') ?>

    <div class="cookie-policy-msg">
        <table border="0" width="100%">

            <tr>
                <td colspan="<?= apply_filters('cookie-policy.message-col-colspan', 2) ?>">
                    <?= $this->message ?>
                </td>
            </tr>

            <tr>
                <td width="<?= apply_filters('cookie-policy.message-more-link-width', '25%') ?>">
                    <?php if ($this->moreUrl && $this->moreLabel) : ?>
                        <a id="cookie-policy-more" href="<?= esc_url($this->moreUrl) ?>">
                            <?= esc_html($this->moreLabel) ?>
                        </a>
                    <?php endif ?>
                </td>
                <td>
                    <a id="cookie-policy-close"
                       href="<?= esc_url($this->closeUrl) ?>"
                       data-cookie-name="<?= esc_attr($this->cookieName) ?>"
                       data-cookie-expire="<?= esc_attr($this->cookieExpire) ?>">
                        <?= esc_html($this->closeLabel) ?>
                    </a>
                </td>
            </tr>

        </table>
    </div>

    <?php do_action('cookie-policy.after-message') ?>

</div>
