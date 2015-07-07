{php /* CRM LOGIN FORM TEMPLATE */ }

	<div class="login-form-container">
		<div class="error">{$FORM.error}</div>

		<form action="" method="post">
			<ul class="login-form">

				<li><label>Email</label>
					{$FORM.email}</li>

				<li><label>Password</label>
					{$FORM.password}</li>

				<li>{$FORM.submit}</li>
			</ul>
		</form>
	</div>