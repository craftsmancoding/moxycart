<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading" id="moxycart_pagetitle">Manage Custom Fields</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Custom Field fields allow you to enter extra data about your product.  The values stored can be displayed to visitors via your product templates.</p></div>

<div class="moxycart_canvas_inner">

<div>
    <span class="button btn moxycart-btn" onclick="javascript:paint('fieldcreate');">Add Custom Field</span>
    <!--form id="searchform">
        <input type="text" class="input input-half" name="label:LIKE" placeholder="Search..." />    
        <span class="button btn" onclick="javascript:searchform('searchform','fields');">Filter</span>
        <span class="button btn" onclick="javascript:paint('fields');">Show All</span>
    </form-->
</div>
<?php if ($data['results']): ?>
<table class="classy">
    <thead>
        <tr>
            <th>
                <!--a href="<?php print self::toggle('slug',$data['baseurl']); ?>">Slug</a-->
                Slug
            </th>
            <th>
                <!--a href="<?php print self::toggle('label',$data['baseurl']); ?>">Label</a-->
                Label
            </th>
            <th>
                <!--a href="<?php print self::toggle('type',$data['baseurl']); ?>">Type</a-->
                Type
            </th>
            <th>
                <!--a href="<?php print self::toggle('group',$data['baseurl']); ?>">Group</a-->
                Group
            </th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($data['results'] as $r) :?>
    <tr>
        <td><?php print $r->get('slug'); ?></td>
        <td><?php print $r->get('label'); ?></td>
        <td><?php print $r->get('type'); ?></td>
        <td><?php print $r->get('group'); ?>
        </td>
        <td>
            <span class="button btn" onclick="javascript:paint('fieldedit',{field_id:<?php print $r->get('field_id'); ?>});">Edit</span>
            <span class="button btn" onclick="javascript:mapi('field','delete',{field_id:<?php print $r->get('field_id'); ?>},function(response){ paint('fields'); });">Delete</span>
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <p>You have not created any custom fields yet.</p>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/update.class.php
$offset = (int) (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $offset, $results_per_page)
    ->setBaseUrl($data['baseurl']);
?>
</div>