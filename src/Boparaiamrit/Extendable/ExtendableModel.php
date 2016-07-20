<?php
namespace Boparaiamrit\Extendable;


use Boparaiamrit\Extendable\CustomField\CustomField;
use Boparaiamrit\Extendable\CustomField\TCustomField;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomFieldModel
 *
 * @property int id
 * @package Boparaiamrit\Extendable
 */
class ExtendableModel extends Model
{
	use TCustomField;
	
	public $customAttributes = [];
	
	/**
	 * Dynamically retrieve attributes on the model or custom fields.
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function __get($name)
	{
		// return custom field value
		if ($this->isCustomField($name)) {
			return $this->getCustomFieldModel($name)->value;
		}
		
		// return model attribute
		return $this->getAttribute($name);
	}
	
	/**
	 * Dynamically set attributes on the model.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 *
	 * @return void
	 */
	public function __set($key, $value)
	{
		// set
		if ($this->isCustomField($key)) {
			if ($value instanceof CustomField) {
				$this->$key = $value;
			} else {
				$this->customAttributes[ $key ] = $value;
			}
		} else {
			parent::__set($key, $value);
		}
	}
	
	/**
	 * Return true if attribute name belongs to fields.
	 *
	 * @param $attributeName
	 *
	 * @return bool
	 */
	public function isCustomField($attributeName)
	{
		return in_array($attributeName, $this->customFieldNames());
	}
	
	/**
	 * Return all custom field names for current model
	 *
	 * @return array
	 */
	public function customFieldNames()
	{
		return $this->getFieldNames(get_class($this));
	}
	
	/**
	 * Returns custom field model
	 *
	 * @param $fieldName
	 *
	 * @return CustomField
	 */
	public function getCustomFieldModel($fieldName)
	{
		$model = $this->getAttribute($fieldName);
		
		if ($model === null) {
			$model = $this->newCustomFieldModel($fieldName);
		}
		
		return $model;
	}
	
	/**
	 * Loads custom field model
	 *
	 * @param $fieldName
	 *
	 * @return mixed
	 */
	public function getAttribute($fieldName)
	{
		$model = parent::getAttribute($fieldName);
		
		if ($model === null && $this->exists) {
			$model = CustomField::where([
				'parent_type' => get_class($this),
				'parent_id'   => $this->id,
				'field_name'  => $fieldName
			])->first();
		}
		
		return $model;
	}
	
	/**
	 * Create new custom field model instance
	 *
	 * @param $fieldName
	 *
	 * @return CustomField
	 */
	public function newCustomFieldModel($fieldName)
	{
		return new CustomField([
			'field_name'  => $fieldName,
			'parent_type' => get_class($this),
			'parent_id'   => $this->id
		]);
	}
	
	/**
	 * Save the model to the database.
	 *
	 * @param  array $options
	 *
	 * @return bool
	 */
	public function save(array $options = [])
	{
		$parentResult = parent::save($options);
		
		// save custom fields
		foreach ($this->customFieldNames() as $name) {
			// custom field model instance
			$customFieldModel        = $this->getCustomFieldModel($name);
			$customFieldModel->value = isset($this->customAttributes[ $name ]) ? $this->customAttributes[ $name ] : null;
			$customFieldModel->save();
		}
		
		return $parentResult;
	}
	
	/**
	 * Fill the model with an array of attributes.
	 *
	 * @param  array $attributes
	 *
	 * @return Model
	 *
	 * @throws \Illuminate\Database\Eloquent\MassAssignmentException
	 */
	public function fill(array $attributes)
	{
		$this->fillCustomAttributes($attributes);
		
		return parent::fill($attributes);
	}
	
	/**
	 * Fill custom fields
	 *
	 * @param array $attributes
	 */
	public function fillCustomAttributes(array $attributes)
	{
		foreach ($this->customFieldNames() as $name) {
			if (isset($attributes[ $name ])) {
				$this->customAttributes[ $name ] = $attributes[ $name ];
			}
		}
	}
	
	/**
	 * Delete the model from the database.
	 *
	 * @return bool|null
	 * @throws \Exception
	 */
	public function delete()
	{
		// delete model
		$parentResult = parent::delete();
		
		// delete custom fields
		if ($parentResult) {
			CustomField::where([
				CustomField::PARENT_TYPE => get_class($this),
				CustomField::PARENT_ID   => $this->id
			])->delete();
		}
		
		return $parentResult;
	}
}
