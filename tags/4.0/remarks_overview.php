<?php

class RemarksOverview {

  private $totalComments;
  private $categoryHighestStat;
  private $authorHighestStat;

  function __construct($totalComments, $categoryHighestStat, $authorHighestStat) {
    $this->totalComments = $totalComments;
    $this->categoryHighestStat = $categoryHighestStat;
    $this->authorHighestStat = $authorHighestStat;
  }

  function render() {
    echo "<div id='overview_div'>";

    if($this->totalComments == 0){
      echo "You haven't approved any comments yet! Please check back when some have been approved.<br/></div>";  
      return;
    }
    echo $this->totalComments . " approved comments in total.<br/>";

    echo "<br/>
      <h5>Most commented Post:</h5>
      <br/>";

    global $remarks_posts_top;
    echo $remarks_posts_top['label']." (".$remarks_posts_top['count'].")";

    echo "<br/>";
    echo "<br/>";

    echo "<h5>Most commented Category:</h5>";
    echo "<br/>";
    echo $this->categoryHighestStat['name'] . " (".$this->categoryHighestStat['count'] .")";

    echo "<br/>";
    echo "<br/>";
    echo "<h5>Most commented Author:</h5>";
    echo "<br/>";

    echo $this->authorHighestStat['name'] . " (".$this->authorHighestStat['count'] .")";

    echo "<br/>";
    echo "<br/>";


    echo "<h5>Origin of most comments:</h5>";
    echo "<br/>";

    global $remarks_countries_top;
    echo $remarks_countries_top['label']." (".$remarks_countries_top['count'].")";

    echo "<br/>";
    echo "<br/>";
    echo "</div>";
  }
}