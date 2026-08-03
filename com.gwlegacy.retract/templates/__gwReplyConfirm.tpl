{* Submit-time confirmation when replies are locked: authors should reread before
   posting, since the reply cannot be edited afterwards. The capture-phase listener runs
   before WBB's own submit handler; inline post edits (inside an article) are ignored. *}
<script data-relocate="true">
	(function () {
		var lock = {if $gwConfirmReplyLock|isset && $gwConfirmReplyLock}true{else}false{/if};
		if (!lock || window.__gwReplyConfirmBound) {
			return;
		}
		window.__gwReplyConfirmBound = true;
		document.addEventListener('click', function (event) {
			var target = event.target;
			if (!(target instanceof Element)) {
				return;
			}
			var button = target.closest('button[data-type="save"], .formSubmit input[type="submit"], .formSubmit button[type="submit"]');
			if (!button || button.closest('article')) {
				return;
			}
			if (!window.confirm('Replies on this board cannot be edited after posting. Please reread your message before continuing.')) {
				event.preventDefault();
				event.stopImmediatePropagation();
			}
		}, true);
	})();
</script>
{* Hide the edit button on locked replies, including replies added later via the quick
   reply. The server already rejects the edit; this removes the dead button. *}
{if $gwConfirmReplyLock|isset && $gwConfirmReplyLock && !$gwReplyEditHideBound|isset}
	{assign var=gwReplyEditHideBound value=true}
	<script data-relocate="true">
		(function () {
			var firstPostID = {@$gwRetractFirstPostID};
			function hide(root) {
				root.querySelectorAll('[data-post-id]').forEach(function (post) {
					if (parseInt(post.dataset.postId, 10) === firstPostID) {
						return;
					}
					var button = post.querySelector('.jsMessageEditButton');
					if (button) {
						(button.closest('li') || button).style.display = 'none';
					}
				});
			}
			hide(document);
			new MutationObserver(function (mutations) {
				mutations.forEach(function (mutation) {
					mutation.addedNodes.forEach(function (node) {
						if (node.nodeType === 1) {
							hide(node.parentNode || document);
						}
					});
				});
			}).observe(document.body, { childList: true, subtree: true });
		})();
	</script>
{/if}
