{* Per-board retract and reply-lock settings, injected into the board ACP form. *}
<dl>
	<dt></dt>
	<dd>
		<label>
			<input type="checkbox" name="gwRetractEnabled" value="1"{if $gwRetractEnabled} checked{/if}>
			Enable retract button on replies
		</label>
		<small>The reply's author can post a new reply quoting the original, marked "Retracted".</small>
	</dd>
</dl>
<dl>
	<dt></dt>
	<dd>
		<label>
			<input type="checkbox" name="gwLockReplies" value="1"{if $gwLockReplies} checked{/if}>
			Lock replies against editing
		</label>
		<small>Once posted, replies on this board can no longer be edited (moderators excepted). Authors should reread before posting.</small>
	</dd>
</dl>
