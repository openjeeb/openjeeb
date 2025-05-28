<?php

class ChartComponent extends Object {

    function formatPieData(&$data,$model) {
        $tempData = array();
        $i = 0;
        foreach ($data as $entry) {
            $tempData[$i]['key'] = $entry[$model]['k'];
            $tempData[$i]['value'] = $entry[0]['value'];
            $i++;
        }
        return $tempData;
    }

}

?>
