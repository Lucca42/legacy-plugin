{* Self-close button, injected into the thread's content-interaction bar. *}
{if $gwCanSelfClose}
	<a href="#" id="gwSelfCloseButton" class="contentInteractionButton button small" title="Close your thread. No new replies will be allowed.">
		{icon name='lock'} <span>Close thread</span>
	</a>
	<script data-relocate="true">
		require(['WoltLabSuite/Core/Ajax'], (Ajax) => {
			const button = document.getElementById('gwSelfCloseButton');
			if (!button) {
				return;
			}
			button.addEventListener('click', async (event) => {
				event.preventDefault();
				if (!window.confirm('Close this thread? No new replies will be allowed. A moderator can reopen it if needed.')) {
					return;
				}
				button.classList.add('disabled');
				try {
					await Ajax.dboAction('close', 'wcf\\data\\thread\\close\\CloseThreadAction')
						.objectIds([{@$gwSelfCloseThreadID}])
						.dispatch();
					window.location.reload();
				} catch (e) {
					button.classList.remove('disabled');
				}
			});
		});
	</script>
{/if}
