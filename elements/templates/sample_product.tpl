<!-- 
@templatename Sample Product
@description A sample Moxycart template for formatting a single product.
For Product Image we used cycle plugin
http://jquery.malsup.com/cycle/
You can use whatever plugins you like, just include it on your product template or add a comma seprated paths for &js_paths=`` or &css_paths=`` on getProductImages
-->

<!-- 
@templatename Sample Product
@description A sample Moxycart template for formatting a single product.
For Product Image we used cycle plugin
http://jquery.malsup.com/cycle/
You can use whatever plugins you like, just include it on your product template or add a comma seprated paths for &js_paths=`` or &css_paths=`` on getProductImages
-->
<!-- 
@templatename Sample Product
@description A sample Moxycart template for formatting a single product.
For Product Image we used cycle plugin
http://jquery.malsup.com/cycle/
You can use whatever plugins you like, just include it on your product template or add a comma seprated paths for &js_paths=`` or &css_paths=`` on getProductImages
-->

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content=" ">
  <meta name="author" content=" ">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

  <title>[[*pagetitle]] - Moxycart Sample Store</title>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="[[++moxycart.assets_url]]css/templates/stylesheet.css">


</head>
<body>

<div class="container">
  <div class="row">
    <div class="col-md-12 clearfix">
      <div class="top-links pull-right">
         <a href="[[++moxycart.domain]]/cart?cart-view" class="cart"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;<span aria-hidden="true" data-icon="&#xe006;"></span>  <span id="fc_quantity">0</span> items : $<span id="fc_total_price">0.00</span></a>&nbsp;&nbsp;
        <a href="#" class="cart"><span class="glyphicon glyphicon-share"></span>&nbsp;Checkout</a>
      </div>
    </div>
  </div>
</div>
 <div class="navbar navbar-inverse" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><img src="[[++moxycart.assets_url]]/images/templates/logo.png" alt=""></a>
    </div>
    <div class="collapse navbar-collapse navbar-right">
      <ul class="nav navbar-nav">
        <li class="active"><a href="/">Home</a></li>
        <li><a href="/sample-store">Shop</a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>

    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="starter-template text-center">
            <h1>Moxycart: Your turn-key eCommerce Solution for MODx</h1>
            <p class="lead">Use this template as a way to quickly start your store template using moxycart.<br> You will see all snippets sample in the core of this file..</p>
            <a href="https://github.com/craftsmancoding/moxycart/wiki" class="btn btn-primary btn-lg">Visit Wiki Page</a>
          </div>
        </div>
      </div>

        <hr>

        <div class="row main-row">

            <div class="col-md-8 images-col">
                  [[getProductImages? 
                    &innerTpl=`<img id="product_thumbnail" src="[[+Asset.url]]" alt="">` 
                    &css_paths=`[[++moxycart.assets_url]]css/templates/product.css` 
                    &js_paths=`http://cdnjs.cloudflare.com/ajax/libs/jquery.cycle/3.03/jquery.cycle.all.min.js,[[++moxycart.assets_url]]js/templates/product.js` 
                    &outerTpl=`ProductImageOuter` 
                    &is_active=`1`
                  ]]
    
            </div>


            <div class="left-content col-md-4 clearfix">



            <h1>[[+name]]</h1>
            <p class="product-code">Product Code: [[+sku]] ($[[+calculated_price]])</p>

            [[!addToCartButton]]
            <br>
            [[+content]]

            </div>

        </div>
    </div><!-- /.container -->

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

</body>
</html>