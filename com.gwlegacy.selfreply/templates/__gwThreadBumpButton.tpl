{* Bump button, injected into the thread's content-interaction bar. *}
{if $gwCanBump}
	<a href="#" id="gwBumpButton" class="contentInteractionButton button small">
		{icon name='arrow-up'} <span>Bump thread</span>
	</a>
	<script data-relocate="true">
		require(['WoltLabSuite/Core/Ajax', 'WoltLabSuite/Core/Ui/Notification'], (Ajax, UiNotification) => {
			const button = document.getElementById('gwBumpButton');
			if (!button) {
				return;
			}
			button.addEventListener('click', async (event) => {
				event.preventDefault();
				button.classList.add('disabled');
				try {
					await Ajax.dboAction('bump', 'wcf\\data\\thread\\bump\\BumpThreadAction')
						.objectIds([{@$gwBumpThreadID}])
						.dispatch();
					UiNotification.show('Thread bumped.', () => {
						window.location.reload();
					});
				} catch (e) {
					button.classList.remove('disabled');
				}
			});
		});
	</script>
{else}
	<span class="contentInteractionButton button small disabled" title="Already resurfaced recently. You can bump again later.">
		{icon name='arrow-up'} <span>Bump thread</span>
	</span>
{/if}
