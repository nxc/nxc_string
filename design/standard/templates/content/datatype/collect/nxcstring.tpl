{def $data_text = cond( is_set( $#collection_attributes[$attribute.id] ), $#collection_attributes[$attribute.id].data_text, $attribute.content )}
<input class="box" type="text" size="70" name="ezcoa_nxc_string_{$attribute.id}" value="{$data_text|wash( xhtml )}" />
{undef $data_text}