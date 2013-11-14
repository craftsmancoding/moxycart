<?php
$specs = array();

$specs[] = array(
    'name' => 'Weight (lb)',
    'description' => 'Weight of the product by itself',
    'group' => 'Default',
    'seq' => 0,
    'type' => 'text'
);
$specs[] = array(
    'name' => 'Shipping Weight (lb)',
    'description' => 'Weight used for shipping calculations',
    'group' => 'Default',
    'seq' => 1,
    'type' => 'text'
);
$specs[] = array(
    'name' => 'Length (in)',
    'description' => 'Length of the product in inches',
    'group' => 'Default',
    'seq' => 1,
    'type' => 'text'
);
$specs[] = array(
    'name' => 'Watts (W)',
    'description' => 'Wattage',
    'group' => 'Electric',
    'seq' => 1,
    'type' => 'text'
);

return $specs;
/*EOF*/