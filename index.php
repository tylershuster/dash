<?php get_header();

$recent_clients = get_user_meta( get_current_user_id(), '_dash_recent_clients', true );
$recent_clients = $recent_clients ? $recent_clients : array();
$recent_sites = get_user_meta( get_current_user_id(), '_dash_recent_sites', true );
$recent_sites = $recent_sites ? $recent_sites : array();
$recent_credentials = get_user_meta( get_current_user_id(), '_dash_recent_credentials', true );
$recent_credentials = $recent_credentials ? $recent_credentials : array();
$recent_references = get_user_meta( get_current_user_id(), '_dash_recent_references', true );
$recent_references = $recent_references ? $recent_references : array();
?>

<div class="cards">

	<div id="clients" class='card'>
		<h2>Clients</h2>
		<ul>
			<?php foreach( $recent_clients as $client_id ) echo "<li><a href='" . get_the_permalink( $client_id ) . "'>" . get_the_title( $client_id ) . "</a></li>"; ?>
			<li class='new-post'><input type='text' data-type='client' placeholder='New Client' /><input type='submit' class='new-post' value='create'/></li>
		</ul>
	</div>

	<div id="sites" class='card'>
		<h2>Sites</h2>
		<ul>
			<?php foreach( $recent_sites as $site_id ) echo "<li><a href='" . get_the_permalink( $site_id ) . "'>" . get_the_title( $site_id ) . "</a></li>"; ?>
			<li class='new-post'><input type='text' data-type='site' placeholder='New Site' /><input type='submit' class='new-post' value='create'/></li>
		</ul>
	</div>

	<div id="credentials" class='card'>
		<h2>Credentials</h2>
		<ul>
			<?php foreach( $recent_credentials as $credential_id ) echo "<li><a href='" . get_the_permalink( $credential_id ) . "'>" . get_the_title( $credential_id ) . "</a></li>"; ?>
			<li class='new-post'><input type='text' data-type='credential' placeholder='New Credential' /><input type='submit' class='new-post' value='create'/></li>
		</ul>
	</div>

	<div id="references" class='card'>
		<h2>References</h2>
		<ul>
			<?php foreach( $recent_references as $reference_id ) echo "<li><a href='" . get_the_permalink( $reference_id ) . "'>" . get_the_title( $reference_id ) . "</a></li>"; ?>
			<li class='new-post'><input type='text' data-type='reference' placeholder='New Reference' /><input type='submit' class='new-post' value='create'/></li>
		</ul>
	</div>

</div>
<?php get_footer(); ?>