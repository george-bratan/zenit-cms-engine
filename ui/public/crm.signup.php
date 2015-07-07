{php /* CRM SIGNUP FORM TEMPLATE */ }

	<div class="signup-form-container">
		<div class="error">{$FORM.error}</div>

		<form action="" method="post">
			<ul class="signup-form">

				<li><label>First Name</label>
					{$FORM.firstname}</li>
				<li><label>Last Name</label>
					{$FORM.lastname}</li>

				<li><label>Email</label>
					{$FORM.email}</li>
				<li><label>Phone</label>
					{$FORM.phone}</li>

				<li><label>Street Address</label>
					{$FORM.street}</li>
				<li><label>City</label>
					{$FORM.city}</li>
				<li><label>State</label>
					{$FORM.state}</li>
				<li><label>Country</label>
					{$FORM.country}</li>
				<li><label>Postal Code</label>
					{$FORM.postcode}</li>

				<li><label>Company Name</label>
					{$FORM.company}</li>
				<li><label>Company Size</label>
					{$FORM.size}</li>
				<li><label>Position</label>
					{$FORM.position}</li>

				<li><label>Password</label>
					{$FORM.password}</li>
				<li><label>Confirm Password</label>
					{$FORM.confirm}</li>

				<li>{$FORM.captcha}</li>
				<li>{$FORM.submit}</li>
			</ul>
		</form>
	</div>