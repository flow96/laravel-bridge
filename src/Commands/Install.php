<?php

namespace LaravelBridge\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Install extends Command
{
	protected $signature = 'install:bridge';

	public function handle()
	{
		$this->info('Installing npm dependencies for API Client...');
		$process = new Process(['npm', 'install', '--save-dev', '@hey-api/openapi-ts', 'typescript@^6.0.3'], base_path());
		$process->setTimeout(null);
		$process->run(function ($type, $buffer) {
			$this->output->write($buffer);
		});
		$this->info('Node dependencies installed.');
	}
}
