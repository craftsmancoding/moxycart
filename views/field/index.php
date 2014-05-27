<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

<h2 class="moxycart_cmp_heading">Manage Custom Fields</h2>

<?php
$a = (int) $_GET['a'];
print $this->getMsg();
?>

<div>
    <a href="<?php print static::url('field','create'); ?>" class="btn">Add Custom Field</a>
    <form action="<?php print $data['baseurl']; ?>" method="get">
        <input type="hidden" name="a" value="<?php print $a; ?>" />
        <input type="hidden" name="class" value="field" />
        <input type="text" name="label:LIKE" placeholder="Search..." />    
        <input type="submit" value="Filter"/>
    </form>
</div>
<?php if ($data['results']): ?>
<table>
    <thead>
        <tr>
            <th>
                <a href="<?php print self::toggle('slug',$data['baseurl']); ?>">Slug</a>
            </th>
            <th>
                <a href="<?php print self::toggle('label',$data['baseurl']); ?>">Label</a>
            </th>
            <th>
                <a href="<?php print self::toggle('type',$data['baseurl']); ?>">Type</a>
            </th>
            <th>
                <a href="<?php print self::toggle('group',$data['baseurl']); ?>">Group</a>
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

    <p>You have not created any custom fields yet.</p>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/update.class.php
$offset = (int) (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $offset, $results_per_page)
    ->setBaseUrl($data['baseurl']);
?>

<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>