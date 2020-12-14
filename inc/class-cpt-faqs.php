<?php
/**
 * Define constant variables
 */
define( 'ORGNK_FAQS_CPT_NAME', 'faq' );
define( 'ORGNK_FAQS_SINGLE_NAME', 'FAQ' );
define( 'ORGNK_FAQS_PLURAL_NAME', 'FAQs' );
define( 'ORGNK_FAQS_SHORTCODE_NAME', 'faqs' );

/**
 * Main Organik_FAQs class
 */
class Organik_FAQs {

	/**
     * The single instance of Organik_FAQs
     */
	private static $instance = null;

	/**
     * Main class instance
     * Ensures only one instance of this class is loaded or can be loaded
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
        }
        return self::$instance;
	}
	
	/**
     * Constructor function
     */
	public function __construct() {

		// Define the CPT rewrite variable on init - required here because we need to use get_permalink() which isn't available when plugins are initialised
		add_action( 'init', array( $this, 'orgnk_faqs_cpt_rewrite_slug' ) );

		// Register taxonomies first
		new Organik_FAQs_Categories();

        // Hook into the 'init' action to add the Custom Post Type
		add_action( 'init', array( $this, 'orgnk_faqs_cpt_register' ) );

        // Change the title placeholder
		add_filter( 'enter_title_here', array( $this, 'orgnk_faqs_cpt_title_placeholder' ) );

		// Switch the default editor to use Teeny MCE
		add_filter( 'wp_editor_settings', array( $this, 'orgnk_faqs_cpt_enable_teeny_editor' ) );

		// Remove unneccessary buttons from the Teeny MCE
		add_filter( 'teeny_mce_buttons', array( $this, 'orgnk_faqs_cpt_remove_teeny_editor_buttons' ) );

		// Add 'about' page to admin menu
		add_action( 'admin_menu', array( $this, 'orgnk_faqs_cpt_admin_about_page' ) );

		// Add post meta to the admin list view for this CPT
		add_filter( 'manage_' . ORGNK_FAQS_CPT_NAME . '_posts_columns', array( $this, 'orgnk_faqs_cpt_admin_table_column' ) );
		add_action( 'manage_' . ORGNK_FAQS_CPT_NAME . '_posts_custom_column', array( $this, 'orgnk_faqs_cpt_admin_table_content' ), 10, 2 );

		// Register shortcode
		add_shortcode( ORGNK_FAQS_SHORTCODE_NAME, array( $this, 'orgnk_faqs_cpt_shortcode' ) );

		// Enqueue front-end scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'orgnk_faqs_cpt_enqueue_scripts' ) );

		// Add schema for this post type to the document head
		add_action( 'wp_head', array( $this, 'orgnk_faqs_cpt_shortcode_schema_head' ) );
	}
	
	/**
	 * orgnk_faqs_cpt_register()
	 * Register the custom post type
	 */
	public function orgnk_faqs_cpt_register() {

		$labels = array(
			'name'                      	=> ORGNK_FAQS_PLURAL_NAME,
			'singular_name'             	=> ORGNK_FAQS_SINGLE_NAME,
			'menu_name'                 	=> ORGNK_FAQS_PLURAL_NAME,
			'name_admin_bar'            	=> ORGNK_FAQS_SINGLE_NAME,
			'archives'              		=> 'FAQ archives',
			'attributes'            		=> 'FAQ Attributes',
			'parent_item_colon'     		=> 'Parent FAQ:',
			'all_items'             		=> 'All FAQs',
			'add_new_item'          		=> 'Add new FAQ',
			'add_new'               		=> 'Add new FAQ',
			'new_item'              		=> 'New FAQ',
			'edit_item'             		=> 'Edit FAQ',
			'update_item'           		=> 'Update FAQ',
			'view_item'             		=> 'View FAQ',
			'view_items'            		=> 'View FAQs',
			'search_items'          		=> 'Search FAQ',
			'not_found'             		=> 'Not found',
			'not_found_in_trash'    		=> 'Not found in Trash',
			'featured_image'        		=> 'Featured Image',
			'set_featured_image'    		=> 'Set featured image',
			'remove_featured_image' 		=> 'Remove featured image',
			'use_featured_image'    		=> 'Use as featured image',
			'insert_into_item'      		=> 'Insert into FAQ',
			'uploaded_to_this_item' 		=> 'Uploaded to this FAQ',
			'items_list'            		=> 'FAQs list',
			'items_list_navigation' 		=> 'FAQs list navigation',
			'filter_items_list'     		=> 'Filter FAQs list'
		);
	
		$rewrite = array(
			'slug'                  		=> ORGNK_FAQS_REWRITE_SLUG, // The slug for single posts
			'with_front'            		=> false,
			'pages'                 		=> true,
			'feeds'                 		=> false
		);
	
		$args = array(
			'label'                 		=> ORGNK_FAQS_SINGLE_NAME,
			'description'           		=> 'Manage and display FAQs',
			'labels'                		=> $labels,
			'supports'              		=> array( 'title', 'editor', 'page-attributes' ),
			'taxonomies'            		=> array(),
			'hierarchical'          		=> false,
			'public'                		=> true,
			'show_ui'               		=> true,
			'show_in_menu'          		=> true,
			'menu_position'         		=> 25,
			'menu_icon'             		=> 'dashicons-format-status',
			'show_in_admin_bar'     		=> true,
			'show_in_nav_menus'     		=> true,
			'can_export'            		=> true,
			'has_archive'           		=> false, // The slug for archive, bool toggle archive on/off
			'publicly_queryable'    		=> false, // Bool toggle single on/off
			'exclude_from_search'   		=> true,
			'capability_type'       		=> 'page',
			'rewrite'						=> $rewrite
		);
		register_post_type( ORGNK_FAQS_CPT_NAME, $args );
	}

