{* Retract button at the bottom of a reply, shown to the reply's own author only.
   Injected once per post into messageFooterButtons; the click handler is emitted once. *}
{assign var=gwPID value=$post->postID}
{if $gwPID != $gwRetractFirstPostID && $__wcf->user->userID && $__wcf->user->userID == $post->userID}
	<li>
		<a href="#" class="button small gwRetractButton" data-post-id="{@$gwPID}" title="Retract this reply">
			{icon name='rotate-left'} <span>Retract</span>
		</a>
	</li>

	{if $gwRetractGuard->renderOnce()}
		<script data-relocate="true">
			require(['WoltLabSuite/Core/Ajax'], (Ajax) => {
				document.addEventListener('click', async (event) => {
					const button = event.target.closest('.gwRetractButton');
					if (!button) {
						return;
					}
					event.preventDefault();
					if (button.classList.contains('disabled')) {
						return;
					}
					if (!window.confirm('Retract this reply? A new reply will be posted quoting it and marked "Retracted". This cannot be undone.')) {
						return;
					}
					button.classList.add('disabled');
					try {
						await Ajax.dboAction('retract', 'wcf\\data\\post\\retract\\RetractAction')
							.objectIds([parseInt(button.dataset.postId, 10)])
							.dispatch();
						window.location.reload();
					} catch (err) {
						button.classList.remove('disabled');
					}
				});
			});
		</script>
	{/if}
{/if}
