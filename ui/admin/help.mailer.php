
<div class="help">
	<h1>Mass Mailing (Newsletters)</h1>

	<p>
		The <strong>Newsletter</strong> creation process is very similar to the CMS page publishing process (see C<strong>ontent Publishing</strong>).
		Newsletters can be created as stand-alone entities or you can derive them from a Template, through the use of inheritance. Similar to page publishing,
		a version control system keeps copies of every published state of your newsletters, so you can always load a previous version.
	</p>

	<p>
		Before sending a newsletter, you're advised to test if using your personal email address. Hitting the <strong>Test</strong> button you will be asked
		for a test mailbox where a sample letter will be sent. Once you agree the letter is in good shape click the Send button, asking you for a <strong>Recipient List</strong>.
		The options you will be presented are all Recipient Lists you have available in your system, each coming from
		different modules you have installed. See below for known modules exporting Recipient Lists. If you want a specific subset of users to receive this
		newsletter, use the Recipient List tab to setup a filtered recipient list (read more below).
	</p>

	<p>
		The <strong>History</strong> tab lists all past newsletters. Clicking the details of a past letter will display the rendered email you sent and a short
		statistical data relating to the letter impact and results.
	</p>

	<p>
		The <strong>Subscribers</strong> tab lists all contacts who've registered for your newsletter. Though this section already exports a Recipient List to
		the Newslettering module, to be used when sending a new letter, subscribers can be further filtered and organized through the use of Recipient Lists.
	</p>

	<p>
		<strong>Recipient Lists</strong>
		are filtered streams of user email boxes. This section scans the entire application for modules exporting Contact Lists and applies the filtering rules
		you setup, outputting new (filtered) Recipient Lists to the Newslettering module.
	</p>

	<p style="text-align:center">
		<img style="width:95%" src="{$CONF.WWW.ROOT}/admin/images/help/flow.recipients.png" alt="" />
	</p>

	<p style="text-align:center">
			Fig. Using a filtered Recipient List
	</p>
	<p style="text-align:center">
		Module X exports a Recipient-Feed which can be used directly, or relayed through the Recipient List section for further filtering.
	</p>

	<p>
		Known modules exporting Recipient Lists:

		<ul>
			<li>eShop Orders: exports Client list</li>
			<li>Contacts (CRM): exports Contact list</li>
			<li>Newslettering: exports Subscribers list</li>
			<li>Newslettering Recipients: exports a filtered Recipient List (one of the above list)</li>
		</ul>
	</p>

	<p>
		Subscribers can also be organized under different <strong>Categories</strong> which you can use either as pure functional back-office labels, or you
		can consider contacts as being subscribed to different newsletters - by sending different newsletters to different categories of subscribers.
	</p>

	<p>
		The <strong>Settings</strong> tab allows you to setup an external SMTP server to be used for by the automated mass-mailer service. If no SMTP server is
		setup, the local sendmail service is used. If you don't know what a SMTP server is, leave this section empty or ask your system administrator for
		counsel.
	</p>

</div>
