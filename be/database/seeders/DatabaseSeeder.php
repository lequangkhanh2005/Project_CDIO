<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Resident;
use App\Models\MaintenanceJob;
use App\Models\WorkLog;
use App\Models\Invoice;
use App\Models\JobHistory;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'phone' => '0909000000',
            'email' => 'admin@condomaint.local',
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        $technician = User::create([
            'name' => 'Nguyen Van An',
            'phone' => '0909000111',
            'email' => 'an.nguyen@example.com',
            'password' => Hash::make('123456'),
            'role' => 'technician',
        ]);

        $residents = [
            [
                'name' => 'Tran Thi Mai',
                'phone' => '0903000222',
                'email' => 'mai.tran@example.com',
                'building' => 'Khu A',
                'unit' => 'B1-08',
                'address' => 'Tang ham B1 - Khu A',
            ],
            [
                'name' => 'Le Van Binh',
                'phone' => '0912000333',
                'email' => 'binh.le@example.com',
                'building' => 'Chung cu X',
                'unit' => '05-12',
                'address' => 'Chung cu X - Tang 5',
            ],
            [
                'name' => 'Ngo Thi Thu',
                'phone' => '0935000444',
                'email' => 'thu.ngo@example.com',
                'building' => 'Toa B',
                'unit' => '12.03',
                'address' => 'Toa B - Can 12.03',
            ],
        ];

        foreach ($residents as $residentData) {
            Resident::create($residentData);
        }

        $jobs = [
            [
                'code' => 'JOB-0001',
                'title' => 'Kiểm tra máy bơm tầng hầm',
                'description' => 'Máy bơm phát tiếng ồn lớn, cần kiểm tra bạc đạn.',
                'location' => 'Tầng hầm B1 - Khu A',
                'appointment_at' => now()->addHours(2),
                'resident_name' => 'Trần Thị Mai',
                'resident_phone' => '0903000222',
                'status' => 'Moi',
            ],
            [
                'code' => 'JOB-0002',
                'title' => 'Thay bóng đèn hành lang',
                'description' => 'Bóng đèn hành lang tầng 5 bị hỏng.',
                'location' => 'Chung cư X - Tầng 5',
                'appointment_at' => now()->addDay(),
                'resident_name' => 'Lê Văn Bình',
                'resident_phone' => '0912000333',
                'status' => 'Dang xu ly',
            ],
            [
                'code' => 'JOB-0003',
                'title' => 'Sửa khóa cửa ra vào',
                'description' => 'Khóa cửa bị kẹt, không đóng mở trơn.',
                'location' => 'Tòa B - Căn 12.03',
                'appointment_at' => now()->addDays(2),
                'resident_name' => 'Ngô Thị Thu',
                'resident_phone' => '0935000444',
                'status' => 'Doi vat tu',
            ],
        ];

        foreach ($jobs as $jobData) {
            $job = MaintenanceJob::create([
                ...$jobData,
                'technician_id' => $technician->id,
            ]);

            WorkLog::create([
                'maintenance_job_id' => $job->id,
                'technician_id' => $technician->id,
                'hours' => 1.5,
                'cost' => 120000,
                'note' => 'Kiểm tra sơ bộ và ghi nhận tình trạng.',
                'logged_at' => now()->subHours(3),
            ]);

            JobHistory::create([
                'maintenance_job_id' => $job->id,
                'action' => 'Tạo job',
                'note' => $job->title,
                'created_by' => $admin->id,
            ]);
        }

        Invoice::create([
            'code' => 'INV-0001',
            'resident_id' => 1,
            'job_id' => 1,
            'amount' => 350000,
            'status' => 'Chờ thanh toán',
            'issued_at' => now()->subDay(),
            'note' => 'Chi phí sửa chữa ban đầu.',
        ]);
    }
}
