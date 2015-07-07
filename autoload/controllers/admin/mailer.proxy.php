<?php

	// MailerProxy

	class MailerProxy extends Page
	{

		static function onLoad()
		{
			//
		}

		static function Get()
		{
			if (Request::GET('m')) {
				//
				$message = Model::MailerMessage( intval(Request::GET('m')) );
				$message->open = 2;

				$message->save();
			}

			if (Request::GET('u')) {
				//
				Request::Redirect(urldecode( Request::GET('u') ));
				return;
			}

			Application::Error(404);
		}

	}

?>
