EU Cookie Policy
================

A simple plugin to show a message for compliance with EU cookie law.

Support Composer, but does not require it. Requires PHP 5.4+.

Mainly developer-oriented, for basic needs can also be used by non-devs: basic configuration are 
available via UI, but advanced customization only via hooks.

## Why yet another "cookie policy" plugin?

There're a few plugins that aim to do same thing, but I needed a plugin that:

- supports Composer natively
- works with javascript disabled
- provides a PHP API to be informed if current user accepted the policy
- provides a javascript API to be informed if current user accepted the policy, and also capable
  to notify other scripts in the moment the user accepts the policy
- doesn't do too much
- doesn't have dozen of options
- is not too "promotional"

And I could not found one that meets all the above requirements. So I wrote it.

## Usage

After activation, a sub-menu will be added under the "Tools" main menu.

In the setting page accessible from there is possible to set the cookie policy notice message and
few other basic options.

Many other customizations can be done via hooks.

## PHP API

When the plugin is active you can be informed if user accepted the policy via a filter:

```php
$accepted = apply_filters('cookie-policy-accepted', false);
```

This way, anything that make use of "profiling cookies" can be disabled / not loaded when `$accepted`
is false, fully applying EU law.

Since it is a filter, it does not lock-in to the plugin. Even if the plugin is disabled any code
that relies on the filter above will not crash if plugin is not there.

However, PHP can reload page components (e.g. ads) when the user clicks the link to accept the
policy (at least not without making another request), this is why there's also a javascript API.

## javascript API

Making it short, the variable `window.cookiePolicy.accepted` is a boolean that is set to `true` when
the user accepted the policy.

There are two custom events that scripts can listen to check that variable and acting accordingly.

The first event is `cookie-policy-loaded`, it happens right after the document is fully loaded.

```js
jQuery(document).on('cookie-policy-loaded', function() {
    var cookiePolicy = window.cookiePolicy || { accepted: false };
    if (cookiePolicy.accepted) {
      // page just loaded, this is a returning user that already accepted policy before
    } else {
      // this user has not accepted policy yet, they might be just landed to website
    }
});
```

The other event is `cookie-policy-accepted`, that is triggered after the user accepted the policy:

```js
jQuery(document).on('cookie-policy-accepted', function() {
    var cookiePolicy = window.cookiePolicy || { accepted: false };
    if (cookiePolicy.accepted) {
      // user just accepted policy, excellent, release the hounds :)
    } else {
      // this should never happen, if it does, something went wrong with javascript
    }
});
```

Just like PHP API, neither javascript API lock-in to the plugin. It is possible to disable the plugin
without breaking anything and also replace the plugin with some other script that may trigger same
events and set same variable for 100% backward compatibility.

## Translations

The plugin comes with some translation:

- Italian
- Romanian (by @rmdiaconu)

PRs with more translations are welcome.

## License

MIT.
