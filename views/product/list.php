<div>
    <span class="btn">Add Product</span> 
    <span class="btn">Manage Inventory</span>
    <input type="text" placeholder="Search..." />
    <span class="btn">Filter</span>
</div>
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
        <td><a href="" class="btn">Edit</a> <a href="" class="btn">Preview</a></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<div>
Pagination Links here
</div>