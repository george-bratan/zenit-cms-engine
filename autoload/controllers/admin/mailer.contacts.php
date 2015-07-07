<?php

	// MailerContacts

	class MailerContacts extends AdminModule
	{
		static
			$TITLE  = 'Subscribers',
			$IDENT  = 'mailer.contacts';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/user.png',
				'LARGE' => 'icon.large/user.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Subscribers',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'mailer.contacts';


		static function Model($id = NULL)
		{
			return Model::MailerContact($id);
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$categories = DB::AssociativeColumn("SELECT '0', '-' UNION (SELECT id, name FROM mailer_categories WHERE status > -1 ORDER BY name ASC)");
			$fields['idcategory']->Type(Input::F_SELECT)->Options($categories);

			return $fields;
		}

		static function DataFeed( $feed = NULL, $filters = NULL )
		{
			$FEEDS = array(
				'MAILER.SIGNUP' => Array(
					'title' => 'Newsleter Signup Form',
					'hint' => '',
				),
			);

			if (!$feed) {
				//
				return $FEEDS;
			}

			if ($feed == 'MAILER.SIGNUP') {

				// POSSIBLE FORM SUBMISSION

				$error = '';
				if (Request::POST('signup.submit')) {
					//
					if (!Request::POST('signup.name') || !Request::POST('signup.email')) {
						$error = 'Please fill in all mandatory fields: Name and Email.';
					}
					if (Request::POST('signup.captcha') !== NULL) {
						if (intval(Request::POST('signup.captcha')) != 5) {
							$error = 'Please make sure you read and enter a value for the Human Validation Textbox.';
						}
					}

					if (!$error) {
						//
						$contact = Model::MailerContact();
						$contact->defaults();

						$contact->name = Request::POST('signup.name');
						$contact->email = Request::POST('signup.email');
						$contact->idcategory = intval(Request::POST('signup.category'));

						$contact->save();

						$error = 'Your registration has been submitted. Thank You!';
					}
				}

				$options = '';
				$categories = DB::AssociativeColumn('SELECT id, name FROM mailer_categories WHERE status = 1 ORDER BY name ASC');
				foreach ($categories as $id => $category) {
					//
					$options .= '<option value="'.$id.'">'.$category.'</option>';
				}

				$form = Array(
					'error' => $error,
					'name' => '<input id="signup-name" type="text" name="signup[name]" value="" />',
					'email' => '<input id="signup-email" type="text" name="signup[email]" value="" />',
					'category' => '<select id="signup-category" name="signup[category]">'.$options.'</select>',

					'captcha' => 'What is two plus three? <input id="signup-captcha" type="text" name="signup[captcha]" value="" />',
					'submit' => '<input id="signup-submit" type="submit" name="signup[submit]" value="Submit" />',
				);

				// BUILD RESPONSE

				$RESULT = Array();
				$RESULT['FEED'] = $feed;
				$RESULT['HINT'] = $FEEDS[ $feed ][ 'hint' ];
				$RESULT['DEFAULT'] = file_get_contents(Conf::Get('APP:UI') . 'public/mailer.signup.php');

				$RESULT['RESULT']['FORM'] = $form;

				$RESULT['PROPERTIES'] = Array(
					//
					'FORM.name' => 'Newsletter: Contact Name',
					'FORM.email' => 'Email Input',
					'FORM.category' => 'Category Select',

					'FORM.captcha' => 'Captcha',
					'FORM.submit' => 'Submit Button',
					'FORM.error' => 'Submission Errors',
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


			if ($feed == 'MAILER.SIGNUP') {
				//
				$data = self::DataFeed($feed);

				return UI::Render('public/mailer.signup.php', $data['RESULT']);
			}
		}

		static function Notification()
		{
			$num = DB::Fetch('SELECT COUNT(*) FROM mailer_contacts WHERE DATE(date) = CURDATE() AND status > -1');

			return $num;
		}

		static function Timeline( $feed = NULL )
		{
			if (!$feed) {
				return array(
					'subscribers' => 'Newsletter Subscribers',
				);
			}

			if ($feed == 'subscribers') {
				return DB::AssociativeColumn("SELECT DATE(T.date), COUNT(*) FROM mailer_contacts AS T WHERE TRUE GROUP BY DATE(T.date) ORDER BY T.date DESC LIMIT 10");
			}

			return parent::Timeline();
		}

		static function RecipientFeed( $feed = NULL, $filters = NULL )
		{
			if (!$feed) {
				//
				return array(
					'MAILER.SUBSCRIBERS' => 'Newsletter Subscribers',
				);
			}

			if ($feed == 'MAILER.SUBSCRIBERS') {
				//
				$model = Model::MailerContact();

				if (isset($filters['category'])) {
					//
					if (intval($filters['category'])) {
						//
						$model->where('idcategory = ?', $filters['category']);
					}
				}
				if (isset($filters['name'])) {
					if ($filters['name'])
						$model->where("name LIKE '%" . $filters['name'] . "%'");
				}
				if (isset($filters['email'])) {
					if ($filters['email'])
						$model->where("email LIKE '%" . $filters['email'] . "%'");
				}
				if (isset($filters['date'])) {
					if (isset($filters['date'][0])) {
						//
						if ($filters['date'][0])
							$model->where("DATE(`date`) >= ?")
								->args($filters['date'][0]);
					}
					if (isset($filters['date'][1])) {
						//
						if ($filters['date'][1])
							$model->where("DATE(`date`) <= ?")
								->args($filters['date'][1]);
					}
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
						if ($model->category) {
							$other[] = $model->category;
						}
						$other[] = 'Registered '.$model->date;

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

				$categories = DB::AssociativeColumn("SELECT 0, '-' UNION (SELECT id, name FROM mailer_categories WHERE status = 1 ORDER BY name ASC)");
				$input = new Input('category');
				$input->Type(Input::F_SELECT)->Options($categories)->Title('Category');
				if (isset($filters['category'])) {
					$input->Value($filters['category']);
				}
				$RESULT['FILTERS']['category'] = $input;

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

				$input = new Input('date');
				$input->Type(Input::F_DATERANGE)->Title('Registered Between')->Width('143px');
				if (isset($filters['date'])) {
					$input->Value($filters['date']);
				}
				$RESULT['FILTERS']['date'] = $input;


				return $RESULT;
			}
		}

	}

?>