	/**
	 * orgnk_faqs_cpt_rewrite_slug()
	 * Conditionally define the CPT archive permalink based on the pages for CPT functionality in Organik themes
	 * Includes a fallback string to use as the slug if the option isn't set
	 */
	public function orgnk_faqs_cpt_rewrite_slug() {
		$default_slug = 'faqs';
		$archive_page_id = get_option( 'page_for_' . ORGNK_FAQS_CPT_NAME );
		$archive_page_slug = str_replace( home_url(), '', get_permalink( $archive_page_id ) );
		$archive_permalink = ( $archive_page_id ? $archive_page_slug : $default_slug );
		$archive_permalink = ltrim( $archive_permalink, '/' );
		$archive_permalink = rtrim( $archive_permalink, '/' );

		define( 'ORGNK_FAQS_REWRITE_SLUG', $archive_permalink );
	}

	/**
	 * orgnk_faqs_cpt_enqueue_scripts()
     * Enqueue front-end scripts
     */
    public function orgnk_faqs_cpt_enqueue_scripts() {
        wp_enqueue_script( 'faqs', plugin_dir_url( __FILE__ ) . '../public/js/faqs-functions.min.js', array('jquery'), ORGNK_FAQS_VERSION, true );
    }

	/** 
	 * orgnk_faqs_cpt_title_placeholder()
	 * Change CPT title placeholder on edit screen
	 */
	public function orgnk_faqs_cpt_title_placeholder( $title ) {

		$screen = get_current_screen();

		if ( $screen && $screen->post_type == ORGNK_FAQS_CPT_NAME ) {
			return 'Add question';
		}
		return $title;
	}

	/**
	 * orgnk_faqs_cpt_enable_teeny_editor()
	 * Convert the default editor to Teeny MCE for this CPT
	 */
	public function orgnk_faqs_cpt_enable_teeny_editor( $settings ) {

		$screen = get_current_screen();

		if ( $screen && $screen->post_type == ORGNK_FAQS_CPT_NAME ) {
			$settings['teeny'] = true;
			$settings['media_buttons'] = false;
		}

		return $settings;
	}

	/**
	 * orgnk_faqs_cpt_remove_teeny_editor_buttons()
	 * Remove some options/buttons from the editor
	 */
	public function orgnk_faqs_cpt_remove_teeny_editor_buttons( $buttons ) {

		$screen = get_current_screen();

		if ( $screen && $screen->post_type == ORGNK_FAQS_CPT_NAME ) {
			$remove_buttons = array(
				'blockquote',
				'alignleft',
				'aligncenter',
				'alignright',
				'fullscreen'
			);

			foreach ( $buttons as $button_key => $button_value ) {
				if ( in_array( $button_value, $remove_buttons ) ) {
					unset( $buttons[ $button_key ] );
				}
			}
		}
		return $buttons;
	}

	/**
	 * orgnk_faqs_cpt_admin_about_page()
	 * Add the CPT 'about' page to the admin menu
	 */
	public function orgnk_faqs_cpt_admin_about_page() {
		add_submenu_page(
			'edit.php?post_type=' . ORGNK_FAQS_CPT_NAME,
			'About ' . ORGNK_FAQS_PLURAL_NAME, 
			'About ' . ORGNK_FAQS_PLURAL_NAME, 
			'edit_pages', 
			'about-' . ORGNK_FAQS_CPT_NAME, 
			array( $this, 'orgnk_faqs_cpt_admin_about_page_content' )
		);
	}

	/**
	 * orgnk_faqs_cpt_admin_about_page_content()
	 * The content for the CPT 'about' page in admin
	 */
	public function orgnk_faqs_cpt_admin_about_page_content() {
		include_once plugin_dir_path( __FILE__ ) . '../lib/about.php';
	}

