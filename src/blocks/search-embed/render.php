<?php
$json = wp_json_encode($attributes);
$placehoder = empty($attributes['inputPlaceholder'])
	? 'Search...'
	: $attributes['inputPlaceholder'];

// prettier...
?>

<div class="wp-block-findkit-search-embed" data-attributes="<?php echo esc_attr(
	$json
); ?>">
    <div class="wp-findkit-input-wrap">
        <input
            class="wp-findkit-search-input"
            type="search"
            placeholder=<?php echo esc_attr($placehoder); ?>
        />
    </div>

    <div class="wp-findkit-container" ></div>

    <?php echo $content; ?>
</div>
