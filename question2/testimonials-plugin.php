<?php
/**
 * Plugin Name: DVC Testimonials Manager
 * Plugin URI: https://portfolio-one-gray-40.vercel.app/ 
 * Description: A complete testimonials management system allowing users to easily add, manage, and display testimonials via a shortcode slider.
 * Version: 1.0.0
 * Author: Syam Suhith
 * Text Domain: dvc-testimonials
 */

// Exit if accessed directly to prevent unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ==========================================
 * PART A: Backend (WordPress Admin) CPT
 * ==========================================
 */
function dvc_register_testimonials_cpt() {
    $labels = array(
        'name'                  => _x( 'Testimonials', 'Post type general name', 'dvc-testimonials' ),
        'singular_name'         => _x( 'Testimonial', 'Post type singular name', 'dvc-testimonials' ),
        'menu_name'             => _x( 'Testimonials', 'Admin Menu text', 'dvc-testimonials' ),
        'name_admin_bar'        => _x( 'Testimonial', 'Add New on Toolbar', 'dvc-testimonials' ),
        'add_new'               => __( 'Add New', 'dvc-testimonials' ),
        'add_new_item'          => __( 'Add New Testimonial', 'dvc-testimonials' ),
        'new_item'              => __( 'New Testimonial', 'dvc-testimonials' ),
        'edit_item'             => __( 'Edit Testimonial', 'dvc-testimonials' ),
        'view_item'             => __( 'View Testimonial', 'dvc-testimonials' ),
        'all_items'             => __( 'All Testimonials', 'dvc-testimonials' ),
        'search_items'          => __( 'Search Testimonials', 'dvc-testimonials' ),
        'not_found'             => __( 'No testimonials found.', 'dvc-testimonials' ),
        'not_found_in_trash'    => __( 'No testimonials found in Trash.', 'dvc-testimonials' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'testimonial' ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-testimonial', // Custom menu icon
        'show_in_rest'       => true, // Enables Gutenberg editor support
        'supports'           => array( 'title', 'editor', 'thumbnail' ), // Title, Testimonial text, Client photo
    );

    register_post_type( 'dvc_testimonial', $args );
}
add_action( 'init', 'dvc_register_testimonials_cpt' );

/**
 * ==========================================
 * PART B: Custom Fields (Meta Box)
 * ==========================================
 */
function dvc_add_testimonial_meta_boxes() {
    add_meta_box(
        'dvc_testimonial_details',
        __( 'Client Details', 'dvc-testimonials' ),
        'dvc_render_testimonial_meta_box',
        'dvc_testimonial',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'dvc_add_testimonial_meta_boxes' );

function dvc_render_testimonial_meta_box( $post ) {
    // Add a nonce field so we can check for it later
    wp_nonce_field( 'dvc_save_testimonial_data', 'dvc_testimonial_nonce' );

    // Retrieve existing values from the database
    $client_name     = get_post_meta( $post->ID, '_dvc_client_name', true );
    $client_position = get_post_meta( $post->ID, '_dvc_client_position', true );
    $company_name    = get_post_meta( $post->ID, '_dvc_company_name', true );
    $rating          = get_post_meta( $post->ID, '_dvc_rating', true );
    if ( empty( $rating ) ) $rating = 5; // Default to 5 stars

    // Output the HTML for the fields
    ?>
    <table class="form-table">
        <tr>
            <th><label for="dvc_client_name"><?php _e( 'Client Name (Required)', 'dvc-testimonials' ); ?></label></th>
            <td>
                <input type="text" id="dvc_client_name" name="dvc_client_name" value="<?php echo esc_attr( $client_name ); ?>" class="regular-text" required />
            </td>
        </tr>
        <tr>
            <th><label for="dvc_client_position"><?php _e( 'Position/Title', 'dvc-testimonials' ); ?></label></th>
            <td>
                <input type="text" id="dvc_client_position" name="dvc_client_position" value="<?php echo esc_attr( $client_position ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="dvc_company_name"><?php _e( 'Company Name', 'dvc-testimonials' ); ?></label></th>
            <td>
                <input type="text" id="dvc_company_name" name="dvc_company_name" value="<?php echo esc_attr( $company_name ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="dvc_rating"><?php _e( 'Rating', 'dvc-testimonials' ); ?></label></th>
            <td>
                <select id="dvc_rating" name="dvc_rating">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <option value="<?php echo $i; ?>" <?php selected( $rating, $i ); ?>><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function dvc_save_testimonial_meta_data( $post_id ) {
    // 1. Verify nonce
    if ( ! isset( $_POST['dvc_testimonial_nonce'] ) || ! wp_verify_nonce( $_POST['dvc_testimonial_nonce'], 'dvc_save_testimonial_data' ) ) {
        return;
    }
    // 2. Check for autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // 3. Check user permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // 4. Sanitize and save data
    if ( isset( $_POST['dvc_client_name'] ) ) {
        update_post_meta( $post_id, '_dvc_client_name', sanitize_text_field( wp_unslash( $_POST['dvc_client_name'] ) ) );
    }
    if ( isset( $_POST['dvc_client_position'] ) ) {
        update_post_meta( $post_id, '_dvc_client_position', sanitize_text_field( wp_unslash( $_POST['dvc_client_position'] ) ) );
    }
    if ( isset( $_POST['dvc_company_name'] ) ) {
        update_post_meta( $post_id, '_dvc_company_name', sanitize_text_field( wp_unslash( $_POST['dvc_company_name'] ) ) );
    }
    if ( isset( $_POST['dvc_rating'] ) ) {
        update_post_meta( $post_id, '_dvc_rating', absint( $_POST['dvc_rating'] ) );
    }
}
add_action( 'save_post_dvc_testimonial', 'dvc_save_testimonial_meta_data' );


/**
 * ==========================================
 * PART C & D: Frontend Display & Shortcode
 * ==========================================
 */
function dvc_testimonials_shortcode( $atts ) {
    // Extract shortcode parameters with defaults
    $atts = shortcode_atts( array(
        'count'   => -1,      // Default: all
        'orderby' => 'date',  // Default: date
        'order'   => 'DESC'   // Default: DESC
    ), $atts, 'testimonials' );

    $args = array(
        'post_type'      => 'dvc_testimonial',
        'posts_per_page' => intval( $atts['count'] ),
        'orderby'        => sanitize_text_field( $atts['orderby'] ),
        'order'          => sanitize_text_field( $atts['order'] ),
        'post_status'    => 'publish'
    );

    $query = new WP_Query( $args );

    if ( ! $query->have_posts() ) {
        return '<p>' . esc_html__( 'No testimonials found.', 'dvc-testimonials' ) . '</p>';
    }

    // Generate a unique ID in case multiple sliders are on the same page
    $slider_id = 'dvc-slider-' . uniqid();

    ob_start();
    ?>
    
    <style>
        .dvc-testimonial-slider-wrapper {
            position: relative;
            max-width: 800px;
            margin: 2rem auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            padding: 2.5rem;
            text-align: center;
            font-family: sans-serif;
        }
        .dvc-slide {
            display: none;
            animation: dvcFadeIn 0.5s;
        }
        .dvc-slide.active {
            display: block;
        }
        @keyframes dvcFadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .dvc-client-photo img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1rem auto;
            border: 3px solid #f3f4f6;
        }
        .dvc-stars {
            color: #fbbf24;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .dvc-testimonial-text {
            font-size: 1.1rem;
            color: #374151;
            font-style: italic;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        .dvc-client-info h4 {
            margin: 0 0 0.25rem 0;
            color: #111827;
            font-size: 1.1rem;
        }
        .dvc-client-meta {
            color: #6b7280;
            font-size: 0.9rem;
        }
        /* Navigation Controls */
        .dvc-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #f3f4f6;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #374151;
            transition: background 0.3s;
        }
        .dvc-nav-btn:hover {
            background: #e5e7eb;
        }
        .dvc-prev { left: 1rem; }
        .dvc-next { right: 1rem; }
        
        /* Responsive adjustments */
        @media (max-width: 600px) {
            .dvc-testimonial-slider-wrapper { padding: 2rem 1rem; }
            .dvc-nav-btn { top: auto; bottom: 1rem; transform: none; }
            .dvc-prev { left: 35%; }
            .dvc-next { right: 35%; }
            .dvc-slide { margin-bottom: 3rem; }
        }
    </style>

    <div class="dvc-testimonial-slider-wrapper" id="<?php echo esc_attr( $slider_id ); ?>">
        <div class="dvc-slides-container">
            <?php 
            $slide_index = 0;
            while ( $query->have_posts() ) : $query->the_post(); 
                $post_id         = get_the_ID();
                $client_name     = get_post_meta( $post_id, '_dvc_client_name', true );
                $client_position = get_post_meta( $post_id, '_dvc_client_position', true );
                $company_name    = get_post_meta( $post_id, '_dvc_company_name', true );
                $rating          = get_post_meta( $post_id, '_dvc_rating', true ) ?: 5;
                $active_class    = ( $slide_index === 0 ) ? 'active' : '';
            ?>
                <div class="dvc-slide <?php echo esc_attr( $active_class ); ?>">
                    
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="dvc-client-photo">
                            <?php the_post_thumbnail( 'thumbnail' ); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="dvc-stars">
                        <?php 
                        // Star rating display
                        echo esc_html( str_repeat( '★', intval( $rating ) ) );
                        echo esc_html( str_repeat( '☆', 5 - intval( $rating ) ) ); 
                        ?>
                    </div>
                    
                    <div class="dvc-testimonial-text">
                        <?php echo wp_kses_post( wpautop( get_the_content() ) ); ?>
                    </div>
                    
                    <div class="dvc-client-info">
                        <h4><?php echo esc_html( $client_name ); ?></h4>
                        <?php if ( $client_position || $company_name ) : ?>
                            <div class="dvc-client-meta">
                                <?php 
                                $meta_parts = array_filter( array( $client_position, $company_name ) );
                                echo esc_html( implode( ', ', $meta_parts ) ); 
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            <?php 
                $slide_index++;
            endwhile; 
            wp_reset_postdata(); 
            ?>
        </div>

        <?php if ( $query->found_posts > 1 ) : ?>
            <button type="button" class="dvc-nav-btn dvc-prev" aria-label="Previous Testimonial">❮</button>
            <button type="button" class="dvc-nav-btn dvc-next" aria-label="Next Testimonial">❯</button>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('<?php echo esc_js( $slider_id ); ?>');
            if (!slider) return;

            const slides = slider.querySelectorAll('.dvc-slide');
            const btnPrev = slider.querySelector('.dvc-prev');
            const btnNext = slider.querySelector('.dvc-next');
            let currentIndex = 0;

            if (slides.length <= 1) return;

            function showSlide(index) {
                slides.forEach(slide => slide.classList.remove('active'));
                slides[index].classList.add('active');
            }

            if (btnPrev) {
                btnPrev.addEventListener('click', () => {
                    currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
                    showSlide(currentIndex);
                });
            }

            if (btnNext) {
                btnNext.addEventListener('click', () => {
                    currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
                    showSlide(currentIndex);
                });
            }
        });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode( 'testimonials', 'dvc_testimonials_shortcode' );
