<?php
namespace Boparaiamrit\Extendable;


use Illuminate\Support\ServiceProvider;

class ExtendableServiceProvider extends ServiceProvider
{
	protected $defer = false;
	
	public function boot()
	{
		$jsonPath      = 'app' . DIRECTORY_SEPARATOR . 'extendable' . DIRECTORY_SEPARATOR . 'custom_fields.json';
		$migrationFile = '2015_07_23_134516_create_custom_fields_table.php';
		$this->publishes([
			__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . $migrationFile => database_path('migrations' . DIRECTORY_SEPARATOR . $migrationFile),
			__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $jsonPath         => storage_path($jsonPath),
		]);
	}
	
	public function register()
	{
		// Nothing to do
	}
}