	/**
	 * orgnk_faqs_cpt_admin_table_column()
	 * Register new column(s) in admin list view
	 */
	public function orgnk_faqs_cpt_admin_table_column( $defaults ) {
		
		$new_order = array();

		foreach( $defaults as $key => $value ) {
			// When we find the date column, slip in the new column before it
			if ( $key == 'date' ) {
				$new_order['id'] = 'ID';
			}
			$new_order[$key] = $value;
		}

		return $new_order;
	}

	/**
	 * orgnk_faqs_cpt_admin_table_content()
	 * Return the content for the new admin list view columns for each post
	 */
	public function orgnk_faqs_cpt_admin_table_content( $column_name, $post_id ) {
			
		global $post;

		if ( $column_name == 'id' ) {
			echo $post_id;
		}
	}

	/**
	 * orgnk_faqs_cpt_shortcode()
	 * Shortcode to print FAQs
	 * Usage: [faqs category='xyz']
	 */
	public function orgnk_faqs_cpt_shortcode( $attributes ) {

		static $instance = 0;
		$instance++;

		$display_type = 'accordion';

		// Setup filter for changing the heading class in theme
		// Can be changed with add_filter( 'orgnk_faqs_shortcode_heading', function() {	return 'h3'; });
		$heading_level = apply_filters( 'orgnk_faqs_shortcode_heading', 'h2' );

		// Setup the query arguments
		$args = array(
			'post_type'         	=> ORGNK_FAQS_CPT_NAME,
			'post_status' 			=> 'publish',
			'orderby'           	=> 'menu_order',
			'order'             	=> 'ASC',
			'posts_per_page'    	=> -1
		);

		// Set the attributes that the user can supply to the shortcode
		$attribute = shortcode_atts( array(
			'category'      		=> NULL,
			'id'      				=> NULL,
			'style'					=> 'accordion'
		), $attributes );

		// If a category is specified in the shortcode, add a tax query to the main loop with the category slug
		if ( isset( $attribute['category'] ) ) {

			$term = get_term_by( 'slug', $attribute['category'], ORGNK_FAQS_CATEGORIES_TAX_NAME );

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

		if ( isset( $attribute['id'] ) ) {
			$post_ids = preg_replace('/\s+/', '', $attribute['id'] ); // Remove all whitespace
			$post_ids = explode( ',', $post_ids ); // Convert string to array by comma seperation
			$args['post__in'] = $post_ids;
		}

		if ( isset( $attribute['style'] ) && $attribute['style'] === 'list' ) {
			$display_type = 'list';
		}

		$faqs_loop = new WP_Query( $args );

		ob_start();

		if ( file_exists( get_template_directory() . '/template-parts/shortcodes/shortcode-faqs.php' ) ) {
			include ( get_template_directory() . '/template-parts/shortcodes/shortcode-faqs.php' );
		}  else {
			include plugin_dir_path( __FILE__ ) . '../public/shortcode/shortcode.php';
		}

		return ob_get_clean();
	}

	/**
	 * orgnk_faqs_cpt_shortcode_schema_head()
	 * Add FAQ schema to the document <head> if a [faqs] shortcode is used on a page
	 * Uses a custom field on each post/page where the author can supply any shortcodes they have used
	 * Note: if multiple of the same FAQ are provided in the custom field, they will be merged together
	 */
	public function orgnk_faqs_cpt_shortcode_schema_head() {

		$schema_script = NULL;

		// Get shortcodes for the queried post and seperate them by line break
        $shortcodes = esc_html( get_post_meta( get_the_ID(), 'entry_shortcode_schema', true ) );
        $shortcodes = preg_split('/\r\n|\r|\n/', $shortcodes);
        $shortcode_parts = array();

        if ( $shortcodes ) {

            // Split each shortcode into an array of its parts
            foreach ( $shortcodes as $shortcode ) {

                // Match the shortcode against all registered shortcodes (regex) so we can check if the supplied shortcode(s) are valid
                preg_match( '/' . get_shortcode_regex() . '/s', $shortcode, $match );

                // Add them to the array of parts, filter the results to exclude any empty arrays and reindexed the array from zero
                if ( $match ) {
                    $parts = array_values( array_filter( $match ) );

                    // Skip any parts that do not have the shortcode type set - array[1] in this case
                    // This prevents any invalid strings in the custom field being passed through the rest of this function
                    if ( isset( $parts[1] ) && $parts[1] === ORGNK_FAQS_SHORTCODE_NAME ) {
                        $shortcode_parts[] = $parts;
                    }
                }
            }
		}

		// At this point, we have an array for each shortcode structured like this:
		// [0]=> "[faqs category="Category Name" id="123"]" - the original shortcode
		// [1]=> "faqs" - the name of the shortcode type
		// [2]=> "category="Category Name" id="123"" - any attributes, which we'll use for shortcode_parse_atts later

		// If shortcodes were found, pass them to our schema script generation function
		if ( $shortcode_parts ) {
			$schema_script = orgnk_faqs_schema_script( $shortcode_parts );
		}

		echo $schema_script;
	}
}
