<?php
/*
Plugin Name: Automatic Email Testing With Telegram Alerts
Plugin URI: https://azbrand.ca/free-automatic-wordpress-email-testing-plugin-with-telegram-alerts/
Description: A plugin to send 6 hour emails and log results and will send an alert to Telegram if emails fail. Admins can send manual tests and receive Telegram notifications on failures.
Version: 1.7.19
Author URI: https://AZBrand.ca
Author: <a href="https://azbrand.ca" target="_blank">AZBrand.ca</a>
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Add custom cron schedule for every 6 hours
function aetwtaha4cca_custom_cron_schedules($schedules) {
    $schedules['aetwtaha4cca_every_six_hours'] = array(
        'interval' => 21600, // 6 hours in seconds
        'display'  => esc_html__('Every Six Hours', 'automatic-email-testing-with-telegram-alerts') 
    );
    return $schedules;
}
add_filter('cron_schedules', 'aetwtaha4cca_custom_cron_schedules');



// Schedule the email sending event
function aetwtaha4cca_schedule_email_event() {
    if (!wp_next_scheduled('aetwtaha4cca_send_email_event')) {
        wp_schedule_event(time(), 'every_six_hours', 'aetwtaha4cca_send_email_event');
    }
}
add_action('wp', 'aetwtaha4cca_schedule_email_event');

// Hook for sending the email
add_action('aetwtaha4cca_send_email_event', 'aetwtaha4cca_send_email');

function aetwtaha4cca_send_email() {
    $email_address = get_option('aetwtaha4cca_email_address');
    $subject = 'Scheduled Email';
    $message = 'This is a test email from your WordPress plugin.';
    
    // Attempt to send the email
    $mail_sent = wp_mail($email_address, $subject, $message);
    
    // Log the email result using WP_Filesystem
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
    }
    
    $log_entry = '[' . gmdate('Y-m-d H:i:s') . '] Email ' . $email_sent . ': ' . ($mail_sent ? 'Success' : 'Failure') . PHP_EOL;
    $log_file = plugin_dir_path(__FILE__) . 'emaillog.txt';
    $wp_filesystem->put_contents($log_file, $log_entry, FS_CHMOD_FILE);
    
    // Telegram notification on failure
    if (!$mail_sent) {
        $bot_id = get_option('aetwtaha4cca_telegram_bot_id');
        $chat_id = get_option('aetwtaha4cca_telegram_chat_id');
        $site_name = get_option('blogname'); // Get the site name
        $telegram_message = "Failed to send email from $site_name to $email_address";
        wp_remote_get("https://api.telegram.org/bot$bot_id/sendMessage?chat_id=$chat_id&text=" . urlencode($telegram_message));
    }
}
//atribution comments//
function aetwtaha4cca_add_custom_comment() {
    echo '<!-- Wordpress Automatic Email Testing With Telegram Alerts - AZBrand.ca -->';
    echo '<!-- Note: This plugin helps ensure your email functionality is working correctly and sends alerts via Telegram if any issues are detected. -->';
}
add_action('wp_head', 'aetwtaha4cca_add_custom_comment');

//atrib page
// Hook to run when the plugin is activated
register_activation_hook(__FILE__, 'aetwtaha4cca_create_attribution_page');

function aetwtaha4cca_create_attribution_page() {
    
    // Set a transient to indicate that the plugin has just been activated
    set_transient('aetwtaha4cca_email_scheduler_activated', true, 30);
}

// Hook to run on admin_init to redirect the user after plugin activation
add_action('admin_init', 'aetwtaha4cca_redirect_after_activation');

function aetwtaha4cca_redirect_after_activation() {
    // Check if the transient is set
    if (get_transient('aetwtaha4cca_email_scheduler_activated')) {
        // Delete the transient so it doesn't redirect again
        delete_transient('aetwtaha4cca_email_scheduler_activated');

        // Redirect to the settings page
        wp_redirect(admin_url('options-general.php?page=email-scheduler'));
        exit;
    }
}



// Add the settings page link to the plugin action links
function aetwtaha4cca_plugin_action_links($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=email-scheduler') . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'aetwtaha4cca_plugin_action_links');


// Create the settings page
function aetwtaha4cca_options_page() {
    if (isset($_POST['aetwtaha4cca_save_settings'])) {
        // Check nonce for security
        $nonce = isset($_POST['aetwtaha4cca_nonce']) ? sanitize_text_field(wp_unslash($_POST['aetwtaha4cca_nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'aetwtaha4cca_save_settings')) {
            die('Security check failed');
        }

        // Sanitize and save email, Telegram bot, and chat ID
        if (isset($_POST['aetwtaha4cca_email_address'])) {
            $email_address = sanitize_email(wp_unslash($_POST['aetwtaha4cca_email_address']));
            update_option('aetwtaha4cca_email_address', $email_address);
        }
        if (isset($_POST['aetwtaha4cca_telegram_bot_id'])) {
            $bot_id = sanitize_text_field(wp_unslash($_POST['aetwtaha4cca_telegram_bot_id']));
            update_option('aetwtaha4cca_telegram_bot_id', $bot_id);
        }
        if (isset($_POST['aetwtaha4cca_telegram_chat_id'])) {
            $chat_id = sanitize_text_field(wp_unslash($_POST['aetwtaha4cca_telegram_chat_id']));
            update_option('aetwtaha4cca_telegram_chat_id', $chat_id);
        }
        if (isset($_POST['aetwtaha4cca_start_time'])) {
            $start_time = sanitize_text_field(wp_unslash($_POST['aetwtaha4cca_start_time']));
            update_option('aetwtaha4cca_start_time', $start_time);
            
            // Clear any existing scheduled events before rescheduling
            $timestamp = wp_next_scheduled('aetwtaha4cca_send_email_event');
            if ($timestamp) {
                wp_unschedule_event($timestamp, 'aetwtaha4cca_send_email_event');
            }
            aetwtaha4cca_schedule_cron_job($start_time);
        }
    }

    // Manual test email handling
    if (isset($_POST['aetwtaha4cca_manual_test'])) {
        aetwtaha4cca_send_email();
        echo '<div class="notice notice-success is-dismissible"><p>Test email sent.</p></div>';
    }

    // Clear all options handling
    if (isset($_POST['aetwtaha4cca_clear_settings'])) {
        delete_option('aetwtaha4cca_email_address');
        delete_option('aetwtaha4cca_telegram_bot_id');
        delete_option('aetwtaha4cca_telegram_chat_id');
        delete_option('aetwtaha4cca_start_time');
        echo '<div class="notice notice-success is-dismissible"><p>All settings cleared.</p></div>';
    }

    // Read the log file contents using WP_Filesystem
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
    }

    $log_file_path = plugin_dir_path(__FILE__) . 'emaillog.txt';
    $log_contents = $wp_filesystem->get_contents($log_file_path) ? $wp_filesystem->get_contents($log_file_path) : 'Log file does not exist.';
    
    $email_address = get_option('aetwtaha4cca_email_address');
    $telegram_bot_id = get_option('aetwtaha4cca_telegram_bot_id');
    $telegram_chat_id = get_option('aetwtaha4cca_telegram_chat_id');
    $next_event = wp_next_scheduled('aetwtaha4cca_send_email_event');
    $next_event_time = $next_event ? gmdate('Y-m-d H:i:s', $next_event) : 'No scheduled email';
    $current_time = gmdate('Y-m-d H:i:s');

    ?>
    <div class="aetwtaha4cca-wrap">
        <h1>Email Scheduler Settings</h1>
        <p>This plugin allows you to schedule hourly emails and receive notifications via Telegram if any emails fail to send.</p>
        <h2>Instructions</h2>
        <ul>
            <li>Enter the email address you want to send emails to.</li>
            <li>Provide your Telegram Bot ID and Chat ID to receive notifications.</li>
            <li>Set the time when the email scheduling should start.</li>
            <li>Click "Save Settings" to apply your changes and schedule the email sending.</li>
            <li>Use the "Send Test Email" button to manually send a test email.</li>
            <li>Click "Clear All Settings" to reset all fields and remove scheduled events.</li>
        </ul>
        <p><strong>Note* If you have low Traffic Testing might not be exactly 6 hours due to limitations of WP-Cron the Advanced Python Script can fix this. at the GitHub Link below </strong></p>
        <p><strong>Its highly suggested to create an email filter to send all the test emails to the trash in your email</strong></p>
        <h2>Advanced Alerts</h2>
        <p>For more advanced 5-second alerts if anything fails, please visit 
            <a href="https://github.com/AZBrandCanada/WordPress-Automatic-Email-Testing-With-Telegram-Advanced-Alerts-Serverside" target="_blank">this GitHub link</a> and run the Python script as a service on your server for multiple websites.
        </p>
        <p>For Technical Support or Feature Requests Visit
            <a href="https://AZBrand.ca" target="_blank">AZBrand.ca</a> .
        </p>
       
        <form method="post" action="">
            <?php wp_nonce_field('aetwtaha4cca_save_settings', 'aetwtaha4cca_nonce'); ?>
            <table class="aetwtaha4cca-form-table">
                <tr valign="top">
                    <th scope="row">Email Address</th>
                    <td><input type="email" name="aetwtaha4cca_email_address" value="<?php echo esc_attr($email_address); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Telegram Bot ID</th>
                    <td><input type="text" name="aetwtaha4cca_telegram_bot_id" value="<?php echo esc_attr($telegram_bot_id); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Telegram Chat ID</th>
                    <td><input type="text" name="aetwtaha4cca_telegram_chat_id" value="<?php echo esc_attr($telegram_chat_id); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Start Time</th>
                    <td><input type="time" name="aetwtaha4cca_start_time" value="<?php echo esc_attr(gmdate('H:i', wp_next_scheduled('aetwtaha4cca_send_email_event'))); ?>" /></td>
                </tr>
            </table>
            <?php submit_button('Save Settings', 'primary', 'aetwtaha4cca_save_settings'); ?>
            <?php submit_button('Send Test Email', 'secondary', 'aetwtaha4cca_manual_test'); ?>
            <?php submit_button('Clear All Settings', 'secondary', 'aetwtaha4cca_clear_settings'); ?>
        </form>
        
        <h2>Current UTC Time: <span id="aetwtaha4cca-current-server-time"><?php echo esc_html($current_time); ?></span></h2>
        <h2>Next Email Send (UTC): <span id="aetwtaha4cca-next-email-send"><?php echo esc_html($next_event_time); ?></span></h2>
        <h2>Time Until Next Send: <span id="aetwtaha4cca-countdown-timer">Calculating...</span></h2>
        
        <h2>Email Log</h2>
        <pre><?php echo esc_html($log_contents); ?></pre>
    </div>
    <?php
}

// Add the options page to the menu
function aetwtaha4cca_admin_menu() {
    add_options_page(
        'Email Scheduler Settings',
        'Email Scheduler',
        'manage_options',
        'email-scheduler',
        'aetwtaha4cca_options_page'
    );
}
add_action('admin_menu', 'aetwtaha4cca_admin_menu');

// Enqueue JavaScript
function aetwtaha4cca_enqueue_scripts($hook) {
    // Only enqueue on the settings page
    if ($hook != 'settings_page_email-scheduler') {
        return;
    }

    // Register the script
    wp_register_script(
        'email-scheduler-js', // Handle
        plugin_dir_url(__FILE__) . 'assets/js/email-scheduler.js', // Path to your JS file
        array(), // Dependencies (if any)
        '1.0.0', // Version number
        true // Load in footer
    );

    // Fetch the next event time from the database
    $next_event_time = get_option('next_event_time'); // Ensure this option exists

    // Prepare the localized data
    $next_event_time_js = $next_event_time ? esc_js(gmdate('c', strtotime($next_event_time))) : '';

    // Localize script to pass PHP variables to JavaScript
    wp_localize_script('email-scheduler-js', 'nextEventData', array(
        'nextEventTime' => $next_event_time_js,
    ));

    // Enqueue the script
    wp_enqueue_script('email-scheduler-js');
}
add_action('admin_enqueue_scripts', 'aetwtaha4cca_enqueue_scripts');


// Define the cron job function
function aetwtaha4cca_schedule_cron_job($start_time) {
    $timestamp = strtotime($start_time . ' UTC');
    wp_schedule_event($timestamp, 'aetwtaha4cca_every_six_hours', 'aetwtaha4cca_send_email_event');
}

