<?php
/*
Plugin Name: OTO plugin
Description: OTO plugin
Version:     0.1
Author:      Daniel Holm, Adam Jacobs Feldstein
Author URI:  http://uclass.se
License:     Apache License Version 2.0
License URI: http://www.apache.org/licenses/
*/
function jquery_init(){
  wp_enqueue_script('jquery');
}
add_filter('wp_enqueue_scripts', 'jquery_init');

require_once('/inc/js-wp-editor.php');

function oto_scripts_n_styles() {
  // Trying to register wp-editor script
  wp_register_script( 'wpeditor-script', plugins_url( '/js/js-wp-editor.min.js', __FILE__ ) );

  //load the wp editor script
  js_wp_editor();

  wp_enqueue_script( 'wpeditor-script' );
}

add_action( 'wp_enqueue_scripts', 'oto_scripts_n_styles' );

//For development
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
@ini_set('display_errors', 1);

//Create the oto table and columns, also set correct formats
function oto_setup_db() {
  global $wpdb;
  global $oto_db_version;
  $oto_db_version = '1.0';

  $start_table_name = $wpdb->prefix . 'oto_start';
  $course_slider_table_name = $wpdb->prefix . 'oto_courses_slider';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $start_table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    row int(11) NOT NULL,
    position int NOT NULL,
    title tinytext NULL,
    on_link text NULL,
    image_url text NULL,
    content text NULL,
    is_dyn int NOT NULL,
    dyn_link text NULL,
    on_link_to_post text NULL,
    on_link_outbound text NULL,
    UNIQUE KEY id (id)
  ) $charset_collate;
  CREATE TABLE $course_slider_table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    row int NOT NULL,
    postion int NOT NULL,
    title tinytext NULL,
    on_link text NULL,
    course text NULL,
    image_url text NULL,
    content text NULL,
    UNIQUE KEY id (id)
  ) $charset_collate;
  CREATE TABLE $lang_elements (
    id int(11) NOT NULL AUTO_INCREMENT,
    page text NOT NULL,
    swe text NULL,
    translation text NULL,
    lang text NULL,
    UNIQUE KEY id (id)
  ) $charset_collate;
  CREATE TABLE $oto_directory_table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    lang tinytext NOT NULL,
    title tinytext NOT NULL,
    school_logo text NOT NULL,
    school_name tinytext NOT NULL,
    school_id text NOT NULL,
    school_domain text NOT NULL,
    activated int(1) NOT NULL,
    UNIQUE KEY id (id)
  ) $charset_collate;
  ";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

  add_option( 'oto_db_version', $oto_db_version );

  $table_name = $wpdb->prefix . 'oto_start';

  //Insert test data
  for($i=0; $i< 6; $i++) {
    if($i < 3) { $row=1;} else{ $row = 2;}
    $placeholder_row = $row;
    $placeholder_position = $i+1;
    $placeholder_title = 'OTO-iOS!';
    $placeholder_on_link = '#tab/guides';
    $placeholder_image_url = 'http://eter.rudbeck.info/wp-content/uploads/2014/05/ETER-logga_100_overstrykning.png';
    $placeholder_content = 'it is working';
    $placeholder_is_dyn ='0';
    $placeholder_dyn_link = '#';
    $placeholder_on_link_to_post = '';
    $placeholder_on_link_outbound = '';

    $wpdb->insert( $table_name, array( 'row' => $placeholder_row, 'position' =>  $placeholder_position,
    'title' =>  $placeholder_title, 'on_link' =>  $placeholder_on_link,
    'image_url' =>  $placeholder_image_url, 'content' =>  $placeholder_content,
    'is_dyn' =>  $placeholder_is_dyn , 'dyn_link' =>  $placeholder_dyn_link,
    'on_link_to_post' =>  $on_link_to_post, 'on_link_outbound' =>  $placeholder_on_link_outbound),
    array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ));

  }

  //$wpdb->show_errors();
}

