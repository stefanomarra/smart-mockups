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
$mockup = new Smart_Mockups_Post( $post_id );

$mockup_data = array(
	'mockup'  => $mockup->get_mockup(),
	'settings'   => array(
            'credits'            => get_option('smartmockups_credits', 1)
		),
	'feedbacks'        => $mockup->get_feedbacks(),
	'discussion'       => $mockup->get_discussion(),
    'approval'         => $mockup->get_approval_signature()
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
            #sr-mockup-viewport main.sr-mockup-wrapper .sr-mockup-dots .sr-feedback .sr-dot { background-color: <?php echo $mockup->get( 'color_feedback_dot' ); ?>; }
            body { background-color: <?php echo $mockup->get( 'color_background' ); ?>; }
        </style>

        <script type="text/javascript">
            var ajax_url = '<?php echo admin_url( "admin-ajax.php" );?>';
            var post_id = <?php echo $post_id; ?>;
            var mockup_options = {
            	'feedbacks_enabled' 	: <?php echo (int)$mockup->is_enabled( 'feedbacks' ); ?>,
            	'discussion_enabled' 	: <?php echo (int)$mockup->is_enabled( 'discussion' ); ?>,
            	'approval_enabled' 		: <?php echo (int)( $mockup->is_enabled( 'approval' ) && !$mockup->get_approval_signature() ); ?>
            };
        </script>

        <!-- JavaScripts -->
        <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ) ?>../js/autosize.js"></script>
        <script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ) ?>../js/jquery.modal.min.js"></script>
        <script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ) ?>../js/tipr.min.js"></script>
        <script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ) ?>../js/smart-mockups-public.js"></script>

        <?php do_action( 'smartmockups_single_after_scripts' ); ?>

        <?php //wp_head(); ?>
    </head>
    <body>

    	<?php /* Viewport */ ?>
    	<div id="sr-mockup-viewport" class="<?php echo join( ' ', $mockup->get_wrapper_classes() ); ?>">

    		<?php /* Header */ ?>
    		<header id="sr-header">
    			<nav class="sr-nav">
    				<ul class="sr-navbar">
                        <h1 class="sm-mockup-title"><?php the_title(); ?></h1>
                    </ul>
                    <ul class="sr-navbar sr-navbar-right">
    					<li class="active" data-tip="<?php _e( 'Show/Hide Feedbacks', SMART_MOCKUPS_DOMAIN ); ?>"><a class="sr-toggle-feedbacks" href="#"><i class="fa fa-eye-slash"></i></a></li>
    					<?php if ( $mockup->is_enabled( 'discussion' ) ) : ?><li data-tip="<?php _e( 'Show/Hide Discussion Panel', SMART_MOCKUPS_DOMAIN ); ?>"><a class="sr-toggle-discussion-panel" href="#"><i class="fa fa-comment"></i></a></li><?php endif; ?>
                        <?php if ( $mockup->is_enabled( 'help_text' ) ) : ?><li data-tip="<?php _e( 'Need Help?', SMART_MOCKUPS_DOMAIN ); ?>"><a class="sr-mockup-help-text" href="#sr-modal-help-text" rel="modal:open"><i class="fa fa-question"></i></a></li><?php endif; ?>
    					<?php if ( $mockup->is_enabled( 'approval' ) && !$mockup->get_approval_signature() ) : ?><li data-tip="<?php _e( 'Approve this Mockup', SMART_MOCKUPS_DOMAIN ); ?>"><a class="sr-mockup-approval" href="#sr-modal-approval" rel="modal:open"><i class="fa fa-check"></i> <?php _e( 'Approve', SMART_MOCKUPS_DOMAIN ); ?></a></li><?php endif; ?>
                        <?php if ( is_array( $mockup_data['approval'] ) ) : ?><li><span class="sr-mockup-approved"><?php _e('Mockup Approved', SMART_MOCKUPS_DOMAIN ); ?> <small><?php _e('by', SMART_MOCKUPS_DOMAIN ); ?> <?php echo $mockup_data['approval']['signature']; ?></small></span></li><?php endif; ?>
    				</ul>
    			</nav>
    		</header>

    		<?php /* Mockup */ ?>
    		<main class="sr-mockup-wrapper">
    			<div class="sr-mockup-image"><img id="sr-mockup-image-src" src="<?php echo $mockup_data['mockup']['url']; ?>"></div>
    			<div class="sr-mockup-dots"></div>
	    		<div class="sr-mockup-discussion">
                    <h3 class="discussion-title"><?php _e('Mockup Discussion', SMART_MOCKUPS_DOMAIN ); ?></h3>
                    <ul class="discussion-comment-list"><?php echo $mockup_data['discussion']['comments']; ?></ul>
                    <form class="discussion-comment-form">
                        <p class="discussion-field-wrapper">
                            <textarea placeholder="<?php _e('Write a comment...', SMART_MOCKUPS_DOMAIN ); ?>" class="discussion-field-comment" name="discussion"></textarea>
                        </p>
                        <p class="discussion-field-wrapper-submit">
                            <input type="submit" class="discussion-field-submit" value="<?php _e( 'Post this Comment', SMART_MOCKUPS_DOMAIN ); ?>">
                        </p>
                    </form>
                </div>
    		</main>

    		<?php /* Footer */ ?>
    		<footer id="sr-footer">
    			<?php if ( $mockup_data['settings']['credits'] ) : ?>
    				<div class="sm-credits">Made with <a href="https://wordpress.org/plugins/smart-mockups/" target="_blank">Smart Mockups</a></div>
    			<?php endif; ?>
    		</footer>

    		<?php /* Pre Load Feedbacks */ ?>
            <div id="sr-feedback-loader">
                <?php foreach ( $mockup_data['feedbacks'] as $id => $feedback ) : ?>
                    <div class="sr-feedback-preload" data-id="<?php echo $id; ?>" data-x="<?php echo $feedback['x']; ?>" data-y="<?php echo $feedback['y']; ?>"><div class="comments"><?php echo join('', $feedback['comments']); ?></div></div>
                <?php endforeach; ?>
            </div>

    		<?php /* Feedback Template */ ?>
    		<div id="sr-feedback-template" class="sr-feedback empty new template">
    			<div class="sr-dot"><span></span></div>
    			<div class="sr-feedback-content">
    				<div class="feedback-status">
    					<div class="feedback-draft">DRAFT</div>
    				</div>
    				<div class="feedback-action">
    					<button class="feedback-delete"><i class="fa fa-trash-o"></i></button>
    					<button class="feedback-close"><i class="fa fa-close"></i></button>
    				</div>
    				<div class="feedback-comments">
    					<ul class="feedback-comment-list"></ul>
    					<form class="feedback-comment-form">
    						<p class="feedback-field-wrapper">
    							<textarea placeholder="<?php _e('Write a comment...', SMART_MOCKUPS_DOMAIN ); ?>" class="feedback-field-comment" name="feedback"></textarea>
    						</p>
    						<p class="feedback-field-wrapper-submit">
    							<input type="submit" class="feedback-field-submit" value="<?php _e( 'Post this Comment', SMART_MOCKUPS_DOMAIN ); ?>">
    						</p>
    					</form>
    				</div>
    			</div>
    		</div>

            <?php /* Approval Modal */ ?>
            <div id="sr-modal-approval" class="sm-modal">
                <h3 class="sr-approval-title"><?php _e( 'Approve this mockup', SMART_MOCKUPS_DOMAIN ); ?></h3>
                <p class="sr-approval-description"><?php _e( 'By entering the digital signature below, you approve the underneath mockup.', SMART_MOCKUPS_DOMAIN ); ?></p>
                <form class="sr-approval-signature-form">
                    <input class="sr-approval-signature-input" type="text" value="<?php echo is_array($mockup_data['approval'])?$mockup_data['approval']['signature']:'' ?>" placeholder="<?php _e( 'Digital Signature', SMART_MOCKUPS_DOMAIN ); ?>" />
                    <input class="sr-approval-signature-submit" type="submit" name="submit" value="<?php _e( 'Approve', SMART_MOCKUPS_DOMAIN ); ?>" />
                    <a class="sr-modal-close" href="#close-modal" rel="modal:close"><?php _e( 'Cancel', SMART_MOCKUPS_DOMAIN ); ?></a>
                </form>
            </div>

            <?php /* Help Text Modal */ ?>
            <?php if ( $mockup->is_enabled( 'help_text' ) ) : ?>
                <div id="sr-modal-help-text" class="sm-modal">
                    <div class="sr-modal-content"><?php echo $mockup->get_help_text(); ?></div>
                    <a class="sr-modal-close" href="#close-modal" rel="modal:close"><?php _e( 'Close', SMART_MOCKUPS_DOMAIN ); ?></a>
                </div>
            <?php endif; ?>

    	</div>
        <?php //wp_footer(); ?>
    </body>
</html>