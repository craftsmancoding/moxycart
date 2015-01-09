<!--  
@name ProductInnerTpl
@description Formats single Product
Placeholders: product_id,alias,content,name,sku,type,track_inventory,qty_inventory,qty_alert,price,category,uri,is_active,seq,calculated_price,calculated_price,
-->
<div class="col-md-4 item-col">
   <div class="thumbnail">
      <img src="http://placehold.it/200x200" alt="" style="height: 200px; width: 100%; display: block;">
     <div class="caption">
       <h3>[[+name]]</h3>
       <p class="price">$[[+calculated_price]]</p>

        <form action="[[++moxycart.domain]]cart" method="post" accept-charset="utf-8">
         <input type="hidden" name="name" value="[[+name]]" />
         <input type="hidden" name="price" value="[[+price]]" />
         <input type="hidden" name="code" value="[[+sku_vendor]]" />
        
       
         <input type="submit" src="[[++assets_url]]/skin1/img/add-cart.gif" alt="Add to Cart" class="btn btn-danger" value="Add to Cart">  
         <a href="[[++site_url]][[+uri]]" class="btn btn-default" role="button">View</a>
       </form>
     </div>
   </div>
</div>