//Do the db setup after theme selection 'eter_courses_slider_install', 'eter_courses_slider_install_data'
register_activation_hook( __FILE__, 'oto_setup_db');

wp_register_style('uclass_framework', plugins_url('oto-plugin/uclass-framework.css'));
wp_enqueue_style( 'uclass_framework');

//Setup a widget on dashboard describing css display none classes
function eter_add_dashboard_widgets() {

  wp_add_dashboard_widget(
  'eter_dashboard_widget',         // Widget slug.
  'OTO',         // Title.
  'eter_dashboard_widget_function' // Display function.
);
}
add_action( 'wp_dashboard_setup', 'eter_add_dashboard_widgets' );
function eter_dashboard_widget_function() {

  // Display whatever you want to tell.
  echo"<p>Gå til OTO-appens inställningar</p>";
  echo "<p>Det går att dölja innehåll från appen eller webbsidan. Detta görs genom att byta innehållsredigerarens läge från visuell till text, och sedan innefatta innehållen för repsektive plattform inom en lämplig utav dessa: </p><code>&lt;div class='app'&gt; Innehåll &lt/div&gt;  &lt;div class='webb'&gt; Innehåll &lt/div&gt; </code>. <p>'app' visas bara i appen och 'webb' visas bara på webben.</p>";
}

// Add a column to the edit post list
add_filter( 'manage_edit-post_columns', 'add_new_columns');

/**
* Add new columns to the post table
*
* @param Array $columns - Current columns on the list post
*/

function add_new_columns( $columns ) {
  $column_meta = array( 'meta' => 'Om guide i kurs, position' );
  $columns = array_slice( $columns, 0, 2, true ) + $column_meta + array_slice( $columns, 2, NULL, true );
  return $columns;
}

// Add action to the manage post column to display the data
add_action( 'manage_posts_custom_column' , 'custom_columns' );

/**
* Display data in new columns
*
* @param  $column Current column
*
* @return Data for the column
*/
function custom_columns( $column ) {
  global $post;

  switch ( $column ) {
    case 'meta':
    $metaData = get_post_meta( $post->ID, 'eter_guide_position', true );

    echo $metaData;
    break;
  }
}



add_action( 'add_meta_boxes', 'cd_meta_box_add' );
function cd_meta_box_add()
{
  add_meta_box( 'eter-meta', 'Om guide för kurs, ange position', 'cd_meta_box_cb', 'post', 'normal', 'high' );
}

function cd_meta_box_cb( $post )
{
  $values = get_post_custom( $post->ID );
  $text = isset( $values['eter_guide_position'] ) ? esc_attr( $values['eter_guide_position'][0] ) : '';
  wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
  ?>
  <p>
    <label for="eter_guide_position">Position</label>
    <input type="text" name="eter_guide_position" id="eter_guide-position" value="<?php echo $text; ?>" />
  </p>
  <?php
}


add_action( 'save_post', 'cd_meta_box_save' );
function cd_meta_box_save( $post_id )
{
  // Bail if we're doing an auto save
  if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

  // if our nonce isn't there, or we can't verify it, bail
  if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;

  // if our current user can't edit this post, bail
  if( !current_user_can( 'edit_post' ) ) return;

  // now we can actually save the data
  $allowed = array(
    'a' => array( // on allow a tags
      'href' => array() // and those anchords can only have href attribute
    )
  );

  // Probably a good idea to make sure your data is set
  if( isset( $_POST['eter_guide_position'] ) )
  update_post_meta( $post_id, 'eter_guide_position', wp_kses( $_POST['eter_guide_position'], $allowed ) );
}

// Add to our admin_init function
add_action('quick_edit_custom_box',  'eter_add_quick_edit', 10, 2);

