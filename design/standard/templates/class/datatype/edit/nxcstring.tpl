{def $limitations = $class_attribute.content}

<div class="block">
    <label>{'Default value'|i18n( 'extension/nxc_string' )}:</label>
    <input class="box" type="text" name="ezca_nxc_string_{$class_attribute.id}_default_value" value="{$class_attribute.data_text1|wash}" />
</div>

{include uri='design:class/datatype/edit/nxcstring/limitation/add.tpl' class_attribute=$class_attribute}

<div class="block">
	<fieldset>

		<legend>{'Matching limitations'|i18n( 'extension/nxc_string' )}</legend>

		{if gt( count( $limitations.matching ), 0 ) }
			{foreach $limitations.matching as $limitation}
				{include uri='design:class/datatype/edit/nxcstring/limitation/edit.tpl' class_attribute=$class_attribute limitation=$limitation}
			{/foreach}
		{else}
			{'There are no limitations'|i18n( 'extension/nxc_string' )}
		{/if}

	</fieldset>
</div>

<div class="block">
	<fieldset>
		<legend>{'Not-matching limitations'|i18n( 'extension/nxc_string' )}</legend>

		{if gt( count( $limitations.not_matching ), 0 ) }
			{foreach $limitations.not_matching as $limitation}
				{include uri='design:class/datatype/edit/nxcstring/limitation/edit.tpl' class_attribute=$class_attribute limitation=$limitation}
			{/foreach}
		{else}
			{'There are no limitations'|i18n( 'extension/nxc_string' )}
		{/if}

	</fieldset>
</div>

{undef $limitations}