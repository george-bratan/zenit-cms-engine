{php /* NEWSLETTER SIGNUP FORM TEMPLATE */ }

	<div class="newsletter-form-container">
		<div class="error">{$FORM.error}</div>

		<form action="" method="post">
			<ul class="newsletter-form">

				<li><label>Name</label>
					{$FORM.name}</li>
				<li><label>Email</label>
					{$FORM.email}</li>
				<li><label>Category</label>
					{$FORM.category}</li>

				<li>{$FORM.captcha}</li>
				<li>{$FORM.submit}</li>
			</ul>
		</form>
	</div>