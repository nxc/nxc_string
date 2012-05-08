<div class="block">
	<fieldset>
		<legend>{'New limitation'|i18n( 'extension/nxc_string' )}</legend>

		<select name="ezca_nxc_string_{$class_attribute.id}_new_type">
			<option value="1">{'Matching'|i18n( 'extension/nxc_string' )}</option>
			<option value="2">{'Not-matching'|i18n( 'extension/nxc_string' )}</option>
		</select>
		<input class="button" type="submit" name="CustomActionButton[{$class_attribute.id}_add_limitation]" value="{'Add'|i18n( 'extension/nxc_string' )}" />

	</fieldset>
</div>