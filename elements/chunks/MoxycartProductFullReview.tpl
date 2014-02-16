<!--  
@name MoxycartProductFullReview
@description Chunk use to format The navigation for Sample Templates
-->
<br><h2>Write a Review</h2>
<p>[[+moxy.review_success_msg]]</p>
<p>[[+moxy.review_error_msg]]</p>
<form action="[[++site_url]][[+uri]]" method="post">

	<fieldset>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="name">Your name</label>
            <input type="text" required="" name="name" class="form-control" id="name">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="email">Your email</label>
            <input type="email" required="" name="email" class="form-control" id="email">
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="raty-label">
          Your rating for this item<br>
          <span class="rate"></span>
        </label>
      </div>

      

      <div class="form-group">
        <label for="content">Your message</label>
        <textarea name="content" class="form-control" id="content" rows="5"></textarea>
      </div>
      <input type="submit" class="btn btn-primary" value="Send message">
      </fieldset>
</form>