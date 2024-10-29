=== Automatic Email Testing With Telegram Alerts ===
Contributors: AZBrand
Donate link: https://buy.stripe.com/aEU038cjmgaSeL64gw
Tags: email, email logs, email monitoring, automated emails, telegram
Requires at least: 5.0
Tested up to: 6.6.2
Requires PHP: 7.4
Stable tag: 1.7.19
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Receive Telegram alerts for email service failures and log results. Admins can send manual tests and get instant notifications 100% FREE.
== Description ==

Automatic Email Testing With Telegram Alerts is a WordPress plugin designed to help you schedule emails every 6 hours, log their success or failure, and receive instant notifications via Telegram if any emails fail to send.
This Plugin is 100% Free with No Paywalls.

https://youtu.be/snSvTh4XhG0

Features:
– Schedule 6 hour emails.
– Log email results to a file.
– Send test emails manually.
– Receive Telegram notifications on email failures.

== Installation ==
1. Upload the zip file to `/wp-content/plugins/` directory and unzip it.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the settings by navigating to `Settings` > `Email Scheduler`.
4. Set the Email Address to send test emails to.
5. Enter your Telegram Bot ID (Found With Botfather Bot on Telegram).
6. Enter your Telegram Chat ID (Can be a group or private chat).
7. Set the Time Ahead of the Current Time when you want it to start testing every 6 hours.
8. Click Save.
9. Click Send Email to test; you should see success in the logs.
10. Test the Telegram connection:
    1. Temporarily disable your SMTP plugin.
    2. Try the test again; it should send an alert to your Telegram.
    3. If it doesn’t, ensure your Telegram Bot ID and Chat ID are correct.
11. (Optional Redundancy) To monitor the log file and ensure it is less than 8 hours old, run this python script as a service on a server or computer(Doesn't have to be the same server as your website) it will periodically test the log file and will send you status updates 1 time per day to telegram for all the websites you're monitoring. if a log is older than 8 hours, it will send you an alert every 10 minutes until you fix it.... [Advanced Alerts Script](https://github.com/AZBrandCanada/WordPress-Automatic-Email-Testing-With-Telegram-Advanced-Alerts-Serverside). This can be used for multiple websites; check the README on GitHub.


== ⭐ Third-Party API Usage ⭐ ==

This plugin uses the Telegram API (api.telegram.org) to send notifications. For more information on their API, please visit their [official documentation](https://core.telegram.org/bots/api).

== Screenshots ==

1. Settings Page
2. Notification on Failure

== Frequently Asked Questions ==

= How do I configure the plugin? =

Go to `Settings` > `Email Scheduler` and enter the email address, Telegram Bot ID, and Chat ID.

= What happens if an email fails to send? =

You will receive a Telegram notification detailing the failure.

= Can I send a test email? =

Yes, you can send a test email from the plugin settings page.

= Can I send a test the Telegram Connection? =

To test the Telegram Connection you have to temporarily disable your smtp plugin, then click Send an Email on the admin page.(future update will have a telegram test button)

= Will This Contantly Send Me Annoying Alerts? =

No, it will only send you telegram alerts if your E-Mail fails to send.

== Changelog ==

= 1.7.19 =
* Updated JavaScript to correctly calculate the time difference between current UTC time and next email send time.
* Added a clear indication when an email is currently being sent.
* Improved the display format of current time and countdown timer.
* Added custom classes for HTML and JavaScript elements.

= 1.7.18 =
* Improved internationalization: avoided using variables or defines as text, context, or text domain parameters.
* Documented use of third-party API (api.telegram.org).
* Updated to use `wp_enqueue` commands for better script management.
* Created unique prefixes for generic function/class/define/namespace/option names.
* Updated transient prefixes.

= 1.7.17 =
* Added new functionality to handle edge cases in email scheduling.
* Improved error logging for better diagnostics.
* Enhanced security features to protect against potential vulnerabilities.

= 1.7.16 =
* Updated the license information to include CC BY-ND 4.0 details.

= 1.7.15 =
* Removed email from the public log.

= 1.7.14 =
* Added nonce verification to the form for improved security.
* Sanitized user input to prevent potential security issues.
* Updated UTC time handling for proper log file calculations.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.7.19 =
This update includes JavaScript improvements for time calculations, better display formats, and the addition of custom classes for HTML and JavaScript elements.

= 1.7.18 =
This update includes improvements to internationalization, documentation of third-party API usage, script management, and security enhancements.

== Donate Link ==

If you find this plugin useful, consider donating to support its development [here](https://buy.stripe.com/aEU038cjmgaSeL64gw).
