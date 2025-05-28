<span style="float: left;vertical-align: bottom;" class="paging">
<?php
$results = array();
foreach ((array) $paginationOptions as $option) {
    if ($paginationLimit == $option) {
        $results[] = $html->link($option, '', array('style'=>'font-weight:normal;','class'=>'white_button disabled'));;
    } else {
        $args = $this->passedArgs;
        $args['Paginate'] = $option;
        $results[] = $html->link($option, $args, array('class'=>'white_button'));
    }
}
echo implode(" ", $results);
?>
</span>