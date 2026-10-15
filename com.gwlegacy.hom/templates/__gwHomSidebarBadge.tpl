{* Hall of Monuments badge in the post author sidebar: title, then points. *}
<dt><a href="{link controller='Hom' application='wcf'}character={$gwHomBadge[characterName]|rawurlencode}{/link}" class="jsTooltip" title="{$gwHomBadge[characterName]}">Hall of Monuments</a></dt>
<dd>{if $gwHomBadge[title]}{$gwHomBadge[title]}<br>{/if}{$gwHomBadge[points]} / 50</dd>
