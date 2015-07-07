


	<!-- Begin content -->
	<div id="content">
		<div class="inner">
			<h1>{$MODULE}</h1>

			{include 'admin/.shared.sections.php'}

			{if isset($HEADER)}
				{$HEADER}
			{/if}

			<!-- Begin one column window -->
			<div class="onecolumn">

				<div class="header">
					<span>{$SECTION}</span> {if isset($SUBSECTION)}<span class="filtered">{$SUBSECTION}</span>{/if}

					{include 'admin/.shared.toolbar.php'}
				</div>

				<br class="clear"/>
				<div class="content">

					{include 'admin/.shared.alerts.php'}

					{$CONTENT}

				</div>

			</div>
			<!-- End one column window -->

			{if isset($FOOTER)}
				{$FOOTER}
			{/if}

		</div>

		<br class="clear"/><br class="clear"/>

		{include 'admin/.shared.footer.php'}

	</div>


