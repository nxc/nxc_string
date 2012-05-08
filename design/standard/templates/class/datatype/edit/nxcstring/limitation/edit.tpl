<div class="nxc_limitation_container">
	<label>{'Regular expression'|i18n( 'extension/nxc_string' )}</label> <input class="halfbox" type="text" name="ezca_nxc_string_{$class_attribute.id}_limitation_{$limitation.id}_expression" value="{$limitation.expression|wash}" />
	<label>{'Description'|i18n( 'extension/nxc_string' )}</label> <input class="halfbox" type="text" name="ezca_nxc_string_{$class_attribute.id}_limitation_{$limitation.id}_description" value="{$limitation.description|wash}" />
	<label>{'Error message'|i18n( 'extension/nxc_string' )}</label> <input class="halfbox" type="text" name="ezca_nxc_string_{$class_attribute.id}_limitation_{$limitation.id}_error" value="{$limitation.error|wash}" />

	<div class="nxc_limitation_controls">
		<input type="hidden" name="ezca_nxc_string_{$class_attribute.id}_limitation_ids[]" value="{$limitation.id}" />
		<input class="button" type="submit" name="CustomActionButton[{$class_attribute.id}_update_limitation_{$limitation.id}]" value="{'Update'|i18n( 'extension/nxc_string' )}" />
		<input class="button" type="submit" name="CustomActionButton[{$class_attribute.id}_remove_limitation_{$limitation.id}]" value="{'Remove'|i18n( 'extension/nxc_string' )}" />
	</div>
	<div class="clear"></div>
</div>