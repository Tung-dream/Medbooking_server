<?php
// Tên file: app/Http/Controllers/Api/Admin/FeedbackController.php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback; // <-- Thêm

class FeedbackController extends Controller
{
    /**
     * Lấy tất cả Phản hồi (mới nhất lên đầu).
     * Chạy khi gọi GET /api/admin/feedbacks hoặc /api/staff/feedbacks
     */
    public function index()
    {
        $feedbacks = Feedback::with(
            'patient',
            'target.user',
            'appointment.doctor.user'
        )
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($feedbacks, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function getTopFeedbacks()
    {
        $feedbacks = Feedback::with('appointment.patient')
            ->where('Rating', 5)
            ->whereNotNull('Comment')
            ->orderBy('create_at', 'desc')
            ->limit(3)
            ->get();
        $data = $feedbacks->map(function ($fb) {
            $patient = $fb->appointment->patient;
            return [
                'FeedbackID' => $fb->FeedbackID,
                'Rating' => $fb->Rating,
                'Comment' => $fb->Comment,
                // SỬA Ở ĐÂY: Dùng đúng tên cột trong Database
                'FullName' => $patient ? $patient->FullName : 'Ẩn danh',
                'avatar_url' => $patient ? $patient->avatar_url : null,
            ];
        });
        return response()->json($data);
    }
    /**
     * 
     */
    // (Chúng ta có thể thêm hàm store, update, destroy ở đây sau)
}