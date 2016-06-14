<?php
/**
 * Single Posts Template
 *
 * Please do not edit this file. This file is part of the Cyber Chimps Framework and all modifications
 * should be made in a child theme.
 *
 * @category CyberChimps Framework
 * @package  Framework
 * @since    1.0
 * @author   CyberChimps
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v3.0 (or later)
 * @link     http://www.cyberchimps.com/
 */
 
get_header(); ?>

<div id="single_page" class="container-full-width">
	
	<div class="container">
		
		<div class="container-fluid">
		
			<?php do_action( 'cyberchimps_before_container'); ?>
			
			<div id="container" <?php cyberchimps_filter_container_class(); ?>>
				
				<?php do_action( 'cyberchimps_before_content_container'); ?>
				
				<div id="content" <?php cyberchimps_filter_content_class(); ?>>
					
					<?php do_action( 'cyberchimps_before_content'); ?>
					
					<?php while ( have_posts() ) : the_post(); ?>
			
						<?php get_template_part( 'content', 'single' ); ?>
							
					<div class="more-content">
					  <div class="row-fluid">
						<div class="span6 previous-post">
						  <?php previous_post_link(); ?>
						</div>
						<div class="span6 next-post">
						  <?php next_post_link(); ?>
						</div>
					  </div>
					</div>
				  
						<?php
							// If comments are open or we have at least one comment, load up the comment template
							if ( comments_open() || '0' != get_comments_number() )
								comments_template( '', true );
						?>
			
					<?php endwhile; // end of the loop. ?>
				
					<?php do_action( 'cyberchimps_after_content'); ?>
					
				</div><!-- #content -->
				
				<?php do_action( 'cyberchimps_after_content_container'); ?>
					
			</div><!-- #container .row-fluid-->
			
			<?php do_action( 'cyberchimps_after_container'); ?>
		
		</div><!--container fluid -->
		
	</div><!-- container -->

</div><!-- container full width -->

<?php get_footer(); ?>