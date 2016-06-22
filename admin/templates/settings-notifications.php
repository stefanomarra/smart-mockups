<form method="post" action="options.php">
    <?php
		$settings = array(
			'notifications' 	=> sm_is_notification_enabled(),
            'recurrence'        => sm_get_notification_recurrence()
		);

    	settings_fields('smartmockups_settings_notifications');

    ?>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" valign="top">
                    <label class="notifications" for="smartmockups_notifications"><?php _e('Notifications', SMART_MOCKUPS_DOMAIN); ?></label>
                </th>
                <td>
                    <input id="smartmockups_notifications" name="smartmockups_notifications" type="checkbox" value="1" <?php echo ( $settings['notifications'] )?'checked':''; ?> />
                    <label for="smartmockups_notifications"><?php _e('Check this to enable scheduled notifications', SMART_MOCKUPS_DOMAIN); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <label class="notifications" for="smartmockups_notifications_recurrence"><?php _e('Recurrence', SMART_MOCKUPS_DOMAIN); ?></label>
                </th>
                <td>
                    <select id="smartmockups_notifications_recurrence" name="smartmockups_notifications_recurrence">
                        <option value="hourly" <?php echo ( $settings['recurrence'] == 'hourly' )?'selected="selected"':''; ?>><?php _e('Hourly', SMART_MOCKUPS_DOMAIN); ?></option>
                        <option value="twicedaily" <?php echo ( $settings['recurrence'] == 'twicedaily' )?'selected="selected"':''; ?>><?php _e('Twice Daily', SMART_MOCKUPS_DOMAIN); ?></option>
                        <option value="daily" <?php echo ( $settings['recurrence'] == 'daily' )?'selected="selected"':''; ?>><?php _e('Daily', SMART_MOCKUPS_DOMAIN); ?></option>
                    </select>
                    <label for="smartmockups_notifications_recurrence"></label>
                </td>
            </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>

</form>