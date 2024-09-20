<?php
// Enqueue Styles and Scripts for the Theme
function my_theme_enqueue_styles() {
    // CSS Files
    wp_enqueue_style('bootstrap-css', get_template_directory_uri() . '/vendor/bootstrap/css/bootstrap.min.css');
    wp_enqueue_style('fontawesome-css', get_template_directory_uri() . '/assets/css/fontawesome.css');
    wp_enqueue_style('templatemo-edu-meeting-css', get_template_directory_uri() . '/assets/css/templatemo-edu-meeting.css');
    wp_enqueue_style('owl-carousel-css', get_template_directory_uri() . '/assets/css/owl.css');
    wp_enqueue_style('lightbox-css', get_template_directory_uri() . '/assets/css/lightbox.css');
    wp_enqueue_style('flex-slider-css', get_template_directory_uri() . '/assets/css/flex-slider.css');
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function my_theme_enqueue_scripts() {
    // Ensure jQuery is loaded
    wp_enqueue_script('jquery', get_template_directory_uri() . '/vendor/jquery/jquery.min.js', array(), null, true);

    // Bootstrap JS
    wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/vendor/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), null, true);

    // Additional JS Files
    wp_enqueue_script('isotope-min-js', get_template_directory_uri() . '/assets/js/isotope.min.js', array('jquery'), null, true);
    wp_enqueue_script('owl-carousel-js', get_template_directory_uri() . '/assets/js/owl-carousel.js', array('jquery'), null, true);
    wp_enqueue_script('lightbox-js', get_template_directory_uri() . '/assets/js/lightbox.js', array('jquery'), null, true);
    wp_enqueue_script('tabs-js', get_template_directory_uri() . '/assets/js/tabs.js', array('jquery'), null, true);
    wp_enqueue_script('video-js', get_template_directory_uri() . '/assets/js/video.js', array('jquery'), null, true);
    wp_enqueue_script('slick-slider-js', get_template_directory_uri() . '/assets/js/slick-slider.js', array('jquery'), null, true);
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), null, true);

    // Custom inline script
    wp_add_inline_script('custom-js', '
        (function($){
            $(document).ready(function() {
                // Add active class to the first menu item
                $(".nav li:first").addClass("active");

                // Function to show sections on click
                var showSection = function(section, isAnimate) {
                    var direction = section.replace(/#/, ""),
                        reqSection = $(".section").filter("[data-section=" + direction + "]"),
                        reqSectionPos = reqSection.offset().top - 0;

                    if (isAnimate) {
                        $("body, html").animate({ scrollTop: reqSectionPos }, 800);
                    } else {
                        $("body, html").scrollTop(reqSectionPos);
                    }
                };

                // Function to check current section and update active class
                var checkSection = function() {
                    $(".section").each(function() {
                        var $this = $(this),
                            topEdge = $this.offset().top - 80,
                            bottomEdge = topEdge + $this.height(),
                            wScroll = $(window).scrollTop();

                        if (topEdge < wScroll && bottomEdge > wScroll) {
                            var currentId = $this.data("section"),
                                reqLink = $("a").filter("[href*=\\#" + currentId + "]");
                            reqLink.closest("li").addClass("active").siblings().removeClass("active");
                        }
                    });
                };

                // Event listener for menu item clicks
                $(".main-menu, .responsive-menu, .scroll-to-section").on("click", "a", function(e) {
                    e.preventDefault();
                    showSection($(this).attr("href"), true);
                });

                // Check section on scroll
                $(window).scroll(function() {
                    checkSection();
                });
            });
        })(jQuery);
    ');
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');

// Theme features including menus, logo, and post thumbnails
function testing_theme_features() {
    // Register navigation menus
    register_nav_menus(array(
        'primary-menu' => __('Header Menu', 'text_domain'),
        'footer-menu' => __('Footer Menu', 'text_domain'),
        'useful-link' => __('Useful Links', 'text_domain')
    ));

    // Enable support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Enable support for post thumbnails
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'testing_theme_features');

// Add custom settings for uploading a banner video in the customizer
function theme_customizer_settings($wp_customize) {
    // Add a custom section for banner video
    $wp_customize->add_section('video_section', array(
        'title' => __('Banner Video', 'yourtheme'),
        'priority' => 30,
    ));

    // Add setting for video URL
    $wp_customize->add_setting('banner_video_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    // Add control to upload video
    $wp_customize->add_control(new WP_Customize_Upload_Control($wp_customize, 'banner_video_url', array(
        'label' => __('Upload Banner Video', 'yourtheme'),
        'section' => 'video_section',
        'settings' => 'banner_video_url',
    )));
}
add_action('customize_register', 'theme_customizer_settings');

// Add custom menu page in WordPress admin
function theme_settings_menu() {
    add_menu_page(
        'Theme Settings',       // Page title
        'Theme Settings',       // Menu title
        'manage_options',       // Capability
        'theme-settings',       // Menu slug
        'theme_settings_page',  // Callback function
        'dashicons-admin-generic', // Dashicon for the menu
        60                      // Position in menu
    );
}
add_action('admin_menu', 'theme_settings_menu');

// The callback function for the Theme Settings page
function theme_settings_page() {
    ?>
    <div class="wrap">
        <h1>Theme Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('theme_settings_group');
            do_settings_sections('theme-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings, sections, and fields for theme settings
function theme_settings_init() {
    // Register settings with sanitization callbacks
    register_setting('theme_settings_group', 'header_background_color', 'sanitize_hex_color');
    register_setting('theme_settings_group', 'typography', 'sanitize_text_field');
    register_setting('theme_settings_group', 'logo_visibility', 'sanitize_text_field');
    register_setting('theme_settings_group', 'logo_width', 'absint');

    // Add settings sections
    add_settings_section('header_menu_section', 'Header Menu', null, 'theme-settings');
    add_settings_section('typography_section', 'Typography', null, 'theme-settings');
    add_settings_section('logo_section', 'Logo', null, 'theme-settings');

    // Add settings fields
    add_settings_field('header_background_color', 'Header Background Color', 'header_menu_field_callback', 'theme-settings', 'header_menu_section');
    add_settings_field('typography', 'Fonts', 'typography_field_callback', 'theme-settings', 'typography_section');
    add_settings_field('logo_visibility', 'Logo Visibility', 'logo_visibility_field_callback', 'theme-settings', 'logo_section');
    add_settings_field('logo_width', 'Logo Width', 'logo_width_field_callback', 'theme-settings', 'logo_section');
}
add_action('admin_init', 'theme_settings_init');

// Callback for the Header Background Color field
function header_menu_field_callback() {
    $value = get_option('header_background_color', '#ffffff'); // Default to white
    echo '<input type="text" name="header_background_color" value="' . esc_attr($value) . '" class="regular-text" />';
}

// Callback for the Typography field
function typography_field_callback() {
    $value = get_option('typography', 'Arial'); // Default font
    echo '<input type="text" name="typography" value="' . esc_attr($value) . '" class="regular-text" />';
}

// Callback for the Logo Visibility field
function logo_visibility_field_callback() {
    $value = get_option('logo_visibility', 'show');
    ?>
    <select name="logo_visibility">
        <option value="show" <?php selected($value, 'show'); ?>>Show</option>
        <option value="hide" <?php selected($value, 'hide'); ?>>Hide</option>
    </select>
    <?php
}

// Callback for the Logo Width field
function logo_width_field_callback() {
    $value = get_option('logo_width', '200'); // Default to 200px
    ?>
    <input type="range" id="logo_width" name="logo_width" min="50" max="500" step="1" value="<?php echo esc_attr($value); ?>" />
    <span id="logo_width_value"><?php echo esc_html($value); ?>px</span>
    <script>
        document.getElementById('logo_width').addEventListener('input', function() {
            document.getElementById('logo_width_value').textContent = this.value + 'px';
        });
    </script>
    <?php
}

// Additional custom settings for the Customizer
function my_custom_customizer_settings($wp_customize) {
    // Add a new custom section
    $wp_customize->add_section('my_custom_section', array(
        'title'    => __('My Custom Section', 'textdomain'),
        'priority' => 30,
    ));

    // Add a setting for text input
    $wp_customize->add_setting('my_text_setting', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    // Add a control for the setting
    $wp_customize->add_control('my_text_setting_control', array(
        'label'    => __('My Text Setting', 'textdomain'),
        'section'  => 'my_custom_section',
        'settings' => 'my_text_setting',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'my_custom_customizer_settings');

