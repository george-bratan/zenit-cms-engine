{php /* CONTACT FORM TEMPLATE */ }

	<div class="contact-form-container">
		<div class="error">{$FORM.error}</div>

		<form action="" method="post">
			<ul class="contact-form">

				<li><label>Name</label>
					{$FORM.name}</li>
				<li><label>Email</label>
					{$FORM.email}</li>

				<li><label>Subject</label>
					{$FORM.subject}</li>
				<li><label>Message</label>
					{$FORM.message}</li>

				<li>{$FORM.captcha}</li>
				<li>{$FORM.submit}</li>
			</ul>
		</form>
	</div>