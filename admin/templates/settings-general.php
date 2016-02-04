<form method="post" action="options.php">
    <?php
    	$post_types = Smart_Reviews_Setup::post_types();

		$settings = array(
			'slug' 		=> get_option('smartreviews_slug', $post_types[SMART_REVIEWS_POSTTYPE]['rewrite']['slug']),
			'credits' 	=> get_option('smartreviews_credits', 1)
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
            <tr valign="top">
                <th scope="row" valign="top">
                    <label class="credits" for="smartreviews_credits"><?php _e('Show Credits'); ?></label>
                </th>
                <td>
                    <input id="smartreviews_credits" name="smartreviews_credits" type="checkbox" value="1" <?php echo ( $settings['credits'] )?'checked':''; ?> />
                    <label for="smartreviews_credits">Check this box to help Smart Reviews by showing a credits message in your site's footer</label>
                </td>
            </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>

</form>