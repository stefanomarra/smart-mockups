<form method="post" action="options.php">
    <?php
		$settings = array(
			'notifications' 	=> sm_is_notification_enabled()
		);

    	settings_fields('smartmockups_settings_notifications');

    ?>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" valign="top">
                    <label class="notifications" for="smartmockups_notifications"><?php _e('Notifications'); ?></label>
                </th>
                <td>
                    <input id="smartmockups_notifications" name="smartmockups_notifications" type="checkbox" value="1" <?php echo ( $settings['notifications'] )?'checked':''; ?> />
                    <label for="smartmockups_notifications">Check this to enable hourly scheduled notifications</label>
                </td>
            </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>

</form>