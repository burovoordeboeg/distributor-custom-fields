<?php
namespace BvdB\Distributor\InternalConnections;

class Utilities {

    public static function get_post_id_by_original_id( $original_id ) {

		global $wpdb;

        $original_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT post_id from $wpdb->postmeta 
			WHERE meta_key = 'dt_original_post_id' 
			AND meta_value = %s",
			$original_id ) );

		if( $original_id ) {
			return $original_id;
		}

		return null;
    }

    public static function get_media_id_by_original_id( $media_id ) {

		global $wpdb;

		$original_id = $wpdb->get_var( $wpdb->prepare( "
		SELECT post_id from $wpdb->postmeta 
		WHERE meta_key = 'dt_original_media_id' 
		AND meta_value = %s", $media_id ) );

		if( $original_id ) {
			return $original_id;
		}
		
		return null;
	}
	
	/**
	 * Gets media id by searching by it's 'guid'.
	 *
	 * @param  int $guid     	  The guid used to query the db.
	 * 
	 * @return int $media[0] The image id.
	 */
	public static function get_media_id_by_guid( $guid ) {

		global $wpdb;
		
		$media = $wpdb->get_col( $wpdb->prepare( "
			SELECT ID FROM $wpdb->posts 
			WHERE guid = '%s'",
			$guid ));

		if( ! empty( $media ) ) {
			return reset( $media );
		}

		return null;
	}

    /**
	 * Gets media id by searching by it's filename of/in the 'guid'.
	 *
	 * @param  int $guid     	  The guid used to query the db.
	 * 
	 * @return int $media[0] The image id.
	 */
	public static function get_media_id_by_guid_filename( $guid ) {

		global $wpdb;

		// Get the image filename.jpg from the GUID
		$pathinfo = pathinfo( $guid );
		$filename = $guid; // fallback

		if ( isset( $pathinfo['basename'] ) ) {
			$filename = $pathinfo['basename'];
		}

		$media = $wpdb->get_col( $wpdb->prepare( "
			SELECT ID FROM $wpdb->posts 
			WHERE guid LIKE '%s'",
			'%' . $filename . '%' ));

		if( ! empty( $media ) ) {
			return reset( $media );
		}

		return null;
	}

	/**
	 * Gets image guid by searching by it's 'IDs'.
	 *
	 * @param  int $image_id     The ID used to query the db.
	 * 
	 * @return int $media[0] The image id.
	 */
	public static function get_media_guid_by_id( $post_id ) {

		global $wpdb;

		$media = $wpdb->get_col( $wpdb->prepare( "
			SELECT guid FROM $wpdb->posts 
			WHERE ID = '%s'",
			$post_id ));

		if( ! empty( $media ) ) {
			return reset( $media );
		}

		return null;
	}

	/**
	 * Flatten the array
	 * 
	 * @props https://gist.github.com/SeanCannon/6585889#gistcomment-2122319
	 */
	public static function array_flatten( $array = null ) {
		$result = array();
	
		if ( !is_array( $array ) ) {
			$array = func_get_args();
		}
	
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$result = array_merge($result, static::array_flatten($value));
			} else {
				$result = array_merge($result, array($key => $value));
			}
		}
	
		return $result;
	}

	/**
	 * Use this just like `add_filter()`, and then run something that calls the filter (like `new WP_Query`, maybe). 
	 * That's it. If the filter gets called again, your callback will not be. 
	 * This works around the common "filter sandwich" pattern where you have to remember to call `remove_filter` again after your call.
	 * 
	 * @props @markjaquith https://gist.github.com/markjaquith/b752e3aa93d2421285757ada2a4869b1
	 */
	public static function add_filter_once( $hook, $callback, $priority = 10, $params = 1 ) {
		add_filter( $hook, function( $first_arg ) use( $callback ) {
			static $ran = false;

			if ( $ran ) {
				return $first_arg;
			}

			$ran = true;
			return call_user_func_array( $callback, func_get_args() );
		}, $priority, $params );
	}
} 

