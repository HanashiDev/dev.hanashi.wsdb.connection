{if $view->record->getDatabase()->enableConnection}
	{foreach from=$connectionDatabases item=connectionDatabase}
		{if $groupedConnections[$connectionDatabase->databaseID]|isset && $groupedConnections[$connectionDatabase->databaseID]|count}
			<section class="box">
				<h2 class="boxTitle">
					{lang}wsdb.record.connections.connected{/lang}
					{$connectionDatabase->getPhrase($__wcf->getLanguage()->languageID, 'recordPlural')}
				</h2>

				<div class="boxContent">
					<ol class="sidebarList">
						{foreach from=$groupedConnections[$connectionDatabase->databaseID] item=connectedRecord}
							<li class="sidebarListItem">
								{if $connectionDatabase->enableCoverPhoto}
									<div class="sidebarListItem__image">
										<a href="{$connectedRecord->getLink()}">
											<img
												src="{$connectedRecord->getCoverPhoto()->getUrl('small')}"
												style="max-width: 70px; max-height: 50px; object-fit: cover; object-position: center center;"
												height="{$connectedRecord->getCoverPhoto()->getHeight('small')}"
												width="{$connectedRecord->getCoverPhoto()->getWidth('small')}"
												loading="lazy"
												alt=""
											>
										</a>
									</div>
								{/if}
								<div class="sidebarListItem__content" style="justify-content: center">
									<h3 class="sidebarListItem__title">
										<a href="{$connectedRecord->getLink()}" class="sidebarListItem__link">{$connectedRecord->getTitle()}</a>
									</h3>
								</div>
							</li>
						{/foreach}
					</ol>
				</div>
			</section>
		{/if}
	{/foreach}
{/if}
