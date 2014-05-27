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

<div>
    <a href="<?php print static::url('optiontype','create'); ?>" class="btn">Add Option Type</a>
    <form action="<?php print $data['baseurl']; ?>" method="get">
        <input type="hidden" name="a" value="<?php print $a; ?>" />
        <input type="hidden" name="class" value="optiontype" />
        <input type="text" name="label:LIKE" placeholder="Search..." />    
        <input type="submit" value="Filter"/>
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
                <a href="<?php print toggle('name',$data['baseurl']); ?>">Name</a>
            </th>
            <th>
                <a href="<?php print toggle('description',$data['baseurl']); ?>">Description</a>
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
            <a href="<?php print static::url('optiontype','edit',array('otype_id'=>$r->get('otype_id'))); ?>" class="btn">Edit</a> 
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <p>You have not created any option types yet.</p>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
$offset = (int) (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $offset, $results_per_page)
    ->setBaseUrl($data['baseurl']);
?>


<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>