function eter_add_quick_edit($column_name, $post_type) {
  if ($column_name != 'meta') return;
  ?>
  <fieldset class="inline-edit-col-left">
    <div class="inline-edit-col">
      <span class="title">Widget Set</span>
      <input type="hidden" name="eter_widget_set_noncename" id="eter_widget_set_noncename" value="" />
      <option class='widget-option' value='0'>None</option>
      <?php // Get all widget sets
      $metaData = get_post_meta( $post->ID, 'eter_guide_position', true );
      ?>
      <label for="eter_guide_position_input">Position</label>
      <input type="text" name="eter_guide_position_input" id="eter_guide_position_input" value="<?php echo $metaData; ?>">

    </div>
  </fieldset>
  <?php
}

//Create and configure the custom post type "Guide"
function Guide_create_post_type() {
  $labels = array(
    'name' => 'Guide',
    'singular_name' => 'Guide',
    'add_new' => 'Add Guide',
    'all_items' => 'All Guides',
    'add_new_item' => 'Add Guide',
    'edit_item' => 'Edit Guide',
    'new_item' => 'New Guide',
    'view_item' => 'View Guide',
    'search_items' => 'Search Guides',
    'not_found' => 'No Guides found',
    'not_found_in_trash' => 'No Guides found in trash',
    'parent_item_colon' => 'Parent Guide'
    //'menu_name' => default to 'name'
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'has_archive' => true,
    'publicly_queryable' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'supports' => array(
      'title',
      'thumbnail',
      //'author',
      //'trackbacks',
      'custom-fields',
      //'comments',
      'revisions',
      //'page-attributes', // (menu order, hierarchical must be true to show Parent option)
      //'post-formats',
    ),
    'taxonomies' => array( 'category', 'post_tag' ), // add default post categories and tags
    'menu_position' => 5,
    'exclude_from_search' => false,
    'register_meta_box_cb' => 'Guide_add_post_type_metabox'
  );
  register_post_type( 'Guide', $args );
  //flush_rewrite_rules();

  register_taxonomy( 'Guide_category', // register custom taxonomy - category
  'Guide',
  array(
    'hierarchical' => true,
    'labels' => array(
      'name' => 'Guide category',
      'singular_name' => 'Guide category',
    )
  )
);
register_taxonomy( 'Guide_tag', // register custom taxonomy - tag
'Guide',
array(
  'hierarchical' => false,
  'labels' => array(
    'name' => 'Guide tag',
    'singular_name' => 'Guide tag',
  )
)
);
}
add_action( 'init', 'Guide_create_post_type' );


function Guide_add_post_type_metabox() { // add the meta box
  add_meta_box( 'Guide_metabox', 'Guide Content', 'Guide_metabox', 'Guide', 'normal' );
}


