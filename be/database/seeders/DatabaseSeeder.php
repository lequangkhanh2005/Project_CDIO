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
            'password' => Hash::make('admin123'),
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
                'title' => 'Kiem tra may bom tang ham',
                'description' => 'May bom phat tieng on lon, can kiem tra bac dan.',
                'location' => 'Tang ham B1 - Khu A',
                'appointment_at' => now()->addHours(2),
                'resident_name' => 'Tran Thi Mai',
                'resident_phone' => '0903000222',
                'status' => 'Moi',
            ],
            [
                'code' => 'JOB-0002',
                'title' => 'Thay bong den hanh lang',
                'description' => 'Bong den hanh lang tang 5 bi hong.',
                'location' => 'Chung cu X - Tang 5',
                'appointment_at' => now()->addDay(),
                'resident_name' => 'Le Van Binh',
                'resident_phone' => '0912000333',
                'status' => 'Dang xu ly',
            ],
            [
                'code' => 'JOB-0003',
                'title' => 'Sua khoa cua ra vao',
                'description' => 'Khoa cua bi ket, khong dong mo tron.',
                'location' => 'Toa B - Can 12.03',
                'appointment_at' => now()->addDays(2),
                'resident_name' => 'Ngo Thi Thu',
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
                'note' => 'Kiem tra so bo va ghi nhan tinh trang.',
                'logged_at' => now()->subHours(3),
            ]);

            JobHistory::create([
                'maintenance_job_id' => $job->id,
                'action' => 'Tao job',
                'note' => $job->title,
                'created_by' => $admin->id,
            ]);
        }

        Invoice::create([
            'code' => 'INV-0001',
            'resident_id' => 1,
            'job_id' => 1,
            'amount' => 350000,
            'status' => 'Cho thanh toan',
            'issued_at' => now()->subDay(),
            'note' => 'Chi phi sua chua ban dau.',
        ]);
    }
}
