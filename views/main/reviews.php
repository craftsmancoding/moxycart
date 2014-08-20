<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Manage Reviews</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p></p></div>

<div class="moxycart_canvas_inner">

<!--div>
    <a href="<?php print static::url('optiontype','create'); ?>" class="btn moxycart-btn">Add Option Type</a>
    <form action="<?php print $data['baseurl']; ?>" method="get">
        <input type="text" name="label:LIKE" class="input input-half" placeholder="Search..." />    
        <input type="submit" class="btn" value="Filter"/>
    </form>
</div-->
<?php if ($data['results']): ?>
<table class="classy">
    <thead>
        <tr>
            <th>
                Name
            </th>
            <th>
                Email
            </th>
            <th>
                Rating
            </th>
            <th>State</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($data['results'] as $r) :?>
    <tr>
        <td><?php print $r->get('name'); ?></td>
        <td><?php print $r->get('email'); ?></td>
        <td><?php print $r->get('rating'); ?></td>
        <td><?php print $r->get('state'); ?></td>
        <td>
            <span class="button btn btn-mini" onclick="javascript:paint('reviewedit',{review_id:<?php print $r->get('review_id'); ?>});">Edit</span>
            <span class="button btn btn-mini" onclick="javascript:mapi('review','delete',{review_id:<?php print $r->get('review_id'); ?>},'reviews');">Delete</span>
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <p>There are no product reviews yet.</p>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
/*
$offset = (int) (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $offset, $results_per_page)
    ->setBaseUrl($data['baseurl']);
*/
?>

</div>