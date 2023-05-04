<?php

class CF7_ChatGPT_Analytics
{
    public function __construct()
    {
        // Initialize the analytics class
            add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
            add_action('admin_menu', array($this, 'add_reports_page'));
            add_action('admin_init', array($this, 'handle_export_data'));
    }

    // Additional methods for tracking and reporting metrics will be added here.
    public function track_auto_reply_sent($form_id, $response_data)
{
    // Get the current timestamp
    $timestamp = time();

    // Record the auto-reply sent event in the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'cf7_chatgpt_analytics';
    $data = array(
        'event' => 'auto_reply_sent',
        'form_id' => $form_id,
        'response_data' => json_encode($response_data),
        'timestamp' => $timestamp
    );
    $format = array('%s', '%d', '%s', '%d');
    $wpdb->insert($table_name, $data, $format);
}
public function add_dashboard_widget()
{
    wp_add_dashboard_widget(
        'cf7_chatgpt_dashboard_widget',
        __('ChatGPT Auto-Reply Metrics', 'cf7-chatgpt-integration'),
        array($this, 'render_dashboard_widget')
    );
}
public function render_dashboard_widget()
{
    // Retrieve the key metrics from the database
    $total_auto_replies_sent = $this->get_total_auto_replies_sent();
    $average_response_time = $this->get_average_response_time();
    $total_link_clicks = $this->get_total_link_clicks();

    // Render the widget content
    echo '<p>';
    echo '<strong>' . __('Total Auto-Replies Sent:', 'cf7-chatgpt-integration') . '</strong> ' . $total_auto_replies_sent;
    echo '</p>';
    echo '<p>';
    echo '<strong>' . __('Average Response Time:', 'cf7-chatgpt-integration') . '</strong> ' . $average_response_time . ' ' . __('seconds', 'cf7-chatgpt-integration');
    echo '</p>';
    echo '<p>';
    echo '<strong>' . __('Total Link Clicks:', 'cf7-chatgpt-integration') . '</strong> ' . $total_link_clicks;
    echo '</p>';
}
public function export_data()
{
    // Export logic will go here.
    $data = $this->get_analytics_data();
    $filename = 'cf7-chatgpt-analytics-' . time() . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $csv_file = fopen('php://output', 'w');
    fputcsv($csv_file, array_keys($data[0])); // Write the header row.

    foreach ($data as $row) {
        fputcsv($csv_file, $row);
    }

    fclose($csv_file);
    exit;

}
    public function render_reports_page()
{
    ?>
    <div class="wrap">
        <h1><?php _e('ChatGPT Auto-Reply Reports', 'cf7-chatgpt-integration'); ?></h1>
        <form method="post" action="">
            <input type="hidden" name="cf7_chatgpt_export_data" value="1">
            <input type="submit" value="<?php _e('Export Analytics Data', 'cf7-chatgpt-integration'); ?>" class="button button-primary">
        </form>
    </div>
    <?php
}
public function handle_export_data()
{
    if (isset($_POST['cf7_chatgpt_export_data']) && current_user_can('manage_options')) {
        $cf7_chatgpt_analytics = new CF7_ChatGPT_Analytics();
        $cf7_chatgpt_analytics->export_data();
    }
}


}
