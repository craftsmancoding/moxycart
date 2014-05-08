<div>
    <span class="btn">Add Product</span> 
    <span class="btn">Manage Inventory</span>
    <input type="text" placeholder="Search..." />
    <span class="btn">Filter</span>
</div>
<?php if ($data['results']): ?>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>SKU</th>
            <th>Foxycart Category</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($data['results'] as $r) :?>
    <tr>
        <td><?php print $r->get('name'); ?></td>
        <td><?php print $r->get('sku'); ?></td>
        <td><?php print $r->get('category'); ?></td>
        <td><a href="<?php print static::url('product','edit',array('product_id'=>$r->get('product_id'))); ?>" class="btn">Edit</a> <a href="<?php print static::url('product','preview',array('product_id'=>$r->get('product_id'))); ?>" class="btn">Preview</a></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <p>Sorry, no products were found.</p>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
$offset = (int) (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $offset, $results_per_page)
    ->setBaseUrl($data['baseurl'])
    ->setTpls(
        array(
            'first' => '<span onclick="javascript:get_data([+offset+]);" class="linklike">&laquo; First</span>  ',
            'last' => ' <span onclick="javascript:get_data([+offset+]);" class="linklike">Last &raquo;</span>',
            'prev' => '<span onclick="javascript:get_data([+offset+]);" class="linklike">&lsaquo; Prev.</span> ',
            'next' => ' <span onclick="javascript:get_data([+offset+]);" class="linklike">Next &rsaquo;</span>',
            'current' => ' <span>[+page_number+]</span> ',
            'page' => ' <span onclick="javascript:get_data([+offset+]);" class="linklike">[+page_number+]</span> ',
            'outer' => '
                <style>
                    span.linklike { cursor: pointer; }
                    span.linklike:hover { color:blue; text-decoration:underline; }
                </style>
                <div id="pagination">[+content+]<br/>
    				Page [+current_page+] of [+page_count+]<br/>
    				Displaying records [+first_record+] thru [+last_record+] of [+record_count+]
    			</div>',
    	)
    );
?>
