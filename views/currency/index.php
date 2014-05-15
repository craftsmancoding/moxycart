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
    <a href="<?php print static::url('currency','create'); ?>" class="btn">Add Currency</a>
    <form action="<?php print $data['baseurl']; ?>" method="get">
        <input type="hidden" name="a" value="<?php print $a; ?>" />
        <input type="hidden" name="class" value="currency" />
        <input type="text" name="name:LIKE" placeholder="Search..." />    
        <input type="submit" value="Filter"/>
    </form>
</div>
<?php if ($data['results']): ?>
<table>
    <thead>
        <tr>
            <th>
                <a href="<?php print toggle('code',$data['baseurl']); ?>">Code</a>
            </th>
            <th>
                <a href="<?php print toggle('name',$data['baseurl']); ?>">Name</a>
            </th>
            <th>
                <a href="<?php print toggle('symbol',$data['baseurl']); ?>">Symbol</a>
            </th>
            <th>
                <a href="<?php print toggle('is_active',$data['baseurl']); ?>">Active?</a>
            </th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($data['results'] as $r) :?>
    <tr>
        <td><?php print $r->get('code'); ?></td>
        <td><?php print $r->get('name'); ?></td>
        <td>&#<?php print $r->get('symbol'); ?>;</td>
        <td>
            <?php if($r->get('is_active')): ?>
                <span style="color:green;">Active</span>
            <?php else: ?>
                <span style="color:red;">Inactive</span>
            <?php endif; ?>
        </td>
        <td>
            <a href="<?php print static::url('currency','edit',array('currency_id'=>$r->get('currency_id'))); ?>" class="btn">Edit</a> 
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <p>Sorry, no currencies were found.</p>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
$offset = (int) (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $offset, $results_per_page)
    ->setBaseUrl($data['baseurl']);
?>
