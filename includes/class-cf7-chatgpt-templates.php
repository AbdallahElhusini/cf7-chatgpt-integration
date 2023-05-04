<?php

class CF7_ChatGPT_Templates
{
    /**
     * The name of the WordPress option used to store the AI response templates.
     *
     * @var string
     */
    const OPTION_NAME = 'cf7_chatgpt_templates';


        // If no matching template is found, return an empty string or a default template


public function conditions_match($conditions, $ai_response)
{
    $matches = 0;
    $total_conditions = count($conditions);

    // Example: Check if the AI response matches a specific sentiment
    if (isset($conditions['sentiment']) && $conditions['sentiment'] === $ai_response['sentiment']) {
        $matches++;
    }

    // Example: Check if the AI response has a specific keyword
    if (isset($conditions['keyword']) && strpos($ai_response['text'], $conditions['keyword']) !== false) {
        $matches++;
    }

    // Example: Check if the AI response has a minimum confidence level
    if (isset($conditions['min_confidence']) && $ai_response['confidence'] >= $conditions['min_confidence']) {
        $matches++;
    }

    // If all conditions match, return true
    if ($matches === $total_conditions) {
        return true;
    }

    // If none or not all of the conditions match, return false
    return false;
}
public function validate_and_sanitize_template_data($template_data) {
    $sanitized_data = array();

    // Sanitize the template name
    $sanitized_data['name'] = sanitize_text_field($template_data['name']);

    // Sanitize the template content
    $sanitized_data['content'] = wp_kses_post($template_data['content']);

    // Validate and sanitize conditions
    if (!empty($template_data['conditions'])) {
        $sanitized_data['conditions'] = array();
        foreach ($template_data['conditions'] as $key => $value) {
            // Add validation and sanitization for each condition
            // For example, for 'min_confidence' condition:
            if ($key === 'min_confidence') {
                $sanitized_value = floatval($value);
                if ($sanitized_value >= 0 && $sanitized_value <= 1) {
                    $sanitized_data['conditions'][$key] = $sanitized_value;
                }
            }
            // Add similar validation and sanitization for other conditions
        }
    }

    return $sanitized_data;
}

    /**
     * Retrieves the AI response templates.
     *
     * @return array The AI response templates.
     */
    public function get_templates()
    {
        $templates = get_option(self::OPTION_NAME, array());
        return $templates;
    }

    /**
     * Saves an AI response template.
     *
     * @param array $template The template data.
     * @return void
     */
public function save_template($template_data)
{
    $sanitized_data = $this->validate_and_sanitize_template_data($template_data);
    $templates = $this->get_templates();
    $templates[] = $sanitized_data;
    update_option(self::OPTION_NAME, $templates);
}

    /**
     * Updates an existing AI response template.
     *
     * @param int $index The index of the template to update.
     * @param array $template The new template data.
     * @return void
     */
public function update_template($index, $template_data)
{
    $sanitized_data = $this->validate_and_sanitize_template_data($template_data);
    $templates = $this->get_templates();
    $templates[$index] = $sanitized_data;
    update_option(self::OPTION_NAME, $templates);
}
    public function handle_template_form_submission() {
        if (isset($_POST['cf7_chatgpt_template_nonce']) && wp_verify_nonce($_POST['cf7_chatgpt_template_nonce'], 'save_cf7_chatgpt_template')) {
            $template_data = array(
                'name' => $_POST['cf7_chatgpt_template_name'],
                'content' => $_POST['cf7_chatgpt_template_content'],
                'conditions' => isset($_POST['cf7_chatgpt_template_conditions']) ? $_POST['cf7_chatgpt_template_conditions'] : array(),
            );

            $sanitized_data = $this->validate_and_sanitize_template_data($template_data);

            if (isset($_POST['cf7_chatgpt_template_id'])) {
                // Updating an existing template
                $template_id = intval($_POST['cf7_chatgpt_template_id']);
                $this->update_template($template_id, $sanitized_data);
            } else {
                // Saving a new template
                $this->save_template($sanitized_data);
            }
        }
    }

    /**
     * Deletes an AI response template.
     *
     * @param int $index The index of the template to delete.
     * @return void
     */
    public function delete_template($index)
    {
        $templates = $this->get_templates();
        unset($templates[$index]);
        $templates = array_values($templates);
        update_option(self::OPTION_NAME, $templates);
    }
public function apply_template($ai_response)
{
    // Retrieve all templates
    $templates = $this->get_templates();

    // Find the first template that matches the AI response conditions
    $matching_template = null;
    foreach ($templates as $template) {
        if ($this->conditions_match($template['conditions'], $ai_response)) {
            $matching_template = $template;
            break;
        }
    }

    // If no matching template is found, return the original AI response
    if ($matching_template === null) {
        return $ai_response['text'];
    }

    // Replace placeholders in the matching template with actual data
    $formatted_response = $this->replace_placeholders($matching_template['text'], $ai_response['form_data']);

    return $formatted_response;
}


    public function replace_placeholders($template, $form_data)
{
    // Replace placeholders with actual form data
    $placeholders = array_keys($form_data);
    $replacement_values = array_values($form_data);

    foreach ($placeholders as $index => $placeholder) {
        $placeholders[$index] = '{' . $placeholder . '}';
    }

    $formatted_response = str_replace($placeholders, $replacement_values, $template);

    return $formatted_response;
}
/**
 * Retrieves the appropriate template based on the AI response.
 *
 * @param array $ai_response The AI response data.
 * @return string The selected template.
 */
public function get_template_by_condition($ai_response)
{
    $templates = $this->get_templates();
    $selected_template = '';

    foreach ($templates as $template) {
        if (!isset($template['conditions']) || empty($template['conditions'])) {
            $selected_template = $template['template'];
            continue;
        }

        if ($this->conditions_match($template['conditions'], $ai_response)) {
            $selected_template = $template['template'];
            break;
        }
    }

    return $selected_template;
}
/**
 * Validates conditions for a template.
 *
 * @param array $conditions The conditions to validate.
 * @return bool True if the conditions are valid, false otherwise.
 */
public function validate_conditions($conditions)
{
    // Add validation logic for your conditions here.
    // Example: check if 'min_confidence' is a float between 0 and 1.
    if (isset($conditions['min_confidence'])) {
        if (!is_numeric($conditions['min_confidence']) || $conditions['min_confidence'] < 0 || $conditions['min_confidence'] > 1) {
            return false;
        }
    }

    // If all conditions are valid, return true.
    return true;
}

}

