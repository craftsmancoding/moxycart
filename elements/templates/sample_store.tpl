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

<link rel="stylesheet" href="//cdn.foxycart.com/static/scripts/colorbox/1.3.23/style1_fc/colorbox.css?ver=1" type="text/css" media="screen" charset="utf-8" />
  <link rel="stylesheet" type="text/css" href="[[++moxycart.assets_url]]css/templates/flexslider.css">
  <link rel="stylesheet" type="text/css" href="[[++moxycart.assets_url]]css/templates/chosen.css">
  <link rel="stylesheet" type="text/css" href="[[++moxycart.assets_url]]css/templates/jquery-ui-1.10.3.custom.min.css">
  <link rel="stylesheet" type="text/css" href="[[++moxycart.assets_url]]css/templates/prettyPhoto.css">
  <link rel="stylesheet" type="text/css" href="[[++moxycart.assets_url]]css/templates/font-awesome.css">

  <link rel="stylesheet" type="text/css" href="[[++moxycart.assets_url]]css/templates/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="[[++moxycart.assets_url]]css/templates/style.css">

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="[[++moxycart.assets_url]]js/templates/bootstrap/js/html5shiv.js"></script>
  <script src="[[++moxycart.assets_url]]js/templates/bootstrap/js/respond.min.js"></script>
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
          <a class="navbar-brand" href="index.html"><img src="[[++moxycart.assets_url]]images/templates/images/logo.png" alt=" "></a>
        </div>

        <div class="navbar-collapse navbar-main-collapse" role="navigation">

      
           <ul class="nav navbar-nav">
            <li class="first"><a href="/" title="Home">Home</a></li>
            <li class="last"><a href="/[[~[[*id]]]]" title="Sample Store">Shop</a></li>
            </ul>
    
          <form class="navbar-form navbar-right navbar-search" role="search">
            <div class="form-group">
              <label class="sr-only" for="navbar-search">Your search</label>
              <input type="search" id="navbar-search" class="form-control">
            </div>
            <button class="btn btn-default navbar-search">
              <span class="fa fa-search">
                  <span class="sr-only">Searching</span>
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
<div class="full-width section-emphasis-1 page-header">
  <div class="container">
    <header class="row">
      <div class="col-md-12">
        <h1 class="strong-header pull-left">[[*pagetitle]]</h1>

        <!-- BREADCRUMBS -->
        <ul class="breadcrumbs list-inline pull-right">
          <li><a href="index.html">Home</a></li>
          <li>Shop</li>
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

<div class="row">
<form>
<div class="shop-list-filters col-sm-4 col-md-3">

  <div class="filters-active element-emphasis-strong" style="display:none;">
    <h3 class="strong-header element-header" style="display:none;">
      You've selected
    </h3>
    <!-- dynamic added selected filters -->
    <ul class="filters-list list-unstyled">
      <li></li>
    </ul>
    <button type="button" class="filters-clear btn btn-primary btn-small btn-block">
      Clear all
    </button>
  </div>

  <button type="button" class="btn btn-default btn-small visible-xs" data-texthidden="Hide Filters" data-textvisible="Show Filters" id="toggleListFilters"></button>

  <div id="listFilters">

    <div class="filters-details element-emphasis-weak">
      <!-- ACCORDION -->
      <div class="accordion">
        <div class="panel-group">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="strong-header panel-title">
                <a class="accordion-toggle" data-toggle="collapse" href="#collapse-001">
                  Price range
                </a>
              </h4>
            </div>
            <div id="collapse-001" class="panel-collapse collapse in">
              <div class="panel-body">
                <div class="filters-range" data-min="10" data-max="150" data-step="5">
                  <div class="filter-widget"></div>
                  <div class="filter-value">
                    <input type="text" class="min">
                    <input type="text" class="max">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="strong-header panel-title">
                <a class="accordion-toggle" data-toggle="collapse" href="#collapse-002">
                  Categories
                </a>
              </h4>
            </div>
            <div id="collapse-002" class="panel-collapse collapse in">
              <div class="panel-body">
                <div class="filters-checkboxes myFilters" data-option-group="category" data-option-type="filter">
                  <div class="form-group">
                    <input type="checkbox" class="sr-only" id="filters-categories-all">
                    <label for="filters-categories-all" data-option-value="" class="selected isotopeFilter">All</label>
                  </div>
                  <div class="form-group">
                    <input type="checkbox" class="sr-only" id="filters-categories-accessories">
                    <label for="filters-categories-accessories" data-option-value=".cat1" class="isotopeFilter">Accessories</label>
                  </div>
                  <div class="form-group">
                    <input type="checkbox" class="sr-only" id="filters-categories-bags_and_purses">
                    <label for="filters-categories-bags_and_purses" data-option-value=".cat2" class="isotopeFilter">Bags & Purses</label>
                  </div>
                  <div class="form-group">
                    <input type="checkbox" class="sr-only" id="filters-categories-dresses">
                    <label for="filters-categories-dresses" data-option-value=".cat3" class="isotopeFilter">Dresses</label>
                  </div>
                  <div class="form-group">
                    <input type="checkbox" class="sr-only" id="filters-categories-hoodies_and_sweatshirts">
                    <label for="filters-categories-hoodies_and_sweatshirts" data-option-value=".cat4" class="isotopeFilter">Hoodies & Sweatshirts</label>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <!-- !ACCORDION -->
    </div>
  </div>
  <!-- / #listFilters -->
</div>

<div class="clearfix visible-xs"></div>
<div class="col-sm-8 col-md-9">
<div class="row">
<div class="shop-list-filters col-sm-6 col-md-8">
  <span class="filters-result-count"><span>24</span> results</span>
</div>
<div class="shop-list-filters col-sm-6 col-md-4">
  <div class="filters-sort">
    <div class="btn-group myFilters" data-option-group="sortby" data-option-type="sortBy">
      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        Original order <span class="caret"></span>
      </button>
      <ul class="dropdown-menu" role="menu">
        <li><a href="#" class="selected isotopeFilter" data-option-value="original-order" data-option-asc="true">Original order</a></li>
        <li><a href="#" class="isotopeFilter" data-option-value="date" data-option-asc="false">Sort by newest</a></li>
        <li><a href="#" class="isotopeFilter" data-option-value="popular" data-option-asc="false">Sort by popularity</a></li>
        <li><a href="#" class="isotopeFilter" data-option-value="rating" data-option-asc="false">Sort by rating</a></li>
        <li><a href="#" class="isotopeFilter" data-option-value="random" data-option-asc="false">Sort by random</a></li>
      </ul>
    </div>
  </div>
</div>
<div class="clearfix"></div>
<div class="col-xs-12">

<!-- ISOTOPE GALLERY -->
<div id="isotopeContainer" class="shop-product-list isotope">
[[!getProducts? &store_id=`[[*id]]`]]
</div>
<!-- !ISOTOPE GALLERY -->

</div>


</div>
</div>


</form>
</div>
<!-- / row -->

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
            <li><a href="#">About us</a></li>
            <li><a href="#">Jobs</a></li>
            <li><a href="#">Affiliates</a></li>
            <li><a href="#">Contact</a></li>
          </ul>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <h3 class="strong-header">
          Help
        </h3>

        <div class="link-widget">
          <ul class="list-unstyled">
            <li><a href="#">Track order</a></li>
            <li><a href="#">FAQs</a></li>
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
            <li><a href="#">My account</a></li>
            <li><a href="#">Wishlist</a></li>
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
        <span class="copyright pull-left">&copy; 2014 Moxycart (Craftsmancoding)</span>
      </div>
    </section>
  </div>
</footer>

</div>

<!-- SCRIPTS -->
<!-- core -->
<script src="[[++moxycart.assets_url]]js/jquery-2.0.3.min.js"></script>
<script src="[[++moxycart.assets_url]]js/templates/bootstrap/js/bootstrap.min.js"></script>

<!-- !core -->

<!-- plugins -->
<script src="[[++moxycart.assets_url]]js/templates/jquery.flexslider-min.js"></script>

<script src="[[++moxycart.assets_url]]js/templates/jquery.isotope.min.js"></script>
<script src="[[++moxycart.assets_url]]js/templates/jquery.ba-bbq.min.js"></script>

<script src="[[++moxycart.assets_url]]js/templates/jquery-ui-1.10.3.custom.min.js"></script>

<script src="[[++moxycart.assets_url]]js/templates/jquery.raty.min.js"></script>

<script src="[[++moxycart.assets_url]]js/templates/jquery.prettyPhoto.js"></script>

<script src="[[++moxycart.assets_url]]js/templates/chosen.jquery.min.js"></script>
<!-- !plugins -->

<script src="[[++moxycart.assets_url]]js/templates/main.js"></script>
<script src="//cdn.foxycart.com/fireproofsocks/foxycart.colorbox.js?ver=2" type="text/javascript" charset="utf-8"></script>

</body>
</html>