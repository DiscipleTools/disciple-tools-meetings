<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Disciple_Tools_Meetings_Tile
{
    private static $_instance = null;
    public static function instance(){
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct(){
        add_filter( 'dt_details_additional_tiles', [ $this, "dt_details_additional_tiles" ], 10, 2 );
        add_action( "dt_details_additional_section", [ $this, "dt_add_section" ], 10, 2 );
        add_action( "dt_details_additional_section", [ $this, "dt_add_meeting_submit" ], 21, 2 );
    }

    /**
     * This function registers a new tile to a specific post type
     *
     * @todo Set the post-type to the target post-type (i.e. contacts, groups, trainings, etc.)
     * @todo Change the tile key and tile label
     *
     * @param $tiles
     * @param string $post_type
     * @return mixed
     */
    public function dt_details_additional_tiles( $tiles, $post_type = "" ) {
        if ( $post_type === "groups" || $post_type === "meetings" ){
            $tiles["disciple_tools_meetings"] = [ "label" => __( "Meetings", 'disciple-tools-meetings' ) ];
        }
        return $tiles;
    }


    public function dt_add_section( $section, $post_type ) {
        /**
         * @todo set the post type and the section key that you created in the dt_details_additional_tiles() function
         */
        if ( $section === "disciple_tools_meetings" ){
            /**
             * These are two sets of key data:
             * $this_post is the details for this specific post
             * $post_type_fields is the list of the default fields for the post type
             *
             * You can pull any query data into this section and display it.
             */
            $this_post = DT_Posts::get_post( $post_type, get_the_ID() );
            $post_type_fields = DT_Posts::get_post_settings( "meetings" );

            ?>

            <!--
            @todo you can add HTML content to this section.
            -->



            <div class="reveal" id="create-meeting-modal" data-reveal data-reset-on-close>
            <h3 class="section-header"><?php esc_html_e( "Create A Meetings", 'disciple-tools-meetings' ) ?></h3>

                <div class="section-subheader"><?php esc_html_e( "Who Attended the Meeting?", 'disciple-tools-meetings' ) ?></div>

                <div class="cell small-12 medium-4">
                    <div class="members-section" style="margin-bottom:10px">
                    <div class="attendee-checklist">
                    </div>
                </div>
                </div>

                <div class="section-subheader"><?php esc_html_e( "Meeting Date", 'disciple-tools-meetings' ) ?></div>
                <div class="disciple_tools_meeting_date input-group">
                    <input id="disciple_tools_meeting_date" class="input-group-field dt_date_picker meeting_date" type="text" autocomplete="off" value="">
                    <div class="input-group-button">
                        <button id="disciple_tools_meeting_date-clear-button" class="button alert clear-date-button" data-inputid="disciple_tools_meeting_date" title="Delete Date" type="button">x</button>
                    </div>
                </div>

                <div class="section-subheader"><?php esc_html_e( "Meeting Topic", 'disciple-tools-meetings' ) ?></div>
                <input id="disciple_tools_meeting_topic" type="text" class="" value="">

                <div class="section-subheader"><?php esc_html_e( "Meeting Notes", 'disciple-tools-meetings' ) ?></div>
                <textarea id="disciple_tools_meeting_notes" class="textarea"></textarea>

                <select class="" id="disciple_tools_meeting_type">
                    <?php foreach ( $post_type_fields['fields']['type']["default"] as $option_key => $option_value ):?>
                        <option value="<?php echo esc_html( $option_key )?>">
                            <?php echo esc_html( $option_value["label"] ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button id="disciple_tools_add_meetings-button" class="button"><?php esc_html_e( 'Add A Meeting', 'disciple-tools-meetings' ) ?></button>
            </div>

        <?php }
    }

    public function dt_add_meeting_submit( $section, $post_type ) {
        if ( $section === "disciple_tools_meetings" && $post_type !== "meetings" ){
        /**
         * These are two sets of key data:
         * $this_post is the details for this specific post
         * $post_type_fields is the list of the default fields for the post type
         *
         * You can pull any query data into this section and display it.
         */
            $this_post = DT_Posts::get_post( $post_type, get_the_ID() );
            $post_type_fields = DT_Posts::get_post_field_settings( $post_type );
            $post_type_label = DT_Posts::get_post_settings( get_post_type() ?: "contacts" )['label_singular'];
        /**
         * @todo set the post type and the section key that you created in the dt_details_additional_tiles() function
         */?>
         <div class="section-subheader" style="display:initial"><?php esc_html_e( 'Past Meetings', 'disciple-tools-meetings' )?></div>
        <ul class="meetings_list">
          <?
            foreach($this_post['meetings'] as $meeting){
                echo "<li><a href=". $meeting['permalink'] . ">" . $meeting['post_title'] . "</a></li>";
            }?>
        </ul>
          <button id="disciple_tools_create_meetings-button" class="button"><?php esc_html_e( 'Create A Meeting', 'disciple-tools-meetings' ) ?></button>
            <!-- </div> -->
            <style>
                .groups-template-default #disciple_tools_meetings-tile .section-body .date, .groups-template-default #disciple_tools_meetings-tile .section-body #meetings_connection, .groups-template-default #disciple_tools_meetings-tile .section-body #meetings_topic, .groups-template-default #disciple_tools_meetings-tile .section-body #meeting_notes, .groups-template-default #disciple_tools_meetings-tile .section-body .select-field, .groups-template-default #disciple_tools_meetings-tile .section-body #contact_connection, .groups-template-default #disciple_tools_meetings-tile .section-body #contacts_connection, #disciple_tools_meetings-tile .section-body .section-subheader {
                    display: none;
                }

                .meetings_list {
                    flex-wrap: wrap;
                    display: flex;
                    margin-bottom: 1rem;
                    margin-left: 0;
                    margin-right: 0;
                    padding: 0.75rem;
                    border: 1px solid #ccc;

                }
                .meetings_list li{
                    display: flex;
                    font-size: 0.875rem;
                    position: relative;
                    background: #ecf5fc;
                    border: 1px solid #c2e0ff;
                    padding: .2em .75em;
                    border-radius: 2px;
                    margin-right: 4px;
                    margin-bottom: 0.375rem;
                }
            </style>
            <script>
                //Initialize Date Picker so it doesn't update the current post.
                function date_picker_init(is_bulk = false, bulk_id = 0) {
                    // Determine field class name to be used.
                    let field_class = (!is_bulk) ? `.meeting_date.dt_date_picker` : `.dt_date_picker-${bulk_id}`;

                    // Assign on click listener.
                    $(field_class).datepicker({
                    constrainInput: false,
                    dateFormat: 'yy-mm-dd',
                    onClose: function (date) {
                        date = window.SHAREDFUNCTIONS.convertArabicToEnglishNumbers(date);
                        if (!$(this).val()) {
                        date = " ";//null;
                        }
                        let id = $(this).attr('id')
                        // new_post[id] = date
                        if (this.value) {
                        this.value = window.SHAREDFUNCTIONS.formatDate(moment.utc(date).unix());
                        }

                        // If bulk related, capture epoch
                        if (is_bulk) {
                        $(this).data('selected-date-epoch', moment.utc(date).unix());
                        }
                    },
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "1900:2050",
                    });
                }

                date_picker_init();


                /* Create attendees CheckList */
                let post = window.detailsSettings.post_fields;
                let attendeeChecklist = document.querySelector('.attendee-checklist');
                let populateattendeeCheckList = ()=>{
                    //empty attendeesChecklist
                    while(attendeeChecklist.firstChild)
                        attendeeChecklist.removeChild(attendeeChecklist.firstChild);

                        post.members.forEach(attendee=>{
                            let attendeeChecklistHTML = `<div class="member-row" style="" data-id="${window.lodash.escape( attendee.ID )}">
                                <input style="margin: 0.25em .5em 0 0" type="checkbox" name="attendeeChecklist-ID" class="attendeeCheckbox" value="${window.lodash.escape( attendee.ID )}">
                                <div style="flex-grow: 1" class="attendee-status">
                                    <span>${window.lodash.escape(attendee.post_title)}</span>
                                </div>`
                                attendeeChecklist.insertAdjacentHTML('beforeend', attendeeChecklistHTML)
                        })
                }

                populateattendeeCheckList();

                //Open Meeting Creation Modal
                let createMeetingButton = document.querySelector("#disciple_tools_create_meetings-button");

                createMeetingButton.addEventListener('click', event => {
                    $('#create-meeting-modal').foundation('open');
                })


                //Get Fields to create Meeting Post
                meetingFields = {
                    "date": "",
                    "assigned_to": <?php echo esc_html( get_current_user_id() ); ?>,
                    "contacts": {
                        "values": []
                    },
                    "groups": {
                        "values": [
                            {
                                "value": <?php echo get_the_ID(); ?>
                            }
                        ]
                    },
                    "type": "",
                    "name": "",
                    "meetings_topic": "",
                }

                var addMeetingButton = document.querySelector("#disciple_tools_add_meetings-button");

                addMeetingButton.addEventListener('click', event => {
                    meetingFields.name = document.querySelector("#disciple_tools_meeting_topic").value;
                    meetingFields.date = document.querySelector("#disciple_tools_meeting_date").value;
                    meetingFields.type = document.querySelector("#disciple_tools_meeting_type").value;
                    meetingFields.meeting_notes = document.querySelector("#disciple_tools_meeting_notes").value;
                    meetingFields.meetings_topic = document.querySelector("#disciple_tools_meeting_topic").value;

                    //gets all attendees checkboxes, filters for those that are checked, addes the ID value to an array to be used in the meetingFields Object.
                    let attendeesArray = [...document.querySelectorAll('.member-row input')].filter(row => { return row.checked }).map(attendees => { return {"value" : attendees.value}});

                    meetingFields.contacts.values = attendeesArray;

                    window.API.create_post('meetings', meetingFields).then((newMeeting)=>{
                        //close modal
                        $('#create-meeting-modal').foundation('close');
                        //clear inputs
                        document.querySelector("#disciple_tools_meeting_topic").value = "";
                        document.querySelector("#disciple_tools_meeting_date").value = "";
                       document.querySelector("#disciple_tools_meeting_type").value = "";
                        document.querySelector("#disciple_tools_meeting_notes").value = "";
                        document.querySelectorAll('.attendeeCheckbox').forEach((attendee) => { attendee.checked = false; })
                        let newMeeting_listItem = `<li><a href="${newMeeting.permalink}">${newMeeting.name}</a></li>`
                        document.querySelector('.meetings_list').insertAdjacentHTML('beforeend', newMeeting_listItem);
                    });
                });
            </script>
            <?php }
    }
}
Disciple_Tools_Meetings_Tile::instance();