function Guide_metabox() {
  global $post;
  // Noncename needed to verify where the data originated
  echo '<input type="hidden" name="Guide_post_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

  // Get the data if its already been entered
  $Guide_post_name = get_post_meta($post->ID, '_Guide_post_name', true);
  $Guide_post_desc = get_post_meta($post->ID, '_Guide_post_desc', true);

  $meta = get_post_meta($post->ID);
  $string = '_Guide_post_steps_';
  $countGuidesExisting = 0;
  ksort($meta);
  foreach ($meta as $key => $data) {
    if (substr_count($key, $string) !== 0) {
      $countGuidesExisting++;
    }
  }
  ?>
  <script type="text/javascript">
  jQuery.noConflict();
  jQuery(document).ready(function($){
    var max_fields      = 1000000; //maximum input boxes allowed
    var wrapper         = $("#guideEditArea"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID

    var i = <?php echo $countGuidesExisting ?>; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
      e.preventDefault();
      if(i < max_fields){ //max input box allowed
        i++; //text box increment
        $(wrapper).append('<tr><th><label>Steg '+i+'</label></th><td><textarea name="Guide_post_steps[]"></textarea></td><a href="#" class="remove_field">Remove</a></tr>'); //add input box

        jQuery('#aa').wp_editor(); // add wp editor to textbox
      }
    });

    $(wrapper).on("click",".remove_field", function(e){
      e.preventDefault(); $(this).parent('div').remove(); i--;
    })
  });
  </script>
  <table class="form-table" id="guideEditArea">
    <tr>
      <th>
        <label>Inledning</label>
      </th>
      <td>
        <?php
        $content = $Guide_post_desc;
        $editor_id = 'Guide_post_desc';
        wp_editor( $content, $editor_id );
        ?>
      </td>
    </tr>
    <tr>
      <th>
        <label><h2>STEG FÖR STEG</h2> <button class="add_field_button">Lägg till ett steg</button></label>
      </th>
    </tr>
    <?php
    $meta = get_post_meta($post->ID);

    $string = '_Guide_post_steps_';
    $count = 0;

    ksort($meta);
    //print_r($meta);
    foreach ($meta as $key => $data) {
      //print_r($data);
      //print_r($key);

      //Settings for wp editor
      $settings = array(
        'textarea_name' => 'Guide_post_steps[]',
      );

      //Check to se if any of the keys contains _Guide_post_steps_, if not 0 display the content
      if (substr_count($key, $string) !== 0) {
        $count++;
        echo "
        <tr>
        <th>
        <label>Steg ".$count."</label>
        </th>
        <td>
        ";
        $content =  $data[0];
        $editor_id = $key;
        wp_editor( $content, $editor_id, $settings);

        echo "
        </td>
        </tr>

        ";
      }
    }
    ?>
  </table>
  <?php
}

function Guide_post_save_meta( $post_id, $post ) { // save the data

  /*
  * We need to verify this came from our screen and with proper authorization,
  * because the save_post action can be triggered at other times.
  */
  if ( ! isset( $_POST['Guide_post_noncename'] ) ) { // Check if our nonce is set.
    return;
  }

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times
  if( !wp_verify_nonce( $_POST['Guide_post_noncename'], plugin_basename(__FILE__) ) ) {
    return $post->ID;
  }

  // is the user allowed to edit the post or page?
  if( ! current_user_can( 'edit_post', $post->ID )){
    return $post->ID;
  }
  // ok, we're authenticated: we need to find and save the data
  // we'll put it into an array to make it easier to loop though
  $Guide_post_meta['_Guide_post_name'] = $_POST['Guide_post_name'];
  $Guide_post_meta['_Guide_post_desc'] = $_POST['Guide_post_desc'];
  $Guide_post_meta['_Guide_post_step1'] = $_POST['Guide_post_step1'];

  //index value varibale must be declared outisde foreach and be -1 to start on 0 in loop
  $i = -1;
  foreach ($_POST['Guide_post_steps'] as $steps => $step) {
    //increment the index each time
    $i++;
    if( get_post_meta( $post->ID, '_Guide_post_steps_'.$i.'', FALSE ) ) { // if the custom field already has a value
      if(filter_var($step, FILTER_SANITIZE_STRING).length == 0) { // delete if blank
        delete_post_meta( $post->ID, '_Guide_post_steps_'.$i.'');
      } else {
        update_post_meta($post->ID, '_Guide_post_steps_'.$i.'', filter_var($step, FILTER_SANITIZE_STRING));
      }
    } else { // if the custom field doesn't have a value
      add_post_meta( $post->ID, '_Guide_post_steps_'.$i.'', filter_var($step, FILTER_SANITIZE_STRING) );
    }

  }

  // add values as custom fields
  foreach( $Guide_post_meta as $key => $value ) { // cycle through the $Guide_post_meta array
    // if( $post->post_type == 'revision' ) return; // don't store custom data twice
    $value = implode(',', (array)$value); // if $value is an array, make it a CSV (unlikely)
    if( get_post_meta( $post->ID, $key, FALSE ) ) { // if the custom field already has a value
      update_post_meta($post->ID, $key, $value);
    } else { // if the custom field doesn't have a value
      add_post_meta( $post->ID, $key, $value );
    }
    if( !$value ) { // delete if blank
      delete_post_meta( $post->ID, $key );
    }
  }
}
add_action( 'save_post', 'Guide_post_save_meta', 1, 2 ); // save the custom fields

