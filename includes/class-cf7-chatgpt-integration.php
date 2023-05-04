<?php
require_once plugin_dir_path(__FILE__) . 'class-cf7-chatgpt-analytics.php';

class CF7_ChatGPT_Integration {

    public function __construct() {
        add_action('wpcf7_before_send_mail', array($this, 'handle_form_submission'));
        add_filter('wpcf7_form_elements', array($this, 'add_gdpr_consent_checkbox'), 10, 2);
          add_action('wpcf7_init', array($this, 'register_cf7_chatgpt_shortcode'));
    add_action('wpcf7_after_save', array($this, 'save_form_meta_data'));
    add_action('wpcf7_add_meta_boxes', array($this, 'add_chatgpt_meta_box'));
  

    }
public function add_chatgpt_meta_box() {
    add_meta_box(
        'cf7-chatgpt-enable', // Meta box ID
        __('ChatGPT Auto-Replies', 'cf7-chatgpt-integration'), // Meta box title
        array($this, 'render_chatgpt_meta_box'), // Callback function to render the meta box
        null, // Screen (null for Contact Form 7)
        'form', // Context
        'core' // Priority
    );
}

public function render_chatgpt_meta_box($post) {
    $is_enabled = $this->is_form_enabled($post->id());
    ?>
    <label for="cf7-chatgpt-enabled">
        <input type="checkbox" name="cf7-chatgpt-enabled" id="cf7-chatgpt-enabled" value="1" <?php checked($is_enabled); ?>>
        <?php _e('Enable AI auto-replies for this form', 'cf7-chatgpt-integration'); ?>
    </label>
    <?php
}

public function save_form_meta_data($cf7) {
    $form_id = $cf7->id();
    $is_enabled = isset($_POST['cf7-chatgpt-enabled']) ? '1' : '0';
    update_post_meta($form_id, 'cf7_chatgpt_enabled', $is_enabled);
}
private function is_form_enabled($form_id) {
    $is_enabled = get_post_meta($form_id, 'cf7_chatgpt_enabled', true);
    return ($is_enabled === '1');
}


    public function handle_form_submission($cf7)
{
    // Check if the current form is enabled for AI auto-replies (replace with actual method to check)
    if (!$this->is_form_enabled($cf7->id())) {
        return;
    }

    // Instantiate the classes
    $chatgpt_api = new CF7_ChatGPT_API();
    $response_template_manager = new CF7_ChatGPT_Templates();

    // Evaluate conditional logic rules, get the template ID, and fetch the template (replace with actual methods)
    // ...

    // Extract form data and map it to ChatGPT API parameters
    $chatgpt_api_params = $this->map_form_data_to_api_params($form_data);

    // Send the extracted form data to the ChatGPT API
    $ai_response = $chatgpt_api->send_request($chatgpt_api_params);

    // Process the AI-generated response and apply the appropriate template
    $auto_reply_content = $response_template_manager->apply_template($ai_response, $template);

    // Replace placeholders in the AI response template with actual data
    $auto_reply_content = $this->replace_placeholders($auto_reply_content, $form_data);

    // Send the auto-reply email to the user
    $this->send_auto_reply_email($cf7, $form_data, $auto_reply_content);
         // Extract form data
    $form_data = $this->extract_form_data($cf7);

    // Map form data to ChatGPT API parameters
    $chatgpt_api_params = $this->map_form_data_to_api_params($form_data);
// Extract form data
    $form_data = $this->extract_form_data($cf7);
    // Process the AI-generated response and apply the appropriate template
    $formatted_response = $this->process_ai_response($chatgpt_response);

    // Prepare the email data
    $email_data = array(
        'to' => $chatgpt_response['form_data']['email'],
        'subject' => 'Auto-Reply: ' . $cf7->title,
        'message' => $formatted_response,
        'from_name' => get_bloginfo('name'),
        'from_email' => get_option('admin_email'),
        'reply_to' => get_option('admin_email')
    );

    // Send the auto-reply email
    $this->send_auto_reply_email($email_data);
    // Map form data to ChatGPT API parameters
    $chatgpt_api_params = $this->map_form_data_to_api_params($form_data);

    // Instantiate the CF7_ChatGPT_API class
    $chatgpt_api = new CF7_ChatGPT_API();
 // Send the auto-reply email
    $this->send_auto_reply_email($email_data);

    // Track the auto-reply sent event
    global $cf7_chatgpt_analytics;
    $cf7_chatgpt_analytics->track_auto_reply_sent($cf7->id(), $chatgpt_response);
    // Send the form data to the ChatGPT API
    try {
        $chatgpt_response = $chatgpt_api->send_request($chatgpt_api_params);
    } catch (Exception $e) {
        $chatgpt_api->process_api_errors($e);
        return;
    }
        
}
private function extract_form_data($cf7)
{
    $submission = WPCF7_Submission::get_instance();
    if (!$submission) {
        return array();
    }

    $posted_data = $submission->get_posted_data();
    if (empty($posted_data)) {
        return array();
    }

    // Extract the necessary form data and return it as an associative array
    $form_data = array(
        'email' => isset($posted_data['your-email']) ? $posted_data['your-email'] : '',
        'name' => isset($posted_data['your-name']) ? $posted_data['your-name'] : '',
        'message' => isset($posted_data['your-message']) ? $posted_data['your-message'] : '',
    );

    return $form_data;
}
private function map_form_data_to_api_params($form_data)
{
    // Map the form data to the appropriate ChatGPT API parameters
    $api_params = array(
        'email' => $form_data['email'],
        'name' => $form_data['name'],
        'message' => $form_data['message'],
    );

    return $api_params;
}
private function process_ai_response($ai_response)
{
    // Instantiate the CF7_ChatGPT_Templates class
    $chatgpt_templates = new CF7_ChatGPT_Templates();

    // Apply the appropriate AI response template
    $formatted_response = $chatgpt_templates->apply_template($ai_response);

    return $formatted_response;
}
private function send_auto_reply_email($email, $subject, $message) 
{
    $email_subject = get_option('cf7_chatgpt_email_subject', __('Your AI-generated response', 'cf7-chatgpt-integration'));
    $sender_name = get_option('cf7_chatgpt_sender_name', get_bloginfo('name'));
    $reply_to_address = get_option('cf7_chatgpt_reply_to_address', get_option('admin_email'));

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        "From: {$sender_name} <{$reply_to_address}>",
        "Reply-To: {$reply_to_address}"
    );

    return wp_mail($email, $email_subject, $message, $headers);

}
public function add_gdpr_consent_checkbox($form_content, $form) {
    // Get plugin settings.
    $options = get_option('cf7_chatgpt_options');

    // Check if the current form is enabled for AI auto-replies and GDPR consent checkbox is enabled.
    if (isset($options['enabled_forms'][$form->id()]) && $options['enabled_forms'][$form->id()]['gdpr_consent']) {
        $gdpr_consent_field = '<p>[checkbox gdpr-consent "I agree to receive AI-generated auto-replies."]</p>';
        $form_content .= $gdpr_consent_field;
    }

    return $form_content;
}
public function check_gdpr_consent($submission) {
    $posted_data = $submission->get_posted_data();

    // Check if the gdpr-consent field is present and consent is given.
    if (isset($posted_data['gdpr-consent']) && !empty($posted_data['gdpr-consent'])) {
        return true;
    }

    return false;
}

}
$cf7_chatgpt_integration = new CF7_ChatGPT_Integration();
$cf7_chatgpt_analytics = new CF7_ChatGPT_Analytics();
