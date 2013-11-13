<html>
<head>
<title>[[++site_name]] - [[*pagetitle]]</title>
<base href="[[++site_url]]" />
</head>
<body>
<h1>Welcome to my Stores</h1>

<!-- form example -->
<form action="https://danieledano.foxycart.com/cart" method="post" accept-charset="utf-8">
<input type="hidden" name="name" value="[[+moxycart.name]]" />
<input type="hidden" name="price" value="[[+moxycart.calculated_price]]" />
<input type="hidden" name="code" value="[[+moxycart.product_id]]-[[+moxycart.sku]]" />

<input type="submit" name="Add a [[+moxycart.name]]" value="Add a [[+moxycart.name]]" class="submit" />
</form>

[[+moxycart.content]]


</body>
</html>