<?php

	// AUTH

	class Auth
	{
		//

		static function Login($user, $pass)
		{
			$u = Model::User();
			$u->where('email = ? AND (pass = ? OR pass = ?)')
				->args($user, md5($user.':'.$pass), $pass)
				->execute();

			if ($u->found()) {

				Session::Set('ACCOUNT.ID', $u->id);
				Session::Set('ACCOUNT.NAME', $u->firstname ? $u->firstname : $u->fullname);
				Session::Set('ACCOUNT.TOKEN', $u->token);
				Session::Set('ACCOUNT.EMAIL', $u->email);

				Session::Set('ACCOUNT.GROUP.ID', 0);
				Session::Set('ACCOUNT.GROUP.TOKEN', '');

				if ($u->idgroup) {

					$g = Model::UserGroup($u->idgroup);

					Session::Set('ACCOUNT.GROUP.ID', $g->id);
					Session::Set('ACCOUNT.GROUP.TOKEN', $g->token);
				}

				$s = Model::SupportUser();
				$s->where('email = ?', $user)
					->execute();

				if ($s->found()) {
					Session::Set('SUPPORT.ID', $s->id);
					Session::Set('SUPPORT.NAME', $s->firstname ? $s->firstname : $s->fullname);
					Session::Set('SUPPORT.EMAIL', $s->email);
					Session::Set('SUPPORT.TOKEN', $s->token);
					Session::Set('SUPPORT.COMPANY.ID', $s->idcompany);
					Session::Set('SUPPORT.COMPANY.NAME', $s->company );
				}

				Session::Set('TWITTER.access.token', Model::Settings('social.twitter.token')->value);
				Session::Set('TWITTER.access.secret', Model::Settings('social.twitter.secret')->value);

				Session::Set('FACEBOOK.access.token', Model::Settings('social.facebook.token')->value);

				return TRUE;
			}

			return FALSE;
		}

		static function Logout()
		{
			Session::Clear('ACCOUNT');
			Session::Clear('SUPPORT');
			Session::Clear('TWITTER');
			Session::Clear('FACEBOOK');
		}

		static function LoggedIn()
		{
			return Session::Get('ACCOUNT.ID');
		}

		static function Grant($req)
		{
			if (!self::LoggedIn()) {
				return FALSE;
			}

			if (!$req) {
				return TRUE;
			}

			if (is_array($req)) {
				$granted = false;

				foreach ($req as $child => $title) {
					$granted = $granted || static::Grant($child);
				}

				return $granted;
			}

			if (in_array($req, Util::split(Session::Get('ACCOUNT.TOKEN'))))
			{
				return TRUE;
			}

			if (in_array($req, Util::split(Session::Get('ACCOUNT.GROUP.TOKEN'))))
			{
				return TRUE;
			}

			return FALSE;
		}

	}

?>