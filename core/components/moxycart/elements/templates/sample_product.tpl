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
<form action="https://danieledano.foxycart.com/cart" method="post" accept-charset="utf-8">
<input type="hidden" name="name" value="[[+name]]" />
<input type="hidden" name="price" value="[[+calculated_price]]" />
<input type="hidden" name="code" value="[[+product_id]]" />

<input type="submit" value="Add a [[+name]]" class="submit" />
</form>




</body>
</html>