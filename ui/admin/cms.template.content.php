

	<div style="min-width:900px; max-height:500px; overflow:hidden;">
		<p>
			<div style="float:left">
			{include 'admin/.shared.tabbar.php'}
			</div>
			<br class="clear">

		</p>
		<br/>

		<div id="tab_article" class="tab">

			<!--
			<p>
				<label>Title:</label><br/>
				<input id="article-title" type="text" style="width:388px"/>
			</p>
			<br/>
			-->

			<p>
				<label>Article Content:</label><br/>
				<textarea id="wysiwyg" style="width:98.5%; height:330px;"></textarea>
			</p>
			<br/>
			<p>
				<input type="button" value="Insert" id="insert-article" />
				<input type="button" value="Close" class="close" />
			</p>
		</div>

		<div id="tab_image" class="tab hide">
			<p>
				<label>Image:</label><br/>
				<input id="image-url" name="image-url" type="text" title="http://" style="width:300px"/>
				<input type="button" id="select-image" class="select-file" value="Select Image" />
			</p>
			<br/>
			<div id="image_filemanager_container" style="display:none">
				<iframe id="image_filemanager" src="" width="100%" height="380" style="background-color:#FFF; border:1px solid #CCC;">
				  <p>Your browser does not support iframes.</p>
				</iframe>
			</div>
			<div id="image_preview">
				<label>Preview:</label><br/>
				<div id="image-container" style="width:49%; height:303px; overflow:auto; float:left; border: 1px solid #CCCCCC; background:#FFF;"></div>
				<div id="attr-container" style="width:49%; height:260px; overflow:auto; float:right;">
					<label>Dimensions:</label><br/>
					<input id="image-width" name="image-width" type="text" title="100px" style="width:50px"/>
					x
					<input id="image-height" name="image-height" type="text" title="100px" style="width:50px"/>
					<br/><br/>
					<label>Link To:</label><br/>
					<input id="image-link" name="image-link" type="text" title="http://" style="width:95%"/>
				</div>
			</div>
			<br class="clear"/><br/>
			<p>
				<input type="button" value="Insert" id="insert-image" />
				<input type="button" value="Close" class="close" />
			</p>
		</div>

		<div id="tab_link" class="tab hide">
			<p>
				<label>URL:</label><br/>
				<input id="link-url" name="link-url" type="text" title="http://" style="width:300px"/>
				<input type="button" id="select-link" class="select-file" value="Select Document" />
			</p>
			<br/>
			<div id="link_filemanager_container" style="display:none">
				<iframe id="link_filemanager" src="" width="100%" height="380" style="background-color:#FFF; border:1px solid #CCC;">
				  <p>Your browser does not support iframes.</p>
				</iframe>
			</div>
			<div id="link_pages">
				<label>Select an Internal Page:</label><br/>
				<select id="link-list" multiple="true" style="width:49%; height:303px; overflow:auto; float:left; border: 1px solid #CCCCCC">

					{foreach $PAGES as $url => $page}
						<option value="{$url}">{$page}</option>
					{/foreach}

				</select>
				<div id="link-attr-container" style="width:49%; height:260px; overflow:auto; float:right;">
					<label>Link Text:</label><br/>
					<input id="link-text" name="ident" type="text" title="Click Here" style="width:95%"/>
					<br/><br/>
					<label>Open in:</label><br/>
					<input type="radio" id="link-same" name="link_open" value="same" checked /><label for="link-same">Same Window</label>
					<input type="radio" id="link-new" name="link_open" value="new" /><label for="link-new">New Window</label>
				</div>
			</div>
			<br class="clear"/><br/>
			<p>
				<input type="button" value="Insert" id="insert-link" />
				<input type="button" value="Close" class="close" />
			</p>
		</div>

		<div id="tab_other" class="tab hide">
			<!--
			<p>
				<label>Title:</label><br/>
				<input id="component-title" type="text" style="width:388px"/>
			</p>
			<br/>
			-->
			<p>
				<label>Component:</label><br/>

				<div id="component-container" style="width:49%;">
					<select id="component-select" multiple="true" style="width:100%; height:368px; overflow:auto; float:left; border: 1px solid #CCCCCC">
						<option value="EDITABLE" style="padding-left:0px; font-weight:bold;">Editable Block</option>

						{foreach $FEED as $MODULE => $BLOCKS}
							<optgroup label="{$MODULE}" style="font-weight:bold; font-style:normal;">
							{foreach $BLOCKS as $ident => $block}
								<option value="{$ident}" style="padding-left:15px; font-weight:bold;">{print is_array($block) ? $block.title : $block}</option>
							{/foreach}
							</optgroup>
						{/foreach}

					</select>
				</div>

				<div id="component-attr-container" style="width:49%; height:360px; overflow:auto; float:right;">

					<div id="component-editable-attr" style="display:none">
						<label>Unique Block Name:</label><br/>
						<input id="editable-ident" name="ident" type="text" title="CONTENT" style="width:95%"/>
						<br/><br/>
					</div>

					<label>Min Size:</label><br/>
					<input id="min-width" name="min_width" type="text" title="100px" style="width:50px"/> x
					<input id="min-height" name="min_height" type="text" title="100px" style="width:50px"/>
					<br/><br/>
					<label>Max Size:</label><br/>
					<input id="max-width" name="max_width" type="text" title="200px" style="width:50px"/> x
					<input id="max-height" name="max_height" type="text" title="200px" style="width:50px"/>
					<br/><br/>

					{foreach $FEED as $MODULE => $BLOCKS}
						{foreach $BLOCKS as $ident => $block}
							{if is_array($block)}
								<div class="component-hint" rel="{$ident}" style="display:none">{$block.hint}</div>
							{/if}
						{/foreach}
					{/foreach}

				</div>
			</p>
			<br class="clear"/><br/>
			<p>
				<input type="button" value="Insert" id="insert-component" />
				<input type="button" value="Close" class="close" />
			</p>
		</div>

		<br class="clear"/>
		<br class="clear"/>
	</div>

