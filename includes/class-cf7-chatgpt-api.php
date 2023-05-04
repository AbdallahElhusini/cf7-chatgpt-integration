<?php
/**
 * Class for handling ChatGPT API requests.
 */

class CF7_ChatGPT_API
{
    /**
     * The API key.
     *
     * @var string
     */
    private $api_key;

    /**
     * The API endpoint.
     *
     * @var string
     */
    private $api_endpoint;

    /**
     * Constructor.
     *
     * @param string $api_key The API key.
     * @param string $api_endpoint The API endpoint.
     */
    public function __construct($api_key, $api_endpoint,$cf7_chatgpt_security)
    {
        $this->api_key = $api_key;
        $this->api_endpoint = $api_endpoint;
        $this->cf7_chatgpt_security = $cf7_chatgpt_security;

    }

    // Additional methods for handling API requests will be added here.
    /**
 * Sends a request to the ChatGPT API and returns the response.
 *
 * @param array $data The data to send to the API.
 * @return array The API response.
 * @throws Exception If there is an error during the API request.
 */
public function send_request($data)
{
    $options = get_option('cf7_chatgpt_options');
    if (isset($options['anonymize_data']) && $options['anonymize_data'] === '1') {
        $data = $this->cf7_chatgpt_security->anonymize_data($data);
    }
        // Limit input tokens by trimming long messages.
        $max_input_tokens = 100; // Set a maximum token limit for input.
        $data['message'] = $this->limit_input_tokens($data['message'], $max_input_tokens);

        // Prepare the API request payload.
        $payload = array(
            'prompt' => $data['message'],
            'max_tokens' => 50, // Limit the response length to 50 tokens.
            'temperature' => 0.3, // Use a lower temperature for more focused output.
            // Add other required parameters for the API call.
        );

    // Set up the request arguments.
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type' => 'application/json',
        ),
            'body' => json_encode($payload),
        'method' => 'POST',
        'timeout' => 30,
    );

    // Send the request.
    $response = wp_remote_post($this->api_endpoint, $args);

    // Check for errors.
    if (is_wp_error($response)) {
        throw new Exception(__('An error occurred during the API request.', 'cf7-chatgpt-integration') . ' ' . $response->get_error_message());
    }

    // Decode the response.
    $response_data = json_decode(wp_remote_retrieve_body($response), true);

    // Check for API errors.
    if (isset($response_data['error'])) {
        throw new Exception(__('An error occurred in the ChatGPT API.', 'cf7-chatgpt-integration') . ' ' . $response_data['error']['message']);
    }

    return $response_data;
}
/**
 * Processes API errors and displays them to the user.
 *
 * @param Exception $exception The exception to process.
 * @return void
 */
public function process_api_errors($exception)
{
    // Log the error message.
    error_log($exception->getMessage());

    // Display an error message to the user.
    add_action('admin_notices', function () use ($exception) {
        ?>
        <div class="notice notice-error">
            <p><?php _e('An error occurred while processing the ChatGPT API request:', 'cf7-chatgpt-integration'); ?></p>
            <p><?php echo esc_html($exception->getMessage()); ?></p>
        </div>
        <?php
    });
}
private function limit_input_tokens($input, $max_tokens)
{
    // Tokenize the input text into words.
    $words = explode(' ', $input);

    // Initialize variables for the resulting text and token counter.
    $trimmed_text = '';
    $token_count = 0;

    // Iterate through the words and assemble the trimmed text.
    foreach ($words as $word) {
        // Estimate the token count for the current word, including spaces.
        $word_token_count = mb_strlen($word) + 1;

        // Check if adding the word would exceed the max_tokens limit.
        if ($token_count + $word_token_count > $max_tokens) {
            // Stop adding words if the token limit is reached.
            break;
        }

        // Add the word to the trimmed text and update the token counter.
        $trimmed_text .= ($token_count > 0 ? ' ' : '') . $word;
        $token_count += $word_token_count;
    }

    // Append an ellipsis if the input text was trimmed.
    if ($token_count < mb_strlen($input)) {
        $trimmed_text .= '...';
    }

    return $trimmed_text;
} 

}
