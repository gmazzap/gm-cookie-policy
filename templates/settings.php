<div class="wrap">
    <h1><?= esc_html($this->title) ?></h1>
    <form method="post" action="<?= esc_url($this->actionUrl) ?>">

        <input type="hidden" name="action" value="<?= esc_attr($this->actionName) ?>">
        <input type="hidden" name="<?= esc_attr($this->nonceName) ?>"
               value="<?= esc_attr($this->nonceValue) ?>">

        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><?= esc_html($this->labels['message']) ?></th>
                <td>
                    <?php wp_editor($this->message, $this->editorId, $this->editorArgs) ?>
                    <p class="description">
                        <?= esc_html_x('Shortcodes allowed.', 'form label', 'gm-cookie-policy') ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?= esc_html($this->labels['show-on']) ?></th>
                <td>
                    <label for="show-on-header">
                        <?= esc_html($this->labels['header']) ?>
                        <input<?= $this->values['show-on'] === 'header' ? ' checked' : ''; ?>
                            type="radio" value="header"
                            id="show-on-header"
                            name="show-on">
                    </label>

                    <label for="show-on-footer">
                        <?= esc_html($this->labels['footer']) ?>
                        <input<?= $this->values['show-on'] === 'footer' ? ' checked' : ''; ?>
                            type="radio" value="footer"
                            id="show-on-footer"
                            name="show-on">
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="close-label"><?= esc_html($this->labels['close-label']) ?></label>
                </th>
                <td>
                    <input
                        type="text"
                        class="text-regular"
                        name="close-label"
                        id="close-label"
                        value="<?= esc_attr($this->values['close-label']) ?>">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="more-url"><?= esc_html($this->labels['more-url']) ?></label>
                </th>
                <td>
                    <input
                        type="text"
                        class="large-text"
                        name="more-url"
                        id="more-url"
                        placeholder="<?= is_ssl() ? 'https://' : 'http://' ?>"
                        value="<?= esc_url($this->values['more-url']) ?>">
                    <p class="description">
                        <?= esc_html_x(
                            'Leave empty for no "Read More" link.',
                            'form desc',
                            'gm-cookie-policy'
                        ) ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="more-label"><?= esc_html($this->labels['more-label']) ?></label>
                </th>
                <td>
                    <input
                        type="text"
                        class="large-text"
                        name="more-label"
                        id="more-label"
                        value="<?= esc_attr($this->values['more-label']) ?>">
                    <p class="description">
                        <?= esc_html_x(
                            'Leave empty for no "Read More" link.',
                            'form desc',
                            'gm-cookie-policy'
                        ) ?>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>

        <hr>

        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><?= esc_html($this->labels['use-style']) ?></th>
                <td>
                    <label for="use-style-yes">
                        <?= esc_html__('Yes') ?>
                        <input<?= $this->values['use-style'] === 'yes' ? ' checked' : ''; ?>
                            type="radio" value="yes"
                            id="use-style-yes"
                            name="use-style">
                    </label>

                    <label for="use-style-no">
                        <?= esc_html__('No') ?>
                        <input<?= $this->values['use-style'] === 'no' ? ' checked' : ''; ?>
                            type="radio" value="no"
                            id="use-style-no"
                            name="use-style">
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row"><?= esc_html($this->labels['use-script']) ?></th>
                <td>
                    <label for="use-script-yes">
                        <?= esc_html__('Yes') ?>
                        <input<?= $this->values['use-script'] === 'yes' ? ' checked' : ''; ?>
                            type="radio" value="yes"
                            id="use-script-yes"
                            name="use-script">
                    </label>

                    <label for="use-style-no">
                        <?= esc_html__('No') ?>
                        <input<?= $this->values['use-script'] === 'no' ? ' checked' : ''; ?>
                            type="radio" value="no"
                            id="use-script-no"
                            name="use-script">
                    </label>
                </td>
            </tr>
            </tbody>
        </table>

        <hr>

        <table class="form-table cookie-policy-colors-settings">
            <thead>
            <tr>
                <th scope="col" colspan="2">
                    <?= esc_html_x('Colors Settings', 'settings title', 'gm-cookie-policy') ?>
                    <p class="description">
                        <?= esc_html_x(
                            'Ignored if "Use Styles" is disabled above.',
                            'settings desc',
                            'gm-cookie-policy'
                        ) ?>
                    </p>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">
                    <label for="txt-color"><?= esc_html($this->labels['txt-color']) ?></label>
                </th>
                <td>
                    <input
                        type="text"
                        name="txt-color"
                        id="txt-color"
                        value="<?= esc_attr($this->values['txt-color']) ?>"
                        data-default-color="<?= esc_attr($this->values['txt-color']) ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="bg-color"><?= esc_html($this->labels['bg-color']) ?></label>
                </th>
                <td>
                    <input
                        type="text"
                        name="bg-color"
                        id="bg-color"
                        value="<?= esc_attr($this->values['bg-color']) ?>"
                        data-default-color="<?= esc_attr($this->values['bg-color']) ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="link-color"><?= esc_html($this->labels['link-color']) ?></label>
                </th>
                <td>
                    <input
                        type="text"
                        name="link-color"
                        id="link-color"
                        value="<?= esc_attr($this->values['link-color']) ?>"
                        data-default-color="<?= esc_attr($this->values['link-color']) ?>">
                </td>
            </tr>
            </tbody>
        </table>

        <?php submit_button() ?>
    </form>
</div>
<script>
    jQuery(document).ready(function ($) {
        $('.cookie-policy-colors-settings input').wpColorPicker();
    });
</script>
