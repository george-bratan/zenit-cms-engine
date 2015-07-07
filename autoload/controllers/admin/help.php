<?php

	// Help

	class Help extends AdminPage
	{
		static
			$TITLE = 'Manual';

		static
			$ICON = Array(
				'SMALL' => 'icon.small/briefcase.png',
				'LARGE' => 'icon.large/briefcase.png',
			);

		static function Get()
		{
			if (Request::URL('handler')) {
				//
				UI::set('TITLE', 'Help');
				UI::set('CONTENT', UI::Render('admin/help.' . Request::URL('handler') . '.php'));

				static::Popup();
				return;
			}

			$help = array();

			foreach (Admin::$SITEMAP as $module => $sections) {
				//
				if (is_array($sections)) {
					//
					if ($module::$HELP) {
						//
						$help[] = $module::$HELP;
					}

					foreach ($sections as $section) {
						//
						if ($section::$HELP) {
							//
							$help[] = $section::$HELP;
						}
					}
				}
				else {
					//
					$module = $sections;

					if ($module::$HELP) {
						//
						$help[] = $module::$HELP;
					}
				}
			}

			$content = '';
			foreach ($help as $ctx) {
				//
				$content .= UI::Render('admin/help.' .$ctx. '.php');
			}

			UI::set('CONTENT', $content);

			UI::set('TOOLBAR.manual', array(
					'url' => Admin::$ROOT . '/manual.pdf',
					'rel' => 'none',
					'icon' => 'icon.small/disk.png',
					'title' => 'Download Manual',
					'attr' => array( 'target' => '_blank' ),
				)
			);

			parent::Get();
		}

	}

?>