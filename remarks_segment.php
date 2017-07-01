<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of remarks_segment
 *
 * @author john
 */
class RemarksSegment {
  
    protected $segmentType;
    protected $segmentData;
    protected $mostPopular;
    protected $highestNumber;
  
    protected function __construct($classType){
        $this->segmentType = $classType;
    }
  
    protected static function reorder($a, $b) {
        if ($a['count'] == $b['count']) {
            return 0;
        }
        return ($a['count'] > $b['count']) ? -1 : 1;
    }
    
    protected function drawBars(){
        echo "<svg width='1000' height='500' id='" . $this->segmentType . "_bar' class='startHidden'></svg>
            <script src='http://d3js.org/d3.v3.min.js'></script>
            <script>
InitChart();

function InitChart() {

  var barData = [";

            foreach ($this->segmentData as $key => $category){
                echo "{ x: '" . $category['name'] . "', y: " . $category['count'] . "}";
                if ($key <= count($this->segmentData) ){
                    echo ",";
                }
            }

            echo "];

  var vis = d3.select('#" . $this->segmentType . "_bar'),
    WIDTH = 1000,
    HEIGHT = 500,
    MARGINS = {
      top: 20,
      right: 20,
      bottom: 20,
      left: 50
    },
    xRange = d3.scale.ordinal().rangeRoundBands([MARGINS.left, WIDTH - MARGINS.right], 0.1).domain(barData.map(function (d) {
      return d.x;
    })),


    yRange = d3.scale.linear().range([HEIGHT - MARGINS.top, MARGINS.bottom]).domain([0,
      d3.max(barData, function (d) {
        return d.y;
      })
    ]),

    xAxis = d3.svg.axis()
      .scale(xRange)
      .tickSize(5)
      .tickSubdivide(true),

    yAxis = d3.svg.axis()
      .scale(yRange)
      .tickSize(5)
      .orient('left')
      .tickSubdivide(true);

  var color = d3.scale.category20b();

  vis.append('svg:g')
    .attr('class', 'x axis')
    .attr('transform', 'translate(0,' + (HEIGHT - MARGINS.bottom) + ')')
    .call(xAxis);

  vis.append('svg:g')
    .attr('class', 'y axis')
    .attr('transform', 'translate(' + (MARGINS.left) + ',0)')
    .call(yAxis);

  vis.selectAll('rect')
    .data(barData)
    .enter()
    .append('rect')
    .attr('x', function (d) {
      return xRange(d.x);
    })
    .attr('y', function (d) {
      return yRange(d.y);
    })
    .attr('width', xRange.rangeBand())
    .attr('height', function (d) {
      return ((HEIGHT - MARGINS.bottom) - yRange(d.y));
    }).attr('fill', '#393b79');

}
        </script>";


/*      $URL = home_url().'/wp-content/plugins/remarks/remarks_barchart.php?';
        foreach ($this->remarks_categories as $category){
            $URL = $URL.$category['name']."=".$category['count']."&";
        }
        $URL = $URL.'chart_title'."=Comment%20Breakdown%20By%20Category";

        echo '<img id="category_bar" alt="Bar Chart of Posts by Categories" class="startHidden" src="'.$URL.'">';*/
    }
    
    protected function drawPie(){
        echo '<div id="' . $this->segmentType . '_pie" class="startHidden" ></div>
            <script src="http://wordpress/d3.min.js"></script><!-- TODO FIX SCRIPT LOCATION -->
            <script>

            (function(d3) {
            "use strict";

            var dataset = [';

            foreach ($this->segmentData as $key => $category){
                echo "{ label: '" . $category['name'] . "', count: " . $category['count'] . "}";
                if ($key <= count($this->segmentData) ){
                    echo ",";
                }
            }

            echo '];

            var width = 360;
            var height = 360;
            var radius = Math.min(width, height) / 2;
            var donutWidth = 75;                            // NEW

            var color = d3.scale.category20b();

            var svg = d3.select("#' . $this->segmentType . '_pie")
              .append("svg")
              .attr("width", width)
              .attr("height", height)
              .append("g")
              .attr("transform", "translate(" + (width / 2) +
                "," + (height / 2) + ")");

            var arc = d3.svg.arc()
              .innerRadius(radius - donutWidth)             // NEW
              .outerRadius(radius);

            var pie = d3.layout.pie()
              .value(function(d) { return d.count; })
              .sort(null);

            var path = svg.selectAll("path")
              .data(pie(dataset))
              .enter()
              .append("path")
            .attr("d", arc)
              .attr("fill", function(d, i) {
                return color(d.data.label);
              });

            })(window.d3);
        </script>';
    }
 
   public function getHighestStat(){
     return $this->segmentData[0]; // has been reordered so that highest is at the top.
   }
     
    public function render(){
        echo "<div id='" . $this->segmentType . "_div' class='startHidden'>";
        remarks_renderNavigationOptions($this->segmentType);
        echo "<br/>";
        $this->renderMatrix();
        echo '<br/>';
        $this->drawBars();
        echo '<br/>';
        $this->drawPie();
        echo '<br/>';
        echo '</div>';
    }
}