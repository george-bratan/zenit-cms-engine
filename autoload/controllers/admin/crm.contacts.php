<?php

	// CrmContacts

	class CrmContacts extends AdminModule
	{
		static
			$TITLE  = 'Contacts',
			$IDENT  = 'crm.contacts';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/user.png',
				'LARGE' => 'icon.large/user.business.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Contacts',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'crm.contacts';

		static
			$METHOD = Array(
				0 => '-',
				1 => 'Phone Call',
				2 => 'SMS Message',
				3 => 'Email',
			);

		static function OnLoad()
		{
			if (isset(static::$PERMISSION['save'])) {
				//
				if (Auth::Grant(static::$AUTH . '.save')) {
					//
					$token = Util::Split( Session::Get('ACCOUNT.TOKEN') );
					$token[] = static::$AUTH . '.note';

					Session::Set('ACCOUNT.TOKEN', implode('|', $token));
				}
			}
		}

		static function Model($id = NULL)
		{
			return Model::CrmContact($id);
		}

		static function Where($model)
		{
			if (Session::Exists(static::$IDENT.".filter.anynotes")) {
				//
				$anynotes = Model::CrmContactNote();
				$anynotes = CrmNotes::ApplyFilter($anynotes, static::$IDENT.".filter.anynotes");
				$anynotes->execute();

				$model->where("id IN ??", array_unique($anynotes->slice('idcontact')));
			}

			if (Session::Exists(static::$IDENT.".filter.notnotes")) {
				//
				$notnotes = Model::CrmContactNote();
				$notnotes = CrmNotes::ApplyFilter($notnotes, static::$IDENT.".filter.notnotes");
				$notnotes->execute();

				$model->where("id NOT IN ??", array_unique($notnotes->slice('idcontact')));
			}

			if (Session::Exists(static::$IDENT.".filter.lastnote")) {
				//
				$lastnote = Model::CrmContactNote();
				$lastnote = CrmNotes::ApplyFilter($lastnote, static::$IDENT.".filter.lastnote");
				$lastnote->order('date DESC')->limit(1)
					->execute();

				$model->where("id IN ??", array_unique($lastnote->slice('idcontact')));
			}

			return $model;
		}

		static function GET_List($page = 0)
		{
			if (Auth::Grant(static::$AUTH .'.details')) {
				UI::set('OPTIONS.details', Array(
					'handler' => 'details',
					'icon' => 'icon.small/edit.png',
					'title' => 'Details',
				));
			}

			if (Auth::Grant(static::$AUTH .'.save')) {
				UI::set('OPTIONS.note', Array(
					'handler' => 'note',
					'rel' => 'modal',
					'icon' => 'icon.small/note.png',
					'title' => 'Quick Note',
				));
			}

			UI::nset('FORMAT.id',
				function($record) {
					return '<a style="margin-left:10px" href="'.Request::$URL.'/card/'.$record['id'].'" rel="modal"><img src="'.Conf::Get('WWW:ROOT').'/admin/images/icon.small/vcard.png"></a>';
				}
			);
			UI::nset('FORMAT.email',
				function($record) {
					if ($record['email'])
						return '<a href="mailto:'.$record['email'].'" onclick="javascript: setTimeout( function() {$(\'#note_'.$record['id'].'\').click();}, 100);">'.$record['email'].'</a>';
					return FALSE;
				}
			);
			UI::nset('FORMAT.phone',
				function($record) {
					if ($record['phone'])
						return '<a href="skype:'.$record['phone'].'" onclick="javascript: setTimeout( function() {$(\'#note_'.$record['id'].'\').click();}, 100);">'.$record['phone'].'</a>';
					return FALSE;
				}
			);

			UI::set('FORMAT.labels', function($record) {
				//
				$result = '';
				if ($record['labels']->found()) {
					//
					$record['labels']->reset();
					while ($record['labels']->next()) {
						//
						$result .= '<span style="float:left; margin-right:10px; display:block; width:12px; height:12px; border:1px solid black; background-color:#'.$record['labels']->color.';" title="'.$record['labels']->name.'"></span>';
					}
				}

				return $result;
			});

			UI::nset('TOOLBAR.advanced', array(
					'url' => Request::$URL.'/advanced',
					'rel' => 'modal',
					'icon' => 'icon.small/settings.png',
					'title' => 'Advanced'
				)
			);

			parent::GET_List($page, static::$IDENT.".filter.contacts");
		}

		static function GET_Details()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			UI::set('CONTACT', $model->record());
			UI::set('CONTACT.LABELS', $model->labels->export());
			UI::set('CONTACT.NOTES', $model->notes->export());
			UI::set('CONTACT.ADDRESSES', $model->addresses->export());

			UI::set('CONTACT.postal', $model->postal->record());
			UI::set('CONTACT.billing', $model->billing->record());

			UI::set('CONTACT.COMPANY', $model->company->record());
			UI::set('SIZE', CrmCompanies::$SIZE);
			UI::set('METHOD', CrmContacts::$METHOD);


			UI::set('ACCOUNT', Session::Get('ACCOUNT'));

			UI::set('FOOTER',  UI::Render('admin/crm.contact.notes.php'));
			UI::set('CONTENT', UI::Render('admin/crm.contact.details.php'));

			UI::set('SECTION', 'Contact: '.$model->fullname);

			self::Wrapper();
		}

		static function GET_Card()
		{
			$model = self::Model( intval(Request::URL('id')) );

			UI::Set('CONTACT', $model->record());

			UI::Set('TITLE', 'Calling Card');
			UI::Set('CONTENT', 'Select labels:');
			UI::Set('CONTENT', UI::Render('admin/crm.contact.card.php'));

			self::Popup();
		}

		static function GET_Labels()
		{
			$model = self::Model( intval(Request::URL('id')) );

			$labels = Model::CrmLabel();
			$labels->where('status > -1 AND type = ?', Models\CrmLabel::F_COLOR)
				->execute();

			UI::Set('TARGET', Request::$URL . '/labels/' . intval(Request::URL('id')));
			UI::Set('RECORDS', $labels->export());
			UI::Set('SELECTED', $model->labels->slice('id'));

			UI::Set('TITLE', 'Labels');
			UI::Set('CONTENT', 'Select labels:');
			UI::Set('CONTENT', UI::Render('admin/.shared.select.php'));

			self::Popup();
		}

		static function POST_Labels()
		{
			$items = Request::POST('items');

			if (!is_array($items)) {
				$items = array();
			}

			$model = self::Model( intval(Request::URL('id')) );

			$sql = new SQL();
			$sql->delete('crm_contacts_labels')
				->where('idcontact = ?', $model->id)
				->execute();

			foreach ($items as $label) {
				//
				$sql = new SQL();
				$sql->insert('crm_contacts_labels')
					->set('idcontact = ?, idlabel = ?', $model->id, $label)
					->execute();
			}
		}

		static function GET_Edit()
		{
			parent::GET_Details();
		}

		static function GET_Addresses()
		{
			if (Request::URL('id')) {
				//
				$model = static::Model( intval( Request::URL('id') ) );

				UI::set('ITEM', $model->record());

				UI::set('TITLE', 'Edit: '.$model->name);
				UI::set('FIELDS', static::AddressForm( $model ));
			}
			else {
				//
				UI::set('TITLE', 'Error');
				UI::set('MESSAGE.ERROR', 'No ID received.');
			}

			UI::set('CONTENT', UI::Render('admin/.shared.edit.php'));

			parent::Popup();
		}

		static function POST_Save()
		{
			$model = static::Model( intval( Request::URL('id') ) );

			if (!Request::URL('id')) {
				$model->defaults();
			}

			$params = Request::POST('VALUES');
			foreach (Request::POST('VALUES') as $field => $value) {
				//
				if (in_array($field, array_keys($model::$schema))) {
					//
					$model->$field = $value;
				}
			}

			$model->save();
		}

		static function GET_Note()
		{
			if (Request::URL('id')) {
				//
				/*
				$model = Model::CrmContactNote();
				UI::set('ITEM', $model->record());
				*/
				$model = self::Model( intval(Request::URL('id')) );
				UI::Set('CONTACT', $model->record());

				UI::set('TITLE', 'Quick Note');
				UI::set('FIELDS', static::QuickNoteForm());
			}
			else {
				//
				UI::set('TITLE', 'Error');
				UI::set('MESSAGE.ERROR', 'No ID received.');
			}

			UI::set('CONTENT', UI::Render('admin/crm.contact.note.php'));

			parent::Popup();
		}

		static function POST_Note()
		{
			$note = Model::CrmContactNote();
			$note->defaults();

			$note->idcontact = intval( Request::URL('id') );
			$note->idadmin = Session::Get('ACCOUNT.ID');
			$note->method = Request::POST('VALUES.method');
			$note->idsubject = Request::POST('VALUES.subject');
			$note->content = Request::POST('VALUES.content');

			$note->flags = Util::FilterChecked( Request::POST('VALUES.flags') );

			$note->save();
		}

		static function MultiSetContext($context, $fields)
		{
			foreach ($fields as $input) {
				//
				$input->Context( $input->Context() . "[{$context}]" )
					->Width('260px');
			}

			return $fields;
		}

		static function GET_Advanced()
		{
			UI::set('TITLE', 'Advanced Filters');

			UI::set('FILTER.CONTACTS', self::MultiSetContext( 'CONTACTS', CrmContacts::FilterForm( static::$IDENT.".filter.contacts" ) ));
			UI::set('FILTER.ANYNOTES', self::MultiSetContext( 'ANYNOTES', CrmNotes::FilterForm( static::$IDENT.".filter.anynotes" ) ));
			UI::set('FILTER.NOTNOTES', self::MultiSetContext( 'NOTNOTES', CrmNotes::FilterForm( static::$IDENT.".filter.notnotes" ) ));
			UI::set('FILTER.LASTNOTE', self::MultiSetContext( 'LASTNOTE', CrmNotes::FilterForm( static::$IDENT.".filter.lastnote" ) ));

			UI::set('CONTENT', UI::Render('admin/crm.contact.advanced.php'));

			parent::Popup();
		}

		static function POST_Advanced()
		{
			CrmContacts::POST_Filter( 'FILTER.CONTACTS', static::$IDENT.".filter.contacts" );
			CrmNotes::POST_Filter( 'FILTER.ANYNOTES', static::$IDENT.".filter.anynotes" );
			CrmNotes::POST_Filter( 'FILTER.NOTNOTES', static::$IDENT.".filter.notnotes" );
			CrmNotes::POST_Filter( 'FILTER.LASTNOTE', static::$IDENT.".filter.lastnote" );
		}

		static function GET_Filter()
		{
			UI::nset('FILTER', self::MultiSetContext( 'CONTACTS', static::FilterForm( static::$IDENT.".filter.contacts" ) ));

			parent::GET_Filter();
		}

		static function POST_Filter()
		{
			$POST_PATH = isset($args[0]) ? $args[0] : 'FILTER.CONTACTS';
			$SESSION_PATH = isset($args[1]) ? $args[1] : static::$IDENT.'.filter.contacts';

			parent::POST_Filter($POST_PATH, $SESSION_PATH);
		}

		static function FilterForm()
		{
			$args = func_get_args();
			$filters = parent::FilterForm(isset($args[0]) ? $args[0] : static::$IDENT.".filter.contacts");

			return $filters;
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			foreach($fields as $key => $field) {
				//
				if (!in_array($key, Util::split('firstname|lastname|idcompany|position|email|phone'))) {
					//
					unset( $fields[ $key ] );
				}
			}

			$companies = DB::AssociativeColumn("SELECT id, name FROM crm_companies WHERE status > -1");
			$fields['idcompany']->Type(Input::F_SELECT)
				->Options(array_merge(array('-'), $companies));

			return $fields;
		}

		static function AddressForm( $model )
		{
			$fields = parent::EditForm( $model );

			foreach($fields as $key => $field) {
				//
				if (!in_array($key, Util::split('idbilling|idpostal'))) {
					//
					unset( $fields[ $key ] );
				}
			}

			$addresses = DB::AssociativeColumn("SELECT id, CONCAT(street, ', ', city, ', ', state) FROM crm_addresses WHERE idcontact = ? AND status > -1", array($model->id));
			$fields['idbilling']->Type(Input::F_SELECT)
				->Options(array_merge(array('-'), $addresses));
			$fields['idpostal']->Type(Input::F_SELECT)
				->Options(array_merge(array('-'), $addresses));

			return $fields;
		}

		static function QuickNoteForm()
		{
			$fields = array();

			$input = new Input('method');
			$input->Type(Input::F_SELECT)->Title('Contact Method')->Context('VALUES')->Options( self::$METHOD );

			$fields['method'] = $input;

			$subjects = DB::AssociativeColumn("SELECT 0, '-' UNION SELECT id, name FROM crm_labels WHERE type = ? AND status = 1", array( Models\CrmLabel::F_SUBJECT ));

			$input = new Input('subject');
			$input->Type(Input::F_SELECT)->Title('Contact Reason')->Context('VALUES')->Options( $subjects );

			$fields['subject'] = $input;

			$flags = DB::AssociativeColumn("SELECT id, name FROM crm_labels WHERE type = ? AND status = 1", array( Models\CrmLabel::F_FLAG ));

			$input = new Input('flags');
			$input->Type(Input::F_CHECKGROUP)->Title('Resolution')->Context('VALUES')->Options( $flags )->VAlign('vertical');

			$fields['flags'] = $input;

			$input = new Input('content');
			$input->Type(Input::F_RICHTEXT)->Title('Notes')->Context('VALUES');

			$fields['content'] = $input;

			return $fields;
		}

		static function DataFeed( $feed = NULL, $filters = NULL )
		{
			$FEEDS = array(
				'CRM.SIGNUP' => Array(
					'title' => 'Signup Form',
					'hint' => '',
				),
				'CRM.LOGIN' => Array(
					'title' => 'Login Form',
					'hint' => '',
				),
			);

			if (!$feed) {
				//
				return $FEEDS;
			}

			if ($feed == 'CRM.SIGNUP') {

				// POSSIBLE FORM SUBMISSION

				$error = '';
				if (Request::POST('signup.submit')) {
					//
					if (!Request::POST('signup.firstname') || !Request::POST('signup.email') || !Request::POST('signup.password')) {
						$error = 'Please fill in all mandatory fields: Full Name, Email and Password.';
					}
					if (Request::POST('signup.password') != Request::POST('signup.confirm')) {
						$error = 'Please make sure you enter the same password in both Password fields.';
					}
					if (strlen(Request::POST('signup.password')) < 5) {
						$error = 'Please enter a password longer than 5 characters.';
					}
					if (DB::Fetch('SELECT COUNT(*) FROM crm_contacts WHERE email = ? AND status > -1', array(Request::POST('signup.email')))) {
						$error = 'This email address is already registered with us.';
					}
					if (Request::POST('signup.captcha') !== NULL) {
						if (intval(Request::POST('signup.captcha')) != 5) {
							$error = 'Please make sure you read and enter a value for the Human Validation Textbox.';
						}
					}

					if (!$error) {
						//
						$contact = Model::CrmContact();
						$contact->defaults();

						$contact->firstname = Request::POST('signup.firstname');
						$contact->lastname = Request::POST('signup.lastname');
						$contact->email = Request::POST('signup.email');
						$contact->phone = Request::POST('signup.phone');
						$contact->password = Request::POST('signup.password');

						$contact->save();

						if (Request::POST('signup.street')) {
							//
							$address = Model::CrmAddress();
							$address->defaults();

							$address->idcontact = $contact->id;
							$address->street = Request::POST('signup.street');
							$address->city = Request::POST('signup.city');
							$address->state = Request::POST('signup.state');
							$address->country = Request::POST('signup.country');
							$address->postcode = Request::POST('signup.postcode');
							$address->save();

							$contact->idbilling = $address->id;
							$contact->idshipping = $address->id;
							$contact->idpostal = $address->id;
							$contact->save();
						}

						if (Request::POST('signup.company')) {
							//
							$company = Model::CrmCompany();
							$company->defaults();

							$company->name = Request::POST('signup.company');
							$company->size = Request::POST('signup.size');
							$company->save();

							$contact->idcompany = $company->id;
							$contact->position = Request::POST('signup.position');
							$contact->save();
						}

						$error = 'Your registration has been submitted. Thank You!';
					}
				}

				$countries = '';
				foreach (Conf::Get('COUNTRIES') as $id => $country) {
					//
					$countries .= '<option value="'.$id.'">'.$country.'</option>';
				}

				$companies = '';
				foreach (CrmCompanies::$SIZE as $id => $company) {
					//
					$companies .= '<option value="'.$id.'">'.$company.'</option>';
				}

				$form = Array(
					'error' => $error,
					'firstname' => '<input id="signup-firstname" type="text" name="signup[firstname]" value="" />',
					'lastname' => '<input id="signup-lastname" type="text" name="signup[lastname]" value="" />',
					'email' => '<input id="signup-email" type="text" name="signup[email]" value="" />',
					'phone' => '<input id="signup-phone" type="text" name="signup[phone]" value="" />',

					'street' => '<input id="signup-street" type="text" name="signup[street]" value="" />',
					'city' => '<input id="signup-city" type="text" name="signup[city]" value="" />',
					'state' => '<input id="signup-state" type="text" name="signup[state]" value="" />',
					'country' => '<select id="signup-country" name="signup[country]">'.$countries.'</select>',
					'postcode' => '<input id="signup-postcode" type="text" name="signup[postcode]" value="" />',

					'company' => '<input id="signup-company" type="text" name="signup[company]" value="" />',
					'size' => '<select id="signup-size" name="signup[size]">'.$companies.'</select>',
					'position' => '<input id="signup-position" type="text" name="signup[position]" value="" />',

					'password' => '<input id="signup-password" type="password" name="signup[password]" value="" />',
					'confirm' => '<input id="signup-confirm" type="password" name="signup[confirm]" value="" />',

					'captcha' => 'What is two plus three? <input id="signup-captcha" type="text" name="signup[captcha]" value="" />',
					'submit' => '<input id="signup-submit" type="submit" name="signup[submit]" value="Submit" />',
				);

				// BUILD RESPONSE

				$RESULT = Array();
				$RESULT['FEED'] = $feed;
				$RESULT['HINT'] = $FEEDS[ $feed ][ 'hint' ];
				$RESULT['DEFAULT'] = file_get_contents(Conf::Get('APP:UI') . 'public/crm.signup.php');

				$RESULT['RESULT']['FORM'] = $form;

				$RESULT['PROPERTIES'] = Array(
					//
					'FORM.firstname' => 'Signup: First Name',
					'FORM.lastname' => 'Signup: Last Name',
					'FORM.email' => 'Signup: Email',
					'FORM.phone' => 'Signup: Phone',

					'FORM.street' => 'Signup: Street Address',
					'FORM.city' => 'Signup: City',
					'FORM.state' => 'Signup: State',
					'FORM.country' => 'Signup: Country',
					'FORM.postcode' => 'Signup: Postal Code',

					'FORM.company' => 'Signup: Company Name',
					'FORM.size' => 'Signup: Company Size',
					'FORM.position' => 'Signup: Position Within Company',

					'FORM.password' => 'Signup: Password',
					'FORM.confirm' => 'Signup: Confirm Password',

					'FORM.captcha' => 'Signup: Captcha',
					'FORM.submit' => 'Signup: Submit Button',
					'FORM.error' => 'Signup: Submission Errors',
				);

				$RESULT['FILTERS'] = Array();

				return $RESULT;
			}

			if ($feed == 'CRM.LOGIN') {
				//
				// POSSIBLE FORM SUBMISSION

				$error = '';
				if (Request::POST('login.submit')) {
					//
					if (!Request::POST('login.email') || !Request::POST('login.password')) {
						$error = 'Please fill in all mandatory fields: Email and Password.';
					}

					if (!$error) {
						//
						$contact = Model::CrmContact();
						$contact->where('email = ? AND password = ?', Request::POST('login.email'), Request::POST('login.password'))
							->execute();

						if ($contact->found()) {
							//
							Session::Set('LOGIN.ID', $contact->id);
							Session::Set('LOGIN.NAME', $contact->firstname);
							Session::Set('LOGIN.COMPANY.ID', $contact->company->id);
							Session::Set('LOGIN.COMPANY.NAME', $contact->company->name);

							$error = 'Welcome, '.Session::Get('LOGIN.NAME').'!';
						}
						else {
							//
							$error = 'Incorrect Email or Password!';
						}
					}
				}

				$form = Array(
					'error' => $error,
					'email' => '<input id="login-email" type="text" name="login[email]" value="" />',
					'password' => '<input id="login-password" type="password" name="login[password]" value="" />',
					'submit' => '<input id="login-submit" type="submit" name="login[submit]" value="Submit" />',
				);

				// BUILD RESPONSE

				$RESULT = Array();
				$RESULT['FEED'] = $feed;
				$RESULT['HINT'] = $FEEDS[ $feed ][ 'hint' ];
				$RESULT['DEFAULT'] = file_get_contents(Conf::Get('APP:UI') . 'public/crm.login.php');

				$RESULT['RESULT']['FORM'] = $form;

				$RESULT['PROPERTIES'] = Array(
					//
					'FORM.email' => 'Signup: Email',
					'FORM.password' => 'Signup: Password',
					'FORM.submit' => 'Signup: Submit Button',
					'FORM.error' => 'Signup: Submission Errors',
				);

				$RESULT['FILTERS'] = Array();

				return $RESULT;
			}
		}

		static function HtmlFeed( $feed = NULL )
		{
			$FEEDS = self::DataFeed();

			if (!$feed) {
				//
				return $FEEDS;
			}


			if ($feed == 'CRM.SIGNUP') {
				//
				$data = self::DataFeed($feed);

				return UI::Render('public/crm.signup.php', $data['RESULT']);
			}


			if ($feed == 'CRM.LOGIN') {
				//
				$data = self::DataFeed($feed);

				return UI::Render('public/crm.login.php', $data['RESULT']);
			}
		}

		static function RecipientFeed( $feed = NULL, $filters = NULL )
		{
			if (!$feed) {
				//
				return array(
					'CRM.CONTACTS' => 'All Contacts',
				);
			}

			if ($feed == 'CRM.CONTACTS') {
				//
				$model = Model::CrmContact();

				if (isset($filters['labels'])) {
					if (is_array($filters['labels']))
						$filters['labels'] = Util::FilterChecked( $filters['labels'] );
						if (count($filters['labels'])) {
							$model->where('(SELECT COUNT(*) FROM crm_contacts_labels WHERE idcontact = id AND idlabel IN ??) > 0', $filters['labels']);
						}
				}
				if (isset($filters['name'])) {
					if ($filters['name'])
						$model->where("CONCAT(firstname, ' ', lastname) LIKE '%" . $filters['name'] . "%'");
				}
				if (isset($filters['email'])) {
					if ($filters['email'])
						$model->where("email LIKE '%" . $filters['email'] . "%'");
				}
				if (isset($filters['company'])) {
					if ($filters['company'])
						$model->where("idcompany = ?", $filters['company']);
				}
				if (isset($filters['city'])) {
					if ($filters['city'])
						//$model->where("city LIKE '%" . $filters['city'] . "%'");
						$model->where("(SELECT COUNT(*) FROM crm_addresses WHERE id = idpostal AND city LIKE '%" . $filters['city'] . "%') > 0");
				}
				if (isset($filters['state'])) {
					if ($filters['state'])
						//$model->where("state LIKE '%" . $filters['state'] . "%'");
						$model->where("(SELECT COUNT(*) FROM crm_addresses WHERE id = idpostal AND state LIKE '%" . $filters['state'] . "%') > 0");
				}
				if (isset($filters['country'])) {
					if ($filters['country'])
						//$model->where("country = ?", $filters['country']);
						$model->where("(SELECT COUNT(*) FROM crm_addresses WHERE id = idpostal AND country = ?) > 0", $filters['country']);
				}

				$model->execute();

				// BUILD RESPONSE

				$result = array();

				if ($model->found()) {
					//
					$model->reset();
					while ($model->next()) {
						//
						$other = array();
						$other[] = $model->postal->street;
						$other[] = $model->postal->city;
						$other[] = $model->postal->state;

						$result[] = array(
							'name' => $model->name,
							'email' => $model->email,
							'other' => implode(', ', $other),
						);
					}
				}

				$RESULT = Array();
				$RESULT['FEED'] = $feed;

				$RESULT['RESULT'] = $result;

				$RESULT['FILTERS'] = Array();

				$input = new Input('name');
				$input->Type(Input::F_TEXT)->Title('Filter by Name');
				if (isset($filters['name'])) {
					$input->Value($filters['name']);
				}
				$RESULT['FILTERS']['name'] = $input;

				$input = new Input('email');
				$input->Type(Input::F_TEXT)->Title('Filter by Email');
				if (isset($filters['email'])) {
					$input->Value($filters['email']);
				}
				$RESULT['FILTERS']['email'] = $input;

				$companies = DB::AssociativeColumn("SELECT '0', '-' UNION (SELECT id, name FROM crm_companies WHERE status = 1 ORDER BY name ASC)");
				$input = new Input('company');
				$input->Type(Input::F_SELECT)->Options($companies)->Title('Company');
				if (isset($filters['company'])) {
					$input->Value($filters['company']);
				}
				$RESULT['FILTERS']['company'] = $input;

				$input = new Input('city');
				$input->Type(Input::F_TEXT)->Title('Filter by City');
				if (isset($filters['city'])) {
					$input->Value($filters['city']);
				}
				$RESULT['FILTERS']['city'] = $input;

				$input = new Input('state');
				$input->Type(Input::F_TEXT)->Title('Filter by State');
				if (isset($filters['state'])) {
					$input->Value($filters['state']);
				}
				$RESULT['FILTERS']['state'] = $input;

				$countries = array_merge(array('' => '-'), Conf::Get('COUNTRIES'));
				$input = new Input('country');
				$input->Type(Input::F_SELECT)->Title('Country')->Options($countries);
				if (isset($filters['country'])) {
					$input->Value($filters['country']);
				}
				$RESULT['FILTERS']['country'] = $input;

				$labels = DB::AssociativeColumn("SELECT id, name FROM crm_labels WHERE type = 0 AND status = 1 ORDER BY name ASC");
				$input = new Input('labels');
				$input->Type(Input::F_CHECKGROUP)->Options($labels)->Title('Labels');
				if (isset($filters['labels'])) {
					$input->Value($filters['labels']);
				}
				$RESULT['FILTERS']['labels'] = $input;


				return $RESULT;
			}
		}
	}

?>