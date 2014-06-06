<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading" id="moxycart_pagetitle">Manage Products</h2>
</div>

<div class="moxycart_canvas_inner">

<div>
    <a href="<?php print static::page('productcreate'); ?>" class="button btn moxycart-btn">Add Product</a>
    <!--span class="btn" onclick="javascript:paint('productinventory');">Manage Inventory</span-->
            
    <form action="<?php print static::page('products'); ?>" method="post">
        <input type="text" name="searchterm" placeholder="Search..." value="<?php print $data['searchterm']; ?>"/>    
        <input type="submit" value="Search"/>
    </form>
    <a href="<?php print static::page('products'); ?>" class="btn">Show All</a>
</div>
<?php if ($data['results']): ?>
<table class="classy">
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

        <td>
            <!--span class="button btn" onclick="javascript:paint('productedit',{product_id:<?php print $r['product_id']; ?>});">Edit</span-->
             <a href="<?php print static::page('productedit',array('product_id'=>$r['product_id'])); ?>" class="button btn">Edit</a>
             <a href="<?php print static::page('productpreview',array('product_id'=>$r['product_id'])); ?>" class="btn" target="_blank">Preview</a></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <p>Sorry, no products were found.</p>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
$tpls = include 'pagination_tpls.php';
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $data['offset'], $results_per_page, $data['baseurl'])->setTpls($tpls);


/*
print '<pre>';
print 'Count: '.$data['count'].'<br/>';
print 'Offset: '.$data['offset'].'<br/>';
print 'Results per page: '. $results_per_page.'<br/>';
print 'Print baseurl: '.$data['baseurl'].'<br/>';
print '</pre>';
*/
?>
</div>