<?php
/**
 *  Template Name: BLOG
 *
 */

// This fetches our themes Header
get_header(); ?>
  <div id="primary" class="content-area">
    <main id="main" class="site-main">
      <h1 class="category--title">Our Blog:</h1>

      <!-- Our filters -->
      <span class="category--filters">
        <strong>Filter your selection</strong>
        <form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="filter" class="posts-filter">
        
          <!-- This dropdown fetches the types of Blogs (subcategories of 'News') that we have -->
          <!-- The first 'foreach' is to get everything in 'News' -->
          <!-- The second one is for getting each subcategory -->
          <select  name="category-type"> 
            <?php 
              $categories = get_categories( array( 'child_of' => 13 ) );
              $this_cat = get_categories( array( 'name' => 'News' )  );
            ?>
            <option disabled hidden selected="true" value="false">Blog type</option>
              <?php 
                foreach ( $this_cat as $category_blog ) {
                  printf( '<option value="%1$s">Everything (%2$s)</option>',
                    esc_attr( $category_blog->cat_ID ),
                    esc_html( $category_blog->category_count )
                  );
                }
                foreach ( $categories as $category_blog ) {
                  printf( '<option calss="wp_categories" value="%1$s">%2$s (%3$s)</option>',
                    esc_attr( $category_blog->cat_ID ),
                    esc_html( $category_blog->cat_name ),
                    esc_html( $category_blog->category_count )
                  );
                }
                ?>
          </select>
          <select  name="category-order">
            <option disabled selected="true" value="false">Order by</option>
            <option value="DESC" name="order">Newer First</option>
            <option value="ASC" name="order">Older First</option>
            <option value="rand" name="order">Random</option>
          </select>
          <input type="hidden" name="action" value="myfilter">
          <input type="hidden" name="category-num" value="13">
          <button>Apply filter</button>
				</form>
			</span>

			<div class="category--list--blog grid">

				<div class="big grid-item sticky">
					<?php 
						// The Query
						$the_args = array(
							'post__in' => get_option( 'sticky_posts' ),
							'ignore_sticky_posts' => 1,
							'order' => 'ASC' );
 
						$sticki = new WP_Query( $the_args );

						// The Loop
						while ( $sticki->have_posts() ) : $sticki->the_post(); ?>
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail(); ?>
								<span class="category--list--item category--list--item--blog"><?php the_title(); ?></span>
							</a>
							<span class="the-tags">
								<?php the_tags( '', '', '' ); ?>							
							</span>
							<span class="the-date"><?php echo get_the_date('d-m-Y'); ?></span>
							<span class="the-excerpt">
							<?php 
							if ( has_excerpt( $sticki->ID ) ) {
								the_excerpt();
							} else { ?>
								<em>It seems you have to enter this post to read more info....</em>
							<?php } ?>
								
							</span>
							<a href="<?php echo get_post_permalink( $sticki->ID ); ?>" class="post-read-more">read more</a>
						<?php

						endwhile;
						wp_reset_postdata();
					?>
				</div>

				<?php
				$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

				$args = array(
					'post__not_in' => get_option( 'sticky_posts'),
					'posts_per_page' => 6,
					'category_name' => 'News',
					'paged' => $paged,
				);

				_fanari_blogs($args);

				?>
			</div>


		</main><!-- #main -->

		<?php wp_reset_query(); 
			  wp_reset_postdata(); ?>
	</div><!-- #primary -->

	<script>
		$(window).load( function() {

			var filter = $('#filter'),
				grid = $('.grid').imagesLoaded( function() {
							// init Packery after all images have loaded
							grid.packery({
							  	// options
							  	imagesLoaded: true,
							  	itemSelector: '.grid div, .grid .prev-next-posts',
							  	transitionDuration: 0,
							  	gutter: 10,
								percentPosition: true,
							});
						});

			$('#filter').submit(function(e){
				e.preventDefault();
				if ($('#filter select').val() != null) {
					$.ajax({
						url:filter.attr('action'),
						data:filter.serialize(), // form data
						type:filter.attr('method'), // POST
						beforeSend:function(xhr){
							filter.addClass('processing');
							grid.find('.prev-next-posts').remove();
						},
						success:function(data){
							filter.removeClass('processing');
							$('.category--filters strong').removeClass('select-pls');
							var grid = $('.grid').imagesLoaded( function() {
								// init Packery after all images have loaded
								grid.packery({
								  	// options
								  	imagesLoaded: true,
								  	itemSelector: '.grid div, .grid .prev-next-posts',
								  	transitionDuration: 0,
								  	gutter: 10,
									percentPosition: true,
								});
							});
							var $data = $(data);
							grid.html($data);
							grid.packery('appended', $data);
						}
					});
					return false;

				} else {
					e.preventDefault();
					$('.category--filters strong').addClass('select-pls');
				}
			});

		});
	</script>

<?php

// This gets the footer of our Wordpress theme
get_footer();
