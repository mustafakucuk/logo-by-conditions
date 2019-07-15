<?php
/**
 * Plugin Name: Logo by Conditions
 * Description: Show different logos by your conditions.
 * Version: 1.0
 * Author: Mustafa KÜÇÜK
 * Author URI: https://mustafakucuk.net
 */

class Logo_By_Conditions {

	public function __construct() {
		## Actions
		add_action( 'category_add_form_fields', [ $this, 'category_add_form_fields' ], 10, 2 );
		add_action( 'category_edit_form_fields', [ $this, 'category_edit_form_fields' ], 10, 2 );
		add_action( 'create_category', [ $this, 'save_category_form_fields' ], 10, 2 );
		add_action( 'edited_category', [ $this, 'save_category_form_fields' ], 10, 2 );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_post' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	## Include assets for admin
	public function admin_enqueue_scripts() {
		if ( isset( $_GET['page'] ) && $_GET['page'] !== 'logo-settings' ) {
			return;
		}

		wp_enqueue_style( 'logo-by-conditions-style', plugins_url( 'assets/css/logo-by-conditions.css', __FILE__ ) );
		wp_enqueue_script( 'logo-by-conditions-script-admin', plugins_url( '/assets/js/logo-by-conditions-admin.js', __FILE__ ), array( 'jquery' ), true, true );
		wp_enqueue_media(); // Call media plugin
	}

	## Include assets for front
	public function enqueue_scripts() {
		wp_enqueue_script( 'logo-by-conditions-script', plugins_url( '/assets/js/logo-by-conditions.js', __FILE__ ), array( 'jquery' ), true, true );
		wp_localize_script(
			'logo-by-conditions-script',
			'logo_by_conditions',
			array(
				'auto_changer'  => get_option( 'auto_changer' ),
				'logo_url'      => $this->get_logo_url(),
				'logo_selector' => get_option( 'logo_selector' ),
			)
		);

	}

	## Admin menu
	public function admin_menu() {
		add_menu_page( __( 'Logo Settings' ), __( 'Logo Settings' ), 'manage_options', 'logo-settings', [ $this, 'logo_settings_page' ] );
	}

	## Settings page
	public function logo_settings_page() {
		if ( $_POST ) {
			if ( ! isset( $_POST['logo_by_conditions'] ) || ! wp_verify_nonce( $_POST['logo_by_conditions'], 'logo_by_conditions' ) ) {
				wp_die( esc_html_e( 'Something went wrong...', 'logo_by_conditions' ) );
			} else {
				$auto_changer = ! empty( $_POST['auto_changer'] ) ? intval( $_POST['auto_changer'] ) : 0;
				update_option( 'default_logo', sanitize_text_field( $_POST['default_logo'] ) );
				update_option( 'auto_changer', $auto_changer );
				update_option( 'logo_selector', sanitize_text_field( $_POST['logo_selector'] ) );
			}
		}

		require_once 'pages/settings.php';
	}

	## Logo settings field for category add page
	public function category_add_form_fields() {        ?>
			<div class="form-field">
				<label for="logo_term_meta[logo]"><?php esc_html_e( 'Logo', 'logo_by_conditions' ); ?></label>
				<input type="text" class="mk-upload" name="logo_term_meta[logo]" id="logo_term_meta[logo]" value="">
				<p class="description"><?php esc_html_e( 'Custom logo for this category.', 'logo_by_conditions' ); ?></p>
			</div>
			<div class="form-field">
				<label for="logo_term_meta[only_category]">
					<input type="radio" name="logo_term_meta[show_type]" value="1" id="logo_term_meta[only_category]">
					<?php esc_html_e( 'Only category', 'logo_by_conditions' ); ?>
				</label>
				<label for="logo_term_meta[all_posts]">
					<input type="radio" name="logo_term_meta[show_type]" value="2" id="logo_term_meta[all_posts]">
					<?php esc_html_e( 'All posts of this category', 'logo_by_conditions' ); ?>
				</label>
			</div>
		<?php
	}

	## Logo settings field for category edit page
	public function category_edit_form_fields( $term ) {
		$term_id   = $term->term_id;
		$logo      = esc_url( get_term_meta( $term_id, 'logo', true ) );
		$show_type = intval( get_term_meta( $term_id, 'show_type', true ) );
		?>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="logo_term_meta[logo]"><?php esc_html_e( 'Logo', 'logo_by_conditions' ); ?></label></th>
				<td>
					<input type="text" class="mk-upload" name="logo_term_meta[logo]" id="logo_term_meta[logo]" value="<?php esc_url( $logo ); ?>">
					<p class="description"><?php esc_html_e( 'Custom logo for this category.', 'logo_by_conditions' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="logo_term_meta[only_category]">
						<input type="radio" name="logo_term_meta[show_type]" value="1" id="logo_term_meta[only_category]" <?php esc_html_e( ! $show_type || $show_type === 1 ? 'checked' : '' ); ?>>
						<?php esc_html_e( 'Show only category', 'logo_by_conditions' ); ?>
					</label>
				</th>

				<th scope="row" valign="top">
					<label for="logo_term_meta[all_posts]">
						<input type="radio" name="logo_term_meta[show_type]" value="2" id="logo_term_meta[all_posts]" <?php esc_html_e( $show_type === 2 ? 'checked' : '' ); ?>>
						<?php esc_html_e( 'Show all posts of this category', 'logo_by_conditions' ); ?>
					</label>
				</th>
			</tr>
		<?php
	}

	## Save fields
	public function save_category_form_fields( $term_id ) {
		$fields = esc_html( $_POST['logo_term_meta'] );
		foreach ( $fields as $key => $value ) {
			$value = sanitize_text_field( $value );
			update_term_meta( $term_id, $key, $value );
		}
	}

	## Add logo settings box for all post type
	public function add_meta_boxes() {
		$screens = get_post_types();
		foreach ( $screens as $key => $screen ) {
			add_meta_box( 'logo-settings', __( 'Logo Settings', 'logo_by_conditions' ), [ $this, 'logo_settings_meta_box' ], $screen, 'side', 'high' );
		}
	}

	## Logo settings view for meta box
	public function logo_settings_meta_box( $post ) {
		wp_nonce_field( 'logo_update', 'logo_update' );
		$logo_url = esc_url( get_post_meta( $post->ID, 'logo', true ) );
		?>
			<label for="logo"><?php esc_html_e( 'Logo', 'logo_by_conditions' ); ?></label>
			<input type="text" class="mk-upload" name="logo" id="logo" value="<?php echo esc_html_e( $logo_url ); ?>">
		<?php
	}

	## Save post
	public function save_post( $post_id ) {
		if ( ! isset( $_POST['logo_update'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['logo_update'], 'logo_update' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['logo'] ) ) {
			return;
		}

		$logo = sanitize_text_field( $_POST['logo'] );

		update_post_meta( $post_id, 'logo', $logo );
	}

	## Return logo of category
	public function category_logo( $term_id ) {
		return get_term_meta( $term_id, 'logo', true );
	}

	## Return url of logo by conditions
	public function get_logo_url() {
		if ( is_category() ) {
			$term_id = get_queried_object_id();
			$logo    = $this->category_logo( $term_id );
		} elseif ( is_singular() ) {
			$post_id = get_the_ID();
			$logo    = get_post_meta( $post_id, 'logo', true );
			if ( empty( $logo ) && ! is_page() ) {
				$term_id   = get_the_category( $post_id )[0]->term_id;
				$show_type = get_term_meta( $term_id, 'show_type', true );
				if ( $show_type === 2 ) {
					$logo = $this->category_logo( $term_id );
				}
			}
		}

		$logo = ! empty( $logo ) ? $logo : get_option( 'default_logo' );

		return esc_url( $logo );
	}
}

new Logo_By_Conditions();
