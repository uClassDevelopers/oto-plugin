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

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
@ini_set('display_errors', 0);

//Set connection to wpdb and needed variables
global $wpdb;
$table_name = $wpdb->prefix . 'oto_start';

//Insert updated content in to database
if(!empty($_POST)) {
    $fieldCount = count($_POST['id']);
    $fieldCountSR = count($_POST['sr_id']);
    $fieldCountFR = count($_POST['fr_id']);
    $fieldCountTI = count($_POST['ti_id']);
    $fieldCountRTI = count($_POST['ti_delete']);

    //Loop through all top images
    for ($i = 0; $i < $fieldCountTI; $i++) {
        //If post id is empty create new row in table
        if("" == trim($_POST['ti_id'][$i])) {
            $rows_affected = $wpdb->query(
                $wpdb->prepare(
                    'INSERT INTO '. $table_name .' SET title = \''.$_POST['ti_title'][$i].'\', on_link = \''.$_POST['ti_url'][$i].'\', image_url = \''.$_POST['ti_image_url'][$i].'\', row = 3, content = \''.$_POST['content'][$i].'\', position = \''.$_POST['ti_position'][$i].'\';'
                )
            ); // $wpdb->query, Else if the content is static set is_dyn to 0 and update fileds
        } 
        if($_POST['ti_row'][$i] == "3" and trim($_POST['ti_id'][$i] > 0)){
            $rows_affected = $wpdb->query(
                $wpdb->prepare(
                    'UPDATE '. $table_name .' SET title = \''.$_POST['ti_title'][$i].'\', on_link = \''.$_POST['ti_url'][$i].'\', image_url = \''.$_POST['ti_image_url'][$i].'\', content = \''.$_POST['ti_content'][$i].'\', is_dyn = 0, position = \''.$_POST['ti_position'][$i].'\', dyn_link = 0 WHERE id = \''.$_POST['ti_id'][$i].'\';'
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
    //Loop through all three top row boxes
    for ($i = 0; $i < $fieldCountFR; $i++) { 
        // if the content is static set is_dyn to 0 and update fileds
        if($_POST['fr_row'][$i] == "1" and !($_POST['is_dyn_fr'][$i] == "1")){ 
            $rows_affected = $wpdb->query(
                $wpdb->prepare(
                    'UPDATE '. $table_name .' SET title = \''.$_POST['fr_title'][$i].'\', on_link = \''.$_POST['fr_url'][$i].'\', image_url = \''.$_POST['fr_image_url'][$i].'\', content = \''.$_POST['fr_content'][$i].'\', is_dyn = 0, dyn_link = 0 WHERE id = \''.$_POST['fr_id'][$i].'\';'
                )
            ); // $wpdb->query, Else if the content is dynamic at row 1, empty all fields and set dyn_link / is_dyn         
         } else if ($_POST['fr_row'][$i] == "1" and $_POST['is_dyn_fr'][$i] == "1") {
            for ($i = 0; $i < 3; $i++) {
                $rows_affected = $wpdb->query(
                    $wpdb->prepare(
                        'UPDATE '. $table_name .' SET title = \''.$_POST['fr_title'][$i].'\', on_link = \''.$_POST['fr_url'][$i].'\', image_url = \''.$_POST['fr_image_url'][$i].'\', content = \''.$_POST['fr_content'][$i].'\', is_dyn = 1, dyn_link = \''.$_POST['dyn_link_fr'].'\' WHERE id = \''.$_POST['fr_id'][$i].'\';'
                    )
                ); // $wpdb->query   
            }
        }
    }
    //Loop through all three boxes at row two
    for ($i = 0; $i < $fieldCountSR; $i++) {
        // if the content is static set is_dyn to 0 and update fileds
        if($_POST['sr_row'][$i] == "2" and !($_POST['is_dyn_sr'][$i] == "1")){
            $rows_affected = $wpdb->query(
                $wpdb->prepare(
                    'UPDATE '. $table_name .' SET title = \''.$_POST['sr_title'][$i].'\', on_link = \''.$_POST['sr_url'][$i].'\', image_url = \''.$_POST['sr_image_url'][$i].'\', content = \''.$_POST['sr_content'][$i].'\', is_dyn = 0, dyn_link = 0 WHERE id = \''.$_POST['sr_id'][$i].'\';'
                )
            ); // $wpdb->query,  Else if the content is dynamic at row 1, empty all fields and set dyn_link / is_dyn              
        } else if ($_POST['sr_row'][$i] == "2" and $_POST['is_dyn_sr'][$i] == "1") {
            for ($i = 0; $i < 3; $i++) {
            $rows_affected = $wpdb->query(
                $wpdb->prepare(
                    'UPDATE '. $table_name .' SET title = \''.$_POST['sr_title'][$i].'\', on_link = \''.$_POST['sr_url'][$i].'\', image_url = \''.$_POST['sr_image_url'][$i].'\', content = \''.$_POST['sr_content'][$i].'\', is_dyn = 1, dyn_link = \''.$_POST['dyn_link_sr'].'\' WHERE id = \''.$_POST['sr_id'][$i].'\';'
                )
            ); // $wpdb->query   
            }
        }
    }
    echo "<p class='notice success animated shake'>".$fieldCount." stycken fält uppdaterades, var av dessa, ".$fieldCountTI."st top images (av dessa raderades ".$fieldCountRTI."), ".$fieldCountFR."st boxar på rad 1 samt ".$fieldCountFR."st boxar på rad 2<span class='tgl-alert'>X</span></p>";
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
    $(document).ready(function() {
        $( ".tgl-alert" ).click(function() {
          $( ".success" ).toggle( "slow", function() {
            // Animation complete.
          });
        });
    });
    //Set new field count to zero
    var count = 0;
    //When the object with id #add_field is pressed, append top_images_container with all needed fields
    $(function(){
        $('#add_field').click(function(){
            count += 1;
            $('#top_images_container').append('<div id="new_'+ count +'" style="margin-bottom: 20px;"><div><p class="del-wth-chbx" style=""><input type="checkbox" name="ti_delete[]" value="1"> RADERA</p></div><div><h3>Bildtext: <input type="text" name="ti_title[]" value="" /> | Position: <input type="text" name="ti_position[]" value=""></h3>Länk till bild: <input style="vertical-align: middle;" type="text" name="ti_image_url[]" value=""> <hr/><input type="hidden" name="ti_id[]" value="" /><input type="hidden" name="id[]" value="" /><input type="hidden" name="ti_url[]" value=""><input type="hidden" name="ti_content[]" value=""><input type="hidden" name="ti_row[]" value="3"></div></div>'
            );
            $("html, body").animate({ scrollTop: $('#new_'+count).offset().top-150 }, 2000);
        });
    });
    //Hide/show varoius inputs depending on if the content is dynamic
    $(function(){
        $("#is_dyn_fr").change(function() {
        if(this.checked) {
            $("#fr_wrapper").toggle();
            $("#f_row").append('<span id="dyntextfr">Välj dynmaiskt innehåll:</span> <select name="dyn_link_fr" id="dyn_link_fr"><option value="http://eter.rudbeck.info/?json=get_recent_posts&apikey=ErtYnDsKATCzmuf6&count=3">Senaste guider</option><option value="http://eter.rudbeck.info/eter-app-api/?apikey=vV85LEH2cUJjshrFx5&list-all-courses=1&parent=43">Senaste kurser</option></select>');
        } else {
            $("#fr_wrapper").toggle();
            $("#dyn_link_fr").remove();
            $("#dyntextfr").remove();
        }
    });
    });
    $(function(){
        $("#is_dyn_sr").change(function() {
        if(this.checked) {
            $("#sr_wrapper").toggle();
            $("#s_row").append('<span id="dyntextsr">Välj dynamsikt innehåll:</span><select name="dyn_link_sr" id="dyn_link_sr"><option value="http://eter.rudbeck.info/?json=get_recent_posts&apikey=ErtYnDsKATCzmuf6&count=3">Senaste guider</option><option value="http://eter.rudbeck.info/eter-app-api/?apikey=vV85LEH2cUJjshrFx5&list-all-courses=1&parent=43">Senaste kurser</option></select>');
        } else {
            $("#sr_wrapper").toggle();
            $("#dyn_link_sr").remove();
            $("#dyntextsr").remove();
        }
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
        <h1>| Startsida</h1>
        <div style="margin-left: 2%;">
        <h2>Välj innehåll Bildkarusell</h2>
        <p>Notera: Bilderna visas bara på sidan efter att du har tryckt på sparaknappen.</p>
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
                    ?>
                    <div style="margin-bottom: 20px;">
                        <div>
                            <p class="del-wth-chbx" style="">
                                 <input type="checkbox" name="ti_delete[]" value="<?php echo $id; ?>"> RADERA
                            </p>
                            <img height="120" style="vertical-align: middle; float: right; vertical-align: top; margin-bottom: 20px; margin-right: 20px;" src="<?php echo $image_url; ?>" alt="Kunde inte ladda bilden">
                        </div>
                        <div>
                            <h3>Bildtext: <input type="text" name="ti_title[]" value="<?php echo $title; ?>" /> | Position: <input type="text" name="ti_position[]" value="<?php echo $position; ?>"></h3>
                            Länk till bild: <input style="vertical-align: middle;" type="text" name="ti_image_url[]" value="<?php echo $image_url; ?>">
                            Länk på bild: <input type="text" name="ti_url[]" value="<?php echo $url; ?>">
                            
                            <input type="hidden" name="ti_id[]" value="<?php echo $id; ?>" />   
                            <input type="hidden" name="id[]" value="<?php echo $id; ?>" />

                            <input type="hidden" name="ti_content[]" value="<?php echo $content; ?>">
                            <input type="hidden" name="ti_row[]" value="3">
                            <hr/>
                        </div>
                    </div>
                    <? endforeach; ?>
                </div>
                <p><a class="button" href="#" id="add_field">Lägg till en bild</a></p>
            </div>
        </div>
        <h2>Välj innehåll Rad 1</h2>
       <?php if($wpdb->get_results("SELECT id FROM `".$table_name."` WHERE row = 1 AND is_dyn=1 ;")): ?>
        <input type="checkbox" name="is_dyn_fr" id="is_dyn_fr_off"  checked value="1"> Kryssa ur denna för att avaktivera Dynamiskt innehåll
        <? foreach( $wpdb->get_results("SELECT * FROM `".$table_name."` WHERE row = 1 LIMIT 1;") as $key => $rows):
           $dyn_link = $rows->dyn_link;
        ?>
          <input type="text" name="dyn_link_fr" value="<?php echo $dyn_link; ?>">  
        <? endforeach; ?>
        <? foreach( $wpdb->get_results("SELECT * FROM `".$table_name."` WHERE row = 1 ;") as $key => $rows):
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
            ?>
            <div style="display: none;">
                <input type="hidden" name="fr_title[]" value="<?php echo $title; ?>">
                <input type="hidden" name="fr_url[]" value="<?php echo $url; ?>">
                <input type="hidden" name="fr_image_url[]" value="<?php echo $image_url; ?>">
                <input type="hidden" name="fr_content[]" value="<?php echo $content; ?>">
                <input type="hidden" name="fr_id[]" value="<?php echo $id; ?>" />
                <input type="hidden" name="fr_row[]" value="<?php echo $row; ?>" />
                <input type="hidden" name="id[]" value="<?php echo $id; ?>" />
            </div>
            <? endforeach; ?>
        <? else: ?>
        <input type="checkbox" name="is_dyn_fr" id="is_dyn_fr" value="1"> Kryssa i denna för Dynamiskt innehåll
        <div style="text-align: center;" id="f_row">
            <div id="fr_wrapper">
            <?php 
            foreach( $wpdb->get_results("SELECT * FROM `".$table_name."` WHERE row = 1 ;") as $key => $rows):
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
            ?>
            
            <div class="box-row">
                <h3>Title: <input type="text" name="fr_title[]" value="<?php echo $title; ?>"></h3>
                <p>URL: <input type="text" name="fr_url[]" value="<?php echo $url; ?>"></p>
                <p>Image url: <input type="text" name="fr_image_url[]" value="<?php echo $image_url; ?>"></p>
                <p><span style="text-align: left;">Fritext:</span><br> <textarea name="fr_content[]"><?php echo $content; ?></textarea></p>
                <input type="hidden" name="fr_id[]" value="<?php echo $id; ?>" />
                <input type="hidden" name="fr_row[]" value="<?php echo $row; ?>" />
                <input type="hidden" name="id[]" value="<?php echo $id; ?>" />
            </div>
            <? endforeach; ?>
            </div>
        </div>
        <?php endif; ?>    
        <h2>Välj innehåll Rad 2</h2>
        <?php if($wpdb->get_results("SELECT id FROM `".$table_name."` WHERE row = 2 AND is_dyn=1 ;")): ?>
        <input type="checkbox" name="is_dyn_sr" id="is_dyn_sr_off" checked value="1"> Kryssa ur denna för att avaktivera Dynamiskt innehåll. Notera att du måste trycka på spara för innan du kan lägga till staiskt innehåll i boxarna. 
        <? foreach( $wpdb->get_results("SELECT * FROM `".$table_name."` WHERE row = 2 LIMIT 1;") as $key => $rows):
           $dyn_link = $rows->dyn_link;
        ?>
          <input type="text" name="dyn_link_sr" value="<?php echo $dyn_link; ?>">  
        <? endforeach; ?>
        <? foreach( $wpdb->get_results("SELECT * FROM `".$table_name."` WHERE row = 2 ;") as $key => $rows):
            // each column will be accessible by these
            $row= $rows->row;
            $position = $rows->position;
            $title = $rows->title;
            $url = $rows->on_link;
            $image_url = $rows->image_url;
            $content = $rows->content;
            $is_dyn = $rows->is_dyn;
            $id = $rows->id;
            ?>
            <div style="display: none;">
                <input type="hidden" name="sr_title[]" value="<?php echo $title; ?>">
                <input type="hidden" name="sr_url[]" value="<?php echo $url; ?>">
                <input type="hidden" name="sr_image_url[]" value="<?php echo $image_url; ?>">
                <input type="hidden" name="content[]" value="<?php echo $content; ?>">
                <input type="hidden" name="sr_id[]" value="<?php echo $id; ?>" />
                <input type="hidden" name="sr_row[]" value="<?php echo $row; ?>" />
                <input type="hidden" name="id[]" value="<?php echo $id; ?>" />
            </div>
            <? endforeach; ?>
        <? else: ?>
        <input type="checkbox" name="is_dyn_sr" id="is_dyn_sr" value="1"> Kryssa i denna för Dynamiskt innehåll
                <div style="text-align: center;" id="s_row">
            <div id="sr_wrapper">
            <?php 
            foreach( $wpdb->get_results("SELECT * FROM `".$table_name."` WHERE row = 2 ;") as $key => $rows):
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
            ?>
            <div class="box-row">
                <h3>Title: <input type="text" name="sr_title[]" value="<?php echo $title; ?>"></h3>
                <p>URL: <input type="text" name="sr_url[]" value="<?php echo $url; ?>"></p>
                <p>Image url: <input type="text" name="sr_image_url[]" value="<?php echo $image_url; ?>"></p>
                <p><span class="txt-al-floated-left">Fritext:</span><br> <textarea name="sr_content[]"><?php echo $content; ?></textarea></p>
                <input type="hidden" name="sr_id[]" value="<?php echo $id; ?>" />
                <input type="hidden" name="sr_row[]" value="<?php echo $row; ?>" />
                <input type="hidden" name="id[]" value="<?php echo $id; ?>" />
            </div>
            <? endforeach; ?>
            </div>
        </div>
        <?php endif; ?>  
            <p>Notera: Dynmaisk data skriver över alla boxarna på samma rad, sparaknappen sparar alla ändringar permanent </p>
            <input type="submit" value="Spara ändringar" class="button button-primary">
        </div>
    </div>
</form>