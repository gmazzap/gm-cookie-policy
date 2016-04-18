<div id="gm-cookie-policy" class="cookie-policy-<?= sanitize_html_class($this->showOn) ?>">

    <?php do_action('cookie-policy.before-message') ?>

    <div class="cookie-policy-msg">

        <?php do_action('cookie-policy.before-message-table') ?>

        <table border="0" width="100%">

            <?php do_action('cookie-policy.before-message-row') ?>

            <tr>

                <?php do_action('cookie-policy.before-message-col') ?>

                <td colspan="<?= apply_filters('cookie-policy.message-col-colspan', 2) ?>">
                    <?= $this->message ?>
                </td>

                <?php do_action('cookie-policy.after-message-col') ?>

            </tr>

            <?php do_action('cookie-policy.after-message-row') ?>

            <tr>

                <?php do_action('cookie-policy.before-message-links-cols') ?>

                <td width="<?= apply_filters('cookie-policy.message-more-link-width', '25%') ?>">
                    <?php if ($this->moreUrl && $this->moreLabel) : ?>
                        <a id="cookie-policy-more" href="<?= esc_url($this->moreUrl) ?>">
                            <?= esc_html($this->moreLabel) ?>
                        </a>
                    <?php endif ?>
                </td>

                <?php do_action('cookie-policy.before-message-close-link-col') ?>

                <td>
                    <a id="cookie-policy-close"
                       href="<?= esc_url($this->closeUrl) ?>"
                       data-cookie-name="<?= esc_attr($this->cookieName) ?>"
                       data-cookie-expire="<?= esc_attr($this->cookieExpire) ?>">
                        <?= esc_html($this->closeLabel) ?>
                    </a>
                </td>

                <?php do_action('cookie-policy.after-message-links-cols') ?>

            </tr>

            <?php do_action('cookie-policy.after-links-row') ?>

        </table>

        <?php do_action('cookie-policy.after-message-table') ?>

    </div>

    <?php do_action('cookie-policy.after-message') ?>

</div>
