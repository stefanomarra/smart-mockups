<form method="post" action="options.php">
    <?php
    	$post_types = Smart_Reviews_Setup::post_types();

		$settings = array(
			'slug' => get_option('smartreviews_slug', $post_types['smartreview']['rewrite']['slug'])
		);

    	settings_fields('smartreviews_settings');
    ?>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" valign="top">
                    <label class="description" for="smartreviews_slug"><?php _e('Custom Slug'); ?></label>
                </th>
                <td>
                    <?php echo get_site_url(); ?>/
                    <input id="smartreviews_slug" name="smartreviews_slug" type="text" class="regular-text" value="<?php esc_attr_e( $settings['slug'] ); ?>" />

                </td>
            </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>

</form>