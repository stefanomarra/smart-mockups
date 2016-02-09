<?php

/**
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 *
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/public/templates
 */

$post_id = get_the_ID();

$mockup_data = array(
    'customization'    => array(
            'background_color'   => get_post_meta( $post_id, 'color_background', true )
        )
);

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo get_the_title(); ?></title>

        <!-- Font Files -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

        <!-- Stylesheets -->
        <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) ?>../css/min/smart-mockups-public.css">

        <style type="text/css">
            body { background-color: <?php echo $mockup_data['customization']['background_color']; ?>; }
        </style>

        <?php do_action( 'smartmockups_single_after_scripts' ); ?>

    </head>
    <body>
	    <div id="sr-password-form-wrapper">
	    	<?php echo get_the_password_form(); ?>
    	</div>
    </body>
</html>