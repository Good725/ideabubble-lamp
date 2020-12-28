<?php
/**
 * Created by JetBrains PhpStorm.
 * Author: dale@ideabubble.ie
 * Date: 03/02/2014
 * Time: 16:13
 */
?>
<div class="row">
    <div class="span12">
        <div class="page-header clearfix">
            <?=(isset($alert)) ? $alert : '';?>
            <h2 class="plugin_title"><img class="plugin_icon" src="" />Extrabubble - Customers</h2>
            <div class="pull-left"><a href='<?=URL::site()?>admin/extra/view_customer'>Add Customer</a></div>
        </div>
    </div>
</div>
<table class='table table-striped dataTable'>
    <thead>
    <tr>
        <th>ID</th>
        <th>Company</th>
        <th>Main Contact</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>County</th>
        <th>Last Login</th>
        <th>Successful Logins</th>
        <th>Attempted Logins</th>
        <th>Delete</th>
        <th>Last Modified</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 1;
    foreach ($customers as $customer => $details): ?>
        <tr>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?=$details['id'];?></a></td>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?=$details['company_title'];?></a></td>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?=$details['contact'];?></a></td>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?=$details['email'];?></a></td>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?=$details['phone'];?></a></td>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?=$details['address1'];?></a></td>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?=$details['county'];?></a></td>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?= ( ! empty($details['last_login'])) ? date('Y-m-d H:i:s', $details['last_login']) : '' ;?></a></td>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?=$details['logins'] + 0;?></a></td>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?= $details['logins'] + $details['logins_fail'] ?></a></td>
            <td id="publish_<?=$details['id']?>" class="delete" data-id="<?=$details['id'];?>">X</td>
            <td><a href='<?=URL::Site('admin/extra/view_customer/'.$details['id']);?>'><?=$details['date_modified'];?></a></td>

        </tr>
        <?php
        ++$i;
    endforeach;?>
    </tbody>
</table>