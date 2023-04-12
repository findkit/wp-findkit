# Findkit WordPress Plugin

See [findkit.com](https://findkit.com/) and [docs.findkit.com](https://docs.findkit.com/).

## Install

Get zip file from [releases](https://github.com/findkit/wp-findkit/releases/) or install using Composer

```
composer require findkit/wp-findkit
```

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

## Live Update

The plugin can automatically trigger near instant recrawls as pages are edited.

## JWT Authentication

The Findkit Search Endpoint can be configured to require JWT token
authentication.

### Setup

This plugin automatically generates a private / public key pair to the
`findkit_pubkey` and `findkit_privkey` options. Add the public key to the
`findkit.toml` file and set the endpoint to private:

```toml
[search-endpoint]
private = true
public_key = """
-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAvbvzQ+AsMP0UnNpXmk4P
39O3M6SHkcqtP3e6TR/S1LI6cVFF/QdentwYYIABUwbEzxJuYWP6v/BLittCAWSg
YsrbImrGHokgO/ItOU/90DrBL+sL6eeMTfECe9guM5l3JrhE70z9dCuQn6GYp8CL
VAJWdLKCgmReTvEVQTwFObLpWh4YniXuWnYkw9MPxADLXkJU8MjDlwcIumQMaesP
POBVjVuPhtQ+i5V6G2BegemXl8ep6qQ2xt8spNRoAKwt6Nekt5+GWz65Q9juTGdD
6HkR15ij6sSZoOjjSWuiR0CDOhmjDXGCLtqQuLivFq6oGNgP7BqXtoR6hNwSXLSj
eFhoszDoQZjRoL7oJ/dE60wxuB8FG5duam+AXx/3IJl93sAeFWFzLPpXYmdXQVG7
2kADsYCcNgdN2RMuKGjg4Qmu/RWKzzFfI7GbNS6K47Ow0VjmSN1pb3UitTkROjAj
tPsFXX8vhV1AG9w327Wl/R4d45nd9m/dEaUPpej32caqHtWjQsVT/Sry/ZXhxzaD
4OO7YhKjEbvvHMkgTzihKAKFDIhR+revbgjAPPuwKxseiTrAeKIXDHAW4FVzUq1r
2c+CmzKcwnTle2ydkpCZhGENvqNEgRiGoj5BC5r0gYImsSQyB3B2obvOqtsXOwjn
TtZof/qoIldypZCe7BA5ETECAwEAAQ==
-----END PUBLIC KEY-----
"""
```

You can get the public key value with the wp cli for example.

```
wp option get findkit_pubkey
```

and deploy the change.

Put the findkit project id to `findkit_project_id` and set `findkit_enable_jwt`
to a truthy value to enable JWT token generation which will be automatically
picked by the `@findkit/ui` library.

```
wp option set findkit_project_id 'plnGp6Rv0'
wp option set findkit_enable_jwt 1
```

### Authentication

By default this plugin allows only users logged in to use the search endpoint but it can be modified using the `findkit_allow_jwt` filter:

<!-- prettier-ignore -->
```php
add_filter('findkit_allow_jwt', function ($allow) {
    return current_user_can('edit_posts');
}, 10, 1);
```
