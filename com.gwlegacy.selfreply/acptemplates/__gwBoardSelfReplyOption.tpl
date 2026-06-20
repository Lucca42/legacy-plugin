{* Per-board settings, injected into the board ACP add/edit form. *}
<dl>
	<dt></dt>
	<dd>
		<label>
			<input type="checkbox" name="gwSelfReplyRestricted" value="1"{if $gwSelfReplyRestricted} checked{/if}>
			No self-reply &amp; daily bump
		</label>
		<small>On this board the thread author cannot reply to their own thread, but may bump it once per interval.</small>
	</dd>
</dl>
<dl>
	<dt><label for="gwBumpInterval">Minimum delay between bumps (seconds)</label></dt>
	<dd>
		<input type="number" id="gwBumpInterval" name="gwBumpInterval" value="{$gwBumpInterval}" min="60" class="short">
		<small>How long the author must wait before bumping the same thread again (86400 = 24h).</small>
	</dd>
</dl>
