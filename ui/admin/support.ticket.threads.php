
	<!-- Begin one column window -->
	<div class="onecolumn">

		<div class="header">
			<span></span>

			<div class="toolbar" id="threads" rel="tabs" style="float:left">
				{php $index = 0}
				{foreach $THREADS as $THREAD}
					{php $tab_type = 'middle'}
					{php $index++}

					{if $index == 1}
						{php $tab_type = 'left'}
					{/if}

					{if $index == count($THREADS)}
						{php $tab_type = 'right'}
					{/if}

					<a id="btn_{$THREAD.id}" tab="tab_{$THREAD.id}" class="{$tab_type}" href="javascript:void(0);" rel="tab">
						{$THREAD.name}
						{if $THREAD.new}
							<em style="color:red">({$THREAD.new})</em>
						{/if}
					</a>
				{/foreach}
			</div>

			<div class="toolbar" style="float:right">
				<a rel="modal" href="{print Request::$URL}/newthread/{$TICKET.id}">New Thread</a>
			</div>

			<br class="clear">

		</div>

		<br class="clear"/>
		<div class="content">

			{foreach $THREADS as $THREAD}
			<div class="tab" id="tab_{$THREAD.id}">
				<table class="data" style="width:100%" cellpadding="0" cellspacing="0">

					<tr><th style="width:20%">User</th>
						<th style="width:80%">Comment</th></tr>

					{foreach $THREAD.comments as $COMMENT}
					<tr><td style="vertical-align:top">
							{if $COMMENT.user}
								<strong>{$COMMENT.user}</strong><br>
								{$COMMENT.company}<br>
							{else}
								<span style="font-weight:bold; color:red;">System</span><br />
							{/if}
							{$COMMENT.date}
							{if $COMMENT.new}
								<span style="color:red">(new)</span>
							{/if}</td>
						<td>
							{if $COMMENT.user}
								<p>{print Util::Links($COMMENT.content)}</p>
							{else}
								<p style="color:red">{print Util::Links($COMMENT.content)}</p>
							{/if}
							{if $COMMENT.file}
								<p><br></p>
								<p><strong>Attachment:</strong> {$COMMENT.file.original} ({$COMMENT.file.size} bytes) &nbsp; - &nbsp; <a href="{print Request::$URL}/download/{$COMMENT.file.id}">Download</a></p>
							{/if}
							</td></tr>
					{/foreach}

					<tr><td style="vertical-align:top"><strong>{$SUPPORT.NAME}</strong><br>
							{$SUPPORT.COMPANY.NAME}<br>
							{print DB::Fetch('SELECT NOW()')}</td>
						<td>
						    <form class="ajax" action="{print Request::$URL}/comment/{$THREAD.id}" method="post" enctype="multipart/form-data">
								<textarea class="wysiwyg" name="VALUES[content]" style="width:100%; height:200px;"></textarea>
								<input type="button" value="Submit Comment" style="margin-top:10px; margin-bottom:5px;"
									onclick="javascript:$(this).closest('form').submit();">
								<span style="float:right; margin-top:10px;">Attach File: <input type="file" name="file" /></span>
							</form></td></tr>

				</table>
			</div>
			{/foreach}

		</div>

	</div>
	<!-- End one column window -->


	<script type="text/javascript">

 	/*
 	$(document).ready(function(){

	 	$('div.toolbar a[rel=tab]').click(function(){
			//switch menu
			$(this).parent().parent().find('a[rel=tab]').removeClass('active');
			$(this).addClass('active');

			//show tab1 content
			$('.tab').addClass('hide');
			$('#' + $(this).attr('tab')).removeClass('hide');

			$.cookie("ADMIN.SUPPORT.LAST", $(this).attr('id'));
		});

		if ($('#' + $.cookie("ADMIN.SUPPORT.LAST")).length)
			$('#' + $.cookie("ADMIN.SUPPORT.LAST")).click();
		else
			$('div.toolbar a[rel=tab]').filter(':first').click();

	});
	*/

	</script>