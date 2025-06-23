<?php

$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
define('TEAM_MEMBERS_PLUGIN', 'team-members/tmm.php');
define('PMPRO_PLUGIN', 'paid-memberships-pro/paid-memberships-pro.php');
define('PMPRO_REGISTER_HELPER_PLUGIN', 'pmpro-register-helper/pmpro-register-helper.php');
define('MAILPOET_PLUGIN', 'mailpoet/mailpoet.php');
define('STRIPE_FEE', 2.65);
define('MEMBERSHIP_COST', '100,00');

/* Rearrange nav bar for logged in/out users */
add_filter('wp_nav_menu_objects', function($items, $args) {
    foreach ($items as $key => $item) {
        if (is_user_logged_in()) {
            if (in_array('menu-item-guests-only', $item->classes)) {
                unset($items[$key]);
            }

            if ($args->theme_location === 'primary') {
                $item->classes[] = 'menu-item-kind-wide';
            }
        } else {
            if (in_array('menu-item-members-only', $item->classes)) {
                unset($items[$key]);
            }
        }
    }

    return $items;
}, 10, 2);

/* Redirect logged-out users to backend or frontend depending on their role */
add_filter('logout_redirect', function ($redirect_to, $requested_redirect_to, $user) {
    if (isset($user->roles) && !in_array('administrator', $user->roles)) {
        return esc_url(home_url());
    }

    return $redirect_to;
}, 10, 3);

if (in_array(TEAM_MEMBERS_PLUGIN, $active_plugins)) {

    /* Load Team Members custom JS */
    wp_enqueue_script('team-members', get_stylesheet_directory_uri() . '/js/team-members.js', array('jquery'));

    /* Team Members shortcode override */
    add_shortcode("tmm", function ($atts) {
        global $post;
        $team_view = '';

        /* Gets table slug (post name). */
        $all_attr = shortcode_atts(array("name" => ''), $atts);
        $name = $all_attr['name'];

        /* Gets the team. */
        $args = array('post_type' => 'tmm', 'name' => $name);
        $custom_posts = get_posts($args);

        foreach ($custom_posts as $post) : setup_postdata($post);
            $members = get_post_meta(get_the_id(), '_tmm_head', true);
            $tmm_columns = get_post_meta($post->ID, '_tmm_columns', true);
            $tmm_color = get_post_meta($post->ID, '_tmm_color', true);
            $tmm_bio_alignment = get_post_meta($post->ID, '_tmm_bio_alignment', true);

            /* Checks if member links open in new window. */
            $tmm_piclink_beh = get_post_meta($post->ID, '_tmm_piclink_beh', true);
            $tmm_plb = $tmm_piclink_beh == 'new' ? 'target="_blank"' : '';

            /* Checks if forcing original fonts. */
            $original_font = get_post_meta($post->ID, '_tmm_original_font', true);
            $ori_f = 'tmm_plugin_f';
            if ($original_font) {
                if ($original_font == "no") {
                    $ori_f = 'tmm_theme_f';
                } else if ($original_font == "yes") {
                    $ori_f = 'tmm_plugin_f';
                }
            }

            $team_view = '';
            $team_view .= '<div class="tmm tmm_' . $name . '">';
            $team_view .= '<div class="tmm_' . $tmm_columns . '_columns tmm_wrap ' . $ori_f . '">';

            if (is_array($members) || is_object($members)) {
                foreach ($members as $key => $member) {
                    /* Creates Team container. */
                    if ($key % 2 == 0) {
                        /* Checks if group of two (alignment). */
                        $team_view .= '<span class="tmm_two_containers_tablet"></span>';
                    }
                    if ($key % $tmm_columns == 0) {
                        /* Checks if first div of group and closes. */
                        if ($key > 0) $team_view .= '</div><span class="tmm_columns_containers_desktop"></span>';
                        $team_view .= '<div class="tmm_container">';
                    }

                    /* START member. */
                    $team_view .= '<div class="tmm_member" style="border-top:' . $tmm_color . ' solid 5px;">';

                    /* Displays member photo. */
                    if (!empty($member['_tmm_photo_url']))
                        $team_view .= '<a ' . $tmm_plb . ' href="' . $member['_tmm_photo_url'] . '" title="' . $member['_tmm_firstname'] . ' ' . $member['_tmm_lastname'] . '">';

                    if (!empty($member['_tmm_photo']))
                        $team_view .= '<div class="tmm_photo tmm_pic_' . $name . '_' . $key . '" style="background: url(' . $member['_tmm_photo'] . '); margin-left: auto; margin-right:auto; background-size:cover !important;"></div>';

                    if (!empty($member['_tmm_photo_url']))
                        $team_view .= '</a>';

                    /* Creates text block. */
                    $team_view .= '<div class="tmm_textblock">';

                    /* Displays names. */
                    $team_view .= '<div class="tmm_names">';
                    if (!empty($member['_tmm_firstname']))
                        $team_view .= '<span class="tmm_fname">' . $member['_tmm_firstname'] . '</span> ';
                    if (!empty($member['_tmm_lastname']))
                        $team_view .= '<span class="tmm_lname">' . $member['_tmm_lastname'] . '</span>';
                    $team_view .= '</div>';

                    /* Displays jobs. */
                    if (!empty($member['_tmm_job']))
                        $team_view .= '<div class="tmm_job">' . $member['_tmm_job'] . '</div>';

                    /* Displays bios. */
                    if (!empty($member['_tmm_desc']))
                        $team_view .= '<div class="tmm_desc" style="text-align:' . $tmm_bio_alignment . '">' . do_shortcode($member['_tmm_desc']) . '</div>';

                    /* Creates social block. */
                    $team_view .= '<div class="tmm_scblock">';

                    /* Displays social links. */
                    for ($i = 1; $i <= 3; $i++) {
                        if ($member['_tmm_sc_type' . $i] != 'nada') {
                            if ($member['_tmm_sc_type' . $i] == 'email') {
                                $team_view .= '<a class="tmm_sociallink" href="mailto:' . (!empty($member['_tmm_sc_url' . $i]) ? $member['_tmm_sc_url' . $i] : '') . '" title="' . (!empty($member['_tmm_sc_title' . $i]) ? $member['_tmm_sc_title' . $i] : '') . '"><img alt="' . (!empty($member['_tmm_sc_title' . $i]) ? $member['_tmm_sc_title' . $i] : '') . '" src="' . plugins_url('inc/img/links/', 'team-members/tmm.php') . $member['_tmm_sc_type' . $i] . '.png"/></a>';
                            } else if ($member['_tmm_sc_type' . $i] == 'phone') {
                                $team_view .= '<a class="tmm_sociallink" href="tel:' . (!empty($member['_tmm_sc_url' . $i]) ? $member['_tmm_sc_url' . $i] : '') . '" title="' . (!empty($member['_tmm_sc_title' . $i]) ? $member['_tmm_sc_title' . $i] : '') . '"><img alt="' . (!empty($member['_tmm_sc_title' . $i]) ? $member['_tmm_sc_title' . $i] : '') . '" src="' . plugins_url('inc/img/links/', 'team-members/tmm.php') . $member['_tmm_sc_type' . $i] . '.png"/></a>';
                            } else {
                                $team_view .= '<a target="_blank" class="tmm_sociallink" href="' . (!empty($member['_tmm_sc_url' . $i]) ? $member['_tmm_sc_url' . $i] : '') . '" title="' . (!empty($member['_tmm_sc_title' . $i]) ? $member['_tmm_sc_title' . $i] : '') . '"><img alt="' . (!empty($member['_tmm_sc_title' . $i]) ? $member['_tmm_sc_title' . $i] : '') . '" src="' . plugins_url('inc/img/links/', 'team-members/tmm.php') . $member['_tmm_sc_type' . $i] . '.png"/></a>';
                            }
                        }
                    }

                    $team_view .= '</div>'; // Closes social block.
                    $team_view .= '</div>'; // Closes text block.
                    $team_view .= '<a href="javascript: void(0);" class="tmm_show_more_btn"></a>'; // Show More button.
                    $team_view .= '</div>'; // END member.

                    $page_count = count($members);
                    if ($key == $page_count - 1) $team_view .= '<div style="clear:both;"></div>';
                }
            }

            $team_view .= '</div>'; // Closes container.
            $team_view .= '</div>'; // Closes wrap.
            $team_view .= '</div>'; // Closes tmm.
        endforeach;

        wp_reset_postdata();
        return $team_view;
    });

}

if (in_array(PMPRO_PLUGIN, $active_plugins)) {

    /* Load PMPro custom JS */
    wp_enqueue_script('pmpro', get_stylesheet_directory_uri() . '/js/pmpro.js', array('jquery'));

    /* Make some PMPro billing fields not required */
	add_action('pmpro_required_billing_fields', function ($fields) {
		if(is_array($fields)) {
			unset($fields['bfirstname']);
			unset($fields['blastname']);
			unset($fields['bstate']);
		}

		return $fields;
	});

    /* Hide default PMPro checkout message */
    add_filter('pmpro_include_pricing_fields', '__return_false');

    /* Hide PMPro checkout email confirmation field */
    add_filter('pmpro_checkout_confirm_email', '__return_false');

    /* PMPro login redirect override */
    add_filter('login_redirect', function ($redirect_to, $request, $user) {
        if (isset($user->roles) && in_array('administrator', $user->roles)) {
            return esc_url(admin_url('index.php'));
        }

        return pmpro_login_redirect($redirect_to, $request, $user);
    }, 10, 3);

    /* Custom order codes for PMPro orders */
    add_filter('pmpro_random_code', function () {
        global $wpdb;

        /**
         * PMPro oder code cannot just be an integer and must contain a string.
         *
         * @var string $code
         */
        $code = 'F-';

        $last_code_id = $wpdb->get_var("SELECT COALESCE(MAX(CAST(SUBSTR(code, 3) AS UNSIGNED)), 0) FROM $wpdb->pmpro_membership_orders");

        // In case the DB query for the last code ID fails, a random string is returned as a fallback.
        if ($last_code_id === null) {
            return $code . wp_generate_password(10, false, false);
        }

        return $code . ((int) $last_code_id + 1);
    });

    /* Add new registration fields to PMPro */
    add_action('init', function () {
        if (!function_exists('pmprorh_add_registration_field')) {
            return false;
        }

        pmprorh_add_registration_field('after_billing_fields', new PMProRH_Field(
            'fiscal_code',
            'text',
            array(
                'label'    => __('Codice Fiscale', 'generatepresschild'),
                'size'     => 16,
                'class'    => 'fiscal_code',
                'profile'  => true,
                'required' => true,
            )
        ));

        pmprorh_add_registration_field('after_billing_fields', new PMProRH_Field(
            'organization',
            'text',
            array(
                'label'    => __("Indicare se amministratore, coadiutore o altro e se iscritti ad un ordine professionale (specificare quale e la città)", 'generatepresschild'),
                'size'     => 64,
                'class'    => 'origin',
                'profile'  => true,
                'required' => true,
            )
        ));
    });

    /* Remove the non-member login/register message on archive pages (only shows when viewing a single post or page) */
    add_action('wp', function () {
        if (!is_single()) {
            remove_filter('the_excerpt', 'pmpro_membership_excerpt_filter', 15);
        }
    });

    /* Add Stripe fee to membership payment */
    add_filter('pmpro_checkout_level', function ($level) {
        if (!empty($_REQUEST['gateway']) && ($_REQUEST['gateway'] == 'stripe')) {

            // Update initial payment value if using one-time payment
            if ($level->initial_payment > 0) {
                $level->initial_payment = $level->initial_payment + STRIPE_FEE;
            }

            // Update billing amount value if using recurring payment
            if ($level->billing_amount > 0) {
                $level->billing_amount = $level->billing_amount + STRIPE_FEE;
            }
        }

        return $level;
    });

    /* Add custom notice about Stripe extra fee */
    add_action('pmpro_checkout_after_payment_information_fields', function () {
        ?>
            <div id="pmpro-stripe-notice">
                <p><?php printf(__('Per i pagamenti con carta verrà applicata una commissione di %s€', 'generatepresschild'), STRIPE_FEE) ?></p>
            </div>
        <?php
    });

    /* Use the pmpro_default_country filter to pre-set the dropdown at checkout to your country of choice. */
    add_filter('pmpro_default_country', function() {
        return "IT";
    });

    /* Change email address for all admin related emails in PMPro */
    add_filter('pmpro_email_recipient', function ($user_email, $email) {
        if (strpos($email->template, '_admin') !== false) {
            $user_email = 'info@inag.it';
        }

        return $user_email;
    }, 10, 2);

    /* Override confirmation email template */
    add_filter('pmpro_email_filter', function ($email) {

        // Only override confirmation emails that have invoices
        if (empty($email) || (strpos($email->template, 'checkout') === false) || empty($email->data['invoice_id'])) {
            return $email;
        }

        $order = new MemberOrder($email->data['invoice_id']);

        // Make sure we have a real order
        if (empty($order) || empty($order->id)) {
            return $email;
        }

        // Update subject
        $email->subject = 'Conferma di associazione a INAG.';

        // Loading invoice values
        $code = $order->code;
        $name = $order->billing->name;
        $street = $order->billing->street;
        $state = $order->billing->state;
        $zip = $order->billing->zip;
        $country = $order->billing->country;
        $today = date_i18n(get_option('date_format'), $order->timestamp);
        $formatted_total = number_format((float)$order->total, 2, ',', ' ');
        $payment_year = date('Y', $order->timestamp);

        if (empty($order->gateway)) {
            $payment_method = __('Test', 'generatepresschild');
        } elseif ($order->gateway == 'check') {
            $payment_method = __('Bonifico', 'generatepresschild');
        } elseif ($order->gateway == 'stripe') {
            $payment_method = __('Carta di credito', 'generatepresschild');
        } else {
            $payment_method = __('Non specificato', 'generatepresschild');
        }

        // Update body
        ob_start();
        include 'email/checkout.php';
        $email->body = ob_get_clean();

        return $email;
    });

    /* Remove membership cancel button on profile page */
    add_filter('pmpro_member_action_links', function ($pmpro_member_action_links) {
        unset($pmpro_member_action_links['cancel']);
        return $pmpro_member_action_links;
    }, 15, 1);

    /* Allow admins to access PMPro restricted content */
    function pmmpro_allow_access_for_admins( $hasaccess, $mypost, $myuser, $post_membership_levels ) {
      // If user is an admin allow access.
      if ( current_user_can( 'manage_options' ) ) {
        $hasaccess = true;
      }
      return $hasaccess;
    }
    add_filter( 'pmpro_has_membership_access_filter', 'pmmpro_allow_access_for_admins', 30, 4 );

    if (in_array(PMPRO_REGISTER_HELPER_PLUGIN, $active_plugins)) {

        /* Adding the Fiscal Code column to the Users table */
        add_action('manage_users_columns', function ($column_headers) {
            unset($column_headers['posts']);
            unset($column_headers['wfls_2fa_status']);

            $offset = array_search('role', array_keys($column_headers));

            return array_merge(
                array_slice($column_headers, 0, $offset),
                array(
                    'fiscal_code'  => __('Codice Fiscale', 'generatepresschild'),
                    'organization' => __("Iscritto all'ordine", 'generatepresschild'),
                ),
                array_slice($column_headers, $offset)
            );
        });

        /* Populating the Fiscal Code column in the Users table */
        add_action('manage_users_custom_column', function ($value, $column_name, $user_id) {
            if ($column_name == 'fiscal_code') {
                return get_user_meta($user_id, 'fiscal_code', true);
            } elseif ($column_name == 'organization') {
                return get_user_meta($user_id, 'organization', true);
            } else {
                return $value;
            }
        }, 10, 3);

    }
}

if (in_array(MAILPOET_PLUGIN, $active_plugins)) {

    /* Mailpoet filters */
    add_filter('mailpoet_newsletter_post_excerpt_length', function () {
        return 0; // Number of words to display
    });

}

/* Add new block styles to Gutenberg */
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_style('editor-style', get_stylesheet_directory_uri() . '/editor-style.css', false, '1.0.0');

    wp_enqueue_script(
        'editor-script',
        get_stylesheet_directory_uri() . '/js/editor.js',
        array('wp-blocks', 'wp-dom'),
        filemtime( get_stylesheet_directory() . '/js/editor.js' ),
        true
    );
});

/* Aggiunge capability ai membership manager per vedere cf7db */
function manipulate_editor_capabilities() {
	$user_role = get_role('pmpro_membership_manager');
	if ($user_role) {
		$user_role->add_cap('cfdb7_access');
	}
}
add_action('init', 'manipulate_editor_capabilities');


/* Remove Yoast 'SEO Manager' role */
if ( get_role('wpseo_manager') ) {
    remove_role( 'wpseo_manager' );
}

/* Remove Yoast 'SEO Editor' role */
if ( get_role('wpseo_editor') ) {
    remove_role( 'wpseo_editor' );
}

/* Remove default dashoboard widgets */
add_action('wp_dashboard_setup', function () {
    global $wp_meta_boxes;

    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);

    remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'side');
	remove_meta_box('wordfence_activity_report_widget', 'dashboard', 'normal');
	remove_meta_box('google_dashboard_widget', 'dashboard', 'normal');
  remove_meta_box('cn_dashboard_stats', 'dashboard', 'normal');
});

/* Google Analytics */
// Add a Customizer setting and control for Google Analytics 4 Measurement ID
add_action('customize_register', function($wp_customize) {
    $wp_customize->add_section('inag_analytics_section', array(
        'title'    => __('Google Analytics', 'inag'),
        'priority' => 160,
    ));

    $wp_customize->add_setting('inag_google_analytics_id', array(
        'type'              => 'option',
        'sanitize_callback' => function($input) {
            // Only allow GA4 Measurement IDs (e.g. G-XXXXXXXXXX)
            return preg_match('/^G-\w+$/', trim($input)) ? trim($input) : '';
        },
    ));

    $wp_customize->add_control('inag_google_analytics_id', array(
        'label'       => __('Google Analytics Measurement ID', 'inag'),
        'section'     => 'inag_analytics_section',
        'type'        => 'text',
        'description' => __('Inserisci solo il codice GA4 (es: G-XXXXXXXXXX).', 'inag'),
    ));
});

// Output the GA4 script in the <head> if the ID is set
add_action('wp_head', function() {
    $ga_id = get_option('inag_google_analytics_id');
    if ($ga_id) : ?>
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga_id); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo esc_js($ga_id); ?>');
</script>
<!-- End Google Analytics -->
    <?php endif;
});
