<?php
	namespace Wpdsr;
	defined( 'ABSPATH' ) || exit;
?>

<div id="wpdsr-dashboard" class="wpdsr-ui wpdsr-dashboard" data-page="multiple" data-step="form">
  <div class="wpdsr-wrapper">

    <main class="wpdsr-content">

			<?php if( ! function_exists('is_woocommerce') ): ?>
				<div class="wpdsr-alert wpdsr-no-woocommerce">
					<div class="wpdsr-alert-desc">
						<strong>WooCommerce</strong> plugin is required but not activated
					</div>
					<div class="wpdsr-alert-options">
						<a href="plugins.php"><?php esc_html_e('Go to plugins', 'wpdsr'); ?></a>
					</div>
				</div>
			<?php endif; ?>

			<div class="wpdsr-header">
				<div class="wpdsr-header-title">
					<h2 class="title"><?php esc_html_e('Add multiple reviews', 'wpdsr'); ?></h2>
					<h5><?php esc_html_e('Add reviews to multiple products in one click', 'wpdsr'); ?></h5>
				</div>
			</div>

			<div class="wpdsr-content-wrapper">

				<form id="wpdsr-form-settings" class="wpdsr-filtered wpdsr-form wpdsr-show-all">
					<input type="hidden" name="action" value="wpdsr_generate_multiple"/>
					<?php
						wp_nonce_field( 'wpdsr_generate', 'wpdsr_nonce' );

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

					<a class="wpdsr-button primary wpdsr-generate button-icon-right" href="#"><span><?php esc_html_e('Generate reviews', 'wpdsr'); ?></span><span class="wpdsr-icon wpdsr-icon-arrow-right"></span></a>

          <!-- <a class="wpdsr-button primary wpdsr-save" href="#">Publish reviews</a> -->

				</form>

        <form id="wpdsr-form-list" class="wpdsr-filtered wpdsr-form wpdsr-show-all">
					<input type="hidden" name="action" value="wpdsr_add_multiple"/>
					<?php
						wp_nonce_field( 'wpdsr_add', 'wpdsr_nonce' );

            echo '<div class="wpdsr-reviews-list">';
            	// ajax content here
            echo '</div>';

					?>

          <a class="wpdsr-button secondary wpdsr-back" href="#">← <?php esc_html_e('Back', 'wpdsr'); ?></a>
          <a class="wpdsr-button primary wpdsr-save-multiple" href="#"><?php esc_html_e('Publish reviews', 'wpdsr'); ?></a>

          <!-- <a class="wpdsr-button primary wpdsr-save" href="#">Publish reviews</a> -->

				</form>


				<p class="legal-notice"><b>Legal notice:</b> In some countries fake reviews are prohibited by law. In that case use this tool only to generate dummy content while developing and remove all reviews before making your site live.</p>
			</div>

    </main>

  </div>

	<div class="wpdsr-notice-sticky wpdsr-notice-settings"><?php esc_html_e('Reviews added', 'wpdsr'); ?></div>
</div>
