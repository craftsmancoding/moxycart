<style>
	.moxy_desc {
		display: block;
	}
	input {
		display: block;
	}
	label {
		font-weight: bold;
	}
	.moxy_desc {
		font-style: italic;
		margin-bottom: 10px;
	}
</style>

<h2>Authorization</h2>

<?php print $data['msg']; ?>

<p>Before you can use the FoxyCart, you must define your FoxyCart store.</p>
<br/>

<form method="post" action="">

<label for="redirect_uri">Redirect URI</label>
<input type="type" name="redirect_uri" id="redirect_uri" size="64" value="<?php print $data['redirect_uri']; ?>" />
<p class="moxy_desc">This should match the URI in your browser window, e.g. <code>https://yoursite.com/manager/?a=123</code> It should only be changed if you migrate your site or change your manager URL.</p>

<label for="project_name">Project Name</label>
<input type="type" name="project_name" id="project_name" value="<?php print $data['project_name']; ?>" />
<p class="moxy_desc">Name of your store in the FoxyCart administration.</p>

<label for="contact_name">Contact Name</label>
<input type="type" name="contact_name" id="contact_name" value="<?php print $data['contact_name']; ?>" />
<p class="moxy_desc">The name of your account's administrator.</p>

<label for="contact_email">Contact Email</label>
<input type="type" name="contact_email" id="contact_email" value="<?php print $data['contact_email']; ?>" />
<p class="moxy_desc">Email address of your account's administrator</p>

<label for="company_phone">Company Phone</label>
<input type="type" name="company_phone" id="company_phone" value="<?php print $data['company_phone']; ?>" />
<p class="moxy_desc">Phone number of your account's administrator, including area code.</p>

	<input type="submit" value="Create" />
</form>