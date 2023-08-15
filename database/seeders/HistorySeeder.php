<?php

namespace Database\Seeders;

use App\Models\History;
use App\Models\Status;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class HistorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Detect if the seeder has not been executed yet
    $this->callOnce([
      StatusSeeder::class,
    ]);

    // Create Pending submission history record
    self::pendingHistory();
    sleep(5);

    // Create Approved submission histories record
    self::approvedHistory();
    sleep(5);

    // Create Rejected submission histories record
    self::rejectedHistory();
    sleep(5);
  }

  public static function pendingHistory(): void
  {
    // A pending submission only contain one record of history
    $pending = Submission::factory()->pending()->create();

    History::factory()->state([
      'submission_id' => $pending->id,
      'user_id' => $pending->user_id,
      // or we can use $pending->status_id
      'status_id' => Status::PENDING['id'],
    ])->create();
  }

  public static function approvedHistory(): void
  {
    // An approved submission will contain two history records
    $approved = Submission::factory()->approved()->create();

    // We need to create another user for approver
    $approver = User::factory()->approver()->create();

    // Create pending & approved history
    History::factory()
      ->count(2)
      ->state(new Sequence(
        [
          'submission_id' => $approved->id,
          'user_id' => $approved->id,
          'status_id' => Status::PENDING['id'],
        ],
        [
          'submission_id' => $approved->id,
          'user_id' => $approver->id,
          'status_id' => Status::APPROVED['id'],
        ],
      ))->create();
  }

  public static function rejectedHistory(): void
  {
    // A rejected submission will also contain two history records
    $rejected = Submission::factory()->rejected()->create();

    // Rejectee user also have an Approver role
    $rejectee = User::factory()->approver()->create();

    // Create pending & rejected history
    History::factory()
      ->count(2)
      ->state(new Sequence(
        [
          'submission_id' => $rejected->id,
          'user_id' => $rejected->user_id,
          'status_id' => Status::PENDING['id'],
        ],
        [
          'submission_id' => $rejected->id,
          'user_id' => $rejectee->id,
          'status_id' => Status::REJECTED['id'],
        ],
      ))->create();
  }
}
