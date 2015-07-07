
<div class="help">
	<h1>Settings</h1>

	<p>
		Most modules already have a private <strong>Settings</strong> tab.
		All remaining system settings and options are located in the Settings module of your Administration Console.
	</p>

	<p>
		The <strong>General</strong> tab contains typical website information:

		<ul>
			<li>Website name, used throughout the public and administration side of your application.</li>
			<li>Your contact information (name and email) used in case of automated notifications and application errors.</li>
		</ul>
	</p>

	<p>
		The <strong>Mail</strong> tab allows the administrator to setup an external SMTP server to be used for any automated messages sent by the Application.
		If no SMTP server is setup, the local SendMail service is used, from the host machine. If you don't know what a SMTP server is, leave this tab empty or
		ask your system administrator for counsel.
	</p>

	<p>
		The <strong>License</strong> tab shows information relating to the license you purchased. To license your copy of Zenit Systems CMS you can upload a
		license file or copy/paste the license text in the available textbox.
	</p>

	<p>
		Zenit CMS uses an alternate <strong>Human Validation</strong> system (known as CAPTCHA). Instead of malformed images which are difficult to understand
		and will often be failed by actual humans, the user is presented with logical questions and a textbox requiring logical thinking and comprehension. Ex:
		What is two plus three? Answer: 5. If there are more than one accepted answer, you can separate them with a comma: 5,five. The human validation answer
		is not case-sensitive, so "five" is the same as "Five" or "FIVE".
	</p>

	<p>
		The <strong>Users</strong> tab lists all Administrator accounts. Make sure users have access to the email box specified here, as any automated messages
		coming from the Administration Console will be sent to this mailbox. Use the key labeled button to grant or deny granular access to individual
		accounts.
	</p>

	<p>
		To mass handle admin access rights, you can bundle multiple Administrators into <strong>Groups</strong> and setup group access rights.
	</p>

	<p>
		<em>
			Note: For any given feature, the system checks if the <strong>User</strong> OR his <strong>Group</strong> is granted the required right. As such,
			if you want to deny access to a user, make sure both his/her account AND group don't have that specific access permission.
		</em>
	</p>

</div>
