<?php

	// SupportInvoices

	class SupportInvoices extends AdminModule
	{
		static
			$TITLE  = 'Invoices',
			$IDENT  = 'support.invoices';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/page.attachment.png',
				'LARGE' => 'icon.large/page.attachment.png',
			);

		static
			$PERMISSION = Array(
				'list' 		=> 'List Invoices',
				'details' 	=> 'View Details',
				'save' 		=> 'Add/Edit Details',
				'delete' 	=> 'Delete',
			);

		static
			$AUTH = 'support.invoices';


		static function Model($id = NULL)
		{
			return Model::SupportInvoice($id);
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

			parent::GET_List($page);
		}

		static function GET_Details()
		{
			$model = static::Model( intval( Request::URL('id') ) );

            UI::set('INVOICE', $model->record());
            UI::set('INVOICE.QUOTES', $model->quotes->export());

			UI::set('CONTENT', UI::Render('admin/support.invoice.details.php'));

			UI::set('SECTION', 'Invoice #'.$model->name);

			self::Wrapper();
		}

		static function GET_Edit()
		{
			parent::GET_Details();
		}

		static function EditForm( $model )
		{
			$fields = parent::EditForm( $model );

			$companies = Model::SupportCompany();
			$companies->execute();

			$companies = DB::AssociativeColumn("SELECT id, name FROM support_companies WHERE id IN ??", array( $companies->slice('id') ));
			$fields['idto']->Type( Input::F_SELECT )->Options( $companies );

			$fields['start']->Type( Input::F_DATE );
			$fields['end']->Type( Input::F_DATE );

			return $fields;
		}

		static function POST_Save()
		{
			$model = parent::POST_Save();

			if (!$model->quotes->found()) {
				//
				$quotes = Model::SupportQuote();
				$quotes->where('idto = ?', $model->idto)
					->where('id NOT IN (SELECT idquote FROM support_invoices_quotes)')
					->execute();

				if ($quotes->found()) {
					//
					$quotes->reset();
					while ($quotes->next()) {
						//
						DB::Execute('INSERT INTO support_invoices_quotes SET idinvoice = ?, idquote = ?', array($model->id, $quotes->id));
					}
				}
			}

			return $model;
		}

		static function GET_File()
		{
			$fields = Array();
			$model = static::Model();

			// FILE
			$input = new Input('file');
			$input->Type(Input::F_FILE)->Title( 'Invoice File' )->Context('');

			$fields['file'] = $input; //->Export();

			UI::set('FIELDS', $fields);

			UI::Set('TITLE', 'Upload Invoice File');
			UI::Set('TARGET', Request::$URL . '/file/' . intval(Request::URL('id')));
			UI::Set('CONTENT', UI::Render('admin/.shared.edit.php'));

			self::Popup();
		}

		static function POST_File()
		{
			$invoice = Model::SupportInvoice( intval(Request::URL('id')) );

			$temp = File::Temporary('file', 'support/tmp');
			$perm = File::Permanent($temp, 'support/invoice', $invoice->id);

			if ($perm) {
				//
				$invoice->file = $perm['name'];
				$invoice->disk = $perm['server_name'];

				$invoice->save();
			}
		}

		static function GET_Download()
		{
			$model = Model::SupportInvoice( intval(Request::URL('id')) );

			File::Send( Conf::Get('APP:UPLOAD').$model->disk, $model->file );
		}

		static function POST_Remove()
		{
			$items = Request::POST('items');

			if (is_array($items)) {
				//
				if (count($items)) {
					//
					DB::Execute('DELETE FROM support_invoices_quotes WHERE idinvoice = ? AND idquote IN ??', array(Request::URL('id'), Request::POST('items')));
				}
			}
		}

		static function GET_Add()
		{
			$model = Model::SupportInvoice( intval(Request::URL('id')) );

			$quotes = Model::SupportQuote();
			$quotes->where('idto = ?', $model->idto)
				->where('id NOT IN (SELECT idquote FROM support_invoices_quotes)')
				->execute();

			UI::Set('QUOTES', $quotes->export());

			UI::Set('TITLE', 'Add Quotes to Invoice');
			UI::Set('TARGET', Request::$URL . '/add/' . intval(Request::URL('id')));
			UI::Set('CONTENT', UI::Render('admin/support.invoice.quotes.php'));

			self::Popup();
		}

		static function POST_Add()
		{
			$model = Model::SupportInvoice( intval(Request::URL('id')) );
			$items = Request::POST('items');

			if (is_array($items)) {
				//
				foreach ($items as $item) {
					//
					DB::Execute('INSERT INTO support_invoices_quotes SET idinvoice = ?, idquote = ?', array($model->id, $item));
				}
			}
		}

	}

?>