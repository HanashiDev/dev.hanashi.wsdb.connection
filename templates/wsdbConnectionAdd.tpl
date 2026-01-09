{capture assign='contentHeaderNavigation'}
	<li>
		<a href="{$connectionListLink}" class="button">
			{icon name='list'}
			<span>{lang}wsdb.record.connections{/lang}</span>
		</a>
	</li>
{/capture}

{include file='header'}

{unsafe:$form->getHtml()}

{include file='footer'}
