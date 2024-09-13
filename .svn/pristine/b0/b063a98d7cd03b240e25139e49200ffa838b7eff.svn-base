<?php

class RemarksCategories extends RemarksSegment {
  
    public function __construct($remarksPostMatrix) {
        $this->populateCategoryMatrix($remarksPostMatrix);
        parent::__construct('category');
    }

    private function renderCategoryMatrixRow($categoryIndex){
        echo "<tr><td><a href='/?cat=" . $this->segmentData[$categoryIndex]['id'] . "'>" . $this->segmentData[$categoryIndex]['name'] . 
          "</a></td><td>" . $this->segmentData[$categoryIndex]['count'] . " comments</td><td>" . $this->segmentData[$categoryIndex]['numPosts'] . "</td></tr>\n";
    }

    public function renderMatrix(){
        echo "<div id='category_table'>\n\n";
        echo "<table class='centralise'>";
        echo "<tr><td><strong>Post Category</strong></td><td><strong>Number of Comments</strong></td><td><strong>Number of Posts</td></strong></tr>\n";
        foreach($this->segmentData as $authorKey => $eachAuthor){
            $this->renderCategoryMatrixRow($authorKey);
        }
        echo "</table>\n\n";
        echo "</div>\n\n";
    }

    public function populateCategoryMatrix($remarksPostMatrix){
        global $wpdb;
        $categoryCountMatrix = array();

        // Get a list of all category ids.
        $getCategoryIdsSql = "SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy='category'";
        $categoryIds = $wpdb->get_results($getCategoryIdsSql, ARRAY_A);

        // Initiate their entry in the matrix to have 0.
        foreach($categoryIds as $categoryId){
          $categoryCountMatrix[$categoryId['term_taxonomy_id']] = array('numPosts' => 0, 'commentCount' => 0);
        }

        // Get a list of how many posts per category.
        foreach($remarksPostMatrix as $remarksPost) {
          foreach ($remarksPost['categories'] as $categoryIndex) {
            if (array_key_exists($categoryIndex, $categoryCountMatrix)){
              $categoryCountMatrix[$categoryIndex]['numPosts']++;
              $categoryCountMatrix[$categoryIndex]['commentCount'] +=  $remarksPost['count'];
            }
          }
        }

        // Fill in the segment data.
        foreach ($categoryCountMatrix as $categoryIndex => $category) {
          $getCategoryNameSql = "SELECT name FROM $wpdb->terms WHERE term_id = " . $categoryIndex;
          $categoryName = $wpdb->get_results($getCategoryNameSql , ARRAY_A);

          $this->segmentData[] = array('name' => $categoryName[0]['name'], 'count' => $category['commentCount'], 'id' => $categoryIndex, 'numPosts' => $category['numPosts']);
        }

        usort($this->segmentData, 'self::reorder');
    }
}