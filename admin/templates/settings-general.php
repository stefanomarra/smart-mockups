<form method="post" action="options.php">
    <?php
    	$post_types = Smart_Mockups_Setup::post_types();

		$settings = array(
			'slug' 		=> get_option('smartmockups_slug', $post_types[SMART_MOCKUPS_POSTTYPE]['rewrite']['slug']),
			'credits' 	=> get_option('smartmockups_credits', 1)
		);

    	settings_fields('smartmockups_settings');
    ?>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" valign="top">
                    <label class="description" for="smartmockups_slug"><?php _e('Custom Slug'); ?></label>
                </th>
                <td>
                    <?php echo get_site_url(); ?>/
                    <input id="smartmockups_slug" name="smartmockups_slug" type="text" class="regular-text" value="<?php esc_attr_e( $settings['slug'] ); ?>" />

                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <label class="credits" for="smartmockups_credits"><?php _e('Show Credits'); ?></label>
                </th>
                <td>
                    <input id="smartmockups_credits" name="smartmockups_credits" type="checkbox" value="1" <?php echo ( $settings['credits'] )?'checked':''; ?> />
                    <label for="smartmockups_credits">Check this box to help Smart Reviews by showing a credits message in your site's footer</label>
                </td>
            </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>

</form>