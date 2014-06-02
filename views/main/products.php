<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading" id="moxycart_pagetitle">Manage Products</h2>
</div>

<div class="moxycart_canvas_inner">

<div>
    <a href="<?php print static::page('productcreate'); ?>" class="button btn moxycart-btn">Add Product</a>
    <!--span class="btn" onclick="javascript:paint('productinventory');">Manage Inventory</span-->
            
    <!--form action="<?php print $data['baseurl']; ?>">
        <input type="text" name="name:LIKE" placeholder="Search..." />    
        <input type="submit" value="Filter"/>
    </form-->
</div>
<?php if ($data['results']): ?>
<table class="classy">
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
        <td>
            <!--span class="button btn" onclick="javascript:paint('productedit',{product_id:<?php print $r->get('product_id'); ?>});">Edit</span-->
             <a href="<?php print static::page('productedit',array('product_id'=>$r->get('product_id'))); ?>" class="button btn">Edit</a>
             <a href="<?php print static::page('productpreview',array('product_id'=>$r->get('product_id'))); ?>" class="btn" target="_blank">Preview</a></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <p>Sorry, no products were found.</p>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));

print \Pagination\Pager::links($data['count'], $data['offset'], $results_per_page)
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
</div>