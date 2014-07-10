<?php
print $this->getMsg();
?>

<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">View Orders</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Moxycart Order List</p></div>

<div class="moxycart_canvas_inner">



<?php if ($data['results']): ?>
<table class="classy">
    <thead>
        <tr>
            <th>
                Customer Name
            </th>
            <th>
                Email
            </th>
            <th>
                Total Amount
            </th>
            <th>
                Order Date
            </th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($data['results'] as $o) :?>
    <tr>
        <td><?php print $o->get('customer_first_name') . ' ' . $o->get('customer_last_name'); ?></td>
        <td><?php print $o->get('customer_email'); ?></td>
        <td><?php print number_format($o->get('order_total'), 2, '.', ''); ?></td>
        <td><?php print $o->get('transaction_date'); ?>
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <div class="danger">No Orders Found.</div>

<?php endif; ?>

<?php 
// Pagination : see the get_data function in the controllers/store/upudate.class.php
$tpls = include 'pagination_tpls.php';
$results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
print \Pagination\Pager::links($data['count'], $data['offset'], $results_per_page, $data['baseurl'])->setTpls($tpls);
?>

</div>
