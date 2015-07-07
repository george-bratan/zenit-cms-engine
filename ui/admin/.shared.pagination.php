

{if (isset($PAGES))}

	<!-- Begin pagination -->
	<div class="pagination">
		<a href="{print Request::$URL.'/page/'}{if $PAGES.index>0}{print $PAGES.index-1}{else}{$PAGES.index}{/if}">&lArr;</a>

		{if $PAGES.count > 10}
			{for $i = 0; $i < 5; $i++}

				<a href="{print Request::$URL.'/page/'}{$i}" {if $i == $PAGES.index}class="active"{/if}>{print $i+1}</a>

			{/for}

			&nbsp;...&nbsp;
			<input type="text" name="gotopage" id="gotopage" value="{print $PAGES.index+1}" style="width:30px; height:14px; text-align:center;">
			&nbsp;...&nbsp;

			{for $i = $PAGES.count - 3; $i < $PAGES.count; $i++}

				<a href="{print Request::$URL.'/page/'}{$i}" {if $i == $PAGES.index}class="active"{/if}>{print $i+1}</a>

			{/for}
		{else}
			{for $i = 0; $i < $PAGES.count; $i++}

				<a href="{print Request::$URL.'/page/'}{$i}" {if $i == $PAGES.index}class="active"{/if}>{print $i+1}</a>

			{/for}
		{/if}

		<a href="{print Request::$URL.'/page/'}{if $PAGES.count}{if $PAGES.index < $PAGES.count-1}{print $PAGES.index+1}{else}{print $PAGES.index}{/if}{else}{print $PAGES.index+1}{/if}">&rArr;</a>
	</div>
	<!-- End pagination -->


	<script type="text/javascript">

	$(document).ready(function() {
		//
		$('input#gotopage').keypress(function(e){
			if(e.which == 13){
				page = $('input#gotopage').val() - 1;
				window.location = '{print Request::$URL}/page/' + page;
				return false;
			}
		});

	});

	</script>

{/if}

