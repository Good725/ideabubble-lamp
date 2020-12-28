<h1>Enquiry Form</h1>
<table border="0">
    <tr><td>Name:</td><td><?=@$form['name'];?></td></tr>
    <tr><td>Phone:</td><td><?=@$form['phone'];?></td></tr>
    <tr><td>Email:</td><td><?=@$form['email'];?></td></tr>
    <tr><td>Product:</td><td><a href="<?=URL::site();?>products.html/<?=@$form['enquiry'];?>"><?=@$form['enquiry'];?></a></td></tr>
    <tr><td>Message:</td><td><?=@$form['message'];?></td></tr>
</table>