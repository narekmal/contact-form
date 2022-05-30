<?php
/**
 * Define contact_form shortcode and its AJAX endpoint
 *
 * @package    Contact Form
 */

new Contact_Form_Shortcode();

/**
 * Registers shortcode and it's AJAX endpoint
 */
class Contact_Form_Shortcode
{
	/**
     * Constructor
     */
    public function __construct()
    {
		add_shortcode( 'contact_form', array($this, 'render_contact_form' ) );

		add_action( 'wp_ajax_nopriv_process_contact_form_data', array($this, 'process_contact_form_data' ) );
		add_action( 'wp_ajax_process_contact_form_data', array($this, 'process_contact_form_data' ) );
    }

	/**
     * Renders contact form
     */
    public function render_contact_form()
    {
        ob_start();
		?>

		<script>const ajaxUrl = "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>";</script>

		<div class="contact-form">
			<form class="contact-form__form js-contact-form" action="/admin-ajax.php" method="POST">
				<div class="contact-form__row">
					<input name="name" type="text" placeholder="<?php esc_html_e( 'Name', 'enquiry' ); ?>" class="contact-form__first-name" 
						value="" >
					<input name="email" type="text" placeholder="<?php esc_html_e( 'Email', 'enquiry' ); ?>" class="contact-form__email"
						value="" >
				</div>
				<div class="contact-form__row">
					<input name="date" type="text" placeholder="<?php esc_html_e( 'Date', 'enquiry' ); ?>" class="contact-form__date">
					<div class="contact-form__radio-container">
						<div class="contact-form__radio-item-container">
							<input type="radio" id="option_1" name="multiple_choice" value="option_1" checked="checked">
							<label for="option_1">Option 1</label>
						</div>
						<div class="contact-form__radio-item-container">
							<input type="radio" id="option_2" name="multiple_choice" value="option_2">
							<label for="option_2">Option 2</label>
						</div>
					</div>
				</div>
				<div style='height: 0px; width: 0px; overflow:hidden;'>
					<input class="js-upload-btn-real" type="file" name="file" onchange="handleFileSelect(this)" />
				</div>
				<div class="contact-form__upload-btn-wrapper">
					<button class="contact-form__upload-btn js-upload-btn">Upload File</button>
					<span class="contact-form__file-name js-file-name"></span>
				</div>
				<div class="contact-form__last-row">
					<?php wp_nonce_field( 'process_contact_form_data', 'contact-form-nonce' ); ?>
					<input type="hidden" name="action" value="process_contact_form_data" >
					<input type="submit" class="contact-form__submit">
					<div class="contact-form__status">
						<div class="contact-form__status-processing"><?php esc_html_e( 'Processing', 'contact-form' ); ?>...</div>
						<div class="contact-form__status-success"><?php esc_html_e( 'Thanks for submitting', 'contact-form' ); ?></div>
						<div class="contact-form__status-failure"><?php esc_html_e( 'Something went wrong, please try to submit again', 'contact-form' ); ?></div>
						<div class="contact-form__status-invalid"><?php esc_html_e( 'Please fill out all fields', 'contact-form' ); ?></div>
					</div>
				</div>
			</form>
		</div>

		<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
		
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
    }

	/**
	 * AJAX endpoint for processing form data
	 */
	function process_contact_form_data() {
		if ( empty( $_POST['contact-form-nonce'] ) ||
			! wp_verify_nonce( $_POST['contact-form-nonce'], 'process_contact_form_data' ) ) {
			wp_send_json_error();
			die();
		}
	
		global $wpdb;
	
		$post_params = array( 'name', 'email', 'date', 'multiple_choice' );
		$db_params   = array();
		$valid       = true;
	
		foreach ( $post_params as $param ) {
			$db_params[ $param ] = ( 'email' == $param ? sanitize_email( wp_unslash( $_POST[ $param ] ) ) : sanitize_text_field( wp_unslash( $_POST[ $param ] ) ) );
		}
	
		// Handle file upload
		$filename = $_FILES['file']['name'];
		if (!empty($filename)) {
			$db_params['file_name'] = sanitize_text_field( wp_unslash( $filename ));
			$location = ABSPATH . '\uploads\\' . $filename;
			if ( ! move_uploaded_file($_FILES['file']['tmp_name'], $location) ) { 
				wp_send_json_error();
				die();
			}
		}
	
		$result = $wpdb->insert( $wpdb->prefix . 'contact_form_data', $db_params );
	
		$result ? wp_send_json_success() : wp_send_json_error();
	
		die();
	}
}
