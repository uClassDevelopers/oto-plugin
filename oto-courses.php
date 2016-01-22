<?php
/*
Copyright 2015 uClass Developers Daniel Holm & Adam Jacobs Feldstein

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

//Set connection to wpdb and needed variables
global $wpdb;
$table_name = $wpdb->prefix . 'oto_courses_slider';

//Insert updated content in to database
if(!empty($_POST)) {
    $fieldCount = count($_POST['id']);
    $fieldCountTI = count($_POST['ti_id']);
    $fieldCountRTI = count($_POST['ti_delete']);

    //Loop through all top images
    for ($i = 0; $i < $fieldCountTI; $i++) {
        //If post id is empty create new row in table
        if("" == trim($_POST['ti_id'][$i])) {
            $rows_affected = $wpdb->query(
                $wpdb->prepare(
                    'INSERT INTO '. $table_name .' SET title = \''.$_POST['ti_title'][$i].'\', on_link = \''.$_POST['ti_url'][$i].'\', image_url = \''.$_POST['ti_image_url'][$i].'\', row = 3, course = \''.$_POST['course'][$i].'\', position = \''.$_POST['ti_position'][$i].'\', content = \''.$_POST['content'][$i].'\';'
                )
            ); // $wpdb->query, Else if the content is static set is_dyn to 0 and update fileds
        } 
        if($_POST['ti_row'][$i] == "3" and trim($_POST['ti_id'][$i] > 0)){
            $rows_affected = $wpdb->query(
                $wpdb->prepare(
                    'UPDATE '. $table_name .' SET title = \''.$_POST['ti_title'][$i].'\', on_link = \''.$_POST['ti_url'][$i].'\', image_url = \''.$_POST['ti_image_url'][$i].'\', course = \''.$_POST['course'][$i].'\', is_dyn = 0, position = \''.$_POST['ti_position'][$i].'\', content = \''.$_POST['content'][$i].'\', dyn_link = 0 WHERE id = \''.$_POST['ti_id'][$i].'\';'
                )
            ); // $wpdb->query              
         } //If the post field delete is set to 1, delete row with id
        if($_POST['ti_row'][$i] == "3" and trim($_POST['ti_id'][$i] > 0) and trim($_POST['ti_delete'][$i] > 0)){
            foreach ($_POST['ti_delete'] as $deleteId) {
                $deleteId = (int)$deleteId;
                $rows_affected = $wpdb->query(
                    $wpdb->prepare(
                        'DELETE FROM '. $table_name .' WHERE id = '. $deleteId .';'
                    )
                ); // $wpdb->query      
            }
         }
    }
    echo "<div class='notice success animated shake'> ".$fieldCount." st slides uppdaterades <span class='tgl-alert'>X</span></div>";
}
?>
<!-- GET daneden animate.css --> 
<link href="<? bloginfo('stylesheet_directory');?>/animate.min.css" rel="stylesheet"/>
<!-- uClass framework main css for eter-options.php -->
<link href="<? bloginfo('stylesheet_directory');?>/uclass-framework.css" rel="stylesheet"/>
<!-- Import local version of jQuery -->
<script type="text/javascript" src="<? bloginfo('stylesheet_directory');?>/jquery.min.js"></script>
<!-- ETER-options.php jQuery scripts --> 
<script type="text/javascript">
    function get_courses(){
        $.ajax({ 
            type: 'GET', 
            url: 'http://eter.rudbeck.info/eter-app-api/?apikey=vV85LEH2cUJjshrFx5&list-courses-name=1&parent=43', 
            dataType: 'json',
            success: function (data) { 
                 $.each(data.list_courses, function(index, element) {
                  $('.course-selector').append($('<option>', {
                                text: element.name
                            }));
                     });
            }
        });
    }
    $(document).ready(function() {
        $( ".tgl-alert" ).click(function() {
          $( ".success" ).toggle( "slow", function() {
            // Animation complete.
          });
        });
        get_courses();    
    });
    //Set new field count to zero
    var count = 0;
    //When the object with id #add_field is pressed, append top_images_container with all needed fields
    $(function(){
        $('#add_field').click(function(){
            count += 1;
            $('#top_images_container').append('<div id="new_'+ count +'" style="margin-bottom: 20px;"><div><p class="del-wth-chbx" style=""><input type="checkbox" name="ti_delete[]" value=""> RADERA</p><img height="120" style="vertical-align: middle; float: right; vertical-align: top; margin-bottom: 20px; margin-right: 20px;" src="" alt="Kunde inte ladda bilden"> </div> <div><h3 id="course-selector">Välj en kurs:<select name="course[]" class="course-selector"><option selected></option></select></h3><h3>Rubrik: <input type="text" name="ti_title[]" value="" /> | Position: <input type="text" name="ti_position[]" value=""></h3><p>Länk till bild: <input style="vertical-align: middle;" type="text" name="ti_image_url[]" value=""> | Länk på bild: <input type="text" name="ti_url[]" value=""></p><h3> Beskriving:</h3><textarea name="content[]" rows="5" cols="65"></textarea><input type="hidden" name="ti_id[]" value="" /><input type="hidden" name="id[]" value="" /><input type="hidden" name="ti_row[]" value="3"><hr/></div></div>                                   '
            );
            get_courses(); 
            $("html, body").animate({ scrollTop: $('#new_'+count).offset().top-150 }, 2000);
        });
    });
</script>
<!-- Wrap everything in a form tag, for makeing it easier to post to the processing  script on top of file -->
<form action=""  method="post" id="ETERStartForm">
    <div id="form-wrapper">
        <a class="animated zoomInDown" id="made_by_uclass" href="http://uclass.se/">
            Made by uClassDevs<img src="<? bloginfo('stylesheet_directory');?>/uclass_logo.png" alt="uClass Logo"/>
        </a>
        <h1>OTO Application Options</h1>
        <h1>| Kurser</h1>
        <div style="margin-left: 2%;">
        <h2>Välj innehåll Kurskarusell</h2>
        <p>Notera: Bilderna visas bara på sidan efter att du har tryckt på sparaknappen. Kurser är postkategorier underordnade kategorin "Kurser". Skapa ny "kurs"
        <a href="edit-tags.php?taxonomy=category">här</a>.</p>

        <div style="text-align: center;">
            <div id="top_images">
                <div id="top_images_container">
                    <?php foreach( $wpdb->get_results("SELECT * FROM `".$table_name."` WHERE row = 3;") as $key => $rows):
                    // each column will be accessible by these
                    $row= $rows->row;
                    $position = $rows->position;
                    $title = $rows->title;
                    $url = $rows->on_link;
                    $image_url = $rows->image_url;
                    $content = $rows->content;
                    $is_dyn = $rows->is_dyn;
                    $dyn_link = $rows->dyn_link;
                    $id = $rows->id;
                    $course = $rows->course;
                    ?>
                    <div class="border-btm">
                        <div>
                            <p class="del-wth-chbx" style="">
                                 <input type="checkbox" name="ti_delete[]" value="<?php echo $id; ?>"> RADERA
                            </p>
                            <img height="120" style="vertical-align: middle; float: right; vertical-align: top; margin-bottom: 20px; margin-right: 20px;" src="<?php echo $image_url; ?>" alt="Kunde inte ladda bilden">
                        </div>
                        <div><h3 id="course-selector">Välj en kurs:</h3>
                            <select name="course[]">
                                <option selected value="<?php echo $course ?>"><?php echo $course ?> (vald)</option>
                                <optgroup class="course-selector" label=""></optgroup>

                            </select>
                            
                            <h3>Rubrik: <input type="text" name="ti_title[]" value="<?php echo $title; ?>" /> | Position: <input type="text" name="ti_position[]" value="<?php echo $position; ?>"></h3>
                             <p>Länk till bild: <input style="vertical-align: middle;" type="text" name="ti_image_url[]" value="<?php echo $image_url; ?>"> | Länk på bild: <input type="text" name="ti_url[]" value="<?php echo $url; ?>">
                            </p>
                            <h3> Beskriving av kursen:</h3><textarea name="content[]" rows="5" cols="65"><?php echo $content; ?></textarea>
                            <input type="hidden" name="ti_id[]" value="<?php echo $id; ?>" />   
                            <input type="hidden" name="id[]" value="<?php echo $id; ?>" />
                            <input type="hidden" name="ti_row[]" value="3">
                        </div>
                    </div>
                    <? endforeach; ?>
                </div>
                <p><a class="button" href="#" id="add_field">Lägg till en Slide</a></p>
            </div>
        </div>
 
            <p>Notera: Sparaknappen sparar alla ändringar permanent </p>
            <input type="submit" value="Spara ändringar" class="button button-primary">
        </div>
    </div>
</form>