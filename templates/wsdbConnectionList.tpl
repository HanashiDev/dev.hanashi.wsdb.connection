{capture assign='contentHeaderNavigation'}
	{if $gridView->getRecord()->canEdit()}
		<li>
			<a href="{$gridView->getConnectionAddFormLink()}" class="button buttonPrimary">
				{icon name='plus'}
				<span>{lang}wsdb.record.connection.add{/lang}</span>
			</a>
		</li>
	{/if}
{/capture}

{include file='header'}

<div class="section">
	{unsafe:$gridView->render()}
</div>

{include file='footer'}
