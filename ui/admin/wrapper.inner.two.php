

	<!-- Begin content -->
	<div id="content">
		<div class="inner">
			<h1>{$MODULE}</h1>

			{include 'admin/.shared.sections.php'}

			{if isset($HEADER)}
				{$HEADER}
			{/if}

			<!-- Begin one column window -->
			<div class="twocolumn">

				<div class="column_left column_large">
					<div class="header">
						<span>{$SECTION}</span> {if isset($SUBSECTION)}<span class="filtered">{$SUBSECTION}</span>{/if}

						{include 'admin/.shared.toolbar.php'}
						{include 'admin/.shared.tabbar.php'}
					</div>

					<br class="clear"/>
					<div class="content">

						{include 'admin/.shared.alerts.php'}

						{$CONTENT}

					</div>
				</div>

				{foreach $PANELS as $PANEL}
				<!-- Begin right column window -->
				<div class="column_right column_small">
					<div class="header">
						<span>{$PANEL.TITLE}</span>

						{if isset($PANEL.TOOLBAR)}
							{php $TOOLBAR = $PANEL.TOOLBAR}
							{include 'admin/.shared.toolbar.php'}
						{/if}
						{if isset($PANEL.TABBAR)}
							{php $TABBAR = $PANEL.TABBAR}
							{include 'admin/.shared.tabbar.php'}
						{/if}
					</div>
					<br class="clear"/>
					<div class="content" style="">

						{$PANEL.CONTENT}

						<br class="clear"/>
					</div>
				</div>
				<!-- End right column window -->

				<br class="clear_right"/>
				{/forech}

			</div>
			<!-- End one column window -->

			{if isset($FOOTER)}
				{$FOOTER}
			{/if}

		</div>

		<br class="clear"/><br class="clear"/>

		{include 'admin/.shared.footer.php'}

	</div>


