<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * Plugin Name: Findkit - Site Search
 * Plugin URI: https://www.findkit.com/wordpress/
 * Description: WordPress Plugin for Findkit Site Search. See findkit.com for details
 * Author: Findkit Team <findkit@findkit.com>
 * Version: 1.3.0
 * License: GPLv2 or later
 */

// To make this plugin work properly for both Composer users and non-composer
// users we must detect whether the project is using a global autoloader. We
// can do that by checking whether our autoloadable classes will autoload with
// class_exists(). If not it means there's no global autoloader in place and
// the user is not using composer. In that case we can try to require the
// bundled autoloader code.
if (
    ! \class_exists( '\Findkit\Loader' ) &&
    \is_readable( __DIR__ . '/vendor/autoload.php' )
) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// If the class still doesn't exist it means the autoloader files are not bundled in the plugin
if ( ! \class_exists( '\Findkit\Loader' ) ) {
    add_action( 'admin_notices', function () {
        $class   = 'notice notice-error';
        $message = __(
            'Failed to fully load the Findkit-plugin. Autoload classes not found. <a target="_blank" href="https://findk.it/wpautoload">Read more</a>.',
            'findkit'
        );

        printf(
            '<div class="%1$s"><p>%2$s</p></div>',
            esc_attr( $class ),
            esc_html( $message )
        );
    } );

    return;
}

\Findkit\Loader::instance();

/**
 * Log Findkit errors to error log
 *
 * @param string $message Error message
 * @param mixed $data Additional data to log
 *
 * @return void
 */
function findkit_log_error( $message, $data = null ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        $log_message = '[WP Findkit Error] ' . $message;
        if ( $data !== null ) {
            $log_message .= ' | Data: ' . ( is_string( $data ) ? $data : wp_json_encode( $data ) );
        }
        error_log( $log_message );
    }
}

/////////////////////////
// Public API functions
/////////////////////////

function findkit_full_crawl( array $options = [] ) {
    try {
        $loader = \Findkit\Loader::instance();

        return $loader->api_client->full_crawl( $options );
    } catch ( \Exception $e ) {
        findkit_log_error( 'Full crawl failed', $e->getMessage() );

        return false;
    }
}

function findkit_manual_crawl( array $urls, array $options = [] ) {
    try {
        $loader = \Findkit\Loader::instance();

        return $loader->api_client->manual_crawl( $urls, $options );
    } catch ( \Exception $e ) {
        findkit_log_error( 'Manual crawl failed', $e->getMessage() );

        return false;
    }
}

function findkit_partial_crawl( array $options = [] ) {
    try {
        $loader = \Findkit\Loader::instance();

        return $loader->api_client->partial_crawl( $options );
    } catch ( \Exception $e ) {
        findkit_log_error( 'Partial crawl failed', $e->getMessage() );

        return false;
    }
}

function findkit_get_page_meta( \WP_Post $post ) {
    try {
        return Findkit\PageMeta::get( $post );
    } catch ( \Exception $e ) {
        findkit_log_error( 'Failed to get page meta', [
            'post_id' => $post->ID,
            'error'   => $e->getMessage()
        ] );

        return [];
    }
}

/**
 * @param string $terms
 * @param array|null $search_params search params https://docs.findkit.com/ui-api/ui.searchparams/
 * @param array|null $options
 *
 * @return object|false Returns search results or false on error
 */
function findkit_search(
    string $terms,
    array $search_params = null,
    array $options = null
) {
    try {
        $result = findkit_search_groups(
            $terms,
            [ $search_params ?? (object) [] ],
            $options
        );

        return $result['groups'][0] ?? [];
    } catch ( \Exception $e ) {
        findkit_log_error( 'Search failed', [
            'terms' => $terms,
            'error' => $e->getMessage()
        ] );

        return false;
    }
}

/**
 * @param string $terms
 * @param array|null $groups array for findkit search params https://docs.findkit.com/ui-api/ui.searchparams/
 * @param null $options
 *
 * @return array|false Returns search results or false on error
 *
 *  Example return value:
 *  [
 *  'duration' => 32,
 *  'groups' => [
 *      [
 *          'total' => 1,
 *          'duration' => 7,
 *          'hits' => [
 *              [
 *                  'score' => 65.79195,
 *                  'superwordsMatch' => false,
 *                  'title' => 'How Findkit Scores Search Results?',
 *                  'language' => 'en',
 *                  'url' =>
 *                      'https://www.findkit.com/how-findkit-scores-search-results/',
 *                  'highlight' =>
 *                      'But what is an index and <em>how</em> the pages are <em>scored</em> when searching?',
 *                  'tags' => [
 *                      'wordpress',
 *                      'domain/www.findkit.com/wordpress',
 *                      'wp_blog_name/findkit',
 *                      'domain/www.findkit.com/wp_blog_name/findkit',
 *                      'public',
 *                      'wp_post_type/post',
 *                      'domain/www.findkit.com/wp_post_type/post',
 *                      'domain/www.findkit.com/wp_taxonomy/category/article',
 *                      'wp_taxonomy/category/article',
 *                      'domain/www.findkit.com',
 *                      'domain/findkit.com',
 *                      'language/en',
 *                  ],
 *                  'created' => '2024-05-20T07:44:47.000Z',
 *                  'modified' => '2024-05-20T10:49:11.000Z',
 *                  'customFields' => [
 *                      'wpPostId' => 34,
 *                      'author' => [
 *                          'type' => 'keyword',
 *                          'value' => 'Esa-Matti Suuronen',
 *                      ],
 *                      'excerpt' => [
 *                          'type' => 'keyword',
 *                          'value' =>
 *                              'Findkit is crawler based search toolkit which stores web pages to a search index. But what is an index and how the pages are scored when searching?',
 *                      ],
 *                  ],
 *              ],
 *          ],
 *      ],
 *  ],
 *  ];
 * /
 */
function findkit_search_groups(
    string $terms,
    array $groups = null,
           $options = null
) {
    try {
        $public_token = $options['publicToken'] ?? \get_option( 'findkit_project_id' );

        if ( ! $public_token ) {
            findkit_log_error( 'Findkit public token is not set, cannot search' );

            return false;
        }

        $subdomain  = 'search';
        $parts      = explode( ':', $public_token );
        $project_id = $parts[0];
        $region     = $parts[1] ?? null;

        if ( $region && $region !== 'eu-north-1' ) {
            $subdomain = "search-$region";
        }

        if ( empty( $groups ) ) {
            $groups = [ (object) [] ];
        }

        $need_jwt = get_option( 'findkit_enable_jwt' );
        if ( $need_jwt ) {
            $jwt = \Findkit\KeyPair::request_jwt_token();
            $qs  = http_build_query( [ 'p' => 'jwt:' . $jwt ] );
        } else {
            $qs = http_build_query( [ 'p' => $project_id ] );
        }

        $response = wp_remote_post(
            "https://$subdomain.findkit.com/c/$project_id/search?" . $qs,
            [
                'method'  => 'POST',
                'headers' => [
                    'content-type' => 'application/json',
                ],
                'body'    => wp_json_encode( [
                    'q'      => $terms,
                    'groups' => $groups,
                ] ),
            ]
        );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            findkit_log_error( 'Findkit request search failed', $error_message );

            return false;
        }

        $body   = json_decode( wp_remote_retrieve_body( $response ), true );
        $status = wp_remote_retrieve_response_code( $response );

        if ( $status !== 200 ) {
            findkit_log_error( "Findkit search failed with status $status", $body );

            return false;
        }

        return $body;
    } catch ( \Exception $e ) {
        findkit_log_error( 'Search groups failed', [
            'terms' => $terms,
            'error' => $e->getMessage()
        ] );

        return false;
    }
}