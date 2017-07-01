<?php

class RemarksInterface {
  private $buttonsList;
  private $totalComments;

  public function __construct($remarks_total_comments) {
    $this->buttonsList = array();
    $this->totalComments = $remarks_total_comments;
        if ($remarks_total_comments > 0){
          $this->populateButtonsList();
        } else {
      $this->buttonsList[] = $this->remarks_addButtonEntry('overview', '<h4>Overview</h4>', 3, false, true);
      $this->buttonsList[] = $this->remarks_addButtonEntry('about', '<h4>About</h4>', 3, false);
        }
  }

    private function populateButtonsList(){

        $this->buttonsList[] = $this->remarks_addButtonEntry('overview', '<h4>Overview</h4>', 0, false, true);
        $this->buttonsList[] = $this->remarks_addButtonEntry('about', '<h4>About</h4>', 0, false);
        $this->buttonsList[] = $this->remarks_addButtonEntry('post', '<h4>Post</h4>', 1, true);
        $this->buttonsList[] = $this->remarks_addButtonEntry('category', '<h4>Category</h4>', 1, true);
        $this->buttonsList[] = $this->remarks_addButtonEntry('author', '<h4>Post Author</h4>', 1, true);
        $this->buttonsList[] = $this->remarks_addButtonEntry('geolocate', '<h4>Geolocation</h4>', 1, true);
    }

    private function makeAllButtons(){
      $currentLine = 0;
      echo "<div id='nav_row_".$currentLine."'>";

      foreach ($this->buttonsList as $button) {
          if ($currentLine < $button['line'] && $this->totalComments > 0) {
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

      public function renderInterface() {
        if ($this->totalComments > 0){
          echo "<div id='main_nav_with_comments'><br/>";
        }
        else {
          echo "<div id='main_nav_no_comments'><br/>";
        }
        $this->makeAllButtons();
        echo "</div><br/>";
    }
}
?>