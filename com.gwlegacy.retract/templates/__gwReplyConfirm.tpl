{* Submit-time confirmation when replies are locked: authors should reread before
   posting, since the reply cannot be edited afterwards. The capture-phase listener runs
   before WBB's own submit handler; inline post edits (inside an article) are ignored. *}
{if $gwConfirmReplyLock|isset && $gwConfirmReplyLock}
	<script data-relocate="true">
		(function () {
			document.addEventListener('click', function (event) {
				var target = event.target;
				if (!(target instanceof Element)) {
					return;
				}
				var button = target.closest('button[data-type="save"]');
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
{/if}
