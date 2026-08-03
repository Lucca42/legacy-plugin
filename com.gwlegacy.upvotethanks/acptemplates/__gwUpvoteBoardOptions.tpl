{* Per-board upvote and first-post-lock settings, injected into the board ACP form. *}
<dl>
	<dt></dt>
	<dd>
		<label>
			<input type="checkbox" name="gwUpvoteEnabled" value="1"{if $gwUpvoteEnabled} checked{/if}>
			Enable upvotes and thanks on replies
		</label>
		<small>Shows an upvote button on replies (any logged-in user except the thread and post authors) and a thanks button for the thread author to thank replies.</small>
	</dd>
</dl>
<dl>
	<dt></dt>
	<dd>
		<label>
			<input type="checkbox" name="gwLockFirstPost" value="1"{if $gwLockFirstPost} checked{/if}>
			Lock the first post against editing
		</label>
		<small>Once posted, the first post of a thread on this board can no longer be edited (moderators excepted).</small>
	</dd>
</dl>
