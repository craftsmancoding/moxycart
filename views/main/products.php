<div class="moxycart_canvas_inner clearfix">
    <h2 class="moxycart_cmp_heading pull-left" id="moxycart_pagetitle">Manage Products</h2>
    <div class="pull-right">
         <a href="<?php print static::page('productcreate'); ?>" class="button btn moxycart-btn btn-primary">Add Product</a>
        &nbsp;
        <span class="btn btn-moxycart" onclick="javascript:open_inventory_modal(0);">Quick Edit</span>
    </div>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
<p>Here you can Manage Products, Add Edit and Preview</p>
</div>

 
    <form action="<?php print static::page('products'); ?>" method="post" class="search-product">
        <input type="text" name="searchterm" placeholder="Search..." class="search-input" value="<?php print $data['searchterm']; ?>"/>    
        <input type="submit" class="button btn moxycart-btn" value="Search"/>
        <a href="<?php print static::page('products'); ?>" class="btn">Show All</a>
    </form>

<div class="moxycart_canvas_inner moxycart_canvas_inner-products">

<?php if ($data['results']): ?>
<table class="classy products-tbl">
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
             <a href="<?php print static::page('productedit',array('product_id'=>$r['product_id'])); ?>" class="button btn btn-mini btn-info">Edit</a>
             <a href="<?php print static::page('productpreview',array('product_id'=>$r['product_id'])); ?>" class="btn btn-mini" target="_blank">Preview</a></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <div class="danger">Sorry, no products were found.</div>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
$tpls = include 'pagination_tpls.php';
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $data['offset'], $results_per_page, $data['baseurl'])->setTpls($tpls);
?>
</div>