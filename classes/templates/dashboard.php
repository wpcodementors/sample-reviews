<?php
	namespace Wpdsr;
	defined( 'ABSPATH' ) || exit;
?>

<div id="wpdsr-dashboard" class="wpdsr-ui wpdsr-dashboard" data-page="dashboard">
  <div class="wpdsr-wrapper">

    <main class="wpdsr-content">

			<div class="wpdsr-header">
				<div class="wpdsr-header-title">
					<h2 class="title">Settings</h2>
					<h5>Sample Reviews for WooCommerce plugin settings</h5>
				</div>
			</div>

			<div class="wpdsr-filters-sticky">
				<ul class="wpdsr-filters">
					<li data-id="reviews">Reviews</li>
					<li data-id="authors">Authors</li>
				</ul>
				<a class="wpdsr-button primary wpdsr-save-settings" href="#">Save changes</a>
			</div>

			<div class="wpdsr-content-wrapper">
				<form id="wpdsr-form-settings" class="wpdsr-filtered wpdsr-form">
					<input type="hidden" name="action" value="wpdsr_save_settings"/>
					<?php
						wp_nonce_field( 'wpdsr_settings', 'wpdsr_nonce' );

						$fields = Main::get_fields();

						foreach( $this->settings as $setting_key => $setting ){

							if( 'heading' == $setting['type'] ){

								// headings

								echo '<div data-group="'. esc_attr($setting['group']) .'" class="wpdsr-row wpdsr-row-heading">';

									echo '<div class="row-column row-column-12">';

										echo '<h4 class="title">'. esc_html( $setting['title'] ) .'</h4>';

										if( ! empty($setting['desc']) ){
											echo '<p>'. esc_html( $setting['desc'] ) .'</p>';
										}

									echo '</div>';

								echo '</div>';

							} else {

								// other fields

								$value = '';

								if( !empty( $this->options[$setting['id']] ) ){
									$value = $this->options[$setting['id']];
								} else if( !empty( $setting['std'] ) ){
									$value = $setting['std'];
								}

								echo '<div data-id="'. esc_attr($setting['id']) .'" data-group="'. esc_attr($setting['group']) .'" data-search="'. esc_attr($setting['id']) .'" class="wpdsr-row">';

									echo '<div class="row-column row-column-4">';

										echo '<label class="form-label" for="wpdsr['. esc_attr($setting['id']) .']">';
											echo esc_html($setting['title']);
											if( ! empty($setting['tooltip']) ){
												echo '<a class="wpdsr-help" data-tooltip="'. esc_html($setting['tooltip']) .'"><span class="wpdsr-icon wpdsr-icon-help"></span></a>';
											}
										echo '</label>';

										if( ! empty($setting['desc']) ){
											echo '<p>'. esc_html( $setting['desc'] ) .'</p>';
										}

									echo '</div>';

									echo '<div class="row-column row-column-8 field-'. esc_attr($setting['type']) .'">';

										require_once $fields[$setting['type']][1];
										$field = new $fields[$setting['type']][0]($setting, $value);


									echo '</div>';

								echo '</div>';

							}

						}

					?>
				</form>

			</div>

    </main>

  </div>

	<div class="wpdsr-notice-sticky wpdsr-notice-settings"><?php esc_html_e('Settings saved', 'wpdsr'); ?></div>
</div>
