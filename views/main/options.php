<?php
$a = (int) $_GET['a'];
print $this->getMsg();
?>

<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Manage Product Options</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Product Options are types of variations in your product such as "size" or "color".  They will show up on the front-end of your site so your visitors can make a selection about which particular version of the product they want to buy.</p></div>

<div class="moxycart_canvas_inner">


<div>

    <span class="btn moxycart-btn" onclick="javascript:paint('optioncreate');">Add Option Type</span>
    <!--form action="<?php print $data['baseurl']; ?>" method="get">
        <input type="hidden" name="a" value="<?php print $a; ?>" />
        <input type="hidden" name="class" value="optiontype" />
        <input type="text" name="label:LIKE" class="input input-half" placeholder="Search..." />    
        <input type="submit" class="btn" value="Filter"/>
    </form-->
</div>
<?php if ($data['results']): ?>
<table class="classy">
    <thead>
        <tr>
            <th>
                Slug
            </th>
            <th>
                Name
            </th>
            <th>
                Description
            </th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($data['results'] as $r) :?>
    <tr>
        <td><?php print $r->get('slug'); ?></td>
        <td><?php print $r->get('name'); ?></td>
        <td><?php print $r->get('description'); ?></td>
        <td>
            <span class="button btn" onclick="javascript:paint('optionedit',{otype_id:<?php print $r->get('otype_id'); ?>});">Edit</span>
            <span class="button btn" onclick="javascript:paint('optionterms',{otype_id:<?php print $r->get('otype_id'); ?>});">Manage Terms</span>
            <span class="button btn" onclick="javascript:mapi('optiontype','delete',{otype_id:<?php print $r->get('otype_id'); ?>},function(response){ paint('options'); });">Delete</span>     
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <div class="danger">You have not created any option types yet.</div>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/update.class.php
/*
$offset = (int) (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $offset, $results_per_page)
    ->setBaseUrl($data['baseurl']);
*/
?>

</div>