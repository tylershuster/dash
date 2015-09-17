<?php

get_header();

	$Client = ClientFactory::create( get_the_id() );

	echo "<ul class='cards'>";

	foreach( $Client->Cards as $Card ){

		echo $Card->Render();

	}

	echo "</ul>";

	echo '<script type="application/javascript">var post_id = "' . get_the_id() . '"</script>';

get_footer(); ?>