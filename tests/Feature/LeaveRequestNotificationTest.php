<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveRequestStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function employee(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'employee',
        ], $overrides));
    }

    private function hr(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'hr',
        ], $overrides));
    }

    private function createLeaveRequest(User $user, string $type = 'cuti', string $status = 'pending'): LeaveRequest
    {
        return LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type' => $type,
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(4)->format('Y-m-d'),
            'reason' => 'Keperluan keluarga penting',
            'status' => $status,
        ]);
    }

    public function test_hr_can_approve_leave_request_and_employee_receives_notification(): void
    {
        $employee = $this->employee();
        $hr = $this->hr();
        $request = $this->createLeaveRequest($employee, 'cuti');

        $response = $this->actingAs($hr)->put(route('hr.leave-requests.update', $request->id), [
            'status' => 'approved',
        ]);

        $response->assertSessionHas('success');

        // Status tersimpan di database
        $request->refresh();
        $this->assertEquals('approved', $request->status);
        $this->assertEquals($hr->id, $request->approved_by);
        $this->assertNotNull($request->approved_at);

        // Notifikasi diterima oleh employee pemilik
        $this->assertCount(1, $employee->notifications);
        $notification = $employee->notifications->first();
        $this->assertEquals(LeaveRequestStatusUpdated::class, $notification->type);
        $this->assertEquals('approved', $notification->data['status']);
        $this->assertEquals('cuti', $notification->data['leave_type']);
        $this->assertStringContainsString('disetujui', $notification->data['message']);
        $this->assertStringContainsString('cuti tahunan', $notification->data['message']);

        // Employee dapat melihat status terbaru di halaman miliknya
        $employeeResponse = $this->actingAs($employee)->get(route('employee.leave-requests.index'));
        $employeeResponse->assertOk()
            ->assertSee('Disetujui');
    }

    public function test_hr_can_reject_leave_request_and_employee_receives_notification(): void
    {
        $employee = $this->employee();
        $hr = $this->hr();
        $request = $this->createLeaveRequest($employee, 'sakit');

        $response = $this->actingAs($hr)->put(route('hr.leave-requests.update', $request->id), [
            'status' => 'rejected',
        ]);

        $response->assertSessionHas('success');

        $request->refresh();
        $this->assertEquals('rejected', $request->status);
        $this->assertEquals($hr->id, $request->approved_by);
        $this->assertNotNull($request->approved_at);

        // Notifikasi penolakan
        $this->assertCount(1, $employee->notifications);
        $notification = $employee->notifications->first();
        $this->assertEquals('rejected', $notification->data['status']);
        $this->assertEquals('sakit', $notification->data['leave_type']);
        $this->assertStringContainsString('ditolak', $notification->data['message']);
        $this->assertStringContainsString('izin sakit', $notification->data['message']);

        // Employee melihat status Ditolak
        $employeeResponse = $this->actingAs($employee)->get(route('employee.leave-requests.index'));
        $employeeResponse->assertOk()
            ->assertSee('Ditolak');
    }

    public function test_all_four_leave_types_are_handled_correctly(): void
    {
        $employee = $this->employee();
        $hr = $this->hr();

        $types = [
            'cuti' => 'cuti tahunan',
            'sakit' => 'izin sakit',
            'penting' => 'izin penting',
            'lainnya' => 'izin lainnya',
        ];

        foreach ($types as $type => $label) {
            $req = $this->createLeaveRequest($employee, $type);

            $this->actingAs($hr)->put(route('hr.leave-requests.update', $req->id), [
                'status' => 'approved',
            ]);

            $req->refresh();
            $this->assertEquals('approved', $req->status);
        }

        $this->assertCount(4, $employee->notifications);

        $messages = $employee->notifications->pluck('data.message')->all();
        $this->assertTrue(collect($messages)->contains(fn ($m) => str_contains($m, 'cuti tahunan') && str_contains($m, 'disetujui')));
        $this->assertTrue(collect($messages)->contains(fn ($m) => str_contains($m, 'izin sakit') && str_contains($m, 'disetujui')));
        $this->assertTrue(collect($messages)->contains(fn ($m) => str_contains($m, 'izin penting') && str_contains($m, 'disetujui')));
        $this->assertTrue(collect($messages)->contains(fn ($m) => str_contains($m, 'izin lainnya') && str_contains($m, 'disetujui')));
    }

    public function test_employee_a_cannot_view_or_access_employee_b_notification_or_leave_request(): void
    {
        $employeeA = $this->employee(['name' => 'Employee A', 'email' => 'a@example.com']);
        $employeeB = $this->employee(['name' => 'Employee B', 'email' => 'b@example.com']);
        $hr = $this->hr();

        $requestB = $this->createLeaveRequest($employeeB, 'cuti');

        // HR approve pengajuan milik Employee B
        $this->actingAs($hr)->put(route('hr.leave-requests.update', $requestB->id), [
            'status' => 'approved',
        ]);

        $notificationB = $employeeB->notifications->first();
        $this->assertNotNull($notificationB);

        // Employee A tidak dapat melihat pengajuan Employee B di halaman detail
        $this->actingAs($employeeA)
            ->get(route('employee.leave-requests.show', $requestB->id))
            ->assertNotFound();

        // Employee A tidak mendapatkan notifikasi apapun
        $this->assertCount(0, $employeeA->notifications);

        // Endpoint notifikasi Employee A tidak memuat notifikasi Employee B
        $apiResponse = $this->actingAs($employeeA)->getJson(route('employee.notifications.index'));
        $apiResponse->assertOk();
        $this->assertEquals(0, $apiResponse->json('unread_count'));
        $this->assertCount(0, $apiResponse->json('notifications'));

        // Employee A tidak dapat menandai notifikasi Employee B sebagai dibaca
        $this->actingAs($employeeA)
            ->postJson(route('employee.notifications.read', $notificationB->id))
            ->assertNotFound();
    }

    public function test_employee_cannot_approve_their_own_leave_request(): void
    {
        $employee = $this->employee();
        $request = $this->createLeaveRequest($employee, 'cuti');

        // Employee biasa diblokir oleh middleware role:hr
        $response = $this->actingAs($employee)->put(route('hr.leave-requests.update', $request->id), [
            'status' => 'approved',
        ]);
        $response->assertForbidden();

        // User dengan role HR yang mengajukan cuti tidak bisa approve pengajuannya sendiri
        $hr = $this->hr();
        $hrRequest = $this->createLeaveRequest($hr, 'sakit');

        $hrResponse = $this->actingAs($hr)->put(route('hr.leave-requests.update', $hrRequest->id), [
            'status' => 'approved',
        ]);
        $hrResponse->assertForbidden();

        $hrRequest->refresh();
        $this->assertEquals('pending', $hrRequest->status);
    }

    public function test_duplicate_processing_does_not_create_duplicate_notifications(): void
    {
        $employee = $this->employee();
        $hr = $this->hr();
        $request = $this->createLeaveRequest($employee, 'penting');

        // Proses pertama (pending -> approved)
        $this->actingAs($hr)->put(route('hr.leave-requests.update', $request->id), [
            'status' => 'approved',
        ]);
        $this->assertCount(1, $employee->notifications);

        // Proses kedua dengan status yang sama (approved -> approved)
        $this->actingAs($hr)->put(route('hr.leave-requests.update', $request->id), [
            'status' => 'approved',
        ]);

        // Jumlah notifikasi tetap 1, tidak duplikat
        $employee->refresh();
        $this->assertCount(1, $employee->notifications);
    }

    public function test_pending_requests_stay_pending_and_have_no_notifications(): void
    {
        $employee = $this->employee();
        $request = $this->createLeaveRequest($employee, 'sakit');

        $this->assertEquals('pending', $request->status);
        $this->assertCount(0, $employee->notifications);

        $response = $this->actingAs($employee)->get(route('employee.leave-requests.index'));
        $response->assertOk()
            ->assertSee('Menunggu');
    }

    public function test_employee_can_mark_notifications_as_read(): void
    {
        $employee = $this->employee();
        $hr = $this->hr();
        $request = $this->createLeaveRequest($employee, 'cuti');

        $this->actingAs($hr)->put(route('hr.leave-requests.update', $request->id), [
            'status' => 'approved',
        ]);

        $notification = $employee->notifications->first();
        $this->assertNull($notification->read_at);

        // Mark single as read
        $readResponse = $this->actingAs($employee)->postJson(route('employee.notifications.read', $notification->id));
        $readResponse->assertOk()
            ->assertJson(['success' => true, 'unread_count' => 0]);

        $notification->refresh();
        $this->assertNotNull($notification->read_at);

        // Buat satu lagi untuk uji mark all read
        $request2 = $this->createLeaveRequest($employee, 'sakit');
        $this->actingAs($hr)->put(route('hr.leave-requests.update', $request2->id), [
            'status' => 'approved',
        ]);

        $this->assertEquals(1, $employee->unreadNotifications()->count());

        $markAllResponse = $this->actingAs($employee)->postJson(route('employee.notifications.mark-all-read'));
        $markAllResponse->assertOk()
            ->assertJson(['success' => true, 'unread_count' => 0]);

        $this->assertEquals(0, $employee->unreadNotifications()->count());
    }

    public function test_employee_can_view_leave_request_details(): void
    {
        $employee = $this->employee();
        $hr = $this->hr();
        $request = $this->createLeaveRequest($employee, 'cuti');

        $this->actingAs($hr)->put(route('hr.leave-requests.update', $request->id), [
            'status' => 'approved',
        ]);

        $response = $this->actingAs($employee)->get(route('employee.leave-requests.show', $request->id));
        $response->assertOk()
            ->assertSee('Detail Permohonan Izin / Cuti')
            ->assertSee('Pengajuan Telah Disetujui')
            ->assertSee('Cuti Tahunan')
            ->assertSee($hr->name);
    }

    public function test_hr_can_view_leave_request_detail_and_take_action(): void
    {
        $employee = $this->employee();
        $hr = $this->hr();
        $request = $this->createLeaveRequest($employee, 'sakit');

        // HR accesses show page
        $response = $this->actingAs($hr)->get(route('hr.leave-requests.show', $request->id));
        $response->assertOk()
            ->assertSee('Detail Permohonan Cuti')
            ->assertSee($employee->name)
            ->assertSee('Izin Sakit')
            ->assertSee('Setujui Pengajuan')
            ->assertSee('Tolak Pengajuan');

        // HR approves
        $actionResponse = $this->actingAs($hr)->put(route('hr.leave-requests.update', $request->id), [
            'status' => 'approved',
        ]);
        $actionResponse->assertRedirect();

        $request->refresh();
        $this->assertEquals('approved', $request->status);
        $this->assertEquals($hr->id, $request->approved_by);
    }

    public function test_hr_live_endpoint_returns_pending_requests_for_notifications(): void
    {
        $employee = $this->employee();
        $hr = $this->hr();
        $request = $this->createLeaveRequest($employee, 'cuti');

        $response = $this->actingAs($hr)->getJson(route('hr.leave-requests.live'));
        $response->assertOk()
            ->assertJsonStructure([
                'total_pending',
                'latest_id',
                'requests' => [
                    '*' => [
                        'id',
                        'name',
                        'leave_type',
                        'start_date',
                        'end_date',
                        'reason',
                        'status',
                        'created_at',
                    ],
                ],
            ]);

        $this->assertEquals($request->id, $response->json('latest_id'));
        $this->assertEquals($request->id, $response->json('requests.0.id'));
    }

    public function test_employee_notifications_ajax_endpoint_returns_realtime_data_and_stats(): void
    {
        $employee = $this->employee();
        $hr = $this->hr();
        $request = $this->createLeaveRequest($employee, 'cuti');

        // Initially pending
        $res1 = $this->actingAs($employee)->getJson(route('employee.notifications.index'));
        $res1->assertOk()
            ->assertJson([
                'unread_count' => 0,
                'stats' => [
                    'total_pending' => 1,
                    'total_approved' => 0,
                    'total_rejected' => 0,
                ],
            ]);

        // HR approves
        $this->actingAs($hr)->put(route('hr.leave-requests.update', $request->id), [
            'status' => 'approved',
        ]);

        // Employee polls via AJAX
        $res2 = $this->actingAs($employee)->getJson(route('employee.notifications.index'));
        $res2->assertOk()
            ->assertJson([
                'unread_count' => 1,
                'stats' => [
                    'total_pending' => 0,
                    'total_approved' => 1,
                    'total_rejected' => 0,
                ],
            ]);

        $this->assertEquals('approved', $res2->json('notifications.0.status'));
        $this->assertEquals($request->id, $res2->json('notifications.0.leave_request_id'));
    }
}
