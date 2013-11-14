<?php
$variation_types = array();

$variation_types[] = array(
    'vtype_id' => 1,
    'name' => 'Color',
    'description' => 'Choose from our array of colors!',
    'seq' => 0
);
$variation_types[] = array(
    'vtype_id' => 2,
    'name' => 'Size',
    'description' => 'Find the size that fits you!',
    'seq' => 1
);
$variation_types[] = array(
    'vtype_id' => 3,
    'name' => 'Style',
    'description' => 'Choose from our custom cuts.',
    'seq' => 2
);

return $variation_types;
/*EOF*/