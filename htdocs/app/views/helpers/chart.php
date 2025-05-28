<?php

class ChartHelper extends AppHelper {
    
    function pie($id,$title,$data,$width=580,$height=500) {
        if(Configure::read('chart')=='native'){
            return $this->nativePie($id, $title, $data);
        } else {
            return $this->googlePie($id, $title, $data, $width, $height);
        }
    }

    function column($id,$title,$columnTitle,$data,$width=580,$height=500) {
        if(Configure::read('chart')=='native'){
            return $this->nativeColumn($id, $title, $columnTitle, $data);
        } else {
            return $this->googleColumn($id, $title, $columnTitle, $data, $width, $height);
        }
    }
    
    function doubleColumn($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$limit=false,$width=580,$height=500) {
        if(Configure::read('chart')=='native'){
            return $this->nativeDoubleColumn($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$limit);
        } else {
            return $this->googleDoubleColumn($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$limit,$width,$height);
        }
    }
    
    function area($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$width=580,$height=500) {
        if(Configure::read('chart')=='native'){
            return $this->nativeArea($id,$title,$s1Title,$s1Data,$s2Title,$s2Data);
        } else {
            return $this->googleArea($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$width,$height);
        }
    }
    
    function line($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$width=580,$height=500) {
        if(Configure::read('chart')=='native'){
            return $this->nativeLine($id,$title,$s1Title,$s1Data,$s2Title,$s2Data);
        } else {
            return $this->googleArea($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$width,$height);
        }
    }
    
    function lineMany($id, $title, $charts)
    {
        $dataseries = $categories = array();
        foreach($charts as $chart) {
            $categories = array_merge( $categories, $chart['data'] );
        }
        $categories = array_unique(Set::classicExtract($categories,'{n}.k'));
        
        usort($categories, array("ChartHelper", "_cmp"));
        $i=0;
        foreach ($categories as $i=>$cat) {
            foreach($charts as $k=>$chart) {
                $res = Set::extract('/data[k='.$cat.']/value', $chart);
                $dataseries[$k][$cat] = empty($res)? 0 : $res[0];
            }
        }
        
        foreach($dataseries as $i=>&$data){
            $data = array(
                'name' => $charts[$i]['title'], 
                'data' => $data );
        }
        
        return $this->nativeLineMany($id,$title,$dataseries);
    }
    
    

    function nativePie($id,$title,$data) {
        return '
<script type="text/javascript">
//<![CDATA[
    $(function(){
        jeeb.Pie("'.$id.'","'.$title.'",'.$this->pieData($data).');
    });
//]]>
</script>
            ';
    }
    
    function googlePie($id,$title,$data,$width,$height) {
        return '           
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn("string","'.$title.'");
        data.addColumn("number","مقدار");
        data.addRows('.$this->pieData($data).');
        var chart = new google.visualization.PieChart(document.getElementById("'.$id.'"));
        var options={width:'.$width.', height:'.$height.', title:"'.$title.'", colors:["#5E8BC0","#C35F5C","#A2BE67","#80699B","#3D96AE","#F49D56","#92A8CD","#A47D7C"]};
        chart.draw(data, options);
    }
</script>
        ';
    }
    
    function nativeColumn($id,$title,$columnTitle,$data){
        return '
<script type="text/javascript">
//<![CDATA[
$(function(){
    columnData=[
            {
                name: "'.$columnTitle.'",
                data: '.$this->formatData($data).'
            }        
    ];
    categoriesData='.$this->toJsArray(Set::classicExtract($data,'{n}.k')).';
    jeeb.Column("'.$id.'","'.$title.'", "مبلغ به ریال", categoriesData, columnData);
});
//]]>
</script>            
        ';
    }

    function googleColumn($id,$title,$columnTitle,$data,$width,$height) {
        return '
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn("string", "ستون");
        data.addColumn("number","'.$columnTitle.'");
        data.addRows('.$this->formatData($data).');
        var chart = new google.visualization.ColumnChart(document.getElementById("'.$id.'"));
        chart.draw(data, {
            title: "'.$title.'",
            width: '.$width.', 
            height: '.$height.',
            colors:["#5E8BC0","#C35F5C","#A2BE67","#80699B","#3D96AE","#F49D56","#92A8CD","#A47D7C"],
            //vAxis: {title: "مبلغ به ریال"}
        });
    }
