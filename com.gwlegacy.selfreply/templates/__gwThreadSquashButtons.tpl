{* Squash + "show previous replies" buttons, injected into the content-interaction bar. *}
{if $gwIsSquashed}
	{if $gwShowingSquashed}
		<a href="#" id="gwHideSquashed" class="contentInteractionButton button small">
			{icon name='eye-slash'} <span>Hide previous replies</span>
		</a>
	{else}
		<a href="#" id="gwShowSquashed" class="contentInteractionButton button small">
			{icon name='clock-rotate-left'} <span>Show previous replies</span>
		</a>
	{/if}
{/if}

{if $gwCanSquash}
	<a href="#" id="gwSquashButton" class="contentInteractionButton button small" title="Hide all current replies. The opening post stays; future replies show normally.">
		{icon name='box-archive'} <span>Squash replies</span>
	</a>
{/if}

{if $gwIsSquashed || $gwCanSquash}
	<script data-relocate="true">
		require(['WoltLabSuite/Core/Ajax'], (Ajax) => {
			const reloadWithArchive = (show) => {
				const url = new URL(window.location.href);
				if (show) {
					url.searchParams.set('showSquashed', '1');
				} else {
					url.searchParams.delete('showSquashed');
				}
				window.location.href = url.toString();
			};

			document.getElementById('gwShowSquashed')?.addEventListener('click', (e) => { e.preventDefault(); reloadWithArchive(true); });
			document.getElementById('gwHideSquashed')?.addEventListener('click', (e) => { e.preventDefault(); reloadWithArchive(false); });

			// Keep showSquashed across pagination: the pagination component derives every
			// page link from its `url` attribute, so fold the parameter into it.
			if (new URLSearchParams(window.location.search).has('showSquashed')) {
				customElements.whenDefined('woltlab-core-pagination').then(() => {
					document.querySelectorAll('woltlab-core-pagination').forEach((pagination) => {
						const base = pagination.url;
						if (!base) {
							return;
						}
						const paged = new URL(base, window.location.href);
						if (!paged.searchParams.has('showSquashed')) {
							paged.searchParams.set('showSquashed', '1');
							pagination.url = paged.toString();
						}
					});
				});
			}

			const squash = document.getElementById('gwSquashButton');
			squash?.addEventListener('click', async (e) => {
				e.preventDefault();
				if (!window.confirm('Hide all current replies on this thread? The opening post stays visible, and everyone can still expand the old replies.')) {
					return;
				}
				squash.classList.add('disabled');
				try {
					await Ajax.dboAction('squash', 'wcf\\data\\thread\\squash\\SquashThreadAction')
						.objectIds([{@$gwSquashThreadID}])
						.dispatch();
					window.location.reload();
				} catch (err) {
					squash.classList.remove('disabled');
				}
			});
		});
	</script>
{/if}
