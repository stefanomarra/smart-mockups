<?php

/**
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 *
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/public/templates
 */


$post_ID = get_the_ID();

$post_data = array();
$post_data['mockup_image_id'] = get_post_meta( $post_ID, 'mockup_image_id', true );
$post_data['mockup_image_url'] = '';

if ( $post_data['mockup_image_id'] )
	$post_data['mockup_image_url'] = wp_get_attachment_url( $post_data['mockup_image_id'] );

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
        <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) ?>../css/min/smart-reviews-public.css">


        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url( "admin-ajax.php" );?>';
            var post_id = <?php echo $post_ID; ?>;
        </script>

        <!-- JavaScripts -->
        <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ) ?>../js/autosize.js"></script>
        <script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ) ?>../js/smart-reviews-public.js"></script>

        <?php do_action( 'smartreviews_single_after_scripts' ); ?>

    </head>
    <body>
    	<div id="sr-mockup-viewport">
    		<main class="sr-mockup-wrapper">
    			<div class="sr-mockup-image"><img id="sr-mockup-image-src" src="<?php echo $post_data['mockup_image_url']; ?>"></div>
    			<div class="sr-mockup-dots"></div>
    			<div class="sr-mockup-review"></div>
    		</main>
    		<div id="dot_template" class="dot-feedback empty new template">
    			<div class="dot"><span></span></div>
    			<div class="feedback-content">
    				<div class="feedback-action">
    					<button class="feedback-delete"><i class="fa fa-trash-o"></i></button>
    					<button class="feedback-close"><i class="fa fa-close"></i></button>
    				</div>
    				<div class="feedback-comments">
    					<ul class="feedback-comment-list"></ul>
    					<form class="feedback-comment-form" action="#">
    						<p class="feedback-field-wrapper">
    							<textarea placeholder="Write a comment..." class="feedback-field-comment" name="feedback_field_comment"></textarea>
    						</p>
    						<p class="feedback-field-wrapper-submit">
    							<input type="submit" class="feedback-field-submit" value="Post this Comment">
    						</p>
    					</form>
    				</div>
    			</div>
    		</div>
    	</div>
    </body>
</html>