
<div class="clearfix">
    <a class="btn" href="<?php print static::url('page','productcreate',array('store_id'=>$data['store_id'])); ?>">Add Product</a> 
    <!--a href="<?php print static::page('productcreate'); ?>" class="button btn moxycart-btn pull-left">Add Product</a-->
    <!--span class="btn" onclick="javascript:paint('productinventory');">Manage Inventory</span-->
        <div class="pull-right">   
                <input type="text" name="searchterm" id="searchterm" placeholder="Search..." value="<?php print htmlentities($data['searchterm']); ?>"/>    
                <span class="button btn moxycart-btn" onclick="javascript:get_products(0);">Search</span>
                <span class="btn" onclick="javascript:show_all_products();">Show All</span>
        </div>

</div>


<?php if ($data['results']): ?>
<table class="classy products-tbl" style="width: 100%; margin-top: 10px;">
    <thead>
        <tr>
            <?php 
            // Configurable columns
            foreach($data['columns'] as $k => $v): ?>
                <th><?php print $v; ?></th>
            <?php endforeach; ?>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($data['results'] as $r) :

//    print '<pre>'; print_r($r); print '</pre>'; exit;
?>
    <tr>
        <?php 
        // Configurable columns
        foreach($data['columns'] as $k => $v): ?>
            <td><?php print $r[$k]; ?></td>
        <?php endforeach; ?>
        
        <td><a href="<?php print static::url('page','productedit',array('product_id'=>$r['product_id'])); ?>" class="btn">Edit</a> <a href="<?php print static::url('page','productpreview',array('product_id'=>$r['product_id'])); ?>" class="btn">Preview</a></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <p>Sorry, no products were found.</p>

<?php endif; ?>

<?php 
print \Pagination\Pager::links($data['count'], $data['offset'], $data['results_per_page'])
    ->setBaseUrl($data['baseurl'])
    ->setTpls(
        array(
            'first' => '<span onclick="javascript:get_products([+offset+]);" class="linklike">&laquo; First</span>  ',
            'last' => ' <span onclick="javascript:get_products([+offset+]);" class="linklike">Last &raquo;</span>',
            'prev' => '<span onclick="javascript:get_products([+offset+]);" class="linklike">&lsaquo; Prev.</span> ',
            'next' => ' <span onclick="javascript:get_products([+offset+]);" class="linklike">Next &rsaquo;</span>',
            'current' => ' <span>[+page_number+]</span> ',
            'page' => ' <span onclick="javascript:get_products([+offset+]);" class="linklike">[+page_number+]</span> ',
            'outer' => '
                <style>
                    span.linklike { cursor: pointer; }
                    span.linklike:hover { color:blue; text-decoration:underline; }
                </style>
                <div id="pagination">[+content+]<br/>
    				<div class="page-count">Page [+current_page+] of [+page_count+]</div>
    				<div class="displaying-page">Displaying records [+first_record+] thru [+last_record+] of [+record_count+]</div>
    			</div>',
    	)
    );
?>
