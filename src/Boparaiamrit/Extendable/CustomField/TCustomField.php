<?php
namespace Boparaiamrit\Extendable\CustomField;


use Cache;
use Carbon\Carbon;
use File;


trait TCustomField
{
	/**
	 * Return all custom field names for specified model
	 *
	 * @param string $entity
	 *
	 * @return array
	 */
	public function getCustomFieldNames($entity)
	{
		$customFields = $this->getCustomFields($entity);
		
		return array_keys($customFields);
	}
	
	/**
	 * Return config customfield configs for given model
	 *
	 * @param string $entity
	 *
	 * @return mixed
	 */
	public function getCustomFields($entity)
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
			$customFields = Cache::get(self::CACHE_CUSTOM_FIELDS, []);
		}
		
		return $customFields[ $entity ];
	}
}
