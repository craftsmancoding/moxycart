<?php
/*
<field key="vterm_id" dbtype="int" precision="11" phptype="integer" null="false" index="pk"  generated="native" />
<field key="vtype_id" dbtype="int" precision="11" phptype="integer" null="false"/>
<field key="name" dbtype="varchar" precision="255" phptype="string" null="false" />
<field key="sku_prefix" dbtype="varchar" precision="16" phptype="string" null="false" />
<field key="sku_suffix" dbtype="varchar" precision="16" phptype="string" null="false" />
<field key="seq" dbtype="tinyint" precision="3" phptype="integer" null="true" />

<index alias="PRIMARY" name="PRIMARY" primary="true" unique="true">
	<column key="vterm_id" collation="A" null="false" />
</index>

*/
$variation_terms = array();

$variation_terms[] = array(
    'vtype_id' => 1,
    'name' => 'Black',
    'sku_prefix' => '',
    'sku_suffix' => '-BLK',
    'seq' => 0
);
$variation_terms[] = array(
    'vtype_id' => 1,
    'name' => 'Blue',
    'sku_prefix' => '',
    'sku_suffix' => '-BLU',
    'seq' => 1
);
$variation_terms[] = array(
    'vtype_id' => 1,
    'name' => 'Grey',
    'sku_prefix' => '',
    'sku_suffix' => '-GRA',
    'seq' => 2
);
$variation_terms[] = array(
    'vtype_id' => 1,
    'name' => 'Green',
    'sku_prefix' => '',
    'sku_suffix' => '-GRN',
    'seq' => 3
);
$variation_terms[] = array(
    'vtype_id' => 1,
    'name' => 'Red',
    'sku_prefix' => '',
    'sku_suffix' => '-RED',
    'seq' => 4
);


$variation_terms[] = array(
    'vtype_id' => 2,
    'name' => 'Small',
    'sku_prefix' => '',
    'sku_suffix' => '-S',
    'seq' => 0
);
$variation_terms[] = array(
    'vtype_id' => 2,
    'name' => 'Medium',
    'sku_prefix' => '',
    'sku_suffix' => '-M',
    'seq' => 1
);
$variation_terms[] = array(
    'vtype_id' => 2,
    'name' => 'Large',
    'sku_prefix' => '',
    'sku_suffix' => '-L',
    'seq' => 2
);

$variation_terms[] = array(
    'vtype_id' => 3,
    'name' => 'V-Neck',
    'sku_prefix' => '',
    'sku_suffix' => '-V',
    'seq' => 0
);
$variation_terms[] = array(
    'vtype_id' => 3,
    'name' => 'Regular',
    'sku_prefix' => '',
    'sku_suffix' => '-R',
    'seq' => 0
);

return $variation_terms;
/*EOF*/