<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('phan_hoi')
            ->leftJoin('nguoi_dung', 'phan_hoi.id_cu_dan', '=', 'nguoi_dung.id_nguoi_dung')
            ->leftJoin('yeu_cau_bao_tri', 'phan_hoi.id_yeu_cau', '=', 'yeu_cau_bao_tri.id_yeu_cau')
            ->select([
                'phan_hoi.id_phan_hoi as id',
                'phan_hoi.id_yeu_cau as request_id',
                'phan_hoi.id_cu_dan as resident_id',
                'phan_hoi.danh_gia as rating',
                'phan_hoi.binh_luan as comment',
                'phan_hoi.created_at',
                'phan_hoi.updated_at',
                'nguoi_dung.ten as resident_name',
                'nguoi_dung.dien_thoai as resident_phone',
                'yeu_cau_bao_tri.mo_ta as request_description',
                'yeu_cau_bao_tri.trang_thai as request_status',
            ])
            ->orderByDesc('phan_hoi.created_at');

        if ($request->filled('resident_phone')) {
            $query->where('nguoi_dung.dien_thoai', $request->query('resident_phone'));
        }
        if ($request->filled('resident_name')) {
            $query->where('nguoi_dung.ten', 'like', '%' . $request->query('resident_name') . '%');
        }
        if ($request->filled('request_id')) {
            $query->where('phan_hoi.id_yeu_cau', $request->query('request_id'));
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }
}
