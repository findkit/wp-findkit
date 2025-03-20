## v1.4.0

2025-03-20

This is a breaking change in terms of error handling. Rather than throwing Exceptions, the public api php functions return WP_Error. More detailed errors will be logged if WP_DEBUG is defined.

-   Change the way the plugin handles errors. Rather than throwing exceptions, log errors. [f76592f](https://github.com/findkit/wp-findkit/commit/f76592f) - Lauri Saarni


All changes https://github.com/findkit/wp-findkit/compare/v1.3.2...v1.4.0

## v1.3.2

2025-02-25

Small maintenance realease.

All changes https://github.com/findkit/wp-findkit/compare/v1.3.1...v1.3.2

## v1.3.1

2024-12-11

-   bumb tested up to [3c5d196](https://github.com/findkit/wp-findkit/commit/3c5d196) - Joonas Varis

All changes https://github.com/findkit/wp-findkit/compare/v1.3.0...v1.3.1

## v1.3.0

2024-09-12

-   Make sidebar title configureable and document it [e1f450e](https://github.com/findkit/wp-findkit/commit/e1f450e) - Joonas Varis

All changes https://github.com/findkit/wp-findkit/compare/v1.2.0...v1.3.0

## v1.2.0

2024-06-10

- Enqueue block editor assets correctly [a48db25](https://github.com/findkit/wp-findkit/commit/a48db25) - Esa-Matti Suuronen
- Fix search group visibility in the block editor [40562b2](https://github.com/findkit/wp-findkit/commit/40562b2) - Esa-Matti Suuronen
- Make "Add example groups" button visible [f13a86f](https://github.com/findkit/wp-findkit/commit/f13a86f) - Esa-Matti Suuronen
- Add min-height to the search embed block [6eb07f7](https://github.com/findkit/wp-findkit/commit/6eb07f7) - Esa-Matti Suuronen
- Rename "Content No Highlight" to "Hidden keywords" [c02df7a](https://github.com/findkit/wp-findkit/commit/c02df7a) - Esa-Matti Suuronen
- Add `findkit_search()` php function [26b9833](https://github.com/findkit/wp-findkit/commit/26b9833) - Esa-Matti Suuronen
- Add wpPostId to custom fields [13ca88c](https://github.com/findkit/wp-findkit/commit/13ca88c) - Esa-Matti Suuronen
- Make only the save button primary on the settings view [a6adeb9](https://github.com/findkit/wp-findkit/commit/a6adeb9) - Esa-Matti Suuronen
- Add JWT settings UI [ed9cfe8](https://github.com/findkit/wp-findkit/commit/ed9cfe8) - Esa-Matti Suuronen
- Fix search button on settings page [49f673d](https://github.com/findkit/wp-findkit/commit/49f673d) - Esa-Matti Suuronen
- Ignore 1password from Findkit settings [06158c8](https://github.com/findkit/wp-findkit/commit/06158c8) - Esa-Matti Suuronen
- Use password input for API Key [f1c756c](https://github.com/findkit/wp-findkit/commit/f1c756c) - Esa-Matti Suuronen
- Apply `the_title` filter on page meta titles [8ff7709](https://github.com/findkit/wp-findkit/commit/8ff7709) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v1.1.0...v1.2.0

## v1.1.0

2024-05-17

- Upgrade @findkit/ui to v1.1.0 [442bb5c](https://github.com/findkit/wp-findkit/commit/442bb5c) - Esa-Matti Suuronen
- Do not log API calls by default [0a3dce5](https://github.com/findkit/wp-findkit/commit/0a3dce5) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v1.0.2...v1.1.0

## v1.0.2

2024-04-11

- Upgrade `@findkit/ui` to v1.0.3

All changes https://github.com/findkit/wp-findkit/compare/v1.0.1...v1.0.2

## v1.0.1

2024-04-09

- Upgrade `@findkit/ui` to v1.0.1

All changes https://github.com/findkit/wp-findkit/compare/v1.0.0...v1.0.1

## v1.0.0

2024-04-09

- Use `@findkit/ui` v1.0.0 in Blocks and wp-admin search
  - Read the [announcement](https://www.findkit.com/findkit-ui-v1-0-a-leap-in-accessibility/) and its [changelog](https://github.com/findkit/findkit/blob/main/packages/ui/CHANGELOG.md#v100)
  - The breaking changes affect the plugin users only if they have customized the builtin blocks the UI with the [`init`](https://docs.findkit.com/ui/api/events/#init) event.
  - Eg. if you have bundled earlier version of Findkit UI to your theme you can update to this version without any changes.
- Add workaround for WordPress ticket [54568](https://core.trac.wordpress.org/ticket/54568) to fix scroll and focus restoration in wp-admin
- Use `router: "hash"` in Findkit UI Blocks to avoid colliding with the Interactivity API popstate monitoring
  - See https://github.com/WordPress/gutenberg/issues/60455

All changes https://github.com/findkit/wp-findkit/compare/v0.5.10...v1.0.0

## v0.5.10

2024-02-26

- Upgrade @findkit/ui to v0.22.0 [34f789a](https://github.com/findkit/wp-findkit/commit/34f789a) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.5.9...v0.5.10

## v0.5.9

2024-02-15

- Ensure meta tag is rendered on all pages [e2f20ac](https://github.com/findkit/wp-findkit/commit/e2f20ac) - Esa-Matti Suuronen
- check prettier in ci [efea203](https://github.com/findkit/wp-findkit/commit/efea203) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.5.8...v0.5.9

## v0.5.8

2024-01-29

- Enqueue block scripts properly to block editor [7d14b14](https://github.com/findkit/wp-findkit/commit/7d14b14) - Esa-Matti Suuronen
- Fixes some js crashes on wp-admin

All changes https://github.com/findkit/wp-findkit/compare/v0.5.7...v0.5.8

## v0.5.7

2024-01-16

- Ignore only map files from the build [d55e751](https://github.com/findkit/wp-findkit/commit/d55e751) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.5.6...v0.5.7

## v0.5.6

2024-01-12

- Add wp playground blueprint [24e28ad](https://github.com/findkit/wp-findkit/commit/24e28ad) - Esa-Matti Suuronen
- Add playground readme links [82d2de5](https://github.com/findkit/wp-findkit/commit/82d2de5) - Esa-Matti Suuronen
- Automatically configure the plugin in wp playground [3174d49](https://github.com/findkit/wp-findkit/commit/3174d49) - Esa-Matti Suuronen
- Handle missing openssl in wp playground [9331661](https://github.com/findkit/wp-findkit/commit/9331661) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.5.5...v0.5.6

## v0.5.5

2024-01-11

- Upgrade to @findit/ui v0.19.1 [e609cd3](https://github.com/findkit/wp-findkit/commit/e609cd3) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.5.4...v0.5.5

## v0.5.4

2024-01-10

- Upgrade to @findkit/ui v0.19.0 [f26bef6](https://github.com/findkit/wp-findkit/commit/f26bef6) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.5.3...v0.5.4

## v0.5.3

2024-01-04

- Do not enqueue admin search script if not enabled [b4e98e3](https://github.com/findkit/wp-findkit/commit/b4e98e3) - Esa-Matti Suuronen
- Upgrade @findkit/ui to v0.18.2 [5810bc9](https://github.com/findkit/wp-findkit/commit/5810bc9) - Esa-Matti Suuronen
- Fix media library filter ui going on top findkit ui [eed7339](https://github.com/findkit/wp-findkit/commit/eed7339) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.5.2...v0.5.3

## v0.5.2

2023-12-20

- Trap focus to admin search opener [c4c4de7](https://github.com/findkit/wp-findkit/commit/c4c4de7) - Esa-Matti Suuronen
- Upgrade @findkit/ui to v0.18.1 [ddcff89](https://github.com/findkit/wp-findkit/commit/ddcff89) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.5.1...v0.5.2

## v0.5.1

2023-12-19

- Build with correct npm package versions
- Always rebuild in release from now on

All changes https://github.com/findkit/wp-findkit/compare/v0.5.0...v0.5.1

## v0.5.0

2023-12-19

- Show backdrop by default on Search Modal block [62abb46](https://github.com/findkit/wp-findkit/commit/62abb46) - Esa-Matti Suuronen
- Upgrade to @findkit/ui v0.18.0 [e256b8d](https://github.com/findkit/wp-findkit/commit/e256b8d) - Esa-Matti Suuronen
- Use zero minTerms only for the embed block [ba3757e](https://github.com/findkit/wp-findkit/commit/ba3757e) - Esa-Matti Suuronen
- New instance id for admin search: admsearch [b970e52](https://github.com/findkit/wp-findkit/commit/b970e52) - Esa-Matti Suuronen
- Close admin search on outside click [eb82011](https://github.com/findkit/wp-findkit/commit/eb82011) - Esa-Matti Suuronen
- Fix admin search hiding admin menus [1bd7f25](https://github.com/findkit/wp-findkit/commit/1bd7f25) - Esa-Matti Suuronen
- Show lastly edited docs on the admin search if no search terms are entered [3bc51ad](https://github.com/findkit/wp-findkit/commit/3bc51ad) - Esa-Matti Suuronen
- Add filters for sidebar controls [f9c94e6](https://github.com/findkit/wp-findkit/commit/f9c94e6) - Esa-Matti Suuronen
- Add context to the admin search button label [5f89f55](https://github.com/findkit/wp-findkit/commit/5f89f55) - Esa-Matti Suuronen
- Add options for settings link in admin search [301634e](https://github.com/findkit/wp-findkit/commit/301634e) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.4.0...v0.5.0

## v0.4.0

2023-12-18

- Add color picker for search blocks [9161212](https://github.com/findkit/wp-findkit/commit/9161212) - Esa-Matti Suuronen
- Add options page filter [9c2ba4f](https://github.com/findkit/wp-findkit/commit/9c2ba4f) - Esa-Matti Suuronen
- Add contentNoHighlight input to the sidebar [29b4ffe](https://github.com/findkit/wp-findkit/commit/29b4ffe) - Esa-Matti Suuronen
- Offset admin search to menus [70e40b5](https://github.com/findkit/wp-findkit/commit/70e40b5) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.3.2...v0.4.0

## v0.3.2

2023-12-14

- plugin meta++ [d4c6ee9](https://github.com/findkit/wp-findkit/commit/d4c6ee9) - Esa-Matti Suuronen
- typo [2341511](https://github.com/findkit/wp-findkit/commit/2341511) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.3.1...v0.3.2

## v0.3.1

2023-12-14

- Plugin meta++ [3f02cb7](https://github.com/findkit/wp-findkit/commit/3f02cb7) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.3.0...v0.3.1

## v0.3.0

2023-12-14

- New Search Blocks [afe4aed](https://github.com/findkit/wp-findkit/commit/afe4aed) - Esa-Matti Suuronen
- Upgrade build-in @findkit/ui to v0.17.0

All changes https://github.com/findkit/wp-findkit/compare/v0.2.8...v0.3.0

## v0.2.8

2023-12-11

- Deployment fixes
- Fix missing vendor from wp deployment [4c83b39](https://github.com/findkit/wp-findkit/commit/4c83b39) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.2.7...v0.2.8

## v0.2.7

2023-12-11

- Broken release :(
- Use 10up wp release action [12dddb6](https://github.com/findkit/wp-findkit/commit/12dddb6) - Esa-Matti Suuronen
- Move assets to .wordpress-org [69c3e7f](https://github.com/findkit/wp-findkit/commit/69c3e7f) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.2.6...v0.2.7

## v0.2.6

2023-12-08

- Fix changelog update on readme.txt on release [33767f5](https://github.com/findkit/wp-findkit/commit/33767f5) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.2.5...v0.2.6

## v0.2.5

2023-12-08

- Release commit message++ [9cab656](https://github.com/findkit/wp-findkit/commit/9cab656) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.2.4...v0.2.5

## v0.2.4

2023-12-08

- Update the changelog on readme.txt on release [3115790](https://github.com/findkit/wp-findkit/commit/3115790) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.2.3...v0.2.4

## v0.2.3

2023-12-08

- WordPress plugin directory fixes
- Add loading indicator to the admin search [165f1fc](https://github.com/findkit/wp-findkit/commit/165f1fc) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.2.2...v0.2.3

## v0.2.2

2023-12-04

- Use the new brand color [20d960b](https://github.com/findkit/wp-findkit/commit/20d960b) - Esa-Matti Suuronen
- Fix \_\_() call [e014f71](https://github.com/findkit/wp-findkit/commit/e014f71) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.2.1...v0.2.2

## v0.2.1

2023-12-01

- More explicit sanitization

All changes https://github.com/findkit/wp-findkit/compare/v0.2.0...v0.2.1

## v0.2.0

2023-12-01

- Upgrade @findkit/ui to v0.16.0
- Bundle all scripts with wp-scripts
- Follow WordPress plugin directory guidelines more closely

All changes https://github.com/findkit/wp-findkit/compare/v0.1.13...v0.2.0

## v0.1.13

2023-11-09

- Render page meta only for WP_Post objects (for now) [4766155](https://github.com/findkit/wp-findkit/commit/4766155) - Esa-Matti Suuronen
  - Fixes crash on terms archive

All changes https://github.com/findkit/wp-findkit/compare/v0.1.12...v0.1.13

## v0.1.12

2023-11-07

- Build changes [ba33e17](https://github.com/findkit/wp-findkit/commit/ba33e17) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.1.11...v0.1.12

## v0.1.11

2023-11-07

- Upgrade @findkit/ui [bfe8fa0](https://github.com/findkit/wp-findkit/commit/bfe8fa0) - Esa-Matti Suuronen
- Use get_queried_object() to get post for Findkit Page Meta [6e3cda6](https://github.com/findkit/wp-findkit/commit/6e3cda6) - Esa-Matti Suuronen
- Gracefully handle broken autoload [a7d0637](https://github.com/findkit/wp-findkit/commit/a7d0637) - Esa-Matti Suuronen
- Better trigger finding [68061d1](https://github.com/findkit/wp-findkit/commit/68061d1) - Esa-Matti Suuronen
- Add global FINDKIT_UI_OPTIONS support to the search trigger block [dfe6829](https://github.com/findkit/wp-findkit/commit/dfe6829) - Esa-Matti Suuronen
- Remove margins from search trigger <figure> elements [a529982](https://github.com/findkit/wp-findkit/commit/a529982) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.1.10...v0.1.11

## v0.1.10

2023-09-28

- Upgrade @findkit/ui [9088f27](https://github.com/findkit/wp-findkit/commit/9088f27) - Esa-Matti Suuronen
- Hide admin search search findkit_project_id is not set [f13e408](https://github.com/findkit/wp-findkit/commit/f13e408) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.1.9...v0.1.10

## v0.1.9

2023-09-22

- fix post meta usage in page meta generation [b6264bf](https://github.com/findkit/wp-findkit/commit/b6264bf) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.1.8...v0.1.9

## v0.1.8

2023-09-22

- Fix admin search edit link in the frontend [38f331d](https://github.com/findkit/wp-findkit/commit/38f331d) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.1.7...v0.1.8

## v0.1.7

2023-09-22

- Use wp-admin colors for admin search [7dd0844](https://github.com/findkit/wp-findkit/commit/7dd0844) - Esa-Matti Suuronen
- Fix findkit admin search on the frontend [82b54e6](https://github.com/findkit/wp-findkit/commit/82b54e6) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.1.6...v0.1.7

## v0.1.6

2023-09-22

- Fix html escaping in wp-admin settings [a23420a](https://github.com/findkit/wp-findkit/commit/a23420a) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.1.5...v0.1.6

## v0.1.5

2023-09-22

- Add Search Trigger Block [438801f](https://github.com/findkit/wp-findkit/commit/438801f) - Esa-Matti Suuronen
- Add search icon to the wp-admin search item [8f65800](https://github.com/findkit/wp-findkit/commit/8f65800) - Esa-Matti Suuronen
- Add Findkit sidebar to Gutenberg [d457e09](https://github.com/findkit/wp-findkit/commit/d457e09) - Esa-Matti Suuronen
- Rename plugin to "Findkit" [6c3945a](https://github.com/findkit/wp-findkit/commit/6c3945a) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.1.4...v0.1.5

## v0.1.4

2023-06-05

- Use PHPStan to validate code
- Fix PHP notices reported by PHPStan
- Fix invalid url generation on wp-admin
- Remove `vendor` from git
- Upload a custom zip file with `vendor` to Github Releases
- Fix composer autoload due to "findkit" typo

All changes https://github.com/findkit/wp-findkit/compare/v0.1.3...v0.1.4

## v0.1.3

2023-06-05

Broken release.

All changes https://github.com/findkit/wp-findkit/compare/v0.1.2...v0.1.3

## v0.1.2

2023-04-12

- Fix url [102d0d9](https://github.com/findkit/wp-findkit/commit/102d0d9) - Esa-Matti Suuronen
- Add link to hub apikey creation page [6c7d315](https://github.com/findkit/wp-findkit/commit/6c7d315) - Esa-Matti Suuronen
- Fix domain [a9ae4fc](https://github.com/findkit/wp-findkit/commit/a9ae4fc) - Esa-Matti Suuronen
- 100% width settings inputs [0cec7f7](https://github.com/findkit/wp-findkit/commit/0cec7f7) - Esa-Matti Suuronen
- Disable api key setting if defined in wp-config [680119e](https://github.com/findkit/wp-findkit/commit/680119e) - Esa-Matti Suuronen

All changes https://github.com/findkit/wp-findkit/compare/v0.1.1...v0.1.2
