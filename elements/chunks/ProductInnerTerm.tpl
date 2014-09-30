<!--  
@name ProductInnerTerm
@description Formats Single Product by Term
-->
<div class="col-md-4 item-col">
   <div class="thumbnail">
   <!-- 	<a href="[[++site_url]][[+Product.uri]]" class="img">
     [[+Product.Image.thumbnail_url:ise=``:then=`[[!getProductImages? &innerTpl=`<img id="product_thumbnail" src="[[+Asset.url]]" width="225" height="155" alt="[[+name]]" title="[[+name]]">` &outerTpl=`ProductImageTpl` &limit=`1`]]`:else=`<img src="[[+Product.Image.thumbnail_url]]" width="130" height="89" alt="[[+Product.name]]" title="[[+Product.name]]">`]]
   
   
     <span class="productName">[[+Product.name]]</span>
   
   </a> -->
      <img src="http://placehold.it/200x200" alt="" style="height: 200px; width: 100%; display: block;">
     <div class="caption">
       <h3>[[+Product.name]]</h3>
       <p class="price">$[[+Product.calculated_price]]</p>

       <form action="[[++moxycart.domain]]cart" method="post" accept-charset="utf-8">
         <input type="hidden" name="name" value="[[+Product.name]]" />
         <input type="hidden" name="price" value="[[+Product.price]]" />
         <input type="hidden" name="code" value="[[+Product.sku_vendor]]" />
        
       
         <input type="submit" src="[[++assets_url]]/skin1/img/add-cart.gif" alt="Add to Cart" class="btn btn-danger" value="Add to Cart">  
         <a href="[[++site_url]][[+Product.uri]]" class="btn btn-default" role="button">View</a>
       </form>
       
     </div>
   </div>
</div>

