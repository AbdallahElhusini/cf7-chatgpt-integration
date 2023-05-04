<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class for implementing conditional logic rules in the plugin.
 */
class CF7_ChatGPT_Conditional_Logic
{
    /**
     * Evaluates a list of conditional logic rules based on form data.
     *
     * @param array $rules The list of conditional logic rules.
     * @param array $form_data The form data to evaluate against.
     * @return int|null The ID of the matched AI response template or null if no rules match.
     */
    public function evaluate_rules($rules, $form_data)
    {
        // Iterate through the rules and evaluate each one.
        foreach ($rules as $rule) {
            // For the sake of this example, we assume that each rule has 'field', 'operator', and 'value' properties.
            $field_value = isset($form_data[$rule['field']]) ? $form_data[$rule['field']] : '';

            // Evaluate the rule based on the specified operator.
            $rule_matches = false;
            switch ($rule['operator']) {
                case 'contains':
                    $rule_matches = strpos($field_value, $rule['value']) !== false;
                    break;
                case 'equals':
                    $rule_matches = $field_value == $rule['value'];
                    break;
                case 'not_equals':
                    $rule_matches = $field_value != $rule['value'];
                    break;
                // Add more operators as needed.
            }

            // If the rule matches, return the associated AI response template ID.
            if ($rule_matches) {
                return $rule['template_id'];
            }
        }

        // If no rules match, return null.
        return null;
    }
}
