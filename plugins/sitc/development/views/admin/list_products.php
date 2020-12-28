<table class="table table-striped dataTable">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">SKU</th>
            <th scope="col">Name</th>
            <th scope="col">IMG</th>
            <th scope="col">Stock</th>
            <th scope="col">Price</th>
        </tr>
    </thead>
    <tbody><?php
    foreach($products as $product):
    $url = $product['id']; ?>
        <tr>
            <td><a href="<?=$url;?>"><?=$product['product_id'];?></a></td>
            <td><a href="<?=$url;?>"><?=$product['sku'];?></a></td>
            <td><a href="<?=$url;?>"><?=$product['name'];?></a></td>
            <td><img src="<?=$product['img_url'];?>"></td>
            <td><a href="<?=$url;?>"><?=$product['stock'];?></a></td>
            <td><a href="<?=$url;?>"><?=$product['price'];?></a></td>
        </tr><?php
    endforeach; ?>
    </tbody>
</table>