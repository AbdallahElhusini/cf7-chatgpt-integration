<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class CF7_ChatGPT_Settings
{
    /**
     * The single instance of this class.
     */
    private static $instance = null;
    // Add the API endpoint property.
    private $api_endpoint = 'https://api.openai.com/v1/engines/text-davinci-003/completions'; // Replace with the actual API endpoint URL.

    /**
     * The options for the plugin.
     */
    private $options;

    /**
     * Returns the single instance of this class.
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
public function __construct()
{
    // Initialize plugin settings.
    add_action('admin_init', array($this, 'init_settings'));

    // Add the settings page.
    add_action('admin_menu', array($this, 'add_settings_page'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts')); // Add this line.

}


/**
 * Initializes plugin settings.
 */
private function register_settings_section($section_id, $section_title, $page)
{
    add_settings_section(
        $section_id,
        __($section_title, 'cf7-chatgpt-integration'),
        null,
        $page
    );
}

private function register_settings_field($field_id, $field_title, $callback, $section_id, $page)
{
    add_settings_field(
        $field_id,
        __($field_title, 'cf7-chatgpt-integration'),
        array($this, $callback),
        $page,
        $section_id
    );
    register_setting($page, $field_id, array($this, 'sanitize_text_input'));
}

public function init_settings()
{
    // Register the API settings section.
    $this->register_settings_section('cf7_chatgpt_api_settings', 'ChatGPT API Settings', 'cf7_chatgpt_integration');

    // Register the API key and endpoint fields.
    $this->register_settings_field('cf7_chatgpt_api_key', 'API Key', 'render_api_key_field', 'cf7_chatgpt_api_settings', 'cf7_chatgpt_integration');

    // Register the Contact Form 7 settings section.
    $this->register_settings_section('cf7_chatgpt_cf7_settings', 'Contact Form 7 Settings', 'cf7_chatgpt_integration');

    // Register the Contact Form 7 form selection field.
    $this->register_settings_field('cf7_chatgpt_form_id', 'Select Form', 'render_form_selection_field', 'cf7_chatgpt_cf7_settings', 'cf7_chatgpt_integration');

    // Register the anonymize data checkbox field.
    add_settings_field(
        'anonymize_data',
        __('Anonymize data', 'cf7-chatgpt-integration'),
        array($this, 'checkbox_callback'),
        'cf7_chatgpt_integration',
        'cf7_chatgpt_general_settings',
        array(
            'label_for' => 'anonymize_data',
            'description' => __('Anonymize form data before sending it to the ChatGPT API.', 'cf7-chatgpt-integration'),
            'option_name' => 'cf7_chatgpt_options',
            'field_name' => 'anonymize_data'
        )
    );

    // Email settings section.
    $this->register_settings_section('cf7_chatgpt_email_settings', 'Email Settings', 'cf7-chatgpt');

    // Register email subject, sender name, and reply-to address fields.
    $this->register_settings_field('cf7_chatgpt_email_subject', 'Email Subject', 'text_input_callback', 'cf7_chatgpt_email_settings', 'cf7-chatgpt');
    $this->register_settings_field('cf7_chatgpt_sender_name', 'Sender Name', 'text_input_callback', 'cf7_chatgpt_email_settings', 'cf7-chatgpt');
    $this->register_settings_field('cf7_chatgpt_reply_to_address', 'Reply-To Address', 'text_input_callback', 'cf7_chatgpt_email_settings', 'cf7-chatgpt');
}

 /* Adds the settings page to the WordPress admin menu.
 */
public function add_settings_page()
{
    add_options_page(
        __('Contact Form 7 - ChatGPT Integration', 'cf7-chatgpt-integration'),
        __('CF7 - ChatGPT Integration', 'cf7-chatgpt-integration'),
        'manage_options',
        'cf7-chatgpt-integration',
        array($this, 'render_settings_page')
    );
}

/**
 * Renders the settings page content.
 */
public function render_settings_page()
{
    ?>
    <div class="wrap">
        <h1><?php _e('Contact Form 7 - ChatGPT Integration', 'cf7-chatgpt-integration'); ?></h1>
        <form action="options.php" method="post">
            <?php
            // Output the settings sections and fields.
            settings_fields('cf7_chatgpt_integration');
            do_settings_sections('cf7_chatgpt_integration');

            // Output the submit button.
            submit_button();
            ?>
        </form>
    </div>
    <?php
        $this->render_templates_section(); // Add this line.
        $this->render_conditional_logic_section();

}
/**
 * Renders a text input field.
 *
 * @param string $id The ID of the text input field.
 * @param string $name The name of the text input field.
 * @param string $value The value of the text input field.
 * @param string $class The CSS class for the text input field.
 */
public function render_text_input_field($id, $name, $value, $class = 'regular-text')
{
    printf(
        '<input type="text" id="%1$s" name="%2$s" value="%3$s" class="%4$s">',
        $id,
        $name,
        esc_attr($value),
        $class
    );
}    
/**
 * Renders the API key field.
 */
public function render_api_key_field()
{
    $api_key = get_option('cf7_chatgpt_api_key', '');
    $this->render_text_input_field('cf7_chatgpt_api_key', 'cf7_chatgpt_api_key', $api_key);
}


/**
 * Sanitizes a text input value.
 *
 * @param string $input The input value.
 * @return string The sanitized value.
 */
public function sanitize_text_input($input)
{
    return sanitize_text_field(stripslashes($input));
}
    
// Add the following method to the class:

/**
 * Renders a checkbox field.
 *
 * @param string $id The ID of the checkbox input field.
 * @param string $name The name of the checkbox input field.
 * @param bool $checked Whether the checkbox is checked or not.
 * @param string $label The label for the checkbox.
 */
public function render_checkbox_field($id, $name, $checked, $label)
{
    printf(
        '<br><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s /><label for="%1$s"> %4$s</label>',
        $id,
        $name,
        checked(1, $checked, false),
        $label
    );
}

// Update the `render_form_selection_field()` method as follows:

/**
 * Renders the form selection field.
 */
public function render_form_selection_field()
{
    // Get the selected form ID from the settings.
    $selected_form_id = get_option('cf7_chatgpt_form_id', '');

    // Get all Contact Form 7 forms.
    $forms = get_posts(array(
        'post_type' => 'wpcf7_contact_form',
        'posts_per_page' => -1,
    ));

    // Render the form selection dropdown and GDPR consent checkboxes.
    ?>
    <select id="cf7_chatgpt_form_id" name="cf7_chatgpt_form_id">
        <option value=""><?php _e('Select a form', 'cf7-chatgpt-integration'); ?></option>
        <?php foreach ($forms as $form) : ?>
            <option value="<?php echo esc_attr($form->ID); ?>" <?php selected($selected_form_id, $form->ID); ?>><?php echo esc_html($form->post_title); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
    $options = get_option('cf7_chatgpt_options');
    foreach ($forms as $form) {
        $gdpr_consent = isset($options['enabled_forms'][$form->ID]['gdpr_consent']) ? $options['enabled_forms'][$form->ID]['gdpr_consent'] : 0;
        $this->render_checkbox_field(
            "gdpr_consent_{$form->ID}",
            "cf7_chatgpt_options[enabled_forms][{$form->ID}][gdpr_consent]",
            $gdpr_consent,
            __('Enable GDPR consent for', 'cf7-chatgpt-integration') . ' ' . esc_html($form->post_title)
        );
    }
}

    
/**
 * Renders the AI response templates section on the settings page.
 *
 * @return void
 */
public function render_templates_section()
{
    ?>
    <h2><?php _e('AI Response Templates', 'cf7-chatgpt-integration'); ?></h2>
    <p><?php _e('Create and manage AI response templates for your auto-replies.', 'cf7-chatgpt-integration'); ?></p>
    <div id="cf7-chatgpt-templates">
        <!-- The AI response templates user interface will be added here. -->
    </div>
    <?php
}
/**
 * Enqueues admin scripts and styles.
 *
 * @return void
 */
public function enqueue_admin_scripts()
{
    wp_enqueue_script('jquery');

    $plugin_version = '1.0.0'; // You should define a constant for the plugin version, e.g., in the main plugin file.
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js');

    wp_enqueue_script('cf7-chatgpt-integration-admin', plugin_dir_url(__FILE__) . '../admin/admin.js', array('jquery'), $plugin_version, true);
    wp_enqueue_style('cf7-chatgpt-integration-admin', plugin_dir_url(__FILE__) . '../admin/admin.css', array(), $plugin_version);
}
public function render_conditional_logic_section()
{
    // Render the HTML for the conditional logic section.
    ?>
    <div id="cf7-chatgpt-conditional-logic">
        <h2>Conditional Logic</h2>
        <p>Set up rules to determine which AI response template should be used based on form field values.</p>
        <div id="cf7-chatgpt-conditional-logic-rules"></div>
        <button type="button" id="cf7-chatgpt-add-rule">Add Rule</button>
    </div>
    <?php
}
public function checkbox_callback($args) {
    $option_name = $args['option_name'];
    $field_name = $args['field_name'];
    $options = get_option($option_name);

    $checked = '';
    if ($field_name === 'anonymize_data') {
        $checked = isset($options[$field_name]) ? 'checked' : '';
    }

    $html = '<input type="checkbox" id="' . $args['label_for'] . '" name="' . $option_name . '[' . $field_name . ']" ' . $checked . '>';
    $html .= '<label for="' . $args['label_for'] . '">' . $args['description'] . '</label>';

    echo $html;
}

}

// Initialize the settings class.
CF7_ChatGPT_Settings::getInstance();
