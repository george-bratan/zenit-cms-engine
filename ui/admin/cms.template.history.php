

		<h3>Previous Versions</h3>

		<ul class="style">
			{if !count($VERSIONS)}
			<li><div style="font-style:italic">No versions have been published.</div></li>
			{/if}

			{foreach $VERSIONS as $VERSION}
			<li>
				<strong>{$VERSION.date}</strong> by <strong>{$VERSION.author}</strong> -
					{if $VERSION.number == $TEMPLATE.version}
						<span style="color:green">Published</span>
					{else}
						<a href="{print Request::$URL}/revert/{$VERSION.id}" rel="modal" title="Publish this version">Revert</a>
					{/if}
					- <a href="{print Request::$URL}/load/{$VERSION.id}" rel="modal" title="Load this version in the editor">Load</a>
			    <div style="font-style:italic">{print nl2br($VERSION.details)}</div>
			</li>
			{/foreach}
		</ul>