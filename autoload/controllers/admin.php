<?php

	// Admin

	class Admin extends AdminBase
	{
		static
			$ROOT = '';

		// used for Main Menu, Module and Section Structure
		static
			$SITEMAP = Array(
				'Dashboard',

				'Crm' => Array(
					'CrmContacts',
					'CrmCompanies',
					'CrmNotes',
					'CrmCalendar',
					'CrmLabels',
				),

				'Shop' => Array(
					'ShopOrders',
					'ShopCalendar',
					'ShopProducts',
					'ShopCategories',
					'ShopDiscounts',
					'ShopTaxes',
					'ShopSettings',
				),

				'Cms' => Array(
					'CmsPages',
					'CmsTemplates',
					'CmsBlocks',
					'CmsContent',
					'CmsMenus',
					'CmsDocuments',
					'CmsSettings',
				),

				'CmsContent' => Array(
					'CmsArticles',
					'CmsComments',
					'CmsCategories',
					'CmsLabels',
				),

				'CmsDocuments',

				'Social' => Array(
					'SocialFacebook',
					'SocialTwitter',
					'SocialYoutube',
					'SocialSettings',
				),

				'Mailer' => Array(
					'MailerLetters',
					'MailerTemplates',
					'MailerCampaigns',
					'MailerContacts',
					'MailerLists',
					'MailerCategories',
					'MailerSettings',
				),

				'Contact' => Array(
					'ContactMessages',
					'ContactReplies',
					'ContactSubjects',
					'ContactFlags',
					'ContactSettings',
				),

				'Support' => Array(
					'SupportTickets',
					'SupportQuotes',
					'SupportTimes',
					'SupportInvoices',
					'SupportUsers',
					'SupportCompanies',
					'SupportSettings',
				),

				'Settings' => Array(
					'SettingsGeneral',
					'SettingsMail',
					'SettingsLicense',
					'Captcha',
					'Permission',
					'PermissionGroups',
					'Help',
				),

				'Logout',
			);

		// used for Admin routing
		static
			$ROUTES = Array(
				'/index' => 'Dashboard',

				'/help/@' => 'Help',

				'/settings' => 'Settings',
				'/settings/general' => 'SettingsGeneral',
				'/settings/mail' => 'SettingsMail',
				'/settings/license' => 'SettingsLicense',
				//'/settings/license/gen/@' => 'SettingsLicenseGen',


				'/settings/captcha/@' => 'Captcha',
				'/settings/users/groups/@' => 'PermissionGroups',
				'/settings/users/@' => 'Permission',

				'/support' => 'Support',
				'/support/tickets/@' => 'SupportTickets',
				'/support/quotes/@' => 'SupportQuotes',
				'/support/times/@' => 'SupportTimes',
				'/support/invoices/@' => 'SupportInvoices',
				'/support/users/@' => 'SupportUsers',
				'/support/companies/@' => 'SupportCompanies',
				'/support/settings' => 'SupportSettings',

				'/social' => 'Social',
				'/social/twitter/@' => 'SocialTwitter',
				'/social/youtube/@' => 'SocialYoutube',
				'/social/facebook/@' => 'SocialFacebook',
				'/social/settings/@' => 'SocialSettings',

				'/crm' => 'Crm',
				'/crm/contacts/address/@' => 'CrmAddresses',
				'/crm/contacts/@' => 'CrmContacts',
				'/crm/companies/@' => 'CrmCompanies',
				'/crm/labels/@' => 'CrmLabels',
				'/crm/notes/@' => 'CrmNotes',
				'/crm/calendar' => 'CrmCalendar',
				'/crm/calendar/@date' => 'CrmCalendar',

				'/cms' => 'Cms',
				'/cms/menus/items/@' => 'CmsMenuItems',
				'/cms/menus/@' => 'CmsMenus',
				'/cms/pages/@' => 'CmsPages',
				'/cms/blocks/@' => 'CmsBlocks',
				'/cms/templates/@' => 'CmsTemplates',
				'/cms/documents' => 'CmsDocuments',
				'/cms/documents/@handler' => 'CmsDocuments',
				'/cms/settings/@' => 'CmsSettings',

				'/cms/content' => 'CmsContent',
				'/cms/articles/@' => 'CmsArticles',
				'/cms/comments/@' => 'CmsComments',
				'/cms/categories/@' => 'CmsCategories',
				'/cms/labels/@' => 'CmsLabels',

				'/shop' => 'Shop',
				'/shop/products/@' => 'ShopProducts',
				'/shop/orders/@' => 'ShopOrders',
				'/shop/deliveries/@' => 'ShopDeliveries',
				'/shop/calendar' => 'ShopCalendar',
				'/shop/calendar/@date' => 'ShopCalendar',
				'/shop/discounts/@' => 'ShopDiscounts',
				'/shop/taxes/@' => 'ShopTaxes',
				'/shop/categories/@' => 'ShopCategories',
				'/shop/settings' => 'ShopSettings',

				'/mailer' => 'Mailer',
				'/mailer/letters/@' => 'MailerLetters',
				'/mailer/campaigns/@' => 'MailerCampaigns',
				'/mailer/templates/@' => 'MailerTemplates',
				'/mailer/contacts/@' => 'MailerContacts',
				'/mailer/categories/@' => 'MailerCategories',
				'/mailer/lists/@' => 'MailerLists',
				'/mailer/settings' => 'MailerSettings',
				'/mailer/proxy' => 'MailerProxy',

				'/contact' => 'Contact',
				'/contact/messages/@' => 'ContactMessages',
				'/contact/replies/@' => 'ContactReplies',
				'/contact/subjects/@' => 'ContactSubjects',
				'/contact/flags/@' => 'ContactFlags',
				'/contact/settings' => 'ContactSettings',

				'/login' => 'Login',
				'/logout' => 'Logout',
			);

		static function onLoad()
		{
			//print 'admin/';
		}

	}

?>