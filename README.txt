=== WeRePack - Reuse Packaging for WooCommerce ===
Contributors: werepack, philippmuenchen
Donate link: https://www.paypal.me/ouun
Tags: woocommerce, shipping, recycle, reuse, sustainable
Requires at least: 4.0.0
Tested up to: 6.7
Stable tag: 1.4.6
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Request customer permission to use reused packaging. Join WeRePack.org to support sustainable e-commerce, reduce waste, and save money.

== Description ==

This plugin is part of the WeRePack.org initiative to avoid packaging waste and helps shop owners and their customers save resources.
To do this, the plugin adds a field in the WooCommerce checkout process where customers can give their consent to receive reused shipping packaging. So simple, so effective. And with lots of customization options via filters, let us know if there's anything else you need.

**Current features**

*   Checkbox in payment process with subtle animation, the position can be changed
*   Reward with a coupon: Shop owners can create a coupon code to encourage customers and share the savings. To do this, simply create an ordinary WooCommerce voucher called 'WeRePack'.
*   Registered users can set their default value in the WooCommerce Dashboard.
*   Shortcodes `[repack]` & `[repack_summary]` to show your support and savings.
*   Clean code, no ads & no annoying notifications
*   Highly customizable via Hooks & Filters & since v1.2.0 via Settings Page
*   Optionally share basic stats, help us improve and get listed on WeRePack.org as a supporter site.
*   Available translations: English, German & German (formal)

**Coming soon**

*  Gutenberg Block additionally to Shortcode, which visually represents the savings to date.

**Shortcodes**

You can use the following shortcodes and functions to show your savings:

***Shortcode `[repack]`***

Display the savings of your site or individual users such as: Amount of reused packages, water saved, CO2 saved and mature trees saved. The shortcode attributes are:

* `type=""`: What amount to display: "packaging" (default), "co2", "water" or "trees"
* `value=""`: Set to `true` if you only want to display the number/quantity without unit (e.g. "litres of water")
* `packages=""`: Lets you overwrite the number of packages the output is calculated with. Leave empty to get your sites counter.
* `user_id=""`: User ID of whom you want to display the saving: Leave blank to use the sites total saving instead.
* `prepend=""`: HTML to prepend to output. Default: Empty string
* `append=""`:  HTML to append to output. Default: Empty string

***Shortcode `[repack_summary]`***

Displays a summary of you sites savings. You can copy and overwrite the template file from plugins folder `/public/templates/summary.php` to your themes folder `/repack/summary.php`.

* `packages=""`: Lets you overwrite the number of packages the output is calculated with. Leave empty to use your sites counter.
* `prepend=""`: HTML to prepend to output. Default: Empty string
* `append=""`:  HTML to append to output. Default: Empty string

**Settings Page**

Adjust the plugin to your liking on the settings page: `WooCommerce Settings -> Shipping -> WeRePack Settings`. If you miss an option use one of the following filters or let us know.

**Available Filters**

You can customize the plugin behavior and text by using the following filters in your themes `functions.php` or `/mu-plugins/wc-repack.php` file:

*   `repack_checkout_consent_position`: Position of the consent, default is `woocommerce_after_order_notes`.
*   `repack_coupon_name`: Name of the coupon code to apply. Default is `WeRePack`.
*   `repack_consent_field_label`: Label of the consent checkbox.
*   `repack_consent_field_description`: Description of the consent checkbox.
*   `repack_consent_field_args`: Arguments of the `woocommerce_form_field()` function to add the consent checkbox.
*   `repack_consent_field_firework`: Set to `false` to deactivate the checkbox animation on consent.
*   `repack_coupon_applied_notice_text`: Notice text after coupon was applied.
*   `repack_coupon_removed_notice_text`: Notice text after coupon was removed.
*   `repack_email_label`: Label in WooCommerce mails if consent was given.
*   `repack_email_text`: Text in WooCommerce mails if consent was given.
*   `repack_deactivate_remove_all_meta`: Set to `true` to delete all plugin related metadata on deactivation.
*   `repack_template_summary_data`: `$data` object passed to summary.php template.
*   `repack_template_summary_saving`: `$saving` object passed to summary.php template.

**Missing something?**

Write us what else is needed to make your shop more sustainable. Whatever it is, we will do our best to get as many shops as possible to join the initiative.

== Installation ==

Install the plugin either via WordPress or as a composer package via `composer require werepack/repack-for-woocommerce`

== Frequently Asked Questions ==

= Am I forced to give a discount? =

No! You are not! But you can show your good will and that you are not taking part to maximize profit, but to support the initiative and environment.

= How to set up and customize a coupon code =

The plugin gives you maximum flexibility and accepts all WooCommerce coupons that can be applied to the shopping cart. Just create a new coupon named 'WeRePack', it will be added or removed automatically when you toggle the checkout checkbox. If you want to rename the coupon, add the filter `add_filter( 'repack_coupon_name', 'MyCouponName' )` to your `functions.php`.

= I like the initiative and want to support you =

Yes, please! We need every heart, hand and mouth. Talk about us, help us improve the code [on GitHub](https://github.com/werepack/repack-for-woocommerce "WeRePack on GitHub"), translate the plugin. We really appreciate every support.

= My language is missing. How to contribute it? =

We are so happy and thankful that you support the initiative with your contribution! Since version 1.1.2 we use the official WordPress Translation System on [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/repack-for-woocommerce/ "translate.wordpress.org").
If it is the first time that you contribute translations, please have a short look at [the First Steps](https://make.wordpress.org/polyglots/handbook/translating/first-steps/#contributing-your-first-translations "First Steps").
Otherwise, just start to translate on [translate.wordpress.org/projects/wp-plugins/repack-for-woocommerce](https://translate.wordpress.org/projects/wp-plugins/repack-for-woocommerce/ "translate.wordpress.org").

== Screenshots ==

1. Checkbox animation, adapts to your Theme Design
2. Shipping notice in WooCommerce Order Overview
3. Optionally integrates with WooCommerce Coupons
4. Optionally share your savings and ...
5. ... get listed as Community Site on WeRePack.org

== Changelog ==

= 1.4.6 =
* Update supported WordPress & WooCommerce versions

= 1.4.5 =
* Update supported WordPress & WooCommerce versions

= 1.4.4 =
* Update supported WordPress & WooCommerce versions

= 1.4.3 =
* Enh: Tested with WooCommerce 6.8.x
* Enh: Improves PHP8 support
* Enh: Various small improvements
* Fix: Error notice if WooCommerce is deactivated
* Fix: Extend API call timeout

= 1.4.2 =
* Fix: In some edge cases (e.g. deleting orders) the WeRePack ratio can become >100%

= 1.4.1 =
* Enh: Dashboard Settings: Improved Notifications

= 1.4.0 =
* Enh: Tested with WooCommerce 6.3.x
* Fix: WeRePack Community Updates by...
* Fix: Switch to REST API

= 1.3.0 =
* Enh: Tested with WooCommerce 6.0
* Enh: Option to disable checkbox animation [#5](https://github.com/WeRePack/repack-for-woocommerce/issues/5)
* Enh: Hide Remove Option for WeRepack Coupons in Cart [#4](https://github.com/WeRePack/repack-for-woocommerce/issues/4)

= 1.2.0 =
* Enh: Settings Page in WooCommerce Settings -> Shipping -> WeRePack Settings
* Enh: Join the WeRePack.org Community from Settings Page (or revoke your consent)
* Enh: Manual Sync from Settings Page
* Enh: Display hint in order preview if consent is given
* Fix: Correct last data submission
* Fix: Various small improvements

= 1.1.8 =
* Enh: Rename plugin to WeRePack to inline with initiatives name

= 1.1.7 =
* Enh: Schedule WordPress Cron for Telemetry

= 1.1.6 =
* Fix: Translation files missing strings

= 1.1.5 =
* Enh: WooCommerce Tested 5.2
* Enh: Add start date to summary template
* Enh: Adds availability of a coupon code to telemetry module
* Fix: Next packaging counter

= 1.1.4 =
* Various small improvements

= 1.1.3 =
* Rename translation files to fit text domain

= 1.1.2 =
* Switch text-domain to support WP.org GlotPress Translations at [translate.wordpress.org/projects/wp-plugins/repack-for-woocommerce](https://translate.wordpress.org/projects/wp-plugins/repack-for-woocommerce/ "Translate WeRePack Plugin")

= 1.1.1 =
* Update Translations: English, German (formal) & German (informal)

= 1.1.0 =
* Various improvements
* Adds summary shortcode and template. You can copy and overwrite it in your theme from plugins folder `/public/templates/summary.php` to your themes folder `/repack/summary.php`
* Adds Telemetry Module: We want to win you as a supporter and measure our joint success. To do this, you can share some stats with us in order to get listed in the supporter directory on WeRePack.org.

= 1.0.6 =
* Adds filter `repack_deactivate_remove_all_meta` which when true removes all plugin related data from the DB on plugin deactivation.

= 1.0.5 =
* WordPress.org Repo: Fix version.

= 1.0.4 =
* Adds filter `repack_checkout_consent_position`: Allows changing the consent position.
* Adds filter `repack_checkout_consent_default_state`: Allows changing the consent default value.
* Fixes user repack counter

= 1.0.3 =
* Minor bug fix.

= 1.0.2 =
* Fixing version numbers.

= 1.0.1 =
* Fix overseen PHP error calling $order->id directly.
* Adds all available filters to Readme.

= 1.0.0 =
* Hello World!
* Contains all features as explained in Readme.

== About WeRePack.org Initiative ==

Learn more about the initiative on [WeRePack.org](https://werepack.org/ "The WeRePack Initiative") and join us.
