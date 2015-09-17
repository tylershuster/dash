<?php //echo do_shortcode('[dash-setkeys]'); ?>
</body>
<script type="application/javascript">var dashPublicKey = <?php echo get_option('dash_publickey') ? "'".str_replace(	"\n", "\\\n",get_option('dash_publickey'))."'" : 0; ?>;</script>
<?php wp_footer(); ?>
</html>