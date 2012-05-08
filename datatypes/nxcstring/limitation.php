<?php
/**
 * @package nxcString
 * @class   nxcStringLimitation
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    09 Sep 2011
 **/

class nxcStringLimitation extends eZPersistentObject
{
	const TYPE_MATCHING     = 1;
	const TYPE_NOT_MATCHING = 2;

	public function __construct( $row = array() ) {
		$this->eZPersistentObject( $row );
	}

	public static function definition() {
		return array(
			'fields'              => array(
				'id' => array(
					'name'       => 'id',
					'datatype'   => 'integer',
					'default'    => 0,
					'required'   => true
				),
				'class_attribute_id' => array(
					'name'       => 'classAttributeID',
					'datatype'   => 'integer',
					'default'    => 0,
					'required'   => true
				),
				'type' => array(
					'name'       => 'type',
					'datatype'   => 'integer',
					'default'    => self::TYPE_MATCHING,
					'required'   => true
				),
				'expression' => array(
					'name'       => 'expression',
					'datatype'   => 'string',
					'default'    => null,
					'required'   => false
				),
				'description' => array(
					'name'       => 'description',
					'datatype'   => 'string',
					'default'    => null,
					'required'   => false
				),
				'error' => array(
					'name'       => 'error',
					'datatype'   => 'string',
					'default'    => null,
					'required'   => false
				)
			),
			'function_attributes' => array(),
			'keys'                => array( 'id' ),
			'sort'                => array( 'id' => 'asc' ),
			'increment_key'       => 'id',
			'class_name'          => 'nxcStringLimitation',
			'name'                => 'nxc_string_limitations'
		);
	}

	public static function fetch( $id ) {
		return eZPersistentObject::fetchObject(
			self::definition(),
			null,
			array( 'id' => $id ),
			true
		);
	}

	public static function fetchList( $classAttributeID, $typeID = null ) {
		$filters = array(
			'class_attribute_id' => $classAttributeID
		);
		if( $typeID !== null ) {
			$filters['type'] = $typeID;
		}

		return eZPersistentObject::fetchObjectList(
			self::definition(),
			null,
			$filters,
			true
		);
	}

	public static function getSerializableAttributes() {
		return array( 'type', 'expression', 'description', 'error' );
	}

}
?>
