<?php include dirname(dirname(__FILE__)).'/header.php';  ?>
<?php
/**
 * Toggle sorting dir ASC|DESC for a given $column
 *
 */
function toggle($column,$base_url='?') {
    if (isset($_GET['sort']) && $_GET['sort'] == $column) {
        if (isset($_GET['dir']) && $_GET['dir'] == 'ASC') {
            return $base_url . '&sort='.$column.'&dir=DESC';
        }
    }
    return $base_url . '&sort='.$column.'&dir=ASC';
}
$a = (int) $_GET['a'];

print $this->getMsg();
?>
<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Manage Assets</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Here you can Manage your Products Assets.</p></div>

<div class="moxycart_canvas_inner">

<div>
    <a href="<?php print static::url('field','create'); ?>" class="btn moxycart-btn">Add Custom Field</a>
    <form action="<?php print $data['baseurl']; ?>" method="get">
        <input type="hidden" name="a" value="<?php print $a; ?>" />
        <input type="hidden" name="class" value="field" />
        <input type="text" class="input input-half" name="label:LIKE" placeholder="Search..." />    
        <input type="submit" value="Filter" class="btn"/>
    </form>
</div>
<?php if ($data['results']): ?>
<table>
    <thead>
        <tr>
            <th>
                <a href="<?php print toggle('slug',$data['baseurl']); ?>">Slug</a>
            </th>
            <th>
                <a href="<?php print toggle('label',$data['baseurl']); ?>">Label</a>
            </th>
            <th>
                <a href="<?php print toggle('type',$data['baseurl']); ?>">Type</a>
            </th>
            <th>
                <a href="<?php print toggle('group',$data['baseurl']); ?>">Group</a>
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
            <a href="<?php print static::url('field','edit',array('field_id'=>$r->get('field_id'))); ?>" class="btn">Edit</a> 
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <div class="danger">You have not created any custom fields yet.</div>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
$offset = (int) (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $offset, $results_per_page)
    ->setBaseUrl($data['baseurl']);
?>
</div>

<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>