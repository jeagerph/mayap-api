<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetupInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setting up your database with default data and setup Passport API and key';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('key:generate');
		// $this->call('storage:link');
		$this->call('migrate');
		$this->call('db:seed');
		$this->call('passport:install');
		$passport = $this->secretKey();
		$this->envUpdate('PASSPORT_CLIENT_SECRET', $passport->secret);
		$this->info('Tapos na ang proseso! Mabuhay!');
	}
	
	/**
     * Update Laravel Env file using key => value pair
     * @param string $key
     * @param string $value
     */
    public static function envUpdate($key, $value)
    {
        $path = base_path('.env');

		if (file_exists($path)):
            file_put_contents($path, str_replace(
                $key . '=' . env($key), $key . '=' . $value, file_get_contents($path)
            ));
        endif;
	}
	
	/**
     * Pull saved client secret
     * @param string $key
     * @param string $value
     */
	public function secretKey()
	{
		$client_secret = DB::table('oauth_clients')->where('id', 2)->first();

		return $client_secret;
	}
}
