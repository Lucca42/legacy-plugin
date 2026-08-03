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
{* Hide the edit button on locked replies (server-side already rejects the edit). *}
{if $gwRetractLockedEdit|isset && $gwRetractLockedEdit|count}
	<script data-relocate="true">
		(function () {
			var ids = [{implode from=$gwRetractLockedEdit item=gwId}{@$gwId}{/implode}];
			ids.forEach(function (pid) {
				var post = document.querySelector('[data-post-id="' + pid + '"]');
				if (!post) {
					return;
				}
				var button = post.querySelector('.jsMessageEditButton');
				if (button) {
					(button.closest('li') || button).style.display = 'none';
				}
			});
		})();
	</script>
{/if}
