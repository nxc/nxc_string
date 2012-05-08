{def $limitations = $class_attribute.content}

<div class="block">
    <label>{'Default value'|i18n( 'extension/nxc_string' )}:</label>
    {if $class_attribute.data_text1}
        <p>{$class_attribute.data_text1|wash}</p>
    {else}
        <p><i>{'Empty'|i18n( 'extension/nxc_string' )}</i></p>
    {/if}
</div>

<div class="block">
    <label>{'Matching limitations'|i18n( 'extension/nxc_string' )}:</label>
	{if gt( count( $limitations.matching ), 0 ) }
		<ul>
		{foreach $limitations.matching as $limitation}
			<li><strong>{$limitation.expression|wash}</strong> {if $limitation.description}({$limitation.description|wash}){/if}</li>
		{/foreach}
		</ul>
	{else}
		{'There are no limitations'|i18n( 'extension/nxc_string' )}
	{/if}
</div>

<div class="block">
    <label>{'Not-matching limitations'|i18n( 'extension/nxc_string' )}:</label>
	{if gt( count( $limitations.not_matching ), 0 ) }
		<ul>
		{foreach $limitations.not_matching as $limitation}
			<li><strong>{$limitation.expression|wash}</strong> {if $limitation.description}({$limitation.description|wash}){/if}</li>
		{/foreach}
		</ul>
	{else}
		{'There are no limitations'|i18n( 'extension/nxc_string' )}
	{/if}
</div>

{undef $limitations}