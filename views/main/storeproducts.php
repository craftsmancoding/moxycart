<div>
    <a class="btn" href="<?php print static::url('page','productcreate',array('store_id'=>$data['store_id'])); ?>">Add Product</a> 
    <!--a class="btn" href="<?php print static::url('page','inventory',array('store_id'=>$data['store_id'])); ?>">Manage Inventory</a-->
        
    <!--form action="">
        <input type="text" name="name:LIKE" placeholder="Search..." />    
        <input type="submit" value="Filter"/>
    </form-->
</div>
<?php if ($data['results']): ?>
<table class="classy" style="width: 100%; margin-top: 10px;">
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
<?php foreach ($data['results'] as $r) :?>
    <tr>
        <?php 
        // Configurable columns
        foreach($data['columns'] as $k => $v): ?>
            <td><?php print $r->get($k); ?></td>
        <?php endforeach; ?>
        
        <td><a href="<?php print static::url('page','productedit',array('product_id'=>$r->get('product_id'))); ?>" class="btn">Edit</a> <a href="<?php print static::url('page','productpreview',array('product_id'=>$r->get('product_id'))); ?>" class="btn">Preview</a></td>
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
    				<div class="page-count">Page [+current_page+] of [+page_count+]</div>
    				<div class="displaying-page">Displaying records [+first_record+] thru [+last_record+] of [+record_count+]</div>
    			</div>',
    	)
    );
?>
