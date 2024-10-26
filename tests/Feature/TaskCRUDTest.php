<?php

namespace Tests\Feature;

use App\Enums\Tasks\TaskPriorityEnum;
use App\Enums\Tasks\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskCRUDTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'John Doe',
        ]);

        $this->token = $this->user->createToken('Test Token')->plainTextToken;
    }

    #[Test]
    public function itShouldSuccessfullyListTasksFromuser(): void
    {
        $task1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Task 1',
            'description' => 'Task 1 description',
        ]);

        $task2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Task 2',
            'description' => 'Task 2 description',
            'status' => TaskStatusEnum::COMPLETED,
            'priority' => TaskPriorityEnum::HIGH,
            'due_date' => now()->addDay(),
            'auto_complete_on_due_date' => true,
            'reminder_sent' => true,
        ]);

        $response = $this->json('GET', '/api/tasks', [], [
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'priority',
                        'due_date',
                        'reminder_sent',
                        'auto_complete_on_due_date',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ])
            ->json();

        $this->assertCount(2, $response['data']);

        $this->assertEquals($task1->id, $response['data'][0]['id']);
        $this->assertEquals($task1->title, $response['data'][0]['title']);
        $this->assertEquals($task1->description, $response['data'][0]['description']);
        $this->assertEquals($task1->due_date->toISOString(), $response['data'][0]['due_date']);
        $this->assertEquals($task1->status, $response['data'][0]['status']);
        $this->assertEquals($task1->priority, $response['data'][0]['priority']);
        $this->assertEquals(false, $response['data'][0]['reminder_sent']);
        $this->assertEquals(false, $response['data'][0]['auto_complete_on_due_date']);


        $this->assertEquals($task2->id, $response['data'][1]['id']);
        $this->assertEquals($task2->title, $response['data'][1]['title']);
        $this->assertEquals($task2->description, $response['data'][1]['description']);
        $this->assertEquals($task2->due_date->toISOString(), $response['data'][1]['due_date']);
        $this->assertEquals(TaskStatusEnum::COMPLETED, $response['data'][1]['status']);
        $this->assertEquals(TaskPriorityEnum::HIGH, $response['data'][1]['priority']);
        $this->assertEquals(true, $response['data'][1]['reminder_sent']);
        $this->assertEquals(true, $response['data'][1]['auto_complete_on_due_date']);
    }

    #[Test]
    public function itShouldSuccessfullyShowTask(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Task to Show',
            'description' => 'This is the task description',
            'status' => TaskStatusEnum::PENDING,
            'priority' => TaskPriorityEnum::MEDIUM,
            'due_date' => now()->addDays(2),
            'auto_complete_on_due_date' => false,
            'reminder_sent' => false,
        ]);

        $response = $this->json('GET', '/api/tasks/' . $task->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'priority',
                        'due_date',
                        'reminder_sent',
                        'auto_complete_on_due_date',
                        'created_at',
                        'updated_at',
                    ]
                ]
            )
            ->json();

        $this->assertEquals($task->id, $response['data']['id']);
        $this->assertEquals($task->title, $response['data']['title']);
        $this->assertEquals($task->description, $response['data']['description']);
        $this->assertEquals($task->due_date->toISOString(), $response['data']['due_date']);
        $this->assertEquals($task->status, $response['data']['status']);
        $this->assertEquals($task->priority, $response['data']['priority']);
        $this->assertEquals(false, $response['data']['reminder_sent']);
        $this->assertEquals(false, $response['data']['auto_complete_on_due_date']);
    }

    #[Test]
    public function itShouldSuccessfullyCreateTask(): void
    {
        $data = [
            'title' => 'New Task',
            'description' => 'Task description',
            'priority' => TaskPriorityEnum::MEDIUM,
            'due_date' => now()->addDays(2)->toDateTimeString(),
            'auto_complete_on_due_date' => false,
        ];

        $response = $this->json('POST', '/api/tasks', $data, [
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'priority',
                    'due_date',
                    'reminder_sent',
                    'auto_complete_on_due_date',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->json();

        $this->assertEquals($data['title'], $response['data']['title']);
        $this->assertEquals($data['description'], $response['data']['description']);
        $this->assertEquals(TaskStatusEnum::PENDING, $response['data']['status']);
        $this->assertEquals($data['priority'], $response['data']['priority']);
        $this->assertEquals(Carbon::parse($data['due_date'])->format('Y-m-d H:i'), Carbon::parse($response['data']['due_date'])->format('Y-m-d H:i'));
        $this->assertEquals(false, $response['data']['reminder_sent']);
        $this->assertEquals(false, $response['data']['auto_complete_on_due_date']);
    }

    #[Test]
    #[DataProvider('taskUpdateDataProvider')]
    public function itShouldUpdateTaskAndUpdateOnlyRequestFilledFields(array $data, array $expected): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Old Task Title',
            'description' => 'Old Task Description',
            'status' => TaskStatusEnum::PENDING,
            'priority' => TaskPriorityEnum::MEDIUM,
            'due_date' => now()->addDays(2),
            'auto_complete_on_due_date' => false,
            'reminder_sent' => false,
        ]);

        $response = $this->json('PUT', '/api/tasks/' . $task->id, $data, [
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'priority',
                    'due_date',
                    'reminder_sent',
                    'auto_complete_on_due_date',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->json();

        $this->assertEquals($expected['title'], $response['data']['title']);
        $this->assertEquals($expected['description'], $response['data']['description']);
        $this->assertEquals($expected['status'], $response['data']['status']);
        $this->assertEquals($expected['priority'], $response['data']['priority']);
        $this->assertEquals(Carbon::parse($expected['due_date'])->format('Y-m-d H:i'), Carbon::parse($response['data']['due_date'])->format('Y-m-d H:i'));
        $this->assertEquals($expected['reminder_sent'], $response['data']['reminder_sent']);
        $this->assertEquals($expected['auto_complete_on_due_date'], $response['data']['auto_complete_on_due_date']);
    }

    #[Test]
    public function itShouldDestroyTask(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Task to be deleted',
        ]);

        $this->assertCount(1, Task::all());

        $this->json('DELETE', '/api/tasks/' . $task->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->assertStatus(200)
            ->json();

        $this->assertCount(0, Task::all());

        $this->json('DELETE', '/api/tasks/' . $task->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->assertStatus(404)
            ->json();
    }

    #[Test]
    public function itShouldReturnUnauthorizedWhenUserTriesToListTasksWithoutAuthorization(): void
    {
        $this->json('GET', '/api/tasks')
            ->assertStatus(401);
    }

    #[Test]
    public function itShouldReturnUnauthorizedWhenUserTriesToShowTaskWithoutAuthorization(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->json('GET', '/api/tasks/' . $task->id)
            ->assertStatus(401);
    }

    #[Test]
    public function itShouldReturnUnauthorizedWhenUserTriesToCreateTaskWithoutAuthorization(): void
    {
        $data = [
            'title' => 'Unauthorized Task',
            'description' => 'This task should not be created',
            'priority' => TaskPriorityEnum::MEDIUM,
            'due_date' => now()->addDays(2)->toDateTimeString(),
            'auto_complete_on_due_date' => false,
        ];

        $this->json('POST', '/api/tasks', $data)
            ->assertStatus(401);
    }

    #[Test]
    public function itShouldReturnUnauthorizedWhenUserTriesToUpdateTaskWithoutAuthorization(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'title' => 'Updated Title',
        ];

        $this->json('PUT', '/api/tasks/' . $task->id, $data)
            ->assertStatus(401);
    }

    #[Test]
    public function itShouldReturnUnauthorizedWhenUserTriesToDeleteTaskWithoutAuthorization(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->json('DELETE', '/api/tasks/' . $task->id)
            ->assertStatus(401);
    }

    #[Test]
    public function itShouldReturnNotFoundWhenUserTriesToListTasksOfAnotherUser(): void
    {
        $anotherUser = User::factory()->create();
        Task::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->json('GET', '/api/tasks', [], [
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->assertStatus(200);

        $this->assertCount(0, $response['data']);
    }

    #[Test]
    public function itShouldReturnNotFoundWhenUserTriesToShowTaskOfAnotherUser(): void
    {
        $anotherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $anotherUser->id]);

        $this->json('GET', '/api/tasks/' . $task->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->assertStatus(404);
    }

    #[Test]
    public function itShouldReturnNotFoundWhenUserTriesToUpdateTaskOfAnotherUser(): void
    {
        $anotherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $anotherUser->id]);

        $data = [
            'title' => 'Updated Title',
        ];

        $this->json('PUT', '/api/tasks/' . $task->id, $data, [
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->assertStatus(404);
    }

    #[Test]
    public function itShouldReturnNotFoundWhenUserTriesToDeleteTaskOfAnotherUser(): void
    {
        $anotherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $anotherUser->id]);

        $this->json('DELETE', '/api/tasks/' . $task->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ])
            ->assertStatus(404);
    }

    #[Test]
    #[DataProvider('createTaskRequestValidationDataProvider')]
    public function itShouldValidateCreateTaskRequestFields($data, $expected)
    {
        $this->json('POST', '/api/tasks', $data, [
            'Authorization' => 'Bearer ' . $this->token,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors($expected);
    }

    public static function taskUpdateDataProvider(): array
    {
        return [
            [
                ['title' => 'Updated Task Title'],
                [
                    'title' => 'Updated Task Title',
                    'description' => 'Old Task Description',
                    'status' => TaskStatusEnum::PENDING,
                    'priority' => TaskPriorityEnum::MEDIUM,
                    'due_date' => now()->addDays(2)->toDateTimeString(),
                    'reminder_sent' => false,
                    'auto_complete_on_due_date' => false
                ],
            ],
            [
                ['description' => 'Updated Task Description'],
                [
                    'title' => 'Old Task Title',
                    'description' => 'Updated Task Description',
                    'status' => TaskStatusEnum::PENDING,
                    'priority' => TaskPriorityEnum::MEDIUM,
                    'due_date' => now()->addDays(2)->toDateTimeString(),
                    'reminder_sent' => false,
                    'auto_complete_on_due_date' => false
                ],
            ],
            [
                ['status' => TaskStatusEnum::COMPLETED],
                [
                    'title' => 'Old Task Title',
                    'description' => 'Old Task Description',
                    'status' => TaskStatusEnum::COMPLETED,
                    'priority' => TaskPriorityEnum::MEDIUM,
                    'due_date' => now()->addDays(2)->toDateTimeString(),
                    'reminder_sent' => false,
                    'auto_complete_on_due_date' => false
                ],
            ],
            [
                ['priority' => TaskPriorityEnum::LOW],
                [
                    'title' => 'Old Task Title',
                    'description' => 'Old Task Description',
                    'status' => TaskStatusEnum::PENDING,
                    'priority' => TaskPriorityEnum::LOW,
                    'due_date' => now()->addDays(2)->toDateTimeString(),
                    'reminder_sent' => false,
                    'auto_complete_on_due_date' => false
                ],
            ],
            [
                ['due_date' => now()->addDays(3)->toDateTimeString()],
                [
                    'title' => 'Old Task Title',
                    'description' => 'Old Task Description',
                    'status' => TaskStatusEnum::PENDING,
                    'priority' => TaskPriorityEnum::MEDIUM,
                    'due_date' => now()->addDays(3)->toDateTimeString(),
                    'reminder_sent' => false,
                    'auto_complete_on_due_date' => false
                ],
            ],
            [
                ['auto_complete_on_due_date' => true],
                [
                    'title' => 'Old Task Title',
                    'description' => 'Old Task Description',
                    'status' => TaskStatusEnum::PENDING,
                    'priority' => TaskPriorityEnum::MEDIUM,
                    'due_date' => now()->addDays(2)->toDateTimeString(),
                    'reminder_sent' => false,
                    'auto_complete_on_due_date' => true
                ],
            ],
            [
                [
                    'title' => 'Updated Task Title',
                    'description' => 'Updated Task Description',
                    'status' => TaskStatusEnum::OVERDUE,
                    'priority' => TaskPriorityEnum::HIGH,
                    'due_date' => now()->addDays(3)->toDateTimeString(),
                    'auto_complete_on_due_date' => true,
                ],
                [
                    'title' => 'Updated Task Title',
                    'description' => 'Updated Task Description',
                    'status' => TaskStatusEnum::OVERDUE,
                    'priority' => TaskPriorityEnum::HIGH,
                    'due_date' => now()->addDays(3)->toDateTimeString(),
                    'reminder_sent' => false,
                    'auto_complete_on_due_date' => true,
                ],
            ],
        ];
    }

    public static function createTaskRequestValidationDataProvider()
    {
        return [
            'missing title' => [
                [],
                ['title'],
            ],
            'invalid title type' => [
                ['title' => 123],
                ['title'],
            ],
            'invalid priority' => [
                ['title' => 'Valid Title', 'priority' => 'invalid_priority'],
                ['priority'],
            ],
            'invalid due_date' => [
                ['title' => 'Valid Title', 'due_date' => 'invalid_date'],
                ['due_date'],
            ],
            'invalid auto_complete_on_due_date type' => [
                ['title' => 'Valid Title', 'auto_complete_on_due_date' => 'not_boolean'],
                ['auto_complete_on_due_date'],
            ],
            'valid title, missing due_date' => [
                ['title' => 'Valid Title'],
                ['due_date'],
            ],
            'valid title, invalid description type' => [
                ['title' => 'Valid Title', 'description' => 123],
                ['description'],
            ],
        ];
    }
}
