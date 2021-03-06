<div id="moxycart_msg"></div>

    <div class="clearfix">
        <h2 class="moxycart_cmp_heading pull-left" id="moxycart_pagetitle">Manage Products</h2>
        <div class="pull-right">
              <a class="btn btn-primary" href="<?php print static::url('page','productcreate',array('store_id'=>$data['store_id'])); ?>">Add Product</a> 
        &nbsp;
        <span class="btn btn-moxycart" onclick="javascript:open_inventory_modal(<?php print $data['store_id']; ?>);">Quick Edit</span>
        </div>
    </div>

<div class="search-form-wrap">
        <input type="text" name="searchterm" id="searchterm" class="search-input2" placeholder="Search..." value="<?php print htmlentities($data['searchterm']); ?>"/>    
    <span class="button btn moxycart-btn" onclick="javascript:get_products(0);">Search</span>
    <span class="btn" onclick="javascript:show_all_products();">Show All</span>
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
<?php foreach ($data['results'] as $r) : ?>
    <tr>
        <?php 
        // Configurable columns
        foreach($data['columns'] as $k => $v): ?>
            <td><?php print $r[$k]; ?></td>
        <?php endforeach; ?>
        
        <td><a href="<?php print static::url('page','productedit',array('product_id'=>$r['product_id'])); ?>" class="btn btn-mini btn-info">Edit</a> <a href="<?php print static::url('page','productpreview',array('product_id'=>$r['product_id'])); ?>" class="btn btn-mini">Preview</a></td>
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
