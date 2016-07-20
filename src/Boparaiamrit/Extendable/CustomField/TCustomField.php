<?php
namespace Boparaiamrit\Extendable\CustomField;


use Cache;
use Carbon\Carbon;
use File;


trait TCustomField
{
	/**
	 * Return column name for current custom field value
	 *
	 * @return string
	 */
	public function getAttributeName()
	{
		return $this->fieldType($this->parent_type, $this->field_name);
	}
	
	/**
	 * Return custom field model field name for specified model and fieldtype
	 *
	 * @param $modelClass
	 * @param $fieldName
	 *
	 * @return string
	 */
	public function fieldType($modelClass, $fieldName)
	{
		$customFields = $this->getConfigurations();
		
		$table = $this->getTableName($modelClass);
		
		switch ($customFields[ $table ][ $fieldName ][ self::CONFIG_FIELD_TYPE ]) {
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
	 * Return config customfield configs for given model
	 *
	 * @return mixed
	 */
	public function getConfigurations()
	{
		if (!Cache::has(self::CACHE_CUSTOM_FIELDS)) {
			$jsonPath = 'app' . DIRECTORY_SEPARATOR . 'extendable' . DIRECTORY_SEPARATOR . 'custom_fields.json';
			
			if (!File::exists(storage_path($jsonPath))) {
				return [];
			}
			
			$jsonData = File::get(storage_path($jsonPath));
			
			$customFields = json_decode($jsonData, true);
			
			Cache::add(self::CACHE_CUSTOM_FIELDS, $customFields, Carbon::now()->addHours(24));
		} else {
			$customFields = Cache::get(self::CACHE_CUSTOM_FIELDS);
		}
		
		return $customFields;
	}
	
	/**
	 * @param $modelClass
	 *
	 * @return mixed
	 */
	public function getTableName($modelClass)
	{
		$table = constant($modelClass . '::TABLE');
		
		return $table;
	}
	
	/**
	 * Return all custom field names for specified model
	 *
	 * @param $modelClass
	 *
	 * @return array
	 */
	public function getFieldNames($modelClass)
	{
		$customFields = $this->getConfigurations();
		
		$table = $this->getTableName($modelClass);
		
		return array_keys(array_get($customFields, $table, []));
	}
}
