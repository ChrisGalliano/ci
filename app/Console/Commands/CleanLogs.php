<?php

  namespace App\Console\Commands;

  use App\Models\Commit;
  use Illuminate\Console\Command;

  class CleanLogs extends Command {

    const DAYS = 10;

    /**
     * @var string
     */
    protected $signature = 'ci:clean-logs {--dry-run}';

    /**
     * @var string
     */
    protected $description = 'Clean old logs';


    /**
     * @inheritdoc
     */
    public function handle() {
      // older than 10 days
      $builder = Commit::query();
      $builder->where('end_time', '<', time() - (self::DAYS * 24 * 3600));
      $builder->where('end_time', '!=', '0');

      $commits = $builder->get();
      /** @var Commit $commit */
      foreach ($commits as $commit) {
        $file = $commit->getLogFilePath();


        if ($this->option('dry-run')) {
          $this->comment('Remove:' . $file);
          continue;
        }

        if (is_file($file)) {
          $this->info('Remove:' . $file);
          unlink($file);
        }

      }

    }
  }
