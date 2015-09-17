<?php

get_header();

if( have_posts() ):

	while( have_posts() ): the_post();

		echo "<h1>" . get_the_title() . "</h1>";

		//DEBUG: echo "<pre>" . print_r( get_post_meta( get_the_id() ), true ) . "</pre>";

		$Site = SiteFactory::create( get_the_id() );

		echo "<ul class='cards'>";

		foreach( $Site->Cards as $Card ){

			echo $Card->Render();

		}

		echo "</ul>";

		echo '<script type="application/javascript">var post_id = "' . get_the_id() . '"</script>';

	endwhile;

else:

	echo "post not found";

endif;

get_footer(); ?>