</script>            
        ';
    }

    function nativeDoubleColumn($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$limit=false) {
        $categories=array_unique(Set::classicExtract(array_merge($s1Data, $s2Data),'{n}.k'));
        usort($categories, array("ChartHelper", "_cmp"));
        if($limit AND count($categories)>$limit) {
            $categories=array_slice($categories, count($categories)-$limit, $limit);
        }
        $i=0;
        foreach ($categories as $entry) {
            $serie1Data[$i]=0;
            $serie2Data[$i]=0;
            foreach ($s1Data as $entry2) {
                if($entry2['k']==$entry) {
                    $serie1Data[$i]=$entry2['value'];
                }
            }
            foreach ($s2Data as $entry2) {
                if($entry2['k']==$entry) {
                    $serie2Data[$i]=$entry2['value'];
                }
            }
            $i++;
        }
        return '
<script type="text/javascript">
//<![CDATA[
$(function(){
    columnData=[
            {
                name: "'.$s1Title.'",
                data: '.$this->toJsArray($serie1Data).'
            },
            {
                name: "'.$s2Title.'",
                data: '.$this->toJsArray($serie2Data).'
            }            
    ];
    categoriesData='.$this->toJsArray($categories).';
    jeeb.Column("'.$id.'","'.$title.'", "مبلغ به ریال", categoriesData, columnData);
});
//]]>
</script>            
        ';
    }

    function googleDoubleColumn($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$limit=false,$width,$height) {
        $categories=array_unique(Set::classicExtract(array_merge($s1Data, $s2Data),'{n}.k'));
        usort($categories, array("ChartHelper", "_cmp"));
        if($limit AND count($categories)>$limit) {
            $categories=array_slice($categories, count($categories)-$limit, $limit);
        }
        $i=0;
        $serieData=array();
        foreach ($categories as $entry) {
            $serieData[$i]=array($entry,0,0);
            foreach ($s1Data as $entry2) {
                if($entry2['k']==$entry) {
                    $serieData[$i][1]=$entry2['value'];
                }
            }
            foreach ($s2Data as $entry2) {
                if($entry2['k']==$entry) {
                    $serieData[$i][2]=$entry2['value'];
                }
            }
            $i++;
        }
        $data="[";
        foreach ($serieData as $entry) {
            $data.="['".$entry[0]."',".$entry[1].",".$entry[2]."],";
        }
        $data=rtrim($data,",");
        $data.="]";
        return '
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn("string", "ستون");
        data.addColumn("number","'.$s1Title.'");
        data.addColumn("number","'.$s2Title.'");
        data.addRows('.$data.');
        var chart = new google.visualization.ColumnChart(document.getElementById("'.$id.'"));
        chart.draw(data, {title: "'.$title.'" ,width: '.$width.', height: '.$height.'});
        chart.draw(data, {title: "'.$title.'" ,width: '.$width.', height: '.$height.', colors:["#5E8BC0","#C35F5C","#A2BE67","#80699B","#3D96AE","#F49D56","#92A8CD","#A47D7C"]});
    }
</script>            
        ';        
    }

    function nativeArea($id,$title,$s1Title,$s1Data,$s2Title,$s2Data) {
        $categories=array_unique(Set::classicExtract(array_merge($s1Data, $s2Data),'{n}.k'));
        usort($categories, array("ChartHelper", "_cmp"));
        $i=0;
        foreach ($categories as $entry) {
            $serie1Data[$i]=0;
            $serie2Data[$i]=0;
            foreach ($s1Data as $entry2) {
                if($entry2['k']==$entry) {
                    $serie1Data[$i]=$entry2['value'];
                }
            }
            foreach ($s2Data as $entry2) {
                if($entry2['k']==$entry) {
                    $serie2Data[$i]=$entry2['value'];
                }
            }
            $i++;
        }
        return '
<script type="text/javascript">
//<![CDATA[
$(function(){
    areaData=[
            {
                name: "'.$s1Title.'",
                data: '.$this->toJsArray($serie1Data).'
            },
            {
                name: "'.$s2Title.'",
                data: '.$this->toJsArray($serie2Data).'
            }            
    ];
    categoriesData='.$this->toJsArray($categories).';
    jeeb.Area("'.$id.'","'.$title.'", "مبلغ به ریال", categoriesData, areaData);
});
//]]>
</script>            
        ';
    }

    function googleArea($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$width,$height) {
        $categories=array_unique(Set::classicExtract(array_merge($s1Data, $s2Data),'{n}.k'));
        usort($categories, array("ChartHelper", "_cmp"));
        $i=0;
        $serieData=array();
        foreach ($categories as $entry) {
            $serieData[$i]=array($entry,0,0);
            foreach ($s1Data as $entry2) {
                if($entry2['k']==$entry) {
                    $serieData[$i][1]=$entry2['value'];
                }
            }
            if(!empty ($s2Data)) {
                foreach ($s2Data as $entry2) {
                    if($entry2['k']==$entry) {
                        $serieData[$i][2]=$entry2['value'];
                    }
                }
            }
            $i++;
        }
        $data="[";
        foreach ($serieData as $entry) {
            if(!empty ($s2Data)) {
                $data.="['".$entry[0]."',".$entry[1].",".$entry[2]."],";
            } else {
                $data.="['".$entry[0]."',".$entry[1]."],";
            }
        }
        $data=rtrim($data,",");
        $data.="]";
        $script='
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn("string", "ستون");;
        data.addColumn("number","'.$s1Title.'");';
        if(!empty ($s2Data)) {
            $script.='data.addColumn("number","'.$s2Title.'");';
        }
        $script.='
        data.addRows('.$data.');
        var chart = new google.visualization.AreaChart(document.getElementById("'.$id.'"));
        chart.draw(data, {title: "'.$title.'" ,width: '.$width.', height: '.$height.', colors:["#5E8BC0","#C35F5C","#A2BE67","#80699B","#3D96AE","#F49D56","#92A8CD","#A47D7C"]});
    }
</script>            
        ';
        return $script;;
    }
    
    function nativeLine($id,$title,$s1Title,$s1Data,$s2Title,$s2Data) {
        $categories=array_unique(Set::classicExtract(array_merge($s1Data, $s2Data),'{n}.k'));
        usort($categories, array("ChartHelper", "_cmp"));
        $i=0;
        foreach ($categories as $entry) {
            $serie1Data[$i]=0;
            $serie2Data[$i]=0;
            foreach ($s1Data as $entry2) {
                if($entry2['k']==$entry) {
                    $serie1Data[$i]=$entry2['value'];
                }
            }
            if(!empty ($s2Data)) {
                foreach ($s2Data as $entry2) {
                    if($entry2['k']==$entry) {
                        $serie2Data[$i]=$entry2['value'];
                    }
                }
            }
            $i++;
        }
        $script='
<script type="text/javascript">
//<![CDATA[
$(function(){
    lineData=[
            {
                name: "'.$s1Title.'",
                data: '.$this->toJsArray($serie1Data).'
            },';
        if(!empty ($s2Data)) {        
            $script.='
                {
                    name: "'.$s2Title.'",
                    data: '.$this->toJsArray($serie2Data).'
                }';
        }
        $script.='
    ];
    categoriesData='.$this->toJsArray($categories).';
    jeeb.Line("'.$id.'","'.$title.'", "مبلغ به ریال", categoriesData, lineData);
});
//]]>
</script>            
        ';
        return $script;
    }
    
    /**
     * $charts = array(
     *  array( 'title' => string, 'data'=>array(key=>val) )
     * )
     * @param string $id
     * @param string $title
     * @param array $charts
     * @return string
     */
    function nativeLineMany($id,$title,$charts) {
        
        $categories = array();
        foreach($charts as &$chart) {
            $categories = array_merge( $categories , array_keys($chart['data']) );
            $chart['data'] = array_values( $chart['data'] );
            array_walk($chart['data'], function(&$val) { $val = intval($val); } );
        }
        $categories = array_unique($categories);
        
        $script='
<script type="text/javascript">
//<![CDATA[
$(function(){
    lineData=
            ' . json_encode($charts) .' ;
    categoriesData='.$this->toJsArray($categories).';
    jeeb.Line("'.$id.'","'.$title.'", "مبلغ به ریال", categoriesData, lineData);
});
//]]>
</script>            
        ';
        return $script;
    }
    
    function barChart($id, $title, $charts)
    {
        $categories = array_keys($charts); // y axis
        $data = $subcategories = array(); // bars
        foreach($charts as &$chart) {
            $subcategories = array_merge( $subcategories , array_keys($chart) );
            array_walk($chart, function(&$val) { $val = intval($val); } );
        }
        
        $subcategories = array_unique($subcategories);
        
        foreach($subcategories as $k=>$cat) {
            $data[$k] = array(
                'name' => $cat,
                'data' => array()
            );
            foreach($charts as &$chart) {
                $data[$k]['data'][] = isset($chart[$cat])? $chart[$cat] : 0;
            }
        }
        
        $script='
<script type="text/javascript">
//<![CDATA[
$(function(){
    barData=
            ' . json_encode($data) .' ;
    categoriesData='.$this->toJsArray($categories).';
    jeeb.BarChart("'.$id.'","'.$title.'", "مبلغ به ریال", categoriesData, barData);
});
//]]>
</script>            
        ';
        return $script;
    }

    function googleLine($id,$title,$s1Title,$s1Data,$s2Title,$s2Data,$width,$height) {
        $categories=array_unique(Set::classicExtract(array_merge($s1Data, $s2Data),'{n}.k'));
        usort($categories, array("ChartHelper", "_cmp"));
        $i=0;
        $serieData=array();
        foreach ($categories as $entry) {
            $serieData[$i]=array($entry,0,0);
            foreach ($s1Data as $entry2) {
                if($entry2['k']==$entry) {
                    $serieData[$i][1]=$entry2['value'];
                }
            }
            if(!empty ($s2Data)) {
                foreach ($s2Data as $entry2) {
                    if($entry2['k']==$entry) {
                        $serieData[$i][2]=$entry2['value'];
                    }
                }
            }
            $i++;
        }
        $data="[";
        foreach ($serieData as $entry) {
            if(!empty ($s2Data)) {
                $data.="['".$entry[0]."',".$entry[1].",".$entry[2]."],";
            } else {
                $data.="['".$entry[0]."',".$entry[1]."],";
            }
        }
        $data=rtrim($data,",");
        $data.="]";
        $script='
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn("string", "ستون");
        data.addColumn("number","'.$s1Title.'");';
        if(!empty ($s2Data)) {
            $script.='data.addColumn("number","'.$s2Title.'");';
        }
        $script.='data.addRows('.$data.');
        var chart = new google.visualization.LineChart(document.getElementById("'.$id.'"));
        chart.draw(data, {title: "'.$title.'" ,width: '.$width.', height: '.$height.', colors:["#5E8BC0","#C35F5C","#A2BE67","#80699B","#3D96AE","#F49D56","#92A8CD","#A47D7C"]});
    }
