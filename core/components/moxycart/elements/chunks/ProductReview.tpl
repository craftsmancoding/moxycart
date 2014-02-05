<!--  
@name ProductReview
@description Chunk use to format Single Review
-->
<article class="review">
  <header>
    <span class="rating" data-score="[[+rating]]"></span><br>
    <h4 class="author">[[+name]]</h4>
    <span class="date">[[+timestamp_created:strtotime:date=`%Y-%m-%d`]]</span>
  </header>
  <p>
   [[+content]]
  </p>
</article>