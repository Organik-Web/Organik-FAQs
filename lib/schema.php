<?php
/**
 * orgnk_faqs_schema_script()
 * Generates the FAQ schema script for outputting in the document head
 * Requires an array of shortcode parts to be passed
 */
function orgnk_faqs_schema_script( $shortcodes = NULL ) {

    $schema = NULL;
    $sub_schema = array();

    if ( $shortcodes ) {

        foreach ( $shortcodes as $shortcode ) {

            // Parse the arributes of the shortcode part through shortcode_parse_atts to generate key => pairs of each attribute
            // Attributes are stored as ["category"] => "name" so we can use them to retrieve posts in the upcoming query
            $attributes = ( isset( $shortcode[2] ) ) ? shortcode_parse_atts( $shortcode[2] ) : NULL;

            // Setup the post query arguments
            $args = array(
                'post_type'         	=> ORGNK_FAQS_CPT_NAME,
                'post_status' 			=> 'publish',
                'orderby'           	=> 'menu_order',
                'order'             	=> 'ASC',
                'posts_per_page'    	=> -1
            );

            // If a category is specified in the shortcode, add a tax query to the main loop with the category slug
            if ( isset( $attributes['category'] ) ) {

                $term = get_term_by( 'slug', $attributes['category'], ORGNK_FAQS_CATEGORIES_TAX_NAME );

                // Add a tax query to the arguments if the term provided in the shortcode is valid
                if ( isset( $term ) ) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy'  			=> ORGNK_FAQS_CATEGORIES_TAX_NAME,
                            'field'     			=> 'slug',
                            'terms'     			=> $term,
                        ),
                    );
                }
            }

            if ( isset( $attributes['id'] ) ) {
                $args['post__in'] = array( $attributes['id'] );
            }

            // Setup query
            $posts_loop = new WP_Query( $args );

            // Check there are posts to output
            if ( $posts_loop->have_posts() ) {
                while ( $posts_loop->have_posts() ) {

                    // Setup post object
                    $posts_loop->the_post();

                    $faq_schema = array(
                        '@type'     		=> 'Question',
                        'name' 				=> esc_html( get_the_title() ),
                        'acceptedAnswer'	=> array(
                            '@type'     		=> 'Answer',
                            'name' 				=> esc_html( get_the_content() )
                        )
                    );

                    // Check if the post hasn't already been stored in the sub schema
                    // This prevents duplicates items in the schema if multiple shortcodes of the same type are provided
                    if ( ! in_array( $faq_schema, $sub_schema ) ) {
                        $sub_schema[] = $faq_schema;
                    }
                }
            }
            wp_reset_postdata();
        }
    }


    
    // Check if anything has been stored for output
    if ( $sub_schema ) {

        $schema = array(
            '@context'  		=> 'http://schema.org',
            '@type'     		=> 'FAQPage',
            'mainEntity'		=> $sub_schema
        );
    }

    // Finally, check if there is any compiled schema to return
    if ( $schema ) {
        // var_dump( json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) );
        return '<script type="application/ld+json" class="organik-faqs-schema">' . json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>';
    }
}
