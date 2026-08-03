{* Upvote button at the bottom of a reply (not the first post). Injected once per post
   into messageFooterButtons; the shared click handler is emitted a single time. *}
{assign var=gwPID value=$post->postID}
{if $gwPID != $gwUpvoteFirstPostID}
	{assign var=gwCount value=0}
	{if $gwUpvoteCount[$gwPID]|isset}{assign var=gwCount value=$gwUpvoteCount[$gwPID]}{/if}
	{assign var=gwMine value=false}
	{if $gwUpvoteMine[$gwPID]|isset}{assign var=gwMine value=true}{/if}
	{assign var=gwEligible value=false}
	{if $__wcf->user->userID && $__wcf->user->userID != $gwUpvoteThreadAuthor && $__wcf->user->userID != $post->userID}
		{assign var=gwEligible value=true}
	{/if}

	<li>
		{if $gwEligible}
			<a href="#" class="button small gwUpvoteButton{if $gwMine} active{/if}" data-post-id="{@$gwPID}" title="Upvote this reply">
				{icon name='thumbs-up'} <span class="gwUpvoteCount">{#$gwCount}</span>
			</a>
		{else}
			<span class="button small gwUpvoteStatic{if $gwMine} active{/if}" title="Upvotes">
				{icon name='thumbs-up'} <span class="gwUpvoteCount">{#$gwCount}</span>
			</span>
		{/if}
	</li>

	{if $gwUpvoteGuard->renderOnce()}
		<script data-relocate="true">
			require(['WoltLabSuite/Core/Ajax'], (Ajax) => {
				document.addEventListener('click', async (event) => {
					const button = event.target.closest('.gwUpvoteButton');
					if (!button) {
						return;
					}
					event.preventDefault();
					if (button.classList.contains('disabled')) {
						return;
					}
					button.classList.add('disabled');
					try {
						const result = await Ajax.dboAction('toggle', 'wcf\\data\\post\\upvote\\UpvoteAction')
							.objectIds([parseInt(button.dataset.postId, 10)])
							.dispatch();
						button.classList.toggle('active', !!result.active);
						const counter = button.querySelector('.gwUpvoteCount');
						if (counter) {
							counter.textContent = result.count;
						}
					} finally {
						button.classList.remove('disabled');
					}
				});
			});
		</script>
	{/if}
{/if}