</script>            
        ';
        return $script;
    }
    
    function formatData(&$data) {
        $temp='[';
        foreach ($data as $entry){
            $temp.="['$entry[k]',".round($entry['value'],1)."],";
        }
        $temp=rtrim($temp,",");
        $temp.=']';
        return $temp;
    }
    
    function pieData(&$data) {
        $temp='[';
        foreach ($data as $entry){
            $temp.="['$entry[key]',".round($entry['value'],1)."],";
        }
        $temp=rtrim($temp,",");
        $temp.=']';
        return $temp;
    }
    
    function lineData(&$data) {
        $temp='[';
        foreach ($data as $entry){
            $temp.=$entry[0]['amount'].",";
        }
        $temp=rtrim($temp,",");
        $temp.=']';
        return $temp;         
    }
    
    function columnData(&$data) {
        $temp='[';
        foreach ($data as $entry){
            $temp.=$entry[0]['amount'].",";
        }
        $temp=rtrim($temp,",");
        $temp.=']';
        return $temp;         
    }
    
    function lineCategories(&$data) {
        $temp='[';
        foreach ($data as $entry){
            $temp.="'".$entry[0]['node']."',";
        }
        $temp=rtrim($temp,",");
        $temp.=']';
        return $temp;        
    }
    
    function columnCategories(&$data) {
        $tempData=array();
        foreach ($data as $entry) {
            $tempData[]=$entry['k'];
        }
        $tempData=array_unique($tempData);
        sort($tempData);
        
        $temp='[';
        foreach ($tempData as $entry){
            $temp.="'".$entry."',";
        }
        $temp=rtrim($temp,",");
        $temp.=']';
        return $temp;        
    }
    
    function toJsArray(&$data) {
        $temp='[';
        foreach ($data as $entry){
            if(is_numeric($entry)) {
                $temp.=$entry.",";
            } else {
                $temp.="'".$entry."',";
            }
        }
        $temp=rtrim($temp,",");
        $temp.=']';
        return $temp;        
    }
    
    function _cmp($a,$b) {
        if ($a == $b) {
            return 0;
        }
        $a=explode("/", $a);
        $b=explode("/", $b);
        if($a[0]!=$b[0]) {
            return ($a[0] < $b[0]) ? -1 : 1;
        } else {
            return ($a[1] < $b[1]) ? -1 : 1;
        }
    }
    
}
?>