<?php
/**
 * @package nxcString
 * @class   nxcStringType
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    08 Sep 2011
 **/

class nxcStringType extends eZDataType {

	const DATA_TYPE_STRING     = 'nxcstring';
	const DEFAULT_STRING_FIELD = 'data_text1';

	public function __construct() {
		$this->eZDataType(
			self::DATA_TYPE_STRING,
			ezpI18n::tr( 'extension/nxc_string', 'NXC String' ),
			array(
				'serialize_supported' => true,
				'object_serialize_map' => array( 'data_text' => 'text' )
			)
		);
	}

	public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute ) {
		$httpBase = 'ezca_nxc_string_' . $classAttribute->attribute( 'id' ) . '_';
		if( $http->hasPostVariable( $httpBase . 'default_value' ) ) {
			$classAttribute->setAttribute(
				self::DEFAULT_STRING_FIELD,
				$http->postVariable( $httpBase . 'default_value' )
			);
		}
		if( $http->hasPostVariable( $httpBase . 'limitation_ids' ) ) {
			$limitationIDs = (array) $http->postVariable( $httpBase . 'limitation_ids' );
			foreach( $limitationIDs as $limitationID ) {
				$limitation = nxcStringLimitation::fetch( $limitationID );
				if( $limitation instanceof nxcStringLimitation ) {
					$this->updateLimitation( $http, $classAttribute, $limitation );
				}
			}
		}
	}

	/**
	 * nxcStringType::validateClassAttributeHTTPInput()
	 *
	 * @todo eZ Publich has no "reason" for class attributes validation
	 * so we can`t check regular expressions
	 */
	public function validateClassAttributeHTTPInput( $http, $base, $classAttribute ) {
		return eZInputValidator::STATE_ACCEPTED;
	}

	/**
	 * nxcStringType::customClassAttributeHTTPAction()
	 *
	 * @todo eZ Publish dosn`t provide any possibility to do any kind of validation
	 */
	public function customClassAttributeHTTPAction( $http, $action, $classAttribute ) {
		$httpBase   = 'ezca_nxc_string_' . $classAttribute->attribute( 'id' ) . '_';
		$limitation = null;

		preg_match( "#^(.*)_([0-9]+)$#", $action, $matches );
		if( count( $matches ) === 3 ) {
			$action     = $matches[1];
			$limitation = nxcStringLimitation::fetch( $matches[2] );
		}

		switch( $action ) {
			case 'add_limitation': {
				if( $http->hasPostVariable( $httpBase . 'new_type' ) ) {
					$limitation = new nxcStringLimitation(
						array(
							'class_attribute_id' => $classAttribute->attribute( 'id' ),
							'type' => (int) $http->postVariable( $httpBase . 'new_type' )
						)
					);
					$limitation->store();
				}
				break;
			}
			case 'update_limitation': {
				if( $limitation instanceof nxcStringLimitation ) {
					$this->updateLimitation( $http, $classAttribute, $limitation );
				}
				break;
			}
			case 'remove_limitation': {
				if( $limitation instanceof nxcStringLimitation ) {
					$limitation->remove();
				}
				break;
			}
		}
	}

	public function classAttributeContent( $classAttribute ) {
		return array(
			'matching'     => nxcStringLimitation::fetchList(
				$classAttribute->attribute( 'id' ),
				nxcStringLimitation::TYPE_MATCHING
			),
			'not_matching' => nxcStringLimitation::fetchList(
				$classAttribute->attribute( 'id' ),
				nxcStringLimitation::TYPE_NOT_MATCHING
			)
		);
	}

	private function updateLimitation( $http, $classAttribute, $limitation ) {
		$httpAttributesBase = 'ezca_nxc_string_' . $classAttribute->attribute( 'id' ) . '_';
		$httpAttributesBase .= 'limitation_' . $limitation->attribute( 'id' ) . '_';

		$fields = array( 'expression', 'description', 'error' );
		foreach( $fields as $field ) {
			if( $http->hasPostVariable( $httpAttributesBase . $field ) ) {
				$limitation->setAttribute(
					$field,
					$http->postVariable( $httpAttributesBase . $field )
				);
			}
		}

		$limitation->store();
	}

	public function cloneClassAttribute( $oldAttribute, $newAttribute ) {
		$newAttribute->setAttribute(
			self::DEFAULT_STRING_FIELD,
			$oldAttribute->attribute( self::DEFAULT_STRING_FIELD )
		);

		$limitations = nxcStringLimitation::fetchList(
			$oldAttribute->attribute( 'id' )
		);

		$newAttribute->store();
		foreach( $limitations as $limitation ) {
			$newLimitation = clone $limitation;
			$newLimitation->setAttribute( 'id', null );
			$newLimitation->setAttribute( 'class_attribute_id', $newAttribute->attribute( 'id' ) );
			$newLimitation->store();
		}
	}

	public function deleteStoredClassAttribute( $classAttribute, $version = null ) {
		if( (int) $classAttribute->attribute( 'version' ) === eZContentClass::VERSION_STATUS_DEFINED ) {
			nxcStringLimitation::removeObject(
				nxcStringLimitation::definition(),
				array( 'class_attribute_id' => $classAttribute->attribute( 'id' ) )
			);
		}
	}

	public function serializeContentClassAttribute(
		$classAttribute, $attributeNode, $attributeParametersNode
	) {
		$dom = $attributeParametersNode->ownerDocument;

		$defaultString     = $classAttribute->attribute( self::DEFAULT_STRING_FIELD );
        $defaultStringNode = $dom->createElement( 'default-string' );
        if( strlen( $defaultString ) > 0 ) {
            $defaultStringNode->appendChild( $dom->createTextNode( $defaultString ) );
        }
        $attributeParametersNode->appendChild( $defaultStringNode );

		$attributes  = nxcStringLimitation::getSerializableAttributes();
		$limitations = nxcStringLimitation::fetchList(
			$classAttribute->attribute( 'id' )
		);
		$limitationsNode = $dom->createElement( 'limitations' );
		foreach( $limitations as $limitation ) {
			$limitationNode = $dom->createElement( 'limitation' );
			foreach( $attributes as $attribute ) {
				if( $limitation->hasAttribute( $attribute ) ) {
					$limitationNode->appendChild(
						$dom->createElement(
							$attribute,
							$limitation->attribute( $attribute )
						)
					);
				}
			}
			$limitationsNode->appendChild( $limitationNode );
		}
		$attributeParametersNode->appendChild( $limitationsNode );
	}

	public function unserializeContentClassAttribute(
		$classAttribute, $attributeNode, $attributeParametersNode
	) {
        $classAttribute->setAttribute(
			self::DEFAULT_STRING_FIELD,
			$attributeParametersNode->getElementsByTagName( 'default-string' )->item( 0 )->textContent
		);

		$attributes      = nxcStringLimitation::getSerializableAttributes();
		$limitationNodes = $attributeParametersNode->getElementsByTagName( 'limitation' );
		foreach( $limitationNodes as $limitationNode ) {
			$limitation = new nxcStringLimitation();
			$limitation->setAttribute(
				'class_attribute_id',
				$classAttribute->attribute( 'id' )
			);
			foreach( $attributes as $attribute ) {
				$limitationAttributeNode = $limitationNode->getElementsByTagName( $attribute );
				if( $limitationAttributeNode->length > 0 ) {
					$limitation->setAttribute(
						$attribute,
						$limitationAttributeNode->item( 0 )->textContent
					);
				}
			}
			$limitation->store();
		}
	}

	public function initializeObjectAttribute( $attribute, $currentVersion, $originalAttribute ) {
		if( $currentVersion != false ) {
			$dataText = $originalAttribute->attribute( "data_text" );
			$attribute->setAttribute(
				'data_text',
				$originalAttribute->attribute( 'data_text' )
			);
		} else {
			$classAttribute = $attribute->contentClassAttribute();
			$defaultValue   = $attribute->attribute( self::DEFAULT_STRING_FIELD );
			if( strlen( $defaultValue ) > 0 ) {
				$attribute->setAttribute( 'data_text', $defaultValue );
			}
		}
	}

	public function fetchObjectAttributeHTTPInput( $http, $base, $attribute ) {
		$httpVariable = 'ezcoa_nxc_string_' . $attribute->attribute( 'id' );
        if( $http->hasPostVariable( $httpVariable ) ) {
            $data = $http->postVariable( $httpVariable );
            $attribute->setAttribute( 'data_text', $data );
            return true;
        }
        return false;
	}

	public function validateObjectAttributeHTTPInput( $http, $base, $attribute ) {
		$httpVariable   = 'ezcoa_nxc_string_' . $attribute->attribute( 'id' );
		$classAttribute = $attribute->attribute( 'contentclass_attribute' );
		$requireInput   =
			$classAttribute->attribute( 'is_information_collector' ) == false
			&& $attribute->validateIsRequired();

		if( $http->hasPostVariable( $httpVariable ) ){
			$data = trim( $http->postVariable( $httpVariable ) );
			if( strlen( $data ) === 0 ) {
				$attribute->setValidationError(
					ezpI18n::tr( 'extension/nxc_string', 'Input required.' )
				);
				return eZInputValidator::STATE_INVALID;
			}
		} elseif( $requireInput ) {
			$attribute->setValidationError(
				ezpI18n::tr( 'extension/nxc_string', 'Input required.' )
			);
			return eZInputValidator::STATE_INVALID;
		}

		$errors = array();
		$types  = array(
			nxcStringLimitation::TYPE_MATCHING     => 1,
			nxcStringLimitation::TYPE_NOT_MATCHING => 0
		);
		foreach( $types as $type => $requiredMatchResult ) {
			$defaultError = $type === nxcStringLimitation::TYPE_MATCHING
				? 'Input should satisfy "%pattern" pattern'
				: 'Input shouldn`t satisfy "%pattern" pattern';

			$limitations = nxcStringLimitation::fetchList(
				$classAttribute->attribute( 'id' ),
				$type
			);
			foreach( $limitations as $limitation ) {
				$expression = $limitation->attribute( 'expression' );
				// Pattern should not be empty
				if( strlen( $expression ) > 0 ) {
					$result = preg_match( $expression, $data );
					// Pattern is valid and there are no preg errors
					if( $result !== false && preg_last_error() === PREG_NO_ERROR ) {
						// Check preg_match result
						if( $result !== $requiredMatchResult ) {
							// Get error message
							if( strlen( $limitation->attribute( 'error' ) ) > 0 ) {
								$error = $limitation->attribute( 'error' );
							} else {
								$error = $defaultError;
							}
							$errors[] = ezpI18n::tr(
								'extension/nxc_string',
								$error,
								null,
								array( '%pattern' => $expression )
							);
						}
					}
				}
			}
		}

		if( count( $errors ) > 0 ) {
			$attribute->setValidationError( implode( ', ', $errors ) );
			return eZInputValidator::STATE_INVALID;
		}
		return eZInputValidator::STATE_ACCEPTED;
	}

	public function objectAttributeContent( $attribute ) {
		return $attribute->attribute( 'data_text' );
	}

	public function hasObjectAttributeContent( $attribute ) {
		return (bool) strlen( trim( $attribute->attribute( 'data_text' ) ) );
	}

	public function isInformationCollector() {
		return true;
	}

	public function fetchCollectionAttributeHTTPInput(
		$collection, $collectionAttribute, $http, $base, $attribute
	) {
		$httpVariable = 'ezcoa_nxc_string_' . $attribute->attribute( 'id' );
        if( $http->hasPostVariable( $httpVariable ) ) {
            $data = $http->postVariable( $httpVariable );
            $collectionAttribute->setAttribute( 'data_text', $data );
            return true;
        }
        return false;
	}

	public function validateCollectionAttributeHTTPInput( $http, $base, $attribute ) {
		return $this->validateObjectAttributeHTTPInput( $http, $base, $attribute );
	}

	public function supportsBatchInitializeObjectAttribute() {
		return true;
	}

	public function batchInitializeObjectAttributeData( $classAttribute ) {
		$default = $classAttribute->attribute( self::DEFAULT_STRING_FIELD );

		if( strlen( $default ) > 0 ) {
			$db      = eZDB::instance();
			$default = '\'' . $db->escapeString( $default ) . '\'';
			$trans   = eZCharTransform::instance();
			return array(
				'data_text'       => $default,
				'sort_key_string' => $trans->transformByGroup( $default, 'lowercase' )
			);
		}

		return array();
	}

	public function title( $attribute, $name = null ) {
		return $attribute->attribute( 'data_text' );
	}

	public function toString( $attribute ) {
		return $attribute->attribute( 'data_text' );
	}

	public function fromString( $attribute, $string ) {
		return $attribute->setAttribute( 'data_text', $string );
	}

	public function isIndexable() {
		return true;
	}

    function metaData( $attribute ) {
		return $attribute->attribute( 'data_text' );
	}

	public function sortKey( $attribute ) {
		$trans = eZCharTransform::instance();
		return $trans->transformByGroup( $attribute->attribute( 'data_text' ), 'lowercase' );
	}

	public function sortKeyType() {
		return 'string';
	}

	public function diff( $old, $new, $options = false ) {
		$diff = new eZDiff();
		$diff->setDiffEngineType( $diff->engineType( 'text' ) );
		$diff->initDiffEngine();
		return $diff->diff( $old->content(), $new->content() );
	}
}

eZDataType::register( nxcStringType::DATA_TYPE_STRING, 'nxcStringType' );
?>
