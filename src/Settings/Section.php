<?php

declare(strict_types=1);

namespace Findkit\Settings;

class Section
{
	private $section = null;
	private $page = null;
	private $title = null;
	private $fields = null;

	function __construct(string $page, string $section_name, array $options)
	{
		$this->fields = [];
		$this->section = $section_name;
		$this->page = $page;

		$this->title = $options['title'];

		\add_action('admin_init', [$this, '__action_admin_init']);
	}

	function get_section_name(): string
	{
		return $this->section;
	}

	function __action_admin_init()
	{
		$show = apply_filters(
			'findkit_show_settings_section',
			current_user_can('manage_options'),
			$this->section,
			$this->page
		);

		if (!$show) {
			return;
		}

		\add_settings_section($this->section, $this->title, null, $this->page);

		foreach ($this->fields as $field) {
			\register_setting($this->page, $field['name']);

			$id = esc_attr($this->get_id($field));

			\add_settings_field(
				$field['name'],
				"<label for='$id'>" . $field['title'] . '</label>',
				function () use ($field) {
					$this->render_input($field);
				},
				$this->page,
				$this->section
			);
		}
	}

	function add_field($field): Section
	{
		if (isset($field['default'])) {
			$option = $field['name'];
			\add_filter("default_option_{$option}", function () use ($field) {
				return $field['default'];
			});
		}

		$this->fields[] = $field;
		return $this;
	}

	function get_id(array $field): string
	{
		return $this->section . "-option-{$field['name']}";
	}

	function render_input(array $field)
	{
		// Intentionally allowing html in the description
		$description = $field['description'] ?? '';
		$placeholder = esc_attr($field['placeholder'] ?? '');
		$disabled = $field['disabled'] ?? false;
		$type = $field['type'] ?? 'input';
		$rows = $field['rows'] ?? '25';
		$option = $field['name'];

		if ($type === 'input') { ?>
            <input
                type="text"
                style="width: 100%"
                <?php echo $disabled ? 'disabled' : ''; ?>
                <?php echo $placeholder ? "placeholder='$placeholder'" : ''; ?>
                name="<?php echo esc_attr($option); ?>"
                id="<?php echo esc_attr($this->get_id($field)); ?>"
                value="<?php echo get_option($option, ''); ?>"

            />
        <?php } elseif ($type === 'textarea') { ?>

            <textarea
                style="width: 100%"
                rows=<?php echo esc_attr($rows); ?>
                type="text"
                id="<?php echo esc_attr($this->get_id($field)); ?>"
                name="<?php echo esc_attr($option); ?>"
                value=""

            ><?php echo get_option($option, ''); ?></textarea>

            <?php } elseif ($type === 'checkbox') { ?>

            <input
                type="checkbox"
                name="<?php echo esc_attr($option); ?>"
                id="<?php echo esc_attr($this->get_id($field)); ?>"
                value="1"
                <?php checked(1, get_option($option), true); ?> />

            <?php }
		?>

		<p class="description">
			<?php echo $description; ?>
			<sub>(<?php echo esc_html($option); ?>)</sub>
		</p>
		<?php
	}
}
