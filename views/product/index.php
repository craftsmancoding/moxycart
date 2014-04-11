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
<div>
<?php print $data['pagination_links']; ?>
</div>