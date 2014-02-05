<!-- 
@templatename Sample Product
@description A sample Moxycart template for formatting a single product.
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

  <title>[[+name]] - Moxycart Sample Product</title>

<link rel="stylesheet" href="//cdn.foxycart.com/static/scripts/colorbox/1.3.23/style1_fc/colorbox.css?ver=1" type="text/css" media="screen" charset="utf-8" />
  <link rel="stylesheet" type="text/css" href="/assets/mycomponents/moxycart/assets/components/moxycart/css/templates/flexslider.css">
  <link rel="stylesheet" type="text/css" href="/assets/mycomponents/moxycart/assets/components/moxycart/css/templates/chosen.css">
  <link rel="stylesheet" type="text/css" href="/assets/mycomponents/moxycart/assets/components/moxycart/css/templates/jquery-ui-1.10.3.custom.min.css">
  <link rel="stylesheet" type="text/css" href="/assets/mycomponents/moxycart/assets/components/moxycart/css/templates/prettyPhoto.css">
  <link rel="stylesheet" type="text/css" href="/assets/mycomponents/moxycart/assets/components/moxycart/css/templates/font-awesome.css">

  <link rel="stylesheet" type="text/css" href="/assets/mycomponents/moxycart/assets/components/moxycart/css/templates/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="/assets/mycomponents/moxycart/assets/components/moxycart/css/templates/style.css">

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/bootstrap/js/html5shiv.js"></script>
  <script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/bootstrap/js/respond.min.js"></script>
  <![endif]-->

</head>
<body>
<div class="wrapper">

<header id="MainNav">
  <div class="container">
    <div class="row">
      <section class="col-md-12" id="TopBar">

        <!-- SHOPPING CART -->
        <div class="shopping-cart-widget pull-right">
           <a href="[[++moxycart.domain]]/cart?cart-view" class="btn btn-link pull-right">
            <span aria-hidden="true" data-icon="&#xe006;"></span>  <span id="fc_quantity">0</span> items : $<span id="fc_total_price">0.00</span>
          </a>
        </div>
        <!-- !SHOPPING CART -->
      </section>
      <nav class="navbar navbar-default">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle btn btn-primary">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.html"><img src="/assets/mycomponents/moxycart/assets/components/moxycart/images/templates/images/logo.png" alt=" "></a>
        </div>

        <div class="navbar-collapse navbar-main-collapse" role="navigation">

         <ul class="nav navbar-nav">
            <li class="first"><a href="/" title="Home">Home</a></li>
            <li><a href="/[[~[[+store_id]]]]">Shop</a></li>
            </ul>

          <form class="navbar-form navbar-right navbar-search" role="search">
            <div class="form-group">
              <label class="sr-only" for="navbar-search">Your search</label>
              <input type="search" id="navbar-search" class="form-control">
            </div>
            <button class="btn btn-default navbar-search">
              <span class="fa fa-search">
                  <span class="sr-only">Search</span>
              </span>
            </button>
          </form>
        </div>
        <!-- /.navbar-collapse -->
      </nav>
    </div>
  </div>
</header>

<section id="Content" role="main">

<div class="container">

  <!-- SECTION EMPHASIS 1 -->
  <!-- FULL WIDTH -->
</div>
<!-- !container -->
<div class="full-width section-emphasis-1 page-header page-header-short">
  <div class="container">
    <header class="row">
      <div class="col-md-12">
        <!-- BREADCRUMBS -->
        <ul class="breadcrumbs list-inline pull-right">
          <li><a href="/">Home</a></li>
          <li><a href="/[[~[[+store_id]]]]">Shop</a></li>
          <li>[[+name]]</li>
        </ul>
        <!-- !BREADCRUMBS -->
      </div>
    </header>
  </div>
</div>
<!-- !full-width -->
<div class="container">
<!-- !FULL WIDTH -->
<!-- !SECTION EMPHASIS 1 -->

<article class="row shop-product-single">
<div class="col-md-6 space-right-20">



	[[getProductImages:gte=`1`:then=`
		 <!-- thumbnailSlider -->
		  <div class="thumbnailSlider">
		    <div class="flexslider flexslider-thumbnails">
		      <ul class="slides">
		       [[getProductImages? &product_id=`[[+product_id]]` &innerTpl=`ProductImage` &is_active=`1` &limit=`0`]]
		      </ul>
		    </div>

		    <ul class="smallThumbnails clearfix">
		    	[[getProductImages? &product_id=`[[+product_id]]` &innerTpl=`ProductImageThumb` &firstClass=`active` &is_active=`1` &limit=`0`]]
		    </ul>
		  </div>
		  <!-- / thumbnailSlider -->
	`? &product_id=`[[+product_id]]` &total=`1` &limit=`0`]]
 

</div>
<div class="clearfix visible-sm visible-xs space-30"></div>
<div class="col-md-6 space-left-20">
<header>
  <span class="rating" data-score="[[!getProductReviewsRating? &product_id=`[[+product_id]]` &state=`approved`]]"></span>
  <a href="#reviews">([[getProductReviews? &product_id=`[[+product_id]]` &total=`1` &limit=`0`]]) Customer Reviews</a>
  <a href="#reviews">Write a review</a>

  <h1>
    [[+name]]
  </h1>
  <span class="product-code">Product Code: [[+product_id]]</span><br><br>
  <span class="price-old">$[[+price_strike_thru]]</span>&nbsp;&nbsp;<span class="price">$[[+calculated_price]]</span>
</header>

 <form role="form" class="foxycart shop-form form-horizontal" action="[[++moxycart.domain]]/cart" method="post" accept-charset="utf-8">
	<input type="hidden" name="quantity" value="1" />
	<input type="hidden" name="name" value="[[+name]]" />
	<input type="hidden" name="price" value="[[+calculated_price]]" />
	
  <div class="form-group">
    <label class="col-xs-2" for="quantity">Qty</label>

    <div class="col-xs-2">
      <input class="form-control spinner-quantity" id="quantity" required>
    </div>
  </div>

	<input type="submit" class="btn btn-primary" value="Add to cart" class="submit" />
<div class="clearfix"></div>
	</form>

<div class="shop-product-single-social">
  <span class="social-label pull-left">Share this product</span>

  <div class="social-widget social-widget-mini social-widget-dark">
    <ul class="list-inline">
      <li>
        <a href="https://www.facebook.com/sharer/sharer.php?u=http://www.createit.pl"
           onclick="window.open(this.href, 'facebook-share','width=580,height=296'); return false;"
           rel="nofollow"
           title="Facebook"
           class="fb">
          <span class="sr-only">Facebook</span>
        </a>
      </li>
      <li>
        <a href="http://twitter.com/share?text=CreateIT&amp;url=http://www.createit.pl"
           onclick="window.open(this.href, 'twitter-share', 'width=550,height=235'); return false;"
           rel="nofollow"
           title=" Share on Twitter"
           class="tw">
          <span class="sr-only">Twitter</span>
        </a>
      </li>
      <li>
        <a href="https://plus.google.com/share?url=http://www.createit.pl"
           onclick="window.open(this.href, 'google-plus-share', 'width=490,height=530'); return false;"
           rel="nofollow"
           title="Google+"
           class="gp">
          <span class="sr-only">Google+</span>
        </a>
      </li>
      <li>
        <a href="http://www.pinterest.com/pin/create/button/?url=http://www.createit.pl/&amp;media=http://www.createit.pl//assets/mycomponents/moxycart/assets/components/moxycart/images/templates/images/frontend/logo.png&amp;description=CreateIT"
           onclick="window.open(this.href, 'pinterest-share', 'width=770,height=320'); return false;"
           rel="nofollow"
           title="Pinterest"
           class="pt">
          <span class="sr-only">Pinterest</span>
        </a>
      </li>
      <li>
        <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=http://developer.linkedin.com&amp;title=LinkedIn%20Developer%20Network&amp;summary=My%20favorite%20developer%20program&amp;source=LinkedIn"
           onclick="window.open(this.href, 'linkedin-share', 'width=600,height=439'); return false;"
           rel="nofollow"
           title="LinkedIn" class="in">
          <span class="sr-only">LinkedIn</span>
        </a>
      </li>
    </ul>
  </div>
</div>
<div class="tabs">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#description" data-toggle="tab">Description</a></li>
    <li><a href="#reviews" data-toggle="tab">([[getProductReviews? &product_id=`[[+product_id]]` &total=`1` &limit=`0`]]) Customer Reviews</a></li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane fade in active" id="description">
      [[+content]]
    </div>
    <section class="tab-pane fade" id="reviews">
      [[getProductReviews? &product_id=`[[+product_id]]` &innerTpl=`ProductReview` &limit=`0` &state=`approved`]]

      [[writeReview? &tpl=`MoxycartProductFullReview` &product_id=`[[+product_id]]`]]
      
    </section>
  </div>
</div>
</div>
</article>
</div>
</section>

<div class="clearfix visible-xs visible-sm"></div>
<!-- fixes floating problems when mobile menu is visible -->

<footer>
  <div class="container">
    <section class="row">
      <div class="col-md-3 col-sm-6">
        <h3 class="strong-header">
          Company
        </h3>

        <div class="link-widget">
          <ul class="list-unstyled">
            <li><a href="13-pages-about.html">About us</a></li>
            <li><a href="#">Jobs</a></li>
            <li><a href="#">Affiliates</a></li>
            <li><a href="16-pages-contact.html">Contact</a></li>
          </ul>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <h3 class="strong-header">
          Help
        </h3>

        <div class="link-widget">
          <ul class="list-unstyled">
            <li><a href="10-a-shop-customer-service-track-order.html">Track order</a></li>
            <li><a href="10-b-shop-customer-service-faq.html">FAQs</a></li>
            <li><a href="#">Shipping info</a></li>
            <li><a href="#">Payment</a></li>
            <li><a href="#">Returns</a></li>
          </ul>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <h3 class="strong-header">
          Quick links
        </h3>

        <div class="link-widget">
          <ul class="list-unstyled">
            <li><a href="#">Size guide</a></li>
            <li><a href="09-a-shop-account-dashboard.html">My account</a></li>
            <li><a href="09-e-shop-account-my-wishlist.html">Wishlist</a></li>
          </ul>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <h3 class="strong-header">
          Follow us
        </h3>

        <div class="social-widget">
          <ul class="list-inline">
            <li><a href="#" class="fb"><span class="sr-only">Facebook</span></a></li>
            <li><a href="#" class="tw"><span class="sr-only">Twitter</span></a></li>
            <li><a href="#" class="gp"><span class="sr-only">Google+</span></a></li>
            <li><a href="#" class="pt"><span class="sr-only">Pinterest</span></a></li>
            <li><a href="#" class="in"><span class="sr-only">LinkedIn</span></a></li>
          </ul>
        </div>
      </div>
    </section>
    <hr>
    <section class="row">
      <div class="col-md-12">
        <span class="copyright pull-left">&copy; 2014 Decima Store</span>
        <ul class="payment-methods list-inline pull-right">
          <li>
            <span class="payment-visa"><span class="sr-only">Visa</span></span>
          </li>
          <li>
            <span class="payment-mastercard"><span class="sr-only">MasterCard</span></span>
          </li>
          <li>
            <span class="payment-paypal"><span class="sr-only">PayPal</span></span>
          </li>
          <li>
            <span class="payment-americanexpress"><span class="sr-only">American Express</span></span>
          </li>
        </ul>
      </div>
    </section>
  </div>
</footer>

</div>

<!-- SCRIPTS -->
<!-- core -->
<script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/jquery.min.js"></script>
<script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/bootstrap/js/bootstrap.min.js"></script>

<!-- !core -->

<!-- plugins -->
<script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/jquery.flexslider-min.js"></script>

<script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/jquery.isotope.min.js"></script>
<script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/jquery.ba-bbq.min.js"></script>

<script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/jquery-ui-1.10.3.custom.min.js"></script>

<script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/jquery.raty.min.js"></script>

<script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/jquery.prettyPhoto.js"></script>

<script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/chosen.jquery.min.js"></script>
<!-- !plugins -->

<script src="/assets/mycomponents/moxycart/assets/components/moxycart/js/templates/main.js"></script>
<script src="//cdn.foxycart.com/fireproofsocks/foxycart.colorbox.js?ver=2" type="text/javascript" charset="utf-8"></script>


</body>
</html>