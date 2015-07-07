<?php

	// Mail

	class Mail
	{		static
			$ERROR = '';

		static function Send($to, $subject, $message, $mailer = 'mail')
		{			self::$ERROR = '';

			if (!Model::Settings( $mailer.'.host' )->value) {				//
				return mail($to, $subject, $message);			}

			require_once Conf::Get('APP:ROOT').'lib/mailer/class.phpmailer.php';

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->Host = Model::Settings( $mailer.'.host' )->value;
			//$mail->SMTPDebug  = 3;

			if (Model::Settings($mailer.'.ssl')->value) {
				//
				$mail->SMTPSecure = Model::Settings( $mailer.'.ssl' )->value;
			}

			if (Model::Settings($mailer.'.port')->value) {
				//
				$mail->Port = Model::Settings( $mailer.'.port' )->value;
			}

			if (Model::Settings( $mailer.'.smtp.auth' )->value == 'true') {
				//
				$mail->SMTPAuth = true;
				$mail->Username = Model::Settings( $mailer.'.smtp.user' )->value;
				$mail->Password = Model::Settings( $mailer.'.smtp.pass' )->value;
			}

			$mail->SetFrom(Model::Settings( 'general.contact.email' )->value, Model::Settings( 'general.website.name' )->value);
			$mail->Subject = $subject;

			$mail->MsgHTML( $message );
			$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";

			$mail->AddAddress($to);

			$sent = $mail->Send();

			if (!$sent) {				//				self::$ERROR = $mail->ErrorInfo;			}

			return $sent;		}	}

?>