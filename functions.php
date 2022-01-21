<?php

$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
define('TEAM_MEMBERS_PLUGIN', 'team-members/tmm.php');
define('PMPRO_PLUGIN', 'paid-memberships-pro/paid-memberships-pro.php');
define('PMPRO_REGISTER_HELPER_PLUGIN', 'pmpro-register-helper/pmpro-register-helper.php');
define('STRIPE_FEE', 2.65);

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
	
	/* Make PMPro 'First Name' and 'Last Name' billing fields not required */
	add_action('pmpro_required_billing_fields', function ($fields) {
		if(is_array($fields)) {
			unset($fields['bfirstname']);
			unset($fields['blastname']);
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
    });

    /* Remove the non-member login/register message on archive pages (only shows when viewing a single post or page) */
    add_action('wp', function () {
        if (!is_single()) {
            remove_filter('the_excerpt', 'pmpro_membership_excerpt_filter', 15);
        }
    });

    /* Change email address for all admin related emails in PMPro */
    add_filter('pmpro_email_recipient', function ($user_email, $email) {		
        if (strpos($email->template, '_admin') !== false) {
            $user_email = 'info@inag.it';
        }
        
        return $user_email;
    }, 10, 2);

    /* Add Stripe fee to membership payment */
    add_filter('pmpro_checkout_level', function ($level) {
        if (!empty($_REQUEST['gateway'])) {
            if ($_REQUEST['gateway'] == 'stripe') {
                $level->initial_payment = $level->initial_payment + STRIPE_FEE; //Updates initial payment value
                $level->billing_amount = $level->billing_amount + STRIPE_FEE; //Updates recurring payment value 
            }
        }
     
        return $level;
    });

    /* Add extra PMPro confirmation fields */
    add_action('pmpro_invoice_bullets_bottom', function ($pmpro_invoice) {
        printf('<li><strong>%s:</strong> %s</li>', __('Codice Fiscale', 'generatepresschild'), get_user_meta($pmpro_invoice->user->ID, 'fiscal_code', true));
    });

    if (in_array(PMPRO_REGISTER_HELPER_PLUGIN, $active_plugins)) {
        
        /* Add meta fields to user confirmation email */
        add_filter('pmpro_email_filter', function ($email) {
            global $wpdb;

            // Only update user confirmation emails
            if (!empty($email) && (strpos($email->template, 'checkout') !== false) && (strpos($email->template, 'admin') === false)) {
                //get the user_id from the email
                $user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_email = '" . $email->data['user_email'] . "' LIMIT 1");

                if (!empty($user_id)) {
                    //get meta fields
                    $fields = pmprorh_getProfileFields($user_id);

                    //add to bottom of email
                    if (!empty($fields)) {
                        foreach ($fields as $field) {
                            if (!is_a($field, 'PMProRH_Field')) {
                                continue;
                            }

                            $value = get_user_meta($user_id, $field->meta_key, true);

                            if (($field->type == 'file') && is_array($value) && !empty($value['fullurl'])) {
                                $field_value = $value['fullurl'];
                            } elseif (is_array($value)) {
                                $field_value = implode(', ', $value);
                            } else {
                                $field_value = $value;
                            }

                            $email->body .= "<p>$field->label: $field_value</p>";
                        }
                    }
                }

                $email->body .= "<hr><p>Operazione fuori campo IVA ex artt. 1 e 4 DPR 633/72. Le ricevute relative all’incasso delle quote associative non sono assoggettate all’imposta di bollo.</p>";
                $email->body .= "<img src='" . site_url('/wp-content/uploads/digital-signature.jpeg') . "' alt='Digital signature' width='200' height='128'>";
            }

            $email->body .= "<p>" . __('Cordialmente', 'generatepresschild') . ", !!sitename!!.</p>";

            return $email;
        });

        /* Adding the Fiscal Code column to the Users table */
        add_action('manage_users_columns', function ($column_headers) {
            unset($column_headers['posts']);

            $offset = array_search('role', array_keys($column_headers));

            return array_merge(
                array_slice($column_headers, 0, $offset),
                array('fiscal_code' => __('Codice Fiscale', 'generatepresschild')),
                array_slice($column_headers, $offset)
            );
        });

        /* Populating the Fiscal Code column in the Users table */
        add_action('manage_users_custom_column', function ($value, $column_name, $user_id) {
            return $column_name == 'fiscal_code' ? get_user_meta($user_id, 'fiscal_code', true) : $value;
        }, 10, 3);
        
    }
}

/* Adding new block styles to Gutenberg */
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
});
