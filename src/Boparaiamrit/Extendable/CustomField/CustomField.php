<?php namespace Boparaiamrit\Extendable\CustomField;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomField
 *
 * @package Boparaiamrit\Extendable
 *
 * @property mixed parent_type
 * @property mixed field_name
 * @property mixed value
 *
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder first($columns = ['*'])
 *
 *
 */
class CustomField extends Model implements ICustomField
{
	use TCustomField;
	
	const FIELD_NAME   = 'field_name';
	const PARENT_ID    = 'parent_id';
	const PARENT_TYPE  = 'parent_type';
	const STRING_VALUE = "string_value";
	const TEXT_VALUE   = "text_value";
	const NUMBER_VALUE = "number_value";
	const DATE_VALUE   = "date_value";
	
	const RELATION_PARENT = 'parent';
	
	// unguard all fields
	public $guarded = [];
	
	// disable timestamps
	public $timestamps = false;
	
	// value accessor
	protected $appends = [self::ATTRIBUTE_VALUE];
	
	/**
	 * Get value for current custom field
	 *
	 * @return mixed
	 */
	public function getValueAttribute()
	{
		$attributeName = $this->getAttributeName();
		
		return $this->$attributeName;
	}
	
	/**
	 * Return column name for current custom field value
	 *
	 * @return string
	 */
	public function getAttributeName()
	{
		/** @var Model $ModelInstance */
		$ModelInstance = new $this->parent_type;
		
		// Get Table Name
		$table = $ModelInstance->getTable();
		
		$customFields = $this->getCustomFields($table);
		
		$type = $customFields[ $this->field_name ][ self::CONFIG_FIELD_TYPE ];
		
		switch ($type) {
			case self::FIELD_CHECKBOX:
			case self::FIELD_SELECT:
			case self::FIELD_STRING:
			case self::FIELD_RADIO:
				return self::STRING_VALUE;
			case self::FIELD_TEXT:
				return self::TEXT_VALUE;
			case self::FIELD_DATETIME:
				return self::DATE_VALUE;
			default:
				return self::STRING_VALUE;
		}
	}
	
	
	/**
	 * @param $value
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function setValueAttribute($value)
	{
		if ($value instanceof self) {
			throw new \Exception('Invalid custom attribute value');
		}
		
		$attributeName        = $this->getAttributeName();
		$this->$attributeName = $value;
		
		return $this;
	}
	
	/**
	 * Return custom field value as string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->value;
	}
}
