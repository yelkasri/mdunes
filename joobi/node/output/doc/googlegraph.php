<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Doc_googlegraph extends Output_Doc_Document {
var $chartXWidth=450;
var $chartYHeight=350;
var $lineChartXWidth=450;
var $notAllowedStringA=array('\'',';','"','.');
var $percentileS='percentile';
public function renderContent(){
$dataA=$this->htmlObj->_data;
$elementsA=$this->htmlObj->elements;$mapListA=$this->htmlObj->mapListA;$modelM=$this->htmlObj->_model;
$chartNameA=array();
$totalElementA=array();
if(!empty($this->htmlObj->graphtype))$chartNameA=WTools::preference2Array($this->htmlObj->graphtype );
$graphName=$this->htmlObj->name;
$elemetLink='';
if(!empty($this->htmlObj->axisstyle))
{
  $axisstyle=$this->htmlObj->axisstyle;
}else{
  $axisstyle='';
}
$html=$this->htmlObj->filtersHTML;
$dropList=array();
$dropdownList=$this->htmlObj->headListFooter;
if(!empty($dropdownList)){
foreach($dropdownList as $dropdown){
if(!empty($dropdown)){
$dropList[]=$dropdown;
}}$this->htmlObj->headListFooter=$dropList;
}
$this->_tableDetailsO=WPage::renderBluePrint('listing');
$this->_tableDetailsO->transform->createHead($this->htmlObj );
static $lineG=false;
$chartType=0; $xLabels=array();
$dataValues=array();
$mapName=null;
$baseValue=null;
$dataName=array();
$this->numberRows=count($dataA);
$this->allDataRowsA=$dataA;
$graph3axe=0;
$chartAxis='';
$axisXSet=false;
$axisYSet=false;
$axisZSet=false;
$xData=array();
$yData=array();
$zData=array();
foreach($dataA as $ndxDataA=> $valueDataA){
$this->rowNumber=$ndxDataA;
$grandeTotalVAlue=0;
foreach($elementsA as $elementsndx=> $element ){
$this->complexMap=$complexMap=$element->map.'_'.$element->sid;
$this->cellValue=(!empty($valueDataA->$complexMap)?$valueDataA->$complexMap : 0 );
$mapName=$element->name;
if(!empty($element->chartaxis) && ($axisstyle=='combined' || $axisstyle=='compared'))
{
if($element->chartaxis=='1d')
{
$htmlData=$this->callListingElement($element, $valueDataA );
$baseValue=$this->cellValue;
$xData[$mapName][]=$baseValue;
$xLabels[$mapName][]=$baseValue;
$axisXSet=true;
}
else if($element->chartaxis=='2d')
{
$htmlData=$this->callListingElement($element, $valueDataA );
$value=$this->cellValue;
$yData[$mapName][]=$value;
$dataValues[$mapName][]=$value;
$axisYSet=true;
if(!empty($element->coltotal) && $element->coltotal==1 )
{
$totalElementA=$element;
}
}
else if($element->chartaxis=='3d')
{
$name=$this->callListingElement($element, $valueDataA );
$zData[$mapName][]=$name;
$dataName[$mapName][]=$name;
$graph3axe=1;
$axisZSet=true;
}
 }
  else if((!empty($element->dsict) && $element->dsict==19)  ||  (!empty($element->graph3axe) && $element->graph3axe==1 )){if($element->map=='created' || $element->map=='startime' || $element->map=='registerdate' || $element->map=='modified'  )
{$htmlData=$this->callListingElement($element, $valueDataA );
$baseValue=$this->cellValue;
  $chartType=1;
$xLabels[]=$baseValue;
}
else if(false)
{$htmlData=$this->callListingElement($element, $valueDataA );
$baseValue=$this->cellValue;
  $chartType=1;
 $baseString='';
 if($baseValue==1)$baseString='subscribed';
 if($baseValue==0)$baseString='unsubscribed';
$xBarLabels[]=$baseString;
}
else if(!empty($element->graph3axe) && $element->graph3axe==1 )
{
$name=$valueDataA->$complexMap;
$htmlData=$this->callListingElement($element, $valueDataA );
$baseValue=$this->cellValue;
$dataName[]=$htmlData;
$chartType=2;
$graph3axe=1;
if(!empty($element->lien)){
   $elemetLink=$element->lien;
   $outputLinkC=WClass::get('output.link');
   $elemetLink=$outputLinkC->convertLink($element->lien, $valueDataA, '',$modelM, $mapListA );
}
}
elseif($element->map=='name'){
$name=$valueDataA->$complexMap;
$dataName[]=$name;
}} else
 {$htmlData=$this->callListingElement($element, $valueDataA );
$value=$this->cellValue;
$dataValues[$mapName][]=$value;
if(!empty($element->coltotal) && $element->coltotal==1 )
{
$totalElementA=$element;
}
}
}}
$title=$this->htmlObj->name;
if(!empty($element->chartaxis) && ($axisstyle=='combined' || $axisstyle=='compared'))
{
    if($axisYSet==false)
  {
 WMessage::log('Need to define Y Axis in the backend for this graph : '.$graphName.' OR need to code for this type of chart : ','GoogleGraph');
 $message=WMessage::get();
 $message->codeE('There is no Y Axis setup done for this Report!');
 return $html;
  }
}
else if(!empty($element->chartaxis) && ($axisstyle==''))
{
WMessage::log('Need to define Y Axis Style in the backend for this graph : '.$graphName.' OR need to code for this type of chart : ','GoogleGraph');
$message=WMessage::get();
$message->codeE('There is no Y Axis Style selected for this Report!');
  }
$this->chartXWidth=WPref::load('PMAIN_NODE_CHART_X_SIZE');
$this->chartYHeight=WPref::load('PMAIN_NODE_CHART_Y_SIZE');
$this->lineChartXWidth=$this->chartXWidth + $this->chartYHeight;
$startDate=null;
$create=true;
$startDate=WGlobals::get('start');if($startDate=='0000-00-00')$create=false;
if(!empty($dataValues) && $create){
if($axisstyle=='combined' || $axisstyle=='compared')
{
    $html .=$this->_coreCharts($yData,$xData,$zData,$elemetLink,$totalElementA,$chartNameA,$axisstyle);
} else
{
if(!empty($baseValue) && $chartType==1)
{
$html .=$this->_lineGraph($dataValues,$xLabels,$dataName);
}
else if($chartType==2)
{
$chart='Column';
$html .=$this->_basicCharts($dataValues,$xLabels,$dataName , $elemetLink , $totalElementA, $chart);}else{
$width=WGlobals::get('graphWidth', 700, 'global');
$html .=$this->_pieChartNew($dataValues, $dataName, $html, $width, $width ,  $graph3axe , $elemetLink );}
 WMessage::log('Need to define Chart/Graph Type in the backend for this graph : '.$graphName.' OR need to code for this type of chart : ','GoogleGraph');
$message=WMessage::get();
$message->codeE('Need to define Chart/Graph Type in the backend for this graph.');
}
}else{
$html .=WText::t('1340849910KWVC');}
return $html;
}
private function _coreCharts($dataValues,$xLabels,$dataName,$elemetLink,$totalElementA,$chartNameA,$axisstyle)
{
$chartNameA=array();
$noZ=false;
$noX=false;
$zName='';
$zValueName='';
$xName='';
$donutPie=false;
$countZ=0;
$chartNameA=array('Pie','Column','Line');
$html='';
if(!empty ($dataValues))
{
  $countY=count($dataValues);
    foreach($dataValues as $eachDataKey=> $eachDataValue)
  {
  $countX=count($eachDataValue);
  }
}else{
WMessage::log('The Y Axis has no data ','GoogleGraph');
$message=WMessage::get();
$message->codeE('The Y Axis has no data  !');
return $html;
}
if(!empty ($xLabels))
{
  foreach($xLabels as $xLabelsKey=> $xLabelsValue)
  {
  $countX=count($xLabelsValue);
$xName=$xLabelsKey;
  }
}else{
$noX=true;
for($j=0; $j<$countX; $j++ )
{
 $xLabels['NoXAxis'][$j]='NoX';
}
if(!empty ($dataName))
{
foreach($dataName as $eachDataNameKey=> $eachDataNameValue)
{
$countZ=count($eachDataNameValue);
if($countZ==1)  $zValueName=$eachDataNameValue[0];
}
}
 if($countZ==1)
 {
 $donutPie=true;
 $axisstyle='combined';
 }
}
if(!empty ($dataName))
{
  foreach($dataName as $dataNameKey=> $dataNameValue)
  {
$zName=$dataNameKey;
  }
}else{
$noZ=true;
for($k=0; $k<$countX; $k++ )
{
 $dataName['NoZAxis'][$k]='NoZ';
}
}
if($noX)
{
$this->chartXWidth=$this->lineChartXWidth;
$this->chartYHeight=$this->lineChartXWidth;
}
$html .=$this->_loadLibrary();
$html .=' function drawChart(){ ';
$formatP=$this->_createFormatP($dataValues, $dataName, $noZ);
$html .=$this->_createDataSec1P($formatP);
if(empty($donutPie))
{
if(!$noX)
{
  $format1C=$this->_createFormatC($dataValues,$xLabels,$dataName,$elemetLink,$totalElementA,1) ;
  $format2C=$this->_createFormatC($dataValues,$xLabels,$dataName,$elemetLink,$totalElementA,2) ;
}
} 
if(!$noX)
{
  $html .=$this->_createDataSec1C($format1C);
  $html .=$this->_createDataSec1L($format2C);
}
if(!$noZ)
{
  $html .=$this->_createDataSec2P($formatP);
  if(!$noX)
  {
$html .=$this->_createDataSec2C($format2C);
  }
}
if($axisstyle=='compared')
{
      $format1LP=$this->_createFormatC($dataValues,$xLabels,$dataName,$elemetLink,$totalElementA,1) ;
    $html .=$this->_createDataSec1C($format1LP,'percentile','Line');
}
$html .=$this->_chartOptions($formatP,$chartNameA,$zName,$xName,$countY,$axisstyle,$noX,$donutPie,$zValueName);
$html .=$this->_createCharts($formatP,$chartNameA,$noZ,$axisstyle,$countY,$noX);
$html .='  }  </script> </head>  <body> ';
$html .='  ';
$html .=$this->_createDivs($formatP,$chartNameA,$axisstyle,$countY,$noZ,$noX);
$html .='  </body> </html>  ';
return $html;
}
private function _loadLibrary(){
$htmlLoadLibrary='';
$htmlLoadLibrary .='
<html>
<head>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
function toggleGraph(id, link, description)
{
var e=document.getElementById(id);
if(e.style.display==\'\')
{
  e.style.display=\'none\';
  link.innerHTML=\'Click here to Expand \' + description;
}
else
{
  e.style.display=\'\';
  link.innerHTML=\'Click here to Collapse \' + description;
}
 }
google.load(\'visualization\', \'1.0\', {\'packages\':[\'corechart\']});
google.setOnLoadCallback(drawChart);
';
return $htmlLoadLibrary;
}
private function _createDataSec1P($formatP)
{
$htmlCreateDataP='';
$htmlCreateDataP .='var dataPie=new google.visualization.DataTable();';
$htmlCreateDataP .='dataPie.addColumn(\'string\', \'\');';
$htmlCreateDataP .='dataPie.addColumn(\'number\', \'Values\');';
foreach($formatP as $yKey=> $yValue )
{
$htmlCreateDataP .='dataPie.addRows([ ';
$countY=count($yValue);
$counterY=0;
$valueTotal=0;
foreach($yValue as $key=> $value )
{
$counterY++;
$valueTotal += $value ;
$yKeyR=str_replace($this->notAllowedStringA, '',$yKey);
if($countY==$counterY)
{
$htmlCreateDataP .=' [\''.$yKeyR.'\','.$valueTotal.']';
}
}
$htmlCreateDataP .=' ]);';
}
return $htmlCreateDataP;
}
private function _createDataSec1L($formatL)
{
$htmlCreateData='';
 $individualName='';
 $chartName='Line';
$iN=$individualName;
$cN=$chartName;
$finalN='data'.$cN.$iN;
$vCounter=0;
$countX=count($formatL);
$counterX=0;
foreach($formatL as $formatKey=> $formatValue)
{
$counterX++;
$name=str_replace(' ','',$formatKey);
$formatKey=str_replace($this->notAllowedStringA, '',$formatKey);
$name=str_replace($this->notAllowedStringA, '',$name);
$htmlCreateData .='var '.$finalN.$name.'=new google.visualization.DataTable(); ';
$htmlCreateData .=$finalN.$name.'.addColumn(\'string\', \'Date\'); ';
$htmlCreateData .=$finalN.$name.'.addColumn(\'number\', \''.$formatKey.'\'); ';
$htmlCreateData .=$finalN.$name.'.addRows([';
$countY=count($formatValue);
$counterY=0;
foreach($formatValue as $key=> $value)
{
$counterY++;
$rev=0;
$keyR=str_replace($this->notAllowedStringA, '',$key);
$htmlCreateData .='[\''.$keyR.'\',';
foreach($value as $k=> $v)
{
if(is_array($v))
{
$rev +=$v[1];
}
else
{
$rev +=0;
}
}
if($counterY==$countY)
{
 $htmlCreateData .=''.$rev.']';
}
else
{
$htmlCreateData .=''.$rev.'],';
}
}
$htmlCreateData .=']);';
}
return $htmlCreateData;
}
private function _createDataSec2P($formatP)
{
$htmlCreateDataP='';
foreach($formatP as $yKey=> $yValue )
{
$name=str_replace(' ','',$yKey);
$name=str_replace($this->notAllowedStringA, '',$name);
$htmlCreateDataP .='var dataPie'.$name.'=new google.visualization.DataTable(); ';
$htmlCreateDataP .='dataPie'.$name.'.addColumn(\'string\', \''.$name.'\'); ';
$htmlCreateDataP .='dataPie'.$name.'.addColumn(\'number\', \'Values\'); ';
$htmlCreateDataP .='dataPie'.$name.'.addRows([ ';
$countY=count($yValue);
$counterY=0;
foreach($yValue as $key=> $value )
{
$keyR=str_replace($this->notAllowedStringA, '',$key);
$counterY++;
if($countY==$counterY)
{
$htmlCreateDataP .=' [\''.$keyR.'\','.$value.']';
}
else
{
$htmlCreateDataP .=' [\''.$keyR.'\','.$value.'],';
}
}
$htmlCreateDataP .=' ]);';
}
return $htmlCreateDataP;
}
private function _createDataSec1C($formatA,$individualName='',$chartName='Column')
{
$htmlCreateData='';
if(empty($individualName ))$individualName='';
if(empty($chartName ))$chartName='Column';
$iN=$individualName;
$cN=$chartName;
$finalN='data'.$cN.$iN;
$vCounter=0;
$htmlCreateData .='var '.$finalN.'=new google.visualization.DataTable(); ';
$htmlCreateData .=$finalN.'.addColumn(\'string\', \'Date\'); ';
$countX=count($formatA);
foreach($formatA as $formatKey=> $formatValue)
{
$countY=count($formatValue);
$name=str_replace(' ','',$formatKey);
$name=str_replace($this->notAllowedStringA, '',$name);
foreach($formatValue as $key=> $value)
{
 $keyR=str_replace($this->notAllowedStringA, '',$key);
 $htmlCreateData .=$finalN.'.addColumn(\'number\', \''.$keyR.'\'); ';
}
  break;
}
$htmlCreateData .=$finalN.'.addRows([
 ';
$counterX=0;
foreach($formatA as $formatKey=> $formatValue)
{
$counterX++;
$name=str_replace(' ','',$formatKey);
$name=str_replace($this->notAllowedStringA, '',$name);
$formatKeyR=str_replace($this->notAllowedStringA, '',$formatKey);
$htmlCreateData .='[\''.$formatKeyR.'\',';
$counterY=0;
foreach($formatValue as $key=> $value)
{
$rev1=0;
$maxFlag=true;
if($individualName=='percentile')
{
  foreach($value as $val)
  {
if(is_array($val))
{
$rev1 +=$val[1];
}
else
{
$rev1 +=0;
}
  }
  if(!(isset($max)))
  {
  $min=$max=$rev1;
  }
  if($min > $rev1)$min=$rev1;  if($max < $rev1)$max=$rev1;
}
$counterY++;
$rev=0;
foreach($value as $k=> $v)
{
if(is_array($v))
{
$rev +=$v[1];
}
else
{
$rev +=0;
}
}
$finalRev=$rev;
if($individualName=='percentile')
{
if($max !=0)
{
$finalRev=(!empty($rev))?$rev * 100 / ($max) : 0;
}else{
$finalRev=0;
}
}
if($counterY==$countY)
{
 $htmlCreateData .=''.$finalRev.']';
}
else
{
$htmlCreateData .=''.$finalRev.',';
}
}
if($counterX==$countX)
{
  $htmlCreateData .=']';
}else{
  $htmlCreateData .=',';
}
}
$htmlCreateData .=');';
return $htmlCreateData;
}
private function _createDataSec2C($formatA)
{
$htmlCreateData='';
$vCounter=0;
foreach($formatA as $formatKey=> $formatValue)
{
$countX=count($formatValue);
$name=str_replace(' ','',$formatKey);
$name=str_replace($this->notAllowedStringA, '',$name);
$htmlCreateData .='var dataColumn'.$name.'=new google.visualization.DataTable(); ';
$htmlCreateData .='dataColumn'.$name.'.addColumn(\'string\', \'Date\'); ';
foreach($formatValue as $key=> $value)
{
$countY=count($value);
foreach($value as $k=> $v)
{
$kR=str_replace($this->notAllowedStringA, '',$k);
$htmlCreateData .='dataColumn'.$name.'.addColumn(\'number\', \''.$kR.'\'); ';
}
break;
}
}
foreach($formatA as $formatKey=> $formatValue)
{
$counterX=0;
$name=str_replace(' ','',$formatKey);
$name=str_replace($this->notAllowedStringA, '',$name);
$htmlCreateData .='dataColumn'.$name.'.addRows([';
foreach($formatValue as $key=> $value)
{
$counterY=0;
$counterX++;
$keyR=str_replace($this->notAllowedStringA, '',$key);
$htmlCreateData .='[\''.$keyR.'\',';
foreach($value as $k=> $v)
{
$counterY++;
if(is_array($v))
{
$rev=$v[1];
}
else
{
$rev=0;
}
if($counterY==$countY)
{
  $htmlCreateData .=''.$rev.'';
}
else
{
  $htmlCreateData .=''.$rev.',';
}
}
if($counterX==$countX)
{
$htmlCreateData .=']';
}
else
{
$htmlCreateData .='],';
}
}
$htmlCreateData .='
 ]);
 ';
}
return $htmlCreateData;
}
private function _chartOptions($formatP,$chartNameA,$zName,$xName,$countY,$axisstyle,$NoX,$donutPie,$zValueName)
{
$htmlChartOptions='';
$width=$this->chartXWidth;
$height=$this->chartYHeight;
$isStacked='true';
$slantedText='true';
$legendPosition='right';
$chartWidth='50%';
$pieHole=0.4;
if(!empty($donutPie))
{
$is3D='false';
}
else
{
$zValueName='';
$is3D='true';
}
$lineWidth=$this->lineChartXWidth;
if($countY==1)
{
$pieSliceText='value';
}else{
$pieSliceText='percentage';
}
if($axisstyle=='combined')
{
    $htmlChartOptions .='
  var options'.$chartNameA[0].'={\'title\':\''.$zValueName.'\',
 \'width\':'.$height.',
  \'is3D\': '.$is3D.',
  \'pieHole\': '.$pieHole.',
  \'pieSliceText\': \''.$pieSliceText.'\',
 \'height\':'.$height.'};
  ';
  if(!$NoX)
  {
  $htmlChartOptions .='
  var options'.$chartNameA[1].'={\'title\':\'\',
 \'width\':'.$width.',
 \'isStacked\': '.$isStacked.',
 hAxis: {slantedText: \''.$slantedText.'\'} ,
 chartArea:{width:\''.$chartWidth.'\'},
 \'height\':'.$height.'};
  ';
  }
 foreach($formatP as $fKey=> $fValue)
 {
  $name=str_replace(' ','',$fKey);
  $name=str_replace($this->notAllowedStringA, '',$name);
  $description='Relationship between '.$zName.' for '.$fKey;
  $description=str_replace($this->notAllowedStringA, '',$description);
  $htmlChartOptions .='
  var options'.$chartNameA[0].$name.'={\'title\':\''.$description.'\',
 \'width\':'.$height.',
 \'is3D\': '.$is3D.',
 \'pieHole\': '.$pieHole.',
 \'height\':'.$height.'};
  ';
  if(!$NoX)
  {
  $htmlChartOptions .='
  var options'.$chartNameA[1].$name.'={\'title\':\''.$description.'\',
 \'width\':'.$width.',
\'isStacked\': '.$isStacked.',
vAxis: {title: \''.$fKey.'\'},
 hAxis: {title: \''.$xName.'\'} ,
 hAxis: {slantedText: \''.$slantedText.'\'} ,
 legend: {position: \''.$legendPosition.'\'} ,
 chartArea:{width:\''.$chartWidth.'\'},
 \'height\':'.$height.'};
 ';
 $htmlChartOptions .='
  var options'.$chartNameA[2].$name.'={\'title\':\'\',
 \'width\':'.$lineWidth.',
vAxis: {title: \''.$fKey.'\'},
 hAxis: {title: \''.$xName.'\'} ,
 hAxis: {slantedText: \''.$slantedText.'\'} ,
 \'pointSize\': 5,
 \'height\':'.$height.'};
 ';
  }
} 
}
else if($axisstyle=='compared')
{
if(!$NoX)
{
$htmlChartOptions .='
  var options'.$chartNameA[2].$this->percentileS.'={\'title\':\'Trending Percentile\',
 \'width\':'.$lineWidth.',
vAxis: {title: \''.$this->percentileS.'\'},
 hAxis: {title: \''.$xName.'\'} ,
 hAxis: {slantedText: \''.$slantedText.'\'} ,
 \'pointSize\': 5,
 \'height\':'.$height.'};
 ';
}
foreach($formatP as $fKey=> $fValue)
 {
  $name=str_replace(' ','',$fKey);
  $name=str_replace($this->notAllowedStringA, '',$name);
  $description='Relationship between '.$zName.' for '.$fKey;
  $description=str_replace($this->notAllowedStringA, '',$description);
  $htmlChartOptions .='
  var options'.$chartNameA[0].$name.'={\'title\':\''.$description.'\',
 \'width\':'.$height.',
 \'is3D\': '.$is3D.',
 \'pieHole\': '.$pieHole.',
 \'height\':'.$height.'};
  ';
  if(!$NoX)
  {
  $htmlChartOptions .='
  var options'.$chartNameA[1].$name.'={\'title\':\''.$description.'\',
 \'width\':'.$width.',
\'isStacked\': '.$isStacked.',
vAxis: {title: \''.$fKey.'\'},
 hAxis: {title: \''.$xName.'\'} ,
 hAxis: {slantedText: \''.$slantedText.'\'} ,
 legend: {position: \''.$legendPosition.'\'} ,
 chartArea:{width:\''.$chartWidth.'\'},
 \'height\':'.$height.'};
 ';
 $htmlChartOptions .='
  var options'.$chartNameA[2].$name.'={\'title\':\'\',
 \'width\':'.$lineWidth.',
vAxis: {title: \''.$fKey.'\'},
 hAxis: {title: \''.$xName.'\'} ,
 hAxis: {slantedText: \''.$slantedText.'\'} ,
 \'pointSize\': 5,
 \'height\':'.$height.'};
 ';
  }
} 
} 
return $htmlChartOptions;
}
private function _createCharts($formatP,$chartNameA,$NoZ,$axisstyle,$countY,$NoX)
{
$htmlCreateChart='';
if($axisstyle=='combined')
{
$htmlCreateChart .='
var '.$chartNameA[0].'=new google.visualization.'.$chartNameA[0].'Chart(document.getElementById(\''.$chartNameA[0].'chart_div\'));
'.$chartNameA[0].'.draw(data'.$chartNameA[0].', options'.$chartNameA[0].');
';
if(!$NoX)
{
$htmlCreateChart .='
var '.$chartNameA[1].'=new google.visualization.'.$chartNameA[1].'Chart(document.getElementById(\''.$chartNameA[1].'chart_div\'));
'.$chartNameA[1].'.draw(data'.$chartNameA[1].', options'.$chartNameA[1].');
';
}
  foreach($formatP as $fKey=> $fValue)
  {
$name=str_replace(' ','',$fKey);
$name=str_replace($this->notAllowedStringA, '',$name);
if(!$NoZ)
{
$htmlCreateChart .='
var '.$chartNameA[0].$name.'=new google.visualization.'.$chartNameA[0].'Chart(document.getElementById(\''.$chartNameA[0].$name.'chart_div\'));
'.$chartNameA[0].$name.'.draw(data'.$chartNameA[0].$name.', options'.$chartNameA[0].$name.');
';
if(!$NoX)
{
$htmlCreateChart .='
var '.$chartNameA[1].$name.'=new google.visualization.'.$chartNameA[1].'Chart(document.getElementById(\''.$chartNameA[1].$name.'chart_div\'));
'.$chartNameA[1].$name.'.draw(data'.$chartNameA[1].$name.', options'.$chartNameA[1].$name.');
';
} 
} 
if(!$NoX)
{
 $htmlCreateChart .='
 var '.$chartNameA[2].$name.'=new google.visualization.'.$chartNameA[2].'Chart(document.getElementById(\''.$chartNameA[2].$name.'chart_div\'));
 '.$chartNameA[2].$name.'.draw(data'.$chartNameA[2].$name.', options'.$chartNameA[2].$name.');
 ';
}  } 
}
else if($axisstyle=='compared')
{
if($countY > 1 && !$NoX)
{
$htmlCreateChart .='
var '.$chartNameA[2].$this->percentileS.'=new google.visualization.'.$chartNameA[2].'Chart(document.getElementById(\''.$chartNameA[2].$this->percentileS.'chart_div\'));
'.$chartNameA[2].$this->percentileS.'.draw(data'.$chartNameA[2].$this->percentileS.', options'.$chartNameA[2].$this->percentileS.');
';
}
  foreach($formatP as $fKey=> $fValue)
  {
$name=str_replace(' ','',$fKey);
$name=str_replace($this->notAllowedStringA, '',$name);
if($NoZ==false)
{
$htmlCreateChart .='
var '.$chartNameA[0].$name.'=new google.visualization.'.$chartNameA[0].'Chart(document.getElementById(\''.$chartNameA[0].$name.'chart_div\'));
'.$chartNameA[0].$name.'.draw(data'.$chartNameA[0].$name.', options'.$chartNameA[0].$name.');
';
if(!$NoX)
{
$htmlCreateChart .='
var '.$chartNameA[1].$name.'=new google.visualization.'.$chartNameA[1].'Chart(document.getElementById(\''.$chartNameA[1].$name.'chart_div\'));
'.$chartNameA[1].$name.'.draw(data'.$chartNameA[1].$name.', options'.$chartNameA[1].$name.');
';
}}
  if(!$NoX)
  {
  $htmlCreateChart .='
  var '.$chartNameA[2].$name.'=new google.visualization.'.$chartNameA[2].'Chart(document.getElementById(\''.$chartNameA[2].$name.'chart_div\'));
  '.$chartNameA[2].$name.'.draw(data'.$chartNameA[2].$name.', options'.$chartNameA[2].$name.');
  ';
  }  } 
}
return $htmlCreateChart;
}
private function _createDivs($formatP,$chartNameA,$axisstyle,$countY,$NoZ,$NoX)
{
$htmlCreateDiv='';
if($axisstyle=='combined')
{
$htmlCreateDiv .=' <table > ';
$htmlCreateDiv .=' <tr> ';
$htmlCreateDiv .='<td>
<div class="googlegraph" id="'.$chartNameA[0].'chart_div" style="width:400; height:300"></div>
</td>
';
if(!$NoX)
{
  $htmlCreateDiv .='<td>
  <div class="googlegraph" id="'.$chartNameA[1].'chart_div" style="width:400; height:300"></div>
  </td>
  ';
}
$htmlCreateDiv .=' </tr> ';
$htmlCreateDiv .=' <tr>  </tr> <tr>
';
if(!$NoX)
{
foreach($formatP as $fKey=> $fValue)
{
$name=str_replace(' ','',$fKey);
$name=str_replace($this->notAllowedStringA, '',$name);
$description=' Individual Graph for  <b> '.$fKey.' </b>';
$description=str_replace($this->notAllowedStringA, '',$description);
$htmlCreateDiv .='<tr><td colspan="2">
<a class="graphLink" href="#" onclick="toggleGraph(\''.$chartNameA[2].$name.'chart_div\', this,\''.$description.'\'); return false;"> Click here to Expand '.$description.'</a>
<div id=\''.$chartNameA[2].$name.'chart_div\' style=\'display: none;\'>
<div class="googlegraph" id=\''.$chartNameA[2].$name.'chart_div\' style="width:400; height:300"></div>
</div>
</td>
</tr>
';
}}
$htmlCreateDiv .='</tr> ';
$htmlCreateDiv .='</table>';
$htmlCreateDiv .='<hr class="graphSeperate">';
foreach($formatP as $fKey=> $fValue)
{
$name=str_replace(' ','',$fKey);
$name=str_replace($this->notAllowedStringA, '',$name);
$htmlCreateDiv .=' <table > ';
$htmlCreateDiv .='<td>
<div class="googlegraph" id="'.$chartNameA[0].$name.'chart_div" style="width:400; height:300"></div>
</td>
';
if(!$NoX)
{
$htmlCreateDiv .='<td>
<div class="googlegraph" id="'.$chartNameA[1].$name.'chart_div" style="width:400; height:300"></div>
</td>
';
}
$htmlCreateDiv .='</table>';
  }
}
else if($axisstyle=='compared')
{
  foreach($formatP as $fKey=> $fValue)
{
$htmlCreateDiv .=' <table > ';
$name=str_replace(' ','',$fKey);
$name=str_replace($this->notAllowedStringA, '',$name);
$htmlCreateDiv .=' <tr > ';
$htmlCreateDiv .='<td>
<div class="googlegraph" id="'.$chartNameA[0].$name.'chart_div"></div>
</td>
';
if(!$NoX)
{
$htmlCreateDiv .='<td>
<div class="googlegraph" id="'.$chartNameA[1].$name.'chart_div"></div>
</td>
';
}
$htmlCreateDiv .=' </tr> ';
$name=str_replace(' ','',$fKey);
$name=str_replace($this->notAllowedStringA, '',$name);
$description=' Individual Graph for  <b> '.$fKey.' </b>';
$description=str_replace($this->notAllowedStringA, '',$description);
if(!$NoX)
{
if($NoZ)
{
$htmlCreateDiv .='<tr><td colspan="2">
<a class="graphLink" href="#" onclick="toggleGraph(\''.$chartNameA[2].$name.'chart_div\', this,\''.$description.'\'); return false;"> Click here to Collapse '.$description.'</a>
<div id=\''.$chartNameA[2].$name.'chart_div\' >
<div class="googlegraph" id=\''.$chartNameA[2].$name.'chart_div\' ></div>
</div>
</td>
</tr>
';
}
else
{
$htmlCreateDiv .='<tr><td colspan="2">
<a class="graphLink" href="#" onclick="toggleGraph(\''.$chartNameA[2].$name.'chart_div\', this,\''.$description.'\'); return false;"> Click here to Expand '.$description.'</a>
<div id=\''.$chartNameA[2].$name.'chart_div\' style=\'display: none;\'>
<div class="googlegraph" id=\''.$chartNameA[2].$name.'chart_div\' ></div>
</div>
</td>
</tr>
';
}}
$htmlCreateDiv .='</table>';
$htmlCreateDiv .='<hr class="graphSeperate">';
}
if($countY > 1 && !$NoX)
 {
$htmlCreateDiv .='<table><tr><td colspan="2">
<div class="googlegraph" id=\''.$chartNameA[2].$this->percentileS.'chart_div\' style="align:center;"></div>
</td>
</tr></table>';
}
} 
return $htmlCreateDiv;
}
private function _createFormatP($dataValues,$dataNames,$noZ)
{
$dataGraph=array();
$data=array();
$dataValuesNew=array();
$dataNameNew=array();
$resultA=array();
foreach($dataNames as $zA)
{
$dataNameA=$zA;
}
$dataName=$dataNameA;
foreach($dataValues as $ndxDataVal=> $valueND)
{
$dataCombined=array();
$oneValue=$valueND ;
$countR=count($oneValue);
for($k=0; $k<$countR; $k++ )
{
if($noZ)
{
  if((!empty($dataName[$k])) && (isset($dataCombined[$dataName[$k]])))
  {
$dataCombined[$dataName[$k].$k]=$dataCombined[$dataName[$k].$k] + $valueND[$k];
  }
  else if((!empty($dataName[$k])) )
  {
$dataCombined[$dataName[$k].$k]=$valueND[$k];
  }
}else{
$name=str_replace($this->notAllowedStringA, '',$dataName[$k]);
  if((!empty($name)) && (isset($dataCombined[$name])))
  {
$dataCombined[$name]=$dataCombined[$name] + $valueND[$k];
  }
  else if((!empty($name)) )
  {
$dataCombined[$name]=$valueND[$k];
  }
}
}
$countC=count($dataCombined);
$counterC=0;
foreach($dataCombined as $cKey=> $cValue)
{
$dataValuesNew[$cKey]=$cValue;
$dataNameNew[$counterC]=$cKey;
$counterC++;
}
$resultA[$ndxDataVal]=$dataValuesNew;
unset($dataCombined);
} 
return $resultA;
}
private function _createFormatC($dataValues,$xLabels,$dataName,$elemetLink,$totalElementA,$section)
{
$dataGraph=array();
$dataValueA=array();
$dc=0;
$dataNameNew=array();
$dataNameA=array();
foreach($xLabels as $xA)
{
$xLabelsA=$xA;
}
foreach($dataName as $zA)
{
$dataNameA=$zA;
}
if(!empty($dataNameA))
{
  $dataNameNew=array_unique($dataNameA);
}
if(!empty($xLabelsA))
{
  $xLabelsNew=array_unique($xLabelsA);
}
$chartCounter=0;
$count=count($xLabels);
$countL=count($xLabelsNew);
$countD=count($dataNameNew);
$dataGraph=$dataValues;
$HTMLoutput='';
$complexA=array();
$complexB=array();
$totalRowA=array();
$complexCounter=0;
$redirectURL=WPage::routeURL($elemetLink,'');
$totalRowA=$this->allDataRowsA;
$dataValCounter=0;
foreach($dataValues as $dataKey=> $dataValue)
{
$dataValCounter++;
$title=$dataKey;
if(!empty($dataValue)){
foreach($dataValue as $val)
{
$dateV=$xLabelsA[$complexCounter];
$typeV=$dataNameA[$complexCounter];
$dateV=str_replace($this->notAllowedStringA, '',$dateV);
$typeV=str_replace($this->notAllowedStringA, '',$typeV);
$revV=$val;
if($section==1)
{
$complexA[$dateV][$dataKey][$typeV]=$val;
}else{
$complexA[$dataKey][$dateV][$typeV]=$val;
}
$complexCounter++;
}
}
$complexCounter=0;
}
$dataValuesNew=array();
$counterDV=0;
if(empty($xLabelsNew)) return false;
foreach($xLabelsNew as $labelNew)
{
foreach($dataNameNew as $dataNew)
{
foreach($dataValues as $dataValueKey=> $dataValueValue)
{
$dataNewString=$dataNew;
$dataNewString=str_replace($this->notAllowedStringA, '',$dataNewString);
if($section==1)
{
$dataValuesNew[$labelNew][$dataValueKey][$dataNewString]=0;
}else{
$dataValuesNew[$dataValueKey][$labelNew][$dataNewString]=0;
}
$counterDV++;
}
}
}
  $result=array_merge_recursive($dataValuesNew,$complexA);
return $result;
}
private function _advanceCharts($dataValues,$xLabels,$dataName,$elemetLink,$totalElementA,$chart)
{
$dataGraph=array();
$dataValueA=array();
$dc=0;
$dataNameNew=array();
$dataNameNew=array_unique($dataName);
$xLabelsNew=array_unique($xLabels);
$chartCounter=0;
$count=count($xLabels);
$countL=count($xLabelsNew);
$countD=count($dataNameNew);
$dataGraph=$dataValues;
$HTMLoutput='';
$complexA=array();
$complexB=array();
$totalRowA=array();
$complexCounter=0;
$redirectURL=WPage::routeURL($elemetLink,'');
$totalRowA=$this->allDataRowsA;
foreach($dataValues as $dataKey=> $dataValue)
{
$title=$dataKey;
$yLabels[]=$dataKey;
foreach($dataValue as $val)
{
$dateV=$xLabels[$complexCounter];
$typeV=$dataName[$complexCounter];
$revV=$val;
$complexA[$typeV][$dateV][$title]=$val;
$complexCounter++;
}
$complexCounter=0;
}
$dataValuesNew=array();
$counterDV=0;
foreach($dataNameNew as $dataNew)
{
foreach($xLabelsNew as $labelNew)
{
foreach($dataValues as $dataValueKey=> $dataValueValue)
{
$dataNewString=$dataNew;
$dataValuesNew[$dataNewString][$labelNew][$dataValueKey]=0;
$counterDV++;
}
}
}
$result=array_merge_recursive($dataValuesNew,$complexA);
$HTMLslider='';
$HTMLcontrol='';
$HTMLbinder='';
$HTMLoutput='
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<title>
Google Visualization API Sample
</title>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load(\'visualization\', \'1.1\', {packages: [\'controls\']});
</script>
<script type="text/javascript">
function drawVisualization(){
// Prepare the data
var data=google.visualization.arrayToDataTable([  ';
$HTMLoutput .=' [ \'3d\', \'1d\',';
$selectedCol='1,';
$countY=count($yLabels);
for($i=0; $i < $countY; $i++)
{
$j=$i+2;
if(($countY - 1)==$i  )
{
$HTMLoutput .=' \''.$yLabels[$i].'\' ';
$selectedCol .=$j;
$HTMLbinder .=$chart.'slider'.$j;
}else{
$HTMLoutput .=' \''.$yLabels[$i].'\',';
$selectedCol .=$j.',';
$HTMLbinder .=$chart.'slider'.$j.',';
}
$HTMLcontrol .='<div id="'.$chart.'sliderControl'.$j.'"></div>';
$HTMLslider .='
  // Define a slider control for the Age column.
  var '.$chart.'slider'.$j.'=new google.visualization.ControlWrapper({
\'controlType\': \'NumberRangeFilter\',
\'containerId\': \''.$chart.'sliderControl'.$j.'\',
\'options\': {
\'filterColumnLabel\': \''.$yLabels[$i].'\',
\'ui\': {\'labelStacking\': \'vertical\'}
}
  }); ';
}
$HTMLoutput .=' ], ';
if(true)
{
$resultCount=count($result);
$resultCounter=0;
$thirdValueCounter=0;
foreach($result as $key=> $value)
{
$insideCount=count($value);
$insideCounter=0;
foreach($value as $oneRowKey=> $oneRowValue)
{
$HTMLoutput .='  [ \' '.$key.' \' ';
$HTMLoutput .=' , \' '.$oneRowKey.' \' ';
foreach($oneRowValue as $finalKey=> $finalValue)
{
  if(is_array($finalValue))
  {
$rev=$finalValue[1];
  }
  else
  {
$rev=0;
  }
  $HTMLoutput .='  , '.$rev.' ';
}
  if(($resultCounter==($resultCount - 1)) && (($insideCounter==($insideCount - 1)) ))
  {
 $HTMLoutput .=' ] ';
  }
  else
  {
 $HTMLoutput .=' ] , ';
}
$insideCounter++;
}
$resultCounter++;
}
}
$HTMLoutput .= ']); ';
$HTMLoutput .=$HTMLslider ;
$HTMLoutput .='
// Define a category picker control for the Gender column
var categoryPicker=new google.visualization.ControlWrapper({
  \'controlType\': \'CategoryFilter\',
  \'containerId\': \'control2\',
  \'options\': {
\'filterColumnLabel\': \'3d\',
\'ui\': {
\'labelStacking\': \'vertical\',
  \'allowTyping\': false,
  \'allowNone\': false,
  \'allowMultiple\': false
}
  }
});
// Define a Pie chart
var pie=new google.visualization.ChartWrapper({
  \'chartType\': \''.$chart.'Chart\',
  \'containerId\': \''.$chart.'\',
  \'options\': {
\'width\': 700,
\'height\': 500,
\'title\': \'\',
\'isStacked\': true
  },
  // Instruct the piechart to use colums 0 (Name) and 3 (Donuts Eaten)
  // from the \'data\' DataTable.
  \'view\': {\'columns\': ['.$selectedCol.']}
});
// Create a dashboard
new google.visualization.Dashboard(document.getElementById(\'dashboard\')).
// Establish bindings, declaring the both the slider and the category
// picker will drive both charts.
bind(['.$HTMLbinder.',categoryPicker], [pie]).
// Draw the entire dashboard.
draw(data);
}
google.setOnLoadCallback(drawVisualization);
</script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
<div id="dashboard">
<table>
<tr style=\'vertical-align: top\'>
  <td style=\'width: 100px;  font-size: 0.9em;\'>
'.$HTMLcontrol.'
<div id="control2"></div>
  </td>
  <td style=\'width: 300px\'>
<div style="float: left;" id="'.$chart.'"></div>
  </td>
</tr>
</table>
</div>
  </body>
</html>
';
return $HTMLoutput;
}
private function _basicCharts($dataValues,$xLabels,$dataName,$elemetLink,$totalElementA,$chart)
{
$dataGraph=array();
$dataValueA=array();
$dc=0;
$dataNameNew=array();
$dataNameNew=array_unique($dataName);
$xLabelsNew=array_unique($xLabels);
$chartCounter=0;
$count=count($xLabels);
$countL=count($xLabelsNew);
$countD=count($dataNameNew);
$dataGraph=$dataValues;
$HTMLoutput='';
$complexA=array();
$complexB=array();
$totalRowA=array();
$complexCounter=0;
$redirectURL=WPage::routeURL($elemetLink,'');
$totalRowA=$this->allDataRowsA;
$dataValCounter=0;
foreach($dataValues as $dataKey=> $dataValue)
{
$dataValCounter++;
$title=$dataKey;
foreach($dataValue as $val)
{
$dateV=$xLabels[$complexCounter];
$typeV=$dataName[$complexCounter];
$revV=$val;
$complexA[$dateV][$typeV]=$val;
$complexCounter++;
}
$complexCounter=0;
$dataValuesNew=array();
$counterDV=0;
foreach($xLabelsNew as $labelNew)
{
foreach($dataNameNew as $dataNew)
{
$dataNewString=$dataNew;
$dataValuesNew[$labelNew][$dataNewString]=0;
$counterDV++;
}
}
$result=array_merge_recursive($dataValuesNew,$complexA);
$HTMLoutput .='
   <html>
  <head>
  <!--Load the AJAX API-->
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
  // Load the Visualization API and the piechart package.
  google.load(\'visualization\', \'1.0\', {\'packages\':[\'corechart\']});
  // Set a callback to run when the Google Visualization API is loaded.
  google.setOnLoadCallback(drawChart);
  // Callback that creates and populates a data table,
  // instantiates the pie chart, passes in the data and
  // draws it.
  function drawChart(){
    ';
$HTMLoutput1='';
$newC=0;
$dataGraph=array();
$dataValueA=array();
$dataValuesNew=array();
$totalValueA=array();
$totalValueCount=0;
$dc=0;
$data=array();
$chartCounter=0;
$count=count($dataName);
$countL=count($xLabels);
$HTMLoutput .=' var data=google.visualization.arrayToDataTable([ ';
$HTMLoutput .=' [ ';
$HTMLoutput .='\'Interval\' ';
$descriptionType='';
foreach($dataNameNew as $name)
{
$totalValueA[$name]=0;
}
$resultCount=count($result);
$resultCounter=0;
$HTMLoutputBefore='';
foreach($result as $key=> $value)
{
$HTMLoutputBefore .='  [ \' '.$key.' \' ';
foreach($value as $oneRowKey=> $oneRowValue)
{
if(is_array($oneRowValue))
{
 $rev=$oneRowValue[1];
}else{
  $rev=0;
}
$totalValueA[$oneRowKey] +=$rev ;
$HTMLoutputBefore .='  , '.$rev.' ';
}
if($resultCounter==($resultCount - 1))
{
$HTMLoutputBefore .=' ] ';
}
else
{
$HTMLoutputBefore .=' ] , ';
}
$resultCounter++;
}
reset($this->allDataRowsA );
$row=current($this->allDataRowsA );
foreach($dataNameNew as $name)
{
$this->cellValue=$totalValueA[$name];
$colTotalValue=$this->callListingElement($totalElementA, $row, 'total');
  $HTMLoutput .=' , \' '.$name.' '.$colTotalValue.' \'  ';
  }
$HTMLoutput .=' ] ,  ';
$HTMLoutput .=$HTMLoutputBefore ;
$HTMLoutput .=' ]); ';
 foreach($totalValueA as $total)
{
$totalValueCount +=$total;
}
$this->cellValue=$totalValueCount;
$totalValueS=$this->callListingElement($totalElementA, $row, 'total');
$HTMLoutput .='  var options={title: \' Total '.$title.' : '.$totalValueS.'\' ,
vAxis: {title: \''.$title.'\'},
  hAxis: {title: \'Period\'} ,
  tooltip: { trigger: \'focus\'  }
  };
  ';
if($chart=='Column')
{
$HTMLoutput .='var chart=new google.visualization.ColumnChart(document.getElementById(\'chart_div'.$dataValCounter.'\'))';
}
else if($chart=='Area')
{
$HTMLoutput .='var chart=new google.visualization.AreaChart(document.getElementById(\'chart_div'.$dataValCounter.'\'))';
}
else if($chart=='Line')
{
$HTMLoutput .='var chart=new google.visualization.LineChart(document.getElementById(\'chart_div'.$dataValCounter.'\'))';
}
else if($chart=='Bar')
{
$HTMLoutput .='var chart=new google.visualization.BarChart(document.getElementById(\'chart_div'.$dataValCounter.'\'))';
}
$HTMLoutput .='
 chart.draw(data, options);
 chart.setAction
(
{
id: \'colChart\',
text: \'Click here for Details\',
action: function()
{
window.location=\''.$redirectURL.'\';
}
});
}
</script>
  </head>
  <body>
<div class="googlegraph" id="chart_div'.$dataValCounter.'" style="width: 900px; height: 500px;"></div>
  </body>
</html>';
} 
return $HTMLoutput;
}
 private function _percentileData($dataValues)
 {
 $newC=0;
 $dataValueA=array();
$dataValuesNew=array();
foreach($dataValues as $ndxDataVal=> $dataValue)
{
$min=$max=$dataValue[0];
$dataValueA=$dataValue;
foreach($dataValue as $val)
{
if($min > $val)$min=$val;if($max < $val)$max=$val;}
foreach($dataValue as $myValue )
{
if($max !=0)
{
$dataValuesNew[$ndxDataVal][]=(!empty($myValue))?$myValue * 100 / ($max) : 0;
}else{
$dataValuesNew[$ndxDataVal][]=0;
}
}
$newC++;
}
return $dataValuesNew;
 }
private function _lineGraph($dataValues,$xLabels,$dataName){
$arraySize=sizeof($dataValues);
$html='';
if($arraySize > 1){
$html .=$this->_createOneLineGraphNew($dataValues, $xLabels  ,$dataName);}
$html .=$this->_createMiniLineGraphNew($dataValues, $xLabels );
return $html;
}
private function _createOneLineGraphNew($dataValues,$xLabels,$dataName)
{
$dataGraph=array();
$dataValueA=array();
$dataValuesNew=array();
$dc=0;
$data=array();
$chartCounter=0;
$count=count($dataValues);
$countL=count($xLabels);
$dataGraph=$dataValues;
$HTMLoutput='
   <html>
  <head>
  <!--Load the AJAX API-->
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
  // Load the Visualization API and the piechart package.
  google.load(\'visualization\', \'1.0\', {\'packages\':[\'corechart\']});
  // Set a callback to run when the Google Visualization API is loaded.
  google.setOnLoadCallback(drawChart);
  // Callback that creates and populates a data table,
  // instantiates the pie chart, passes in the data and
  // draws it.
  function drawChart(){
    ';
$HTMLoutput1='';
$newC=0;
foreach($dataValues as $ndxDataVal=> $dataValue)
{
$min=$max=$dataValue[0];
$dataValueA=$dataValue;
foreach($dataValue as $val){
if($min > $val)$min=$val;if($max < $val)$max=$val;}
$HTMLoutput1 .='min : '.$min.' max : '.$max.' <br />';
foreach($dataValue as $myValue){
$HTMLoutput1 .=' - ';
if($max !=0)
{
$dataValuesNew[$newC][]=(!empty($myValue))?$myValue * 100 / ($max) : 0;
}else{
$dataValuesNew[$newC][]=0;
}
}$HTMLoutput1 .='<br />';
$newC++;
}
$HTMLoutput .=' var data=google.visualization.arrayToDataTable([ ';
$HTMLoutput .=' [ ';
$HTMLoutput .='\'Interval\' ';
for($i=0; $i < $count; $i++)
{
$titleV=key($dataGraph);
$HTMLoutput .=' , \' '.$titleV.'\'  ';
next($dataGraph);
}
$HTMLoutput .=' ] ,  ';
for($i=0; $i < $countL; $i++)
{
$HTMLoutput .='  [ \' '.$xLabels[$i].' \' ';
foreach($dataValuesNew as $oneData=> $dataValue)
{
$HTMLoutput .=','.$dataValue[$i];
}
if($i==($countL - 1))
{
$HTMLoutput .=' ] ';
}
else
{
$HTMLoutput .=' ] , ';
}
}
$HTMLoutput .=' ]); ';
$HTMLoutput .='  var options={title: \'Trending Percentile\' , \'width\':'.$this->lineChartXWidth.',\'height\':'.$this->chartYHeight.'  }; ';
$HTMLoutput .='var chart=new google.visualization.LineChart(document.getElementById(\'chart_div\'));chart.draw(data, options);
}
</script>
  </head>
  <body>
<div class="googlegraph" id="chart_div"></div>
  </body>
</html>';
return $HTMLoutput;
}
private function _createMiniLineGraphNew($dataValues,$xLabels){
$index=0;
$column=1;$ctr=2;
 $dataGraph=array();
$htmlOutput='<tr>';
$hexIndex=0;
$chartCounter=0;
$countChart=0;
$finalChart=0;
$dataValues=array_reverse($dataValues);
$countChart=count($dataValues);
$HTMLoutput='
   <html>
  <head>
  <!--Load the AJAX API-->
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
  // Load the Visualization API and the piechart package.
  google.load(\'visualization\', \'1.0\', {\'packages\':[\'corechart\']});
  // Set a callback to run when the Google Visualization API is loaded.
  google.setOnLoadCallback(drawChart);
  // Callback that creates and populates a data table,
  // instantiates the pie chart, passes in the data and
  // draws it.
  function drawChart(){
    ';
$dataGraph=$dataValues ;
foreach($dataValues as  $dataValue)
{
$HTMLoutput .='var data'.$chartCounter.'=google.visualization.arrayToDataTable([ ';
$countDataValue=count($dataValue);
$HTMLoutput .=' [ \'Interval\' , \''.key($dataGraph).'\' ] ,  ';
$index=0;
foreach($dataValue as $val)
{
if($countDataValue==($index+1))
{
$HTMLoutput .=' [ \''.$xLabels[$index].'\','.$val.' ]]); ';
}else{
$HTMLoutput .=' [ \''.$xLabels[$index].'\','.$val.' ] ,  ';
}
$index++;
}
$HTMLoutput .=' var options'.$chartCounter.'={  title: \''.key($dataGraph).'\' ,  \'pointSize\': 5, \'width\':'.$this->lineChartXWidth.',\'height\':'.$this->chartYHeight.'}; ';
next($dataGraph);
$chartCounter++;
}
 for($i=0; $i<$countChart; $i++ )
  {
$HTMLoutput .='  var indvLineChart'.$i.'=new google.visualization.LineChart(document.getElementById(\'indvLineChart_div'.$i.'\'));indvLineChart'.$i.'.draw(data'.$i.', options'.$i.');';
  }
$HTMLoutput .=' }
</script>
  </head> ';
  $HTMLoutput .='<body> ';
   for($j=0; $j<$countChart; $j++ )
   {
   $HTMLoutput .='<div id="indvLineChart_div'.$j.'" style="width: 900px; height: 500px;"></div> ';
   }
  $HTMLoutput .='
  </body> ';
  $HTMLoutput .=' </html>';
 return $HTMLoutput;
 }
private function _pieChartNew($dataValues,$dataName,$htmlScript,$width=500,$height=500,$graph3axe,$elemetLink)
{
  $dataGraph=array();
$data=array();
$width=$width + 200;
$height=$height + 200;
$dataValues=array_reverse($dataValues);
$dataCombined=array();
$dataValuesNew=array();
$dataNameNew=array();
if($graph3axe==1)
{
foreach($dataValues as $ndxDataVal=> $valueND)
{
$oneValue=$valueND ;
$countR=count($oneValue);
for($k=0; $k<$countR; $k++ )
{
if((!empty($dataName[$k])) && (isset($dataCombined[$dataName[$k]])))
{
  $dataCombined[$dataName[$k]]=$dataCombined[$dataName[$k]] + $oneValue[$k];
}
else if((!empty($dataName[$k])) )
{
  $dataCombined[$dataName[$k]]=$oneValue[$k];
}
}
$countC=count($dataCombined);
$counterC=0;
foreach($dataCombined as $cKey=> $cValue)
{
$dataValuesNew[$counterC]=$cValue;
$dataNameNew[$counterC]=$cKey;
$counterC++;
}
$dataValues[$ndxDataVal]=$dataValuesNew;
$dataName=$dataNameNew;
} } 
$HTMLoutput='
   <html>
  <head>
  <!--Load the AJAX API-->
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
  // Load the Visualization API and the piechart package.
  google.load(\'visualization\', \'1.0\', {\'packages\':[\'corechart\']});
  // Set a callback to run when the Google Visualization API is loaded.
  google.setOnLoadCallback(drawChart);
  // Callback that creates and populates a data table,
  // instantiates the pie chart, passes in the data and
  // draws it.
  function drawChart(){
    ';
 $chartCounter=0;
foreach($dataValues as $ndxDataVal=> $valueND)
{
$dataGraph=$valueND;
  $HTMLoutput .='
  // Set chart options
  var options'.$chartCounter.'={\'title\':\''.$ndxDataVal.' \',
  \'width\':'.$width.',
  \'height\':'.$height.'};
  ';
  $HTMLoutput .='
    // Create the data table.
  var data'.$chartCounter.'=new google.visualization.DataTable();
  data'.$chartCounter.'.addColumn(\'string\', \'Topping\');
  data'.$chartCounter.'.addColumn(\'number\', \'Slices\');
  data'.$chartCounter.'.addRows([
    ';
$index=0;
foreach($dataName as $name )
  {
  $HTMLoutput .='
 [\''.$name.'\','.$dataGraph[$index].'],
 ';
  $index++;
}
$HTMLoutput .='
  ]);
  ';
$chartCounter++;
}
for($j=0; $j<$chartCounter; $j++ )
  {
$HTMLoutput .='
  // Instantiate and draw our chart, passing in some options.
  var chart'.$j.'=new google.visualization.PieChart(document.getElementById(\'chart_div'.$j.'\'));
  chart'.$j.'.draw(data'.$j.', options'.$j.');';
}
  $HTMLoutput .='
 }
 </script>
 ';
$HTMLoutput .=' </head>  <body> ';
  for($i=0; $i<$chartCounter; $i++ )
  {
$HTMLoutput .='
<!--Div that will hold the pie chart-->
<div class="googlegraph" id="chart_div'.$i.'"></div> ';
  }
$HTMLoutput .=' </body> </html>  ';
return  $HTMLoutput;
 } 
}