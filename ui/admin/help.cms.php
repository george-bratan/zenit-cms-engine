
<div class="help">
	<h1>Content Publishing (CMS)</h1>

	<p>
		Your public <strong>Pages</strong> use an inheritance system to render itself.
		When you create a new page you can choose to start from scratch or inherit an existing template.
		<strong>Templates</strong> are very similar to regular pages, but they may contain empty cells or slots,
		named <strong>Editable Blocks</strong>, which will be filled by all pages inheriting the template.
	</p>

	<p>
		By inheriting a template, your page will contain all information from the parent template and you will be required to fill in the missing empty slots
		(Editable Blocks). Templates are also built on inheritance from one another, so you may have a root template which is inherited by a child template
		which in turn is inherited by a page.
	</p>

	<p style="text-align:center">
		<img style="width:95%" src="{$CONF.WWW.ROOT}/admin/images/help/flow.template.png" alt="" />
	</p>

	<p style="text-align:center">
		<strong>Figure: Page / Template Inheritance</strong>
	</p>

	<p style="text-align:center">
		Child Page (green) inherits Child Template (red) which inherits Blank Template (blue.)
	</p>

	<p>
		On the Page detail screen, you will find 4 sections:
		<ul>
			<li><strong>Preview</strong>: actual page, as it will be rendered for the public site</li>
			<li><strong>HTML</strong>: contains the html code for each slot your page is required to fill
			<li><strong>CSS</strong>: contains the CSS code specific to this page.
				Note that all CSS from parent templates is also inherited, even if you won't see it here</li>
			<li><strong>JS</strong>: contains any javascript code you wish to insert in this page.</li>
		</ul>
	</p>

	<p>
		Whenever you inherit a template, your child page will only be allowed to fill in the editable blocks allowed by the parent template. You may add new
		editable slots in a child template for future children to inherit and fill.
	</p>

	<p>
		To enter content in an editable block, click the editable cell in your <strong>Preview</strong> screen and a <strong>Content</strong> box will pop out.
		You can use this box to add Rich text, Images, Links (to documents or internal pages) and Other components.
	</p>

	<p>
		If you click the <strong>Other</strong> components tab, you will be presented with a list of additional CMS components you can insert in the current
		editable slot. The first component is always a new <strong>Editable Block</strong>; by inserting this component, you are specifying that this is an
		empty slot that will be editable by any future child inheriting the current template. All other components are being pulled out from various modules
		exporting them and they usually contain module specific dynamic data (feeds) or forms. All feeds coming from other modules have a default HTML
		presentation layer and may be directly included in a page or template, but you are able to change this default presentation layer by using the
		<strong>Content Blocks</strong> sections.
	</p>

	<p style="text-align:center">
		<img style="width:95%" src="{$CONF.WWW.ROOT}/admin/images/help/content.box.png" alt="" />
	</p>

	<p>
		There are many cases when you want certain URL values to be passed to a dynamic component. For example, you want to create an article page, but since
		you don't want to create a separate page for every article, you will need to use an article ID to render a dynamic article block. This situation
		requires the use of <strong>URL Params</strong>, or <strong>@params</strong>. If you define your page url as <strong>/articles/@id</strong> , the page
		will be rendered when a visitor requests any url starting with <strong>/articles/XX</strong> . Ex: if user requests <strong>/articles/2</strong>, the
		page you defined will be matched as a candidate for rendering, the <strong>@id</strong> url param will be filled with the attribute <strong>2</strong>
		and sent to the <strong>Articles</strong> section to be rendered.
	</p>

	<p>
		To keep track of your page history, your CMS makes use of a <strong>version control system</strong>. A page always has a <strong>Working Copy</strong>,
		which is what you see in your Preview panel. This is a draft only version, available to administrators and is not yet published to the public. Once
		you're happy with your page, publish the copy by pressing the <strong>Publish</strong> button and enter a short text describing your changes. For every
		published version, the system keeps a copy in your history list (right-bottom panel) allowing you to:
		<ul>
			<li><strong>Revert</strong> an older copy to be the current Active Version (public version)</li>
			<li><strong>Load</strong> an older copy in to the editor, thus becoming the new Working Copy.</li>
		</ul>
	</p>

	<p>
		<strong>Content Blocks</strong>
		are used to render data streams obtained from other modules into a presentation layer suitable for the public site. Modules expose data streams with
		relevant information they handle, data which is relayed to the Content Blocks section which adds an HTML wrapper around the data for presentation
		purposes and forwards its result to the page renderer.
	</p>

	<p style="text-align:center">
		<img style="width:95%" src="{$CONF.WWW.ROOT}/admin/images/help/content.block.png" alt="" />
	</p>

	<p>
		All data streams contain a list of available filters. These fields are used to pre-filter the data stream coming from an exporting module, and only the
		resulting filtered entries will be rent to be rendered. Some data streams will also allow URL filtering through the use of <strong>@param</strong>
		parameters in the URL. When this is the case, a hint with all available URL filters will be displayed. Ex: if you need to display all products from a
		single category, you will select the Product List feed into your content block, and use the category filter box to choose the category you need to
		render.
	</p>

	<p>
		Known published data streams:
		<ul>
			<li><strong>Product List</strong>: a set of objects, each containing several product attributes with minimal list specific information.
				This stream is used for rendering a list of products.</li>
			<li><strong>Product Details</strong>: an object containing all product attributes.
				This stream is used for rendering a product detail block.</li>
			</li><strong>Contact Signup Form</strong>: contains signup specific form input boxes (name, email, etc)</li>
			<li><strong>Contact Login Form</strong>: contains login form input boxes</li>
			<li><strong>Menu</strong>: contains all menu items and sub-menus</li>
			<li><strong>Article List</strong>: list of objects, each with minimal article attributes.
				Used for rendering an article list. The article list can be filtered by category or keywords.</li>
			<li><strong>Article Details</strong>: object with all article attributes, used to render an article detail page.</li>
			<li><strong>Newsletter Subscribe Form</strong>: contains a newsletter form input boxes.</li>
			<li><strong>Contact Form</strong>: contains all contact form input boxes</li>
			<li><strong>Contact Message Thread</strong>: contains an object representing the message to be rendered and a list of comments.</li>
		</ul>
	</p>

	<p>
		The Content Block details page features 3 main columns:
		<ul>
			<li>HTML wrapper column (left)</li>
			<li>Data Feed and Data Attributes (center)</li>
			<li>Data Filters (right)</li>
		</ul>
	</p>

	<p>
		The HTML panel contains 3 tabs: Visual/HTML, CSS and Javascript. Though there are 4 buttons available, the Visual and HTML editor are in fact handling
		the same information, that is the HTML tags wrapping the data. When a Content Block assembles it's data, it stitches up it's 3 panels of code (html,
		css and javascript) together and sends this chunk to the renderer for integration with the rest of the page.
	</p>

	<p>
		The HTML panel also allows the use of a simple scripting language, named <strong>Logicode</strong>, for cases where you want your information to be
		displayed in a dynamic manner. Logicode allows the following PHP similar instructions:

		<ul>
			<li><pre>&#123;$ATTR&#125;</pre>: is replaced by the value contained in the <strong>$ATTR</strong> attribute.</li>
			<li><pre>&#123;print $ATTR&#125;</pre>: same as above, it is replaced by the value of <strong>$ATTR</strong>,
				but allows the use of most common PHP functions, like <strong>&#123;print intval($attr)&#125;</strong></li>
			<li><pre>&#123;if ($attr == "value")&#125; &lt;html code&gt; &#123;/if&#125;</pre>
				<ul>
					<li>the html code within the IF statement is included in the page only if the condition is evaluated as being True.</li>
				</ul></li>
			<li><pre>&#123;if ($attr == "value")&#125; &lt;html code&gt; &#123;else&#125; &lt;other code&gt; &#123;/if&#125;</pre>
				<ul>
					<li>if the condition is computed as True, the first part of the instruction is included in the page and the second is removed.
						Similarly, the second part is included if the condition is computed as being False.</li>
				</ul></li>
			<li><pre>&#123;foreach ($list as $attr)&#125; &lt;html code&gt; &#123;/foreach&#125;</pre>
				<ul>
					<li>repeats the html code within the for-each loop for each element in <strong>$list</strong>,
						where <strong>$list</strong> is a set of elements, like a product list.</li>
				</ul></li>
		</ul>
	</p>

	<p>&nbsp;</p>

	<p>
		A typical example of <strong>Logicode</strong> is an image gallery built using an article's image list:

		<pre>
	&#123;if count($ARTICLE.images) &gt; 1&#125;
		&lt;ul class="gallery"&gt;

			&#123;foreach $ARTICLE.images as $IMAGE&#125;
			&lt;li&gt;
				&lt;a href="&#123;$IMAGE.url&#125;" title="&#123;$IMAGE.title&#125;"&gt;
					&lt;img src="&#123;$IMAGE.url&#125;" alt="&#123;$IMAGE.title&#125;" /&gt;&lt;/a&gt;

				&lt;div class="title"&gt;&#123;$IMAGE.title&#125;&lt;/div&gt;
				&lt;div class="description"&gt;&#123;$IMAGE.description&#125;&lt;/div&gt;
			&lt;/li&gt;
			&#123;/foreach&#125;

		&lt;/ul&gt;
	&#123;/if&#125;
		</pre>
	</p>

	<p>
		Once you select a <strong>Data-Feed</strong> for a Content Block, all available attributes (center column) and filters (right column) are loaded into
		the Content Block page. Double-click an attribute to insert it in the active HTML panel.
	</p>

	<p>
		<em>
			Note: The first data attribute is always the <strong>Default Layout</strong> coming with the selected Data-Feed.
			Use this layout to get an understanding of how to build your own custom layout.
		</em>
	</p>

	<p>
		<strong>Menus</strong>
		are tree like structures of fixed title/link pairs. You can define any number of menus, depending on your needs. Ex: main menu with your central
		navigation, side menu going further into more detailed navigation, footer menu containing privacy and contact links, etc.
	</p>

	<p>
		Note: defining a menu is not enough for it to be included in the public website. To add a menu in a public page or template, you must include the
		specific menu in a page through the Content Box (Page Details - Preview - Content Box - Other - Menus - Menu X).
	</p>

	<p>
		The <strong>Settings</strong> tab allows an administrator to open or close the public site, and setup common functional pages. Once you close your
		public site, it will enter in "Maintenance" mode, meaning that only one page will be served to your public visitors: the Maintenance page. Additional
		functional pages are:
		<ul>
			<li><strong>Home Page</strong>, the first page presented to the visitor if no page is specified</li>
			<li><strong>Error Page</strong>, presented to the visitor if an error occurred and the page he/she requested is not available.
				For example, if the page no longer exists, but the user had it bookmarked.</li>
			<li><strong>Maintenance Page</strong>, used when your website is closed.
				This is the only page delivered to the user when your site is marked as Closed.</li>
		</ul>
	</p>

</div>
