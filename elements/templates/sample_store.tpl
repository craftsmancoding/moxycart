<!-- 
@templatename Sample Store
@description A sample Moxycart template for formatting the Product Container (Store).
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
         <a href="#" class="cart"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;View Cart</a>&nbsp;&nbsp;
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
        <li><a href="[[~[[*id]]]]">Shop</a></li>
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

            <div class="col-md-3">
              <h1>Product Categories</h1>
              <ul class="nav nav-stack">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="#">Messages</a></li>
              </ul>
            </div>


            <div class="left-content col-md-9 clearfix">
                 <div class="col-md-4">
                  <div class="thumbnail">
                    <img src="http://placehold.it/200x200" style="height: 200px; width: 100%; display: block;">
                    <div class="caption">
                      <h3>Thumbnail label</h3>
                      <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                      <p><a href="#" class="btn btn-danger" role="button">Add to Cart</a> <a href="#" class="btn btn-default" role="button">View</a></p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="thumbnail">
                    <img src="http://placehold.it/200x200" style="height: 200px; width: 100%; display: block;">
                    <div class="caption">
                      <h3>Thumbnail label</h3>
                      <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                      <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="thumbnail">
                    <img src="http://placehold.it/200x200" style="height: 200px; width: 100%; display: block;">
                    <div class="caption">
                      <h3>Thumbnail label</h3>
                      <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                      <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="thumbnail">
                    <img src="http://placehold.it/200x200" style="height: 200px; width: 100%; display: block;">
                    <div class="caption">
                      <h3>Thumbnail label</h3>
                      <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                      <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                    </div>
                  </div>
                </div>
            </div>

        </div>
    </div><!-- /.container -->

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

</body>
</html>