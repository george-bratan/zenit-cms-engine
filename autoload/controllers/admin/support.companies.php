<?php

	// SupportCompanies

	class SupportCompanies extends AdminModule
	{
		static
			$TITLE  = 'Companies',
			$IDENT  = 'support.companies';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/user.group.png',
				'LARGE' => 'icon.large/user.group.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Companies',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'support.companies';


		static function OnLoad()
		{
			//static::$PERMISSION['access'] = static::$PERMISSION['details'];
		}

		static function Model($id = NULL)
		{
			return Model::SupportCompany($id);
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			if (!Request::URL('id')) {
			    //
				$sql = new SQL();
				$sql->insert('support_companies_companies')
					->set('idcompany1 = ?', Session::Get('SUPPORT.COMPANY.ID'))
					->set('idcompany2 = ?', $model->id)
					->execute();

				$user = Model::SupportUser();
				$user->defaults();

				$user->idcompany = $model->id;
				$user->firstname = $model->name;
				$user->email = $model->email;
				$user->pass = substr(md5($model->name .':'. time()), 0, 6);

				$user->save();

				// SEND INVITATION

				$DATA = array(
					'SENDER' => Session::Get('SUPPORT.NAME'),
					'RECIPIENT' => $model->name,
					'WEBSITE' => Conf::Get('APP:NAME'),
					'LOGIN' => 'http://' . Request::$HOST . '/support/login',
					'EMAIL' => $user->email,
					'PASSWORD' => $user->pass,
				);
				$subject = 'Invitation to connect on '.Conf::Get('APP:NAME');
				$message = UI::Render('admin/support.company.notification.php', $DATA);

				if ( Mail::Send($user->email, $subject, $message) ) {
					//
				}
				else {
					//
					$info = '';
					$info .= '<p>The invitation could not be sent. Please relay the following information to <strong>'.$model->name.'</strong>:';
					$info .= '<ul style="padding:auto; margin:auto; list-style:none; margin-left:15px">
							<li><label style="display:inline-block; width:100px;">Login URL:</label> <a href="'.$DATA['LOGIN'].'">'.$DATA['LOGIN'].'</a></li>
							<li><label style="display:inline-block; width:100px;">Email:</label> <strong>'.$DATA['EMAIL'].'</strong></li>
							<li><label style="display:inline-block; width:100px;">Password:</label> <strong>'.$DATA['PASSWORD'].'</strong></li>
						</ul>';
					$info .= '</p>';

					UI::Set('MESSAGE.ERROR', (Mail::$ERROR ? Mail::$ERROR : 'Mail Server Error.').$info);

					UI::Serve('admin/.shared.alerts.php');
				}
			}

			return $model;
		}

	}

?>