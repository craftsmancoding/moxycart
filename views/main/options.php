<?php
$a = (int) $_GET['a'];
print $this->getMsg();
?>

<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Manage Product Options</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Product Options define ways by which your product can vary, e.g. by size or by color.  These options will show up on the front-end of your site so your visitors can make a selection about which particular version of the product they want to buy.</p></div>

<div class="moxycart_canvas_inner">


<div class="clearfix">
     <span class="btn moxycart-btn pull-left" onclick="javascript:paint('optioncreate');">Add Option</span>


        <div class="pull-right">   
            <form action="<?php print static::page('options'); ?>" method="post">
                <input type="text" name="searchterm" placeholder="Search..." value="<?php print $data['searchterm']; ?>"/>    
                <input type="submit" class="button btn moxycart-btn" value="Search"/>
                <a href="<?php print static::page('options'); ?>" class="btn">Show All</a>
            </form>
            
        </div>
   </div>  
<?php if ($data['results']): ?>
<form action="<?php print static::page('options'); ?>" method="post">
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
    <tbody class="ui-sortable">
<?php foreach ($data['results'] as $r) :?>
    <tr>
        <td>
            <input type="hidden" name="seq[]" value="<?php print $r->get('otype_id'); ?>" />
            <?php print $r->get('slug'); ?>
        </td>
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
<br>
<input type="submit" class="button btn" value="Save Order"/>
</form>
<?php else: ?>

    <div class="danger">You have not created any product options yet.</div>

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