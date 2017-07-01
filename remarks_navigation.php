<?php

class RemarksInterface {

    private function populateButtonsList(){
        global $buttons_List;

        $buttons_List[] = $this->remarks_addButtonEntry('overview', '<h4>Overview</h4>', 0, false, true);
        $buttons_List[] = $this->remarks_addButtonEntry('about', '<h4>About</h4>', 0, false);
        $buttons_List[] = $this->remarks_addButtonEntry('post', '<h4>Post</h4>', 1, true);
        $buttons_List[] = $this->remarks_addButtonEntry('category', '<h4>Category</h4>', 1, true);
        $buttons_List[] = $this->remarks_addButtonEntry('author', '<h4>Post Author</h4>', 1, true);
        $buttons_List[] = $this->remarks_addButtonEntry('geolocate', '<h4>Geolocation</h4>', 1, true);
    }

    private function makeAllButtons($remarks_total_comments){
      global $buttons_List;

      $currentLine = 0;
      echo "<div id='nav_row_".$currentLine."'>";

      foreach ($buttons_List as $button) {
          if ($currentLine < $button['line'] && $remarks_total_comments > 0) {
            echo "</div>";
            $currentLine++;
            echo "<div id='nav_row_".$currentLine."'>";
            echo "<br/>\n";
          }
           $this->makeButton($button);
      }
       echo "</div>";
    }

     /* POPULATE */
     private function remarks_addButtonEntry($tag, $label, $lineNumber, $bPrintPreamble, $startEnabled=false){
          return array('tag' => $tag, 'id' => $tag.'_button', 'div' => $tag.'_div', 'label' => $label, 'line' => $lineNumber, 'printPreamble' => $bPrintPreamble, 'startEnabled' => $startEnabled);
     }


     /* PRINT */
     private function makeButton($button){
          global $buttons_List;
          echo "<div ";

          if ($button['startEnabled'] == true){
            echo "class='remarks_button remarks_button_selected ".$button['tag']."_bg_colour'";
          } else {
            echo "class='remarks_button'";
          }

          echo "id='".$button['id']."'>\n";
          if ($button['printPreamble'] == true){
            echo "\t<div class='preamble'>\n";
            echo "Show Comments by";
            echo "\t</div>\n";
          }
          echo "\t<div class='title'>\n";
          echo $button['label'];
          echo "\n\t</div>\n";
          echo "</div>\n";
     }

      public static function remarks_renderNavigationOptions($section){
       echo "\t<nav id='$section"."_options'>\n";
           echo "\t\t<div id='$section"."_options_table' class='remarks_subbutton ".$section."_bg_colour remarks_subbutton_selected'>Table</div>\n";
           echo "\t\t<div id='$section"."_options_bar' class='remarks_subbutton'>Bar Chart</div>\n";
           echo "\t\t<div id='$section"."_options_pie' class='remarks_subbutton'>Pie Chart</div>\n";
       echo "\t</nav><!-- end $section"."_options -->\n";
     }

      public function renderInterface($remarks_total_comments) {
        if ($remarks_total_comments > 0){
echo "<div id='main_nav_with_comments'><br/>";
          $this->populateButtonsList();
          $this->makeAllButtons($remarks_total_comments);
echo "</div>
<br/>";
    } else {
      echo "<div id='main_nav_no_comments'><br/>";
      global $buttons_List;
      $buttons_List[] = $this->remarks_addButtonEntry('overview', '<h4>Overview</h4>', 3, false, true);
      $buttons_List[] = $this->remarks_addButtonEntry('about', '<h4>About</h4>', 3, false);

      makeAllButtons(0);
      echo "
</div>
<br/>";
    }
     }
}
?>