<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php wp_title(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body>

	<header>
		<nav>
			<ul>
				<li><a href="<?php echo get_bloginfo('url'); ?>">Home</a></li>
				<li>
					<select name="dash_clients">
						<option>- clients -</option>
						<?php foreach( ClientFactory::select('url') as $url => $name ) echo "<option value='$url'>$name</option>"; ?>
					</select>
				</li>
				<li>
					<select name="dash_sites">
						<option>- sites -</option>
						<?php foreach( SiteFactory::select('url') as $url => $name ) echo "<option value='$url'>$name</option>"; ?>
					</select>
				</li>
			</ul>
		</nav>
	</header>
