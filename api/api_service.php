<?php
 
class ApiService_Lytex_Pagamentos {

	public function __construct() {
		if ( ! defined( 'DEBUG_REQUESTS' ) ) {
			define( 'DEBUG_REQUESTS', false );
		}

		if ( ! defined( 'CACHE_REQUESTS_SECONDS' ) ) {
			define( 'CACHE_REQUESTS_SECONDS', 900 );
		}
	}
    function get( $api, $path, $headers = [], $body = [],  $params = [], $json_data = true, $timeout = 30 ) {

        if ( DEBUG_REQUESTS ) {
            $time = microtime( 1 );
        }

        if ( $api === null ) {
            die( "API $api not defined" );
        }

        $url = $api . $path . http_build_query( $params, '', '&' );

        $api_response = wp_remote_get( $url, [
            'sslverify' => false,
            'headers'   => $json_data ? array_merge( [ 'Content-Type' => 'application/json; charset=utf-8' ], $headers ) : $headers,
            'timeout'   => $timeout,
            'body'      => null,
            'method'    => 'GET'
        ] );

        $response = (object) [
            'status'  => wp_remote_retrieve_response_code( $api_response ),
            'headers' => wp_remote_retrieve_headers( $api_response ),
            'data'    => $json_data ? @json_decode( wp_remote_retrieve_body( $api_response ) ) : wp_remote_retrieve_body( $api_response )
        ];


        if ( DEBUG_REQUESTS ) {
            echo '<blockquote>';
            echo '<strong>Pedido </strong>: ' . esc_url($url). ' (' . esc_html( $response->status) . ') <br/>';
            echo ' <strong>Tempo </strong>: ', ( microtime( 1 ) - esc_html($time) ), "s";
            echo '</blockquote>';
        }

        return $response;
    }

	function post( $api, $path, $body = [], $headers = [], $params = [], $json_data = true, $timeout = 30 ) {
        
		if ( DEBUG_REQUESTS ) {
			$time = microtime( 1 );
		}

		if ( $api === null ) {
			die( "API $api not defined" );
		}

		$url = $api . $path . http_build_query( $params, '', '&' );
        
		$api_response = wp_remote_post( $url, [
			'sslverify' => false,
			'headers'   => $json_data ? array_merge( [ 'Content-Type' => 'application/json; charset=utf-8' ], $headers ) : $headers,
			'timeout'   => $timeout,
			'body'      => json_encode( $body ),
			'method'    => 'POST'
		] );

		$response = (object) [
			'status'  => wp_remote_retrieve_response_code( $api_response ),
			'headers' => wp_remote_retrieve_headers( $api_response ),
			'data'    => $json_data ? @json_decode( wp_remote_retrieve_body( $api_response ) ) : wp_remote_retrieve_body( $api_response )
		];


        if ( DEBUG_REQUESTS ) {
            echo '<blockquote>';
            echo '<strong>Pedido </strong>: ' . esc_url($url). ' (' . esc_html( $response->status) . ') <br/>';
            echo ' <strong>Tempo </strong>: ', ( microtime( 1 ) - esc_html($time) ), "s";
            echo '</blockquote>';
        }

		return $response;
	}

    function put( $api, $path, $body = [], $headers = [], $params = [], $json_data = true, $timeout = 30 ) {

        if ( DEBUG_REQUESTS ) {
            $time = microtime( 1 );
        }

        if ( $api === null ) {
            die( "API $api not defined" );
        }

        $url = $api . $path . http_build_query( $params, '', '&' );
        $args = array(

            'headers' => $json_data ? array_merge( [ 'Content-Type' => 'application/json' ], $headers ) : $headers,
            'body'      => json_encode( $body ),
            'method'    => 'PUT'
        );
        $api_response = wp_remote_request( $url, $args );

        $response = (object) [
            'status'  => wp_remote_retrieve_response_code( $api_response ),
            'headers' => wp_remote_retrieve_headers( $api_response ),
            'data'    => $json_data ? @json_decode( wp_remote_retrieve_body( $api_response ) ) : wp_remote_retrieve_body( $api_response )
        ];


        if ( DEBUG_REQUESTS ) {
            echo '<blockquote>';
            echo '<strong>Pedido </strong>: ' . esc_url($url). ' (' . esc_html( $response->status) . ') <br/>';
            echo ' <strong>Tempo </strong>: ', ( microtime( 1 ) - esc_html($time) ), "s";
            echo '</blockquote>';
        }

        return $response;
    }
}
