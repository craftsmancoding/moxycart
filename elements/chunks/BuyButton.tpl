<!--  
@name BuyButton
@description Generates the "Buy Button"
-->
<form action="https://[[++cultureKey:is=`de`:then=`tick-de`:else=``]][[++cultureKey:is=`eu`:then=`tick-eu`:else=``]][[++cultureKey:is=`en`:then=`tick`:else=``]].foxycart.com/cart" method="post" accept-charset="utf-8">
    <input type="hidden" name="name" value="[[+name]]" />
    <input type="hidden" name="price" value="[[+calculated_price]]" />
    <input type="hidden" name="code" value="[[+sku]]" />
    <input type="hidden" name="product_id" value="[[+product_id]]" />
    <input type="hidden" name="image" value="[[+thumb]]" />
    <input type="hidden" name="quantity" value="1" />
    [[+options]]
    
    [[+submit]]
</form>