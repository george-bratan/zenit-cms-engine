
<div id="inspector">
	<div class="tab" id="tab_iseo">

		<h4>Meta-Data</h4>
		<!--p>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in porta lectus. Maecenas dignissim enim quis ipsum
			mattis aliquet. Maecenas id velit et elit gravida bibendum. Duis nec rutrum lorem. Donec egestas metus a risus
			euismod ultricies. Maecenas lacinia orci at neque commodo commodo. Donec egestas metus a risus
			euismod ultricies.
		</p-->

		<form class="ajax meta" action="{print Request::$URL}/meta/{$TEMPLATE.id}" method="post">
		<ul class="style">
			<li>
				<label for="url">URL</label>
				<input type="text" name="url" id="url" title="/about/contact" value="{$TEMPLATE.url}" />
			</li>
			<li>
				<label for="meta-title">Title</label>
				<input type="text" name="meta_title" id="meta-title" title="website name" value="{$TEMPLATE.meta_title}" />
			</li>
			<li>
				<label for="meta-keywords">Keywords</label>
				<input type="text" name="meta_keywords" id="meta-keywords" title="keyword 1, keyword 2" value="{$TEMPLATE.meta_keywords}" />
			</li>
			<li>
				<label for="meta-description">Description</label>
			</li>
			<li>
				<textarea id="meta_description" name="meta_description" title="Website Name is a manufacturing company ...">{$TEMPLATE.meta_description}</textarea>
			</li>
			<li>
				<input type="submit" id="save-meta" value="Update" style="float:right">
			</li>
		</ul>
		</form>

	</div>

	<div class="tab" id="tab_icss">

		<h4>CSS Inspector</h4>

		<div style="max-height:300px; overflow-y:scroll;">
			<ul class="css-attributes">
				<li><label for="css-id">id</label><input type="text" id="css-id" value=""></li>
				<li><label for="css-class">class</label><input type="text" id="css-class" value=""></li>
			</ul>

			<ul class="css-attributes" id="css">
			</ul>
		</div>

	</div>
</div>