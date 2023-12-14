<div class="wp-block-findkit-search-modal" data-attributes="<?php echo esc_attr(
	wp_json_encode($attributes)
); ?>" onclick="(this.querySelector('a,img') || this).dataset.clicked=true">
    <?php \Findkit\Utils::echo_inner_blocks($content); ?>
</div>
