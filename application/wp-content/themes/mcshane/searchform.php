<form class="search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
	<input type="text" name="s" placeholder="Search" value="<?php the_search_query(); ?>" />
	<input type="submit" value="" />
</form>