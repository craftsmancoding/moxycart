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
/*
    $col = $r->toArray('',false,false,true);
    $col = $r->flattenArray($col);
//        $col = $this->modx->toPlaceholders($col);
        if ($r->get('asset_id')) {
        
        print_r($col); exit;
        }
*/
?>
    <tr>
        <?php 
        // Configurable columns
        foreach($data['columns'] as $k => $v): ?>
            <td><?php 
                print $r[$k];
                //print $r->get($k); 
            
            ?></td>
        <?php endforeach; ?>

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
print \Pagination\Pager::links($data['count'], $data['offset'], $results_per_page, $data['baseurl']);

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