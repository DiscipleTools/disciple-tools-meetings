<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Class Disciple_Tools_Meetings_Base
 * Load the core post type hooks into the Disciple.Tools system
 */
class Disciple_Tools_Meetings_Base  {
    /**
     * Define post type variables
     * @var string
     */
    public $post_type = 'meetings';
    public $single_name = 'Meeting';
    public $plural_name = 'Meetings';
    public static function post_type(){
        return 'meetings';
    }

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        //setup post type
        add_action( 'after_setup_theme', [ $this, 'after_setup_theme' ], 100 );
        add_filter( 'dt_set_roles_and_permissions', [ $this, 'dt_set_roles_and_permissions' ], 20, 1 ); //after contacts

        //setup tiles and fields
        add_filter( 'dt_custom_fields_settings', [ $this, 'dt_custom_fields_settings' ], 10, 2 );
        add_filter( 'dt_details_additional_tiles', [ $this, 'dt_details_additional_tiles' ], 10, 2 );
        add_action( 'dt_details_additional_section', [ $this, 'dt_details_additional_section' ], 20, 2 );
        add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 99 );

        // hooks
        add_action( 'post_connection_removed', [ $this, 'post_connection_removed' ], 10, 4 );
        add_action( 'post_connection_added', [ $this, 'post_connection_added' ], 10, 4 );
        add_filter( 'dt_post_update_fields', [ $this, 'dt_post_update_fields' ], 10, 3 );
        add_filter( 'dt_post_create_fields', [ $this, 'dt_post_create_fields' ], 10, 2 );
        add_action( 'dt_post_created', [ $this, 'dt_post_created' ], 10, 3 );
        add_action( 'dt_comment_created', [ $this, 'dt_comment_created' ], 10, 4 );

        //list
        add_filter( 'dt_user_list_filters', [ $this, 'dt_user_list_filters' ], 10, 2 );
        add_filter( 'dt_filter_access_permissions', [ $this, 'dt_filter_access_permissions' ], 20, 2 );
        add_filter( 'dt_capabilities', [ $this, 'dt_capabilities' ], 100, 1 );
    }

    public function after_setup_theme(){
        if ( class_exists( 'Disciple_Tools_Post_Type_Template' ) ) {

            $this->single_name = __( 'Meeting', 'disciple-tools-meetings' );
            $this->plural_name = __( 'Meetings', 'disciple-tools-meetings' );

            new Disciple_Tools_Post_Type_Template( $this->post_type, $this->single_name, $this->plural_name );
        }
    }

    /**
     * Adding capability meta.
     */
    public function dt_capabilities( $capabilities ){
        $capabilities['access_meetings'] = [
            'source' => 'Meetings',
            'description' => 'The user can view meetings.'
        ];
        $capabilities['update_meetings'] = [
            'source' => 'Meetings',
            'description' => 'The user can edit existing existing.'
        ];
        $capabilities['create_meetings'] = [
            'source' => 'Meetings',
            'description' => 'The user can create meetings.'
        ];
        return $capabilities;
    }

    /**
     * Documentation
     * @link https://github.com/DiscipleTools/Documentation/blob/master/Theme-Core/roles-permissions.md#rolesd
     */
    public function dt_set_roles_and_permissions( $expected_roles ){

        // if the user can access contact they also can access this post type
        foreach ( $expected_roles as $role => $role_value ){

            if ( isset( $expected_roles[$role]['permissions']['access_groups'] ) && $expected_roles[$role]['permissions']['access_groups'] ){
                $expected_roles[$role]['permissions']['access_meetings'] = true;
                $expected_roles[$role]['permissions']['update_meetings'] = true;
                $expected_roles[$role]['permissions']['create_meetings'] = true;
            }
        }
        return $expected_roles;
    }

    /**
     * Documentation
     * @link https://github.com/DiscipleTools/Documentation/blob/master/Theme-Core/fields.md
     */
    public function dt_custom_fields_settings( $fields, $post_type ){
        if ( $post_type === $this->post_type ){
            $fields['date'] = [
                'name' => __( 'Date', 'disciple-tools-meetings' ),
                'type' => 'date',
                'tile' => 'disciple_tools_meetings',
                'in_create_form' => true
            ];
            $fields['meetings_topic'] = [
                'name'        => __( 'Meeting Topic', 'disciple-tools-meetings' ),
                'description' => _x( 'Topics discussed in the meeting', 'disciple-tools-meetings' ),
                'type'        => 'text',
                'default'     => '',
                'tile' => 'disciple_tools_meetings',
                'icon' => get_template_directory_uri() . '/dt-assets/images/edit.svg',
            ];
            $fields['meeting_notes'] = [
                'name' => __( 'Meeting Notes', 'disciple-tools-meetings' ),
                'type' => 'textarea',
                'tile' => 'disciple_tools_meetings',
            ];
            $fields['type'] = [
                'name' => __( 'Meeting Type', 'disciple-tools-meetings' ),
                'type' => 'key_select',
                'tile' => 'disciple_tools_meetings',
                'in_create_form' => true,
                'select_cannot_be_empty' => true,
                'default' => apply_filters('disciple_tools_meetings_types', [
                        'default' => [
                            'label' => __( 'In Person Meeting', 'disciple-tools-meetings' ),
                            'description' => __( 'Face to Face Meeting', 'disciple-tools-meetings' )
                        ],
                        'online' => [
                            'label' => __( 'Online Meeting', 'disciple-tools-meetings' ),
                            'description' => __( 'Online Audio or Video Call', 'disciple-tools-meetings' )
                        ]
                    ]
                ),
            ];
            $fields['contacts'] = [
                'name' => __( 'Attendees', 'disciple-tools-meetings' ),
                'type' => 'connection',
                'p2p_direction' => 'from',
                'post_type' => 'contacts',
                'tile' => 'disciple_tools_meetings',
                'p2p_key' => 'meetings_to_contacts',
            ];
            $fields['assigned_to'] = [
                'name'        => __( 'Assigned To', 'disciple-tools-meetings' ),
                'type'        => 'user_select',
                'default'     => '',
                'tile'        => 'status',
                'icon' => get_template_directory_uri() . '/dt-assets/images/assigned-to.svg?v=2',
                'show_in_table' => 25,
                'custom_display' => false,
                'in_create_form' => true
            ];
            $fields['leaders'] = [
                'name' => __( 'Leaders', 'disciple-tools-meetings' ),
                'type' => 'connection',
                'p2p_direction' => 'to',
                'post_type' => 'contacts',
                'tile' => 'status',
                'p2p_key' => 'meetings_to_leaders'
            ];
            $fields['groups'] = [
                'name' => __( 'Group', 'disciple-tools-meetings' ),
                'type' => 'connection',
                'p2p_direction' => 'from',
                'post_type' => 'groups',
                'tile' => 'details',
                'p2p_key' => 'meetings_to_groups'
            ];
            $fields['tags'] = [
                'name'        => __( 'Tags', 'disciple_tools' ),
                'description' => _x( 'A useful way to group related items and can help group contacts associated with noteworthy characteristics. e.g. business owner, sports lover. The contacts can also be filtered using these tags.', 'Optional Documentation', 'disciple_tools' ),
                'type'        => 'tags',
                'default'     => [],
                'tile'        => 'disciple_tools_meetings',
                'icon' => get_template_directory_uri() . '/dt-assets/images/tag.svg',
            ];

            // $fields['disciple_tools_meetings_date'] = [
            //     'name'        => __( 'Meeting Date', 'disciple-tools-meetings' ),
            //     'description' => _x( 'Date of the meeting', 'Optional Documentation', 'disciple-tools-meetings' ),
            //     'type'        => 'date',
            //     'default'     => '',
            //     'tile' => 'disciple_tools_meetings',
            //     'icon' => get_template_directory_uri() . '/dt-assets/images/edit.svg',
            // ];
            $fields['disciple_tools_meetings_attendees'] = [
                'name' => __( 'Meeting Attendee List', 'disciple_tools' ),
                'description' => _x( 'The members who attended this meeting.', 'Optional Documentation', 'disciple-tools-meetings' ),
                'type' => 'connection',
                'post_type' => 'contacts',
                'p2p_direction' => 'to',
                'p2p_key' => 'contacts_to_meeting',
                'icon' => get_template_directory_uri() . '/dt-assets/images/list.svg?v=2',
            ];
        }
        if ( $post_type === 'contacts' ){
            $fields['meetings'] = [
                'name' => __( 'Meetings', 'disciple-tools-meetings' ),
                'type' => 'connection',
                'p2p_direction' => 'to',
                'post_type' => 'meetings',
                'tile' => 'other',
                'p2p_key' => 'meetings_to_contacts'
            ];

            $fields['meetings_led'] = [
                'name' => __( 'Leader of meetings', 'disciple-tools-meetings' ),
                'type' => 'connection',
                'p2p_direction' => 'from',
                'post_type' => 'meetings',
                'tile' => 'other',
                'p2p_key' => 'meetings_to_leaders'
            ];
        }
        if ( $post_type === 'groups' ){
            $fields['meetings'] = [
                'name' => __( 'Meetings', 'disciple-tools-meetings' ),
                'type' => 'connection',
                'p2p_direction' => 'to',
                'post_type' => 'meetings',
                'tile' => 'disciple_tools_meetings',
                'p2p_key' => 'meetings_to_groups'
            ];
            $fields['date'] = [
                'name' => __( 'Meetings Date', 'disciple-tools-meetings' ),
                'type' => 'date',
                'tile' => 'disciple_tools_meetings',
                'in_create_form' => true
            ];
            $fields['meetings_topic'] = [
                'name'        => __( 'Meeting Topic', 'disciple-tools-meetings' ),
                'description' => _x( 'Topics discussed in the meeting', 'disciple-tools-meetings' ),
                'type'        => 'text',
                'default'     => '',
                'tile' => 'disciple_tools_meetings',
                'icon' => get_template_directory_uri() . '/dt-assets/images/edit.svg',
            ];
            $fields['meeting_notes'] = [
                'name' => __( 'Meeting Notes', 'disciple-tools-meetings' ),
                'type' => 'textarea',
                'tile' => 'disciple_tools_meetings',
            ];
            $fields['type'] = [
                'name' => __( 'Meetings Type', 'disciple-tools-meetings' ),
                'type' => 'key_select',
                'tile' => 'disciple_tools_meetings',
                'in_create_form' => true,
                'select_cannot_be_empty' => true,
                'default' => apply_filters('disciple_tools_meetings_types', [
                        'default' => [
                            'label' => __( 'Default', 'disciple-tools-meetings' ),
                            'description' => __( 'General purpose', 'disciple-tools-meetings' )
                        ]
                    ]
                ),
            ];
            $fields['contacts'] = [
                'name' => __( 'Attendees', 'disciple-tools-meetings' ),
                'type' => 'connection',
                'p2p_direction' => 'from',
                'post_type' => 'contacts',
                'tile' => 'disciple_tools_meetings',
                'p2p_key' => 'meetings_to_contacts',
            ];
        }

        return $fields;
    }

    /**
     * @link https://github.com/DiscipleTools/Documentation/blob/master/Theme-Core/field-and-tiles.md
     */
    public function dt_details_additional_tiles( $tiles, $post_type = '' ){
        if ( $post_type === $this->post_type ){
            $tiles['other'] = [ 'label' => __( 'Other', 'disciple_tools' ) ];
        }
        return $tiles;
    }

    /**
     * Documentation
     * @link https://github.com/DiscipleTools/Documentation/blob/master/Theme-Core/field-and-tiles.md#add-custom-content
     */
    public function dt_details_additional_section( $section, $post_type ){
    }

    /**
     * action when a post connection is added during create or update
     * @todo catch field changes and do additional processing
     *
     * The next three functions are added, removed, and updated of the same field concept
     */
    public function post_connection_added( $post_type, $post_id, $field_key, $value ){
    }

    //action when a post connection is removed during create or update
    public function post_connection_removed( $post_type, $post_id, $field_key, $value ){
    }

    //filter at the start of post update
    public function dt_post_update_fields( $fields, $post_type, $post_id ){
        return $fields;
    }


    //filter when a comment is created
    public function dt_comment_created( $post_type, $post_id, $comment_id, $type ){
    }

    // filter at the start of post creation
    public function dt_post_create_fields( $fields, $post_type ){
        if ( $post_type === $this->post_type ) {
            if ( !isset( $fields['date'] ) ){
                $fields['date'] = time();
            }
        }
        return $fields;
    }

    //action when a post has been created
    public function dt_post_created( $post_type, $post_id, $initial_fields ){
    }


    //build list page filters
    public static function dt_user_list_filters( $filters, $post_type ){
        /**
         * @todo process and build filter lists
         */
        if ( $post_type === self::post_type() ){
            $post_label_plural = DT_Posts::get_post_settings( $post_type )['label_plural'];

            $filters['tabs'][] = [
                'key' => 'default',
                'label' => __( 'Default Filters', 'disciple_tools' ),
                'order' => 7
            ];
            $filters['filters'][] = [
                'ID' => 'all_my_meetings',
                'tab' => 'default',
                'name' => sprintf( _x( 'All %s', 'All records', 'disciple_tools' ), $post_label_plural ),
                'labels' =>[
                    [
                        'id' => 'all',
                        'name' => sprintf( _x( 'All %s I can view', 'All records I can view', 'disciple_tools' ), $post_label_plural ),
                    ]
                ],
                'query' => [
                    'sort' => '-post_date',
                ],
            ];
            $filters['filters'][] = [
                'ID' => 'recent',
                'tab' => 'default',
                'name' => __( 'My Recently Viewed', 'disciple_tools' ),
                'query' => [
                    'dt_recent' => true
                ],
                'labels' => [
                    [ 'id' => 'recent', 'name' => __( 'Last 30 viewed', 'disciple_tools' ) ]
                ]
            ];
        }
        return $filters;
    }

    // access permission
    public static function dt_filter_access_permissions( $permissions, $post_type ){
        if ( $post_type === self::post_type() ){
            if ( DT_Posts::can_view_all( $post_type ) ){
                $permissions = [];
            }
        }
        return $permissions;
    }

    // scripts
    public function scripts(){
        if ( is_singular( $this->post_type ) ){
            // @todo add enqueue scripts
            dt_write_log( __METHOD__ );
        }
    }
}


