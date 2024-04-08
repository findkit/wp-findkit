<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

/**
 * Monkey patch to fix history.state bug in wp-admin
 *
 * WordPress incorrectly clears history.state on every wp-admin page load
 * See: https://github.com/WordPress/WordPress/blob/6.4.3/wp-admin/includes/misc.php#L1403-L1407
 *
 * This breaks FindkitUI scroll and focus restoration when coming back from a
 * search result page.
 *
 * This is a known issue but there has been no movement on it for years, hence this workaround.
 * See https://core.trac.wordpress.org/ticket/54568
 */
class BugFixWpAdminHistoryState
{
	function bind()
	{
		// Inject early to be before the buggy call
		// See https://github.com/WordPress/WordPress/blob/6.4.3/wp-admin/includes/admin-filters.php#L49
		\add_action('admin_head', [$this, '__action_admin_head'], -10, 0);
	}

	function __action_admin_head()
	{
		// Replace the history.repaceState with a wrapper function which detects
		// the erronous call and fixes it to keep the history state correctly.
		// Afterwards restore the original function.
		?>
        <script>
        // Bugfix for #54568 by wp-findkit
        // https://core.trac.wordpress.org/ticket/54568
        (() => {
            const original = history.replaceState;
            history.replaceState = function findkitReplaceStatePatch(state, unused, url) {
                const canonical = document.getElementById('wp-admin-canonical').href + window.location.hash;
                if (state === null && url === canonical) {
                    original.call(history, history.state, unused, url);
                } else {
                    original.call(history, state, unused, url);
                }
                history.replaceState = original;
            }
        })();
        </script>
  		<?php
	}
}
