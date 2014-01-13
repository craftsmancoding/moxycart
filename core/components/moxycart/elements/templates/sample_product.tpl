<!-- 
@templatename Sample Product
@description A sample Moxycart template for formatting a single product.
-->
<html>
<head>
<title>[[++site_name]] - [[+name]]</title>
<base href="[[++site_url]]" />
</head>
<body>
<h1>[[+name]]</h1>

<p>[[+description]]</p>

<h2>Buy now for only $[[+calculated_price]]</h2>

<p>[[+content]]</p>


<!-- form example -->
<form action="https://[[++moxycart.domain]]/cart" method="post" accept-charset="utf-8">
    <input type="text" name="quantity" value="[[+qty_min]]" />
    <input type="hidden" name="quantity_min" value="[[+qty_min]]" />
    <input type="hidden" name="quantity_max" value="[[+qty_max]]" />
    <input type="hidden" name="name" value="[[+name]]" />
    <input type="hidden" name="price" value="[[+calculated_price]]" />
    <input type="hidden" name="code" value="[[+product_id]]" />
    <input type="hidden" name="url" value="[[++site_url]][[+url]]" />
    <input type="hidden" name="category" value="[[+category]]" />
    
    <input type="submit" value="Add a [[+name]]" class="submit" />
    
</form>


</body>
</html>