# Findkit WordPress Plugin

## Page Meta

The plugin automatically exposes basic Findkit [Page
Meta](https://docs.findkit.com/crawler/meta-tag) by generating the Findkit Meta
Tag.

Following fields are automatically added

- `showInSearch`: Archive pages are automatically excluded
- `title`: Clean page title
- `created`: Created time
- `modified`: Modified time
- `langugage`: Language from the post or Polylang
- `tags`: Some basic tags including public taxonomies

These can be modified and other fields can be added using the
`findkit_page_meta` filter.

<!-- prettier-ignore -->
```php
add_filter('findkit_page_meta', function ($meta, $post) {
    $meta['title'] = 'My custom title';
    return $meta;
}, 10, 2);
```

## JWT Authentication

NOTE: This is this WIP

The Findkit Search Endpoint can be configured to require JWT token
authentication.

This plugin automatically generates a private / public key pair to the
`findkit_pubkey` and `findkit_privkey` options. Copy the public key to the
Findkit project on the Findkit Hub and configure the project id to the
`findkit_project_id` option. Set `findkit_enable_jwt` to a truthy value to
enable JWT token generation which will be automatically picked by the
`@findkit/ui` library.
