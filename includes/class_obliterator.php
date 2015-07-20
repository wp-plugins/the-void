<?php

if( !class_exists( 'TV_Obliterator' ) ) {
	
	class TV_Obliterator {

		function remove_posts( $type ) {

			$args = array(
				'post_type' => $type,
				'posts_per_page' => -1,
				'post_status' => array( 'any', 'trash', 'auto-draft' )
			);

			$query = new WP_Query( $args );
			
			if( $query->have_posts() ) {
				$error = false;
				$i = 1;
				$deleted = 0;
				while( $query->have_posts() ) {
					$query->the_post();
					if( false === wp_delete_post( get_the_ID(), true ) ) {
						$error = true;
					} else {
						$deleted++;
					}
					$i++;
				}

				if( $error ) {
					$result = array( 'result' => 'errors', 'message' => __( 'Posts where deleted, but some errors occurred', 'thevoid' ), 'count' => $i, 'deleted' => $deleted );
				} else {
					$result = array( 'result' => 'ok', 'message' => __( 'All posts of type ' . $type . ' where deleted', 'thevoid' ), 'count' => $i, 'deleted' => $deleted );
				}
			} else {
				$result = array( 'result' => 'ko', 'message' => __( 'There are no posts of type ' . $type, 'thevoid' ), 'count' => 0, 'deleted' => 0 );
			}

			wp_reset_query();
			wp_reset_postdata();

			return $result;
					
		}

		function remove_terms( $tax ) {
			
			$args = array(
				'hide_empty' => false
			);
			$terms = get_terms( $tax, $args );
			$count = count( $terms );
			if( $count > 0 ) {
				$error = false;
				$deleted = 0;
				foreach( $terms as $term ) {
					if( false === wp_delete_term( $term->term_id, $tax ) ) {
						$error = true;
					} else {
						$deleted++;
					}
				}

				if( $error ) {
					$result = array( 'result' => 'errors', 'message' => __( 'Terms where deleted, but some errors occurred', 'thevoid' ), 'count' => $count, 'deleted' => $deleted );
				} else {
					$result = array( 'result' => 'ok', 'message' => __( 'All terms in taxonomy ' . $tax . ' where deleted', 'thevoid' ), 'count' => $count, 'deleted' => $deleted );
				}				
			} else {
				$result = array( 'result' => 'ko', 'message' => __( 'There are no terms in taxonomy ' . $tax, 'thevoid' ), 'count' => 0, 'deleted' => 0 );
			}

			return $result;
	
		}
		
	}
	
}
