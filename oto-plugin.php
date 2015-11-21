<?php
/*
Plugin Name: OTO Main plugin
Description: OTO Main plugin
Version:     0.1
Author:      Daniel Holm, Adam Jacobs Feldstein
Author URI:  http://URI_Of_The_Plugin_Author
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

//Setup eter_start table in wpdb
global $eter_start_db_version;
$eter_start_db_version = '1.0'; //Set version of table

//Create the table and colums, also set correct formats on the columns
function eter_start_install() {
  global $wpdb;
  global $eter_start_db_version;

  $table_name = $wpdb->prefix . 'eter_start';
  
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
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
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

  add_option( 'eter_start_db_version', $eter_start_db_version );
}

//Populate the dtabase with startdata
function eter_start_install_data() {
  global $wpdb;
  
    $placeholder_row ='1';
  $placeholder_position = '1';
  $placeholder_title = 'OTO-iOS!';
    $placeholder_url ='#tab/guides';
    $placeholder_image_url = 'http://eter.rudbeck.info/wp-content/uploads/2014/05/ETER-logga_100_overstrykning.png';
    $placeholder_content = 'it is working';
    $placeholder_is_dyn ='0';
    $placeholder_dyn_link = '';
    
  
  $table_name = $wpdb->prefix . 'eter_start';
  
  $wpdb->insert( 
    $table_name, 
    array( 
      'row' => $placeholder_row, 
      'position' => $placeholder_position, 
      'title' => $placeholder_title, 
      'url' => $placeholder_url, 
      'image_url' => $placeholder_image_url, 
      'content' => $placeholder_content, 
      'is_dyn' => $placeholder_is_dyn, 
      'dyn_link' => $placeholder_dyn_link, 
    ) 
  );
}
//Setup eter_start table in wpdb
global $eter_courses_slider_db_version;
$eter_courses_slider_db_version = '1.0'; //Set version of table

//Create the table and colums, also set correct formats on the columns
function eter_courses_slider_install() {
  global $wpdb;
  global $eter_courses_slider_db_version;

  $table_name = $wpdb->prefix . 'eter_courses_slider';
  
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    row int NOT NULL,
        postion int NOT NULL,
    title tinytext NULL,
    on_link text NULL,
    course text NULL,
        image_url text NULL,
        content text NULL,
    UNIQUE KEY id (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

  add_option( 'eter_courses_slider_db_version', $eter_courses_slider_db_version );
}

//Populate the dtabase with startdata
function eter_courses_slider_install_data() {
  global $wpdb;
  
    $placeholder_row ='1';
  $placeholder_position = '1';
  $placeholder_title = 'OTO-iOS!';
    $placeholder_url ='#tab/guides';
    $placeholder_image_url = 'http://eter.rudbeck.info/wp-content/uploads/2014/05/ETER-logga_100_overstrykning.png';
    $placeholder_content = 'it is working';
    $placeholder_is_dyn ='0';
    $placeholder_dyn_link = '';
    
  
  $table_name = $wpdb->prefix . 'eter_courses_slider';
  
  $wpdb->insert( 
    $table_name, 
    array( 
      'row' => $placeholder_row, 
      'position' => $placeholder_position, 
      'title' => $placeholder_title, 
      'url' => $placeholder_url, 
      'image_url' => $placeholder_image_url, 
      'content' => $placeholder_content, 
      'is_dyn' => $placeholder_is_dyn, 
      'dyn_link' => $placeholder_dyn_link, 
    ) 
  );
}

//Do the db setup after theme selection 'eter_courses_slider_install', 'eter_courses_slider_install_data'
register_activation_hook( __FILE__, 'eter_start_install', 'eter_courses_install_data','eter_courses_slider_install', 'eter_courses_slider_install_data' );

wp_register_style('uclass_framework', plugins_url('central-oto-plugin/uclass-framework.css'));
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
    echo"<p><a href='Gå til OTO-appens inställningar'</a></p>";
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
        <h3>jQuey</h3>
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
//Remove comment when development/ testing
//$wpdb->show_errors(); 
?>