<!--  
@name MoxycartProductFullReview
@description Format Full Review Form
-->
<p>[[+moxy.review_success_msg]]</p>
<p>[[+moxy.review_error_msg]]</p>
<form action="[[++site_url]][[+uri]]" method="post">
	<label for="name">Name: </label>
	<input type="text" name="name" id="name" /><br>
	<label for="email">Email: </label>
	<input type="text" name="email" id="email" /><br>
	<label for="product_name">Product Name: </label>
	<input type="text" name="product_name" id="product_name" value="[[+name]]"/><br>
	<label for="rating">Rating: </label>
	<input type="radio" name="rating" value="1" checked="checked">1
	<input type="radio" name="rating" value="2">2
	<input type="radio" name="rating" value="3">3
	<input type="radio" name="rating" value="4">4
	<input type="radio" name="rating" value="5">5<br>
	<label for="content">Message: </label>
	<textarea name="content" id="content" cols="30" rows="10"></textarea><br>
	<input type="submit" name="submit" value="Submit">

</form>