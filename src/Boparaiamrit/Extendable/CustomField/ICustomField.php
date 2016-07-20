<?php
namespace Boparaiamrit\Extendable\CustomField;

interface ICustomField
{
	const FIELD_STRING   = "string";
	const FIELD_TEXT     = "text";
	const FIELD_SELECT   = "select";
	const FIELD_RADIO    = "radio";
	const FIELD_CHECKBOX = "checkbox";
	const FIELD_DATETIME = "datetime";
	
	const ATTRIBUTE_VALUE = 'value';
	
	const CONFIG_FIELD_TYPE = 'type';
	
	const CACHE_CUSTOM_FIELDS = 'custom_fields';
}
