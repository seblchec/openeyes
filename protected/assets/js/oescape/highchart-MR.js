// highSTOCK https://api.highcharts.com/highstock/

/*
* Positioning of Elements below the xAxis.
* The xAxis is offset to allow space for the banners
* Banners and data Flags are drawn from the xAxis up.
*/

var drugs = [];	// drug banners
var bannersOffset = 45 * drugs.length; 			// offset xAxis to allow space for drug banners
var xAxisOffset ; 			// allow for the '>5' flags
var flagYoffset = -40; 							// drug flags are positioned relative to xAxis
var total_height = 900;
var eye_side = 'right';
var eye_side_label = 'R';
var octImgStack;
/*
* Highchart options (data and positioning only)
* all UI stylng is handled in CSS
*/
var options_MR = {
  chart: {
    events: {
      load: function() {
        highHelp.drawBanners(this,Object.keys(drugs[eye_side]));
      },
      redraw: function(){
        if ($(this['renderTo']).hasClass('highcharts-right')){
          side = 'right';
        } else {
          side = 'left';
        }

        highHelp.drawBanners(this,Object.keys(drugs[side]));
      }
    },
    height: total_height, 						// chart height fixed px
    marginTop:80,						// make px space for Legend
    spacing: [30, 10, 15, 10], 			// then make space for title - default: [10, 10, 15, 10]
    type: 'line' 						// Can be any of the chart types listed under plotOptions. ('line' default)
  },

  credits: { enabled: false },  // highcharts url (logo) removed
  exporting: false,

  title: 	{
    text: "Retinal thickness-Visual acuity",
    align: 'center',
    y:-10, // title needs offset to not go under legend in small mode
  },

  // standard settings
  legend: 		highHelp.chartLegend(),
  navigator: 		highHelp.chartNavigator(),
  rangeSelector: 	highHelp.chartRangeSelector(-25,-60),	// offset from bottom right (x,y) "Show all" button

  tooltip: {
    useHtml: true,
    formatter: function(){
      if(this.series.name.startsWith('(VA)')) {
        return OEScape.toolTipFormatters.VA(this);
      } else {
        return OEScape.toolTipFormatters.Default(this);
      }
    }
  },

  yAxis: [{
    // primary y axis
    title: {
      text: 'CRT (um)'
    },
    opposite: false,
    reversed: false,
  },{
    // secondary y axis
    title: {
      text: 'VA ()'
    },
    min: 1,
    max: 150,
    opposite: true,
  }],

  xAxis: {
    type: 'datetime',
    crosshair: {
      snap: false,		// blue line smooth
    },
    labels: {
      y:30				// move labels below ticks
    },
    offset: xAxisOffset,   	// this moves the chart up to allow for the banners and other flags
    tickPixelInterval: 50,  // if this is too high the last tick isn't shown (default 100) but depends on chart width
    startOnTick: false, //If the charts are forced to start and end on ticks they can't align properly
    endOnTick: false,
    plotLines: []
  },

  plotOptions: {
    series: {
      animation:false,
      point: {

      },

      label: {
        enabled:false,
      },

      marker: {
        symbol:'circle',
      }
    },

    flags: {
      shape: "square",
      showInLegend: false,
      tooltip: {
        pointFormatter : function () {
          var s = '<b>'+this.info+'</b>';
          return s;
        }
      }
    }
  },

  // ----------------------  Medical Retina Data
  series: []
};

/* exported changeSetting */
function changeSetting(enter_drugs, side) {
  drugs = enter_drugs;
  bannersOffset = 45 * Object.keys(drugs[side]).length; 			// offset xAxis to allow space for drug banners
  xAxisOffset = bannersOffset + 10; 			// allow for the '>5' flags
  eye_side = side;
  eye_side_label = (eye_side=='right')?'R':'L';
  options_MR['yAxis'][0]['height'] =  total_height - bannersOffset - 350;
  options_MR['yAxis'][1]['height'] =  total_height - bannersOffset - 350;
  options_MR['title']['text']="Retinal thickness-Visual acuity ("+side+" Eye)";
  options_MR['chart']['className'] = 'oes-chart-mr-'+eye_side;
}

/**
 * Draw series for MR oescape, including the line series, flag series and injection series
 * @param chart_MR
 * @param VA_data
 * @param CRT_data
 * @param VA_lines_data
 * @param injections_data
 * @param axis_type
 */
function drawMRSeries(chart_MR, VA_data, CRT_data, VA_lines_data, injections_data, axis_type){
  var VA_options = {
    type: 'line',
    colorIndex: (eye_side=='right')?11:21,
    yAxis: 1,
    showInNavigator: true
  };
  var CRT_options = {
    type: 'line',
    colorIndex: (eye_side=='right')?12:22,
    yAxis: 0,
    showInNavigator: true
  };
  var VA_lines_options = {
    type: "flags",
    className: 'oes-hs-eye-'+eye_side+'-dull',
    y: (0 - xAxisOffset - 15)
  };
  addSeries(chart_MR, '(VA)'+axis_type+'  ('+eye_side_label+')', VA_data[eye_side], VA_options);
  addSeries(chart_MR, 'CRT ('+eye_side_label+')',  CRT_data[eye_side], CRT_options);
  addSeries(chart_MR, 'VA > 5 lines',  VA_lines_data[eye_side], VA_lines_options);

  var i = 0;
  for ( var injection_name in injections_data[eye_side]) {
    var injections_options = {
      type: "flags",
      className: 'oes-hs-eye-'+eye_side+'-dull',
      y: flagYoffset-i*40,
    };
    var size = injections_data[eye_side][injection_name].length;
    addSeries(chart_MR, injection_name+"("+size+")", injections_data[eye_side][injection_name], injections_options);
    i++;
  }
}

/**
 * Set the MR images stack in the right side according to the left side's point data.
 * @param container
 * @param img_id_prefix
 * @param initID
 * @param callBack
 */
function setImgStack(container,img_id_prefix, initID, callBack) {
  octImgStack = new initStack(container, img_id_prefix, initID, callBack);
  options_MR['plotOptions']['series']['point'] = {
    events: {
      mouseOver: function(){
        octImgStack.setImg( this.oct, this.side ); // link chart points to OCTs
      }
    }
  }
}