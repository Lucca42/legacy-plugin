{* Thanks button at the bottom of a reply. Clickable only by the thread author, on
   replies that are not their own. The thanked state is shown to everyone. *}
{assign var=gwPID value=$post->postID}
{if $gwPID != $gwUpvoteFirstPostID}
	{assign var=gwThanks value=false}
	{if $gwThanked[$gwPID]|isset}{assign var=gwThanks value=true}{/if}
	{assign var=gwCanThank value=false}
	{if $__wcf->user->userID && $__wcf->user->userID == $gwUpvoteThreadAuthor && $__wcf->user->userID != $post->userID}
		{assign var=gwCanThank value=true}
	{/if}

	{if $gwCanThank}
		<li>
			<a href="#" class="button small gwThanksButton{if $gwThanks} active{/if}" data-post-id="{@$gwPID}" title="Thank this reply"{if $gwThanks} style="color:#c0392b;"{/if}>
				{icon name='heart'} <span class="gwThanksLabel">{if $gwThanks}Thanked{else}Thanks{/if}</span>
			</a>
		</li>
	{elseif $gwThanks}
		<li>
			<span class="button small gwThanksStatic active" title="The thread author thanked this reply">
				{icon name='heart'} <span>Thanked</span>
			</span>
		</li>
	{/if}

	{if $gwCanThank && $gwThanksGuard->renderOnce()}
		<script data-relocate="true">
			require(['WoltLabSuite/Core/Ajax'], (Ajax) => {
				document.addEventListener('click', async (event) => {
					const button = event.target.closest('.gwThanksButton');
					if (!button) {
						return;
					}
					event.preventDefault();
					if (button.classList.contains('disabled')) {
						return;
					}
					button.classList.add('disabled');
					try {
						const result = await Ajax.dboAction('toggle', 'wcf\\data\\post\\thanks\\ThanksAction')
							.objectIds([parseInt(button.dataset.postId, 10)])
							.dispatch();
						button.classList.toggle('active', !!result.active);
						button.style.color = result.active ? '#c0392b' : '';
						const label = button.querySelector('.gwThanksLabel');
						if (label) {
							label.textContent = result.active ? 'Thanked' : 'Thanks';
						}
					} finally {
						button.classList.remove('disabled');
					}
				});
			});
		</script>
	{/if}
{/if}