add_action( 'pre_get_posts', 'add_guide_to_query' ); // Inject the guides in to main query
function add_guide_to_query( $query ) {
  if ( is_home() && $query->is_main_query() )
  //get the normal type and guides
  $query->set( 'post_type', array( 'post', 'guide' ) );
  return $query;
}

//Construct the sigle guide template for wp site
add_filter('the_content', 'guide_content_controller');
function guide_content_controller($content)
{
  if (is_singular('guide') && in_the_loop()) {
    $meta = get_post_meta(get_the_id());

    $string = '_Guide_post_steps_';
    $count = 0;

    ksort($meta);
    //print_r($meta);
    foreach ($meta as $key => $data) {
      //print_r($data);
      //print_r($key);

      //Check to see if the guide introduction exist
      if (substr_count($key, '_Guide_post_desc') !== 0 ) {
        echo "
        <div id='introduction'>
        ";
        echo $data[0];
        echo "
        </div>
        ";
      }
      //Check to se if any of the keys contains _Guide_post_steps_, if not 0 display the content
      if (substr_count($key, $string) !== 0 ) {
        $count++;
        echo "
        <div id='step".$count."' class='guide'>
        <h2>Steg ".$count."</h2>
        ";
        echo $data[0];
        echo "
        </div>

        ";
      }
    }
  }

  return $content;
}

//Include a sigle-post template for guides to simplify for administrators
/*add_filter('single_template', 'oto_guide_template');
function oto_guide_template($single) {
global $wp_query, $post;

if ($post->post_type == "guide"){
if(file_exists(PLUGIN_PATH . '/single-guide.php'))
return PLUGIN_PATH . '/single-guide.php';
}
return $single;
}*/

// Sidebar Menu configuration
add_action('admin_menu', 'addEterMenu');
function addEterMenu() {
  add_menu_page('OTO-APP', 'OTO-APP', 0, 'eter-ios-mobile-options', 'eterMenu');
  add_submenu_page('eter-ios-mobile-options', 'OTO Startpage', 'OTO Startpage', 'manage_options', 'eter-ios-mobile-options' );
  add_submenu_page('eter-ios-mobile-options', 'OTO Courses', 'OTO Courses', 0, 'eter-courses', 'eterCourses' );
  add_submenu_page('eter-ios-mobile-options', 'OTO Licences', 'OTO Licences', 0, 'eter-Licences', 'eterLicences' );
}
function eterMenu() {
  include 'oto-options.php';
}
function eterCourses() {
  include 'oto-courses.php';
}

function eterLicences() {

  echo '<div class="wrap">';
  echo '<h1>License for WP-OTO</h1>';
  echo '<h3>Copyright 2015 uClass Developers Daniel Holm & Adam Jacobs Feldstein</h3>';
  echo'
  <p>
  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at
  </p>
  ';
  echo'
  <a href="http://www.apache.org/licenses/LICENSE-2.0">http://www.apache.org/licenses/LICENSE-2.0</a>
  ';
  echo'
  <p>
  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
  </p>';
  echo'
  <h1>Licences for included software</h1>
  <h3>jQuery</h3>
  <p>Copyright 2010, John Resig</p>
  <p>Dual licensed under the MIT or GPL Version 2 licenses.</p>
  <a href="http://jquery.org/license">http://jquery.org/license</a>
  ';
  echo'
  <h3>daneden Animate.css</h3>
  <p>Animate.css is licensed under the MIT license. (<a href="http://opensource.org/licenses/MIT">http://opensource.org/licenses/MIT</a>)</p>
  <p>Browse source on github: <href="https://github.com/daneden/animate.css">daneden/animate.css
  </a></p>
  ';
  echo '</div>';

}
?>
