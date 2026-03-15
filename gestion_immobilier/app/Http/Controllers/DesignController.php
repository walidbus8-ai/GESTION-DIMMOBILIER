<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    /**
     * جلب كاع التصاميم ديال المستخدم الحالي
     */
    public function index(Request $request)
    {
        return Design::whereHas('room', function($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->with('room', 'furniture')->get();
    }

    /**
     * إنشاء سجل تصميم جديد في قاعدة البيانات
     */
    public function store(Request $request, Room $room)
    {
        $data = $request->validate([
            'style' => 'required|string',
        ]);

        $design = $room->design()->create($data);
        
        if ($request->has('furniture_ids')) {
            $design->furniture()->sync($request->furniture_ids);
        }
        return response()->json($design, 201);
    }

    /**
     * عرض تفاصيل تصميم معين
     */
    public function show(Design $design)
    {
        return $design->load('room', 'furniture');
    }

    /**
     * تحديث بيانات التصميم (يدوياً)
     */
    public function update(Request $request, Design $design)
    {
        $data = $request->validate([
            'style' => 'sometimes|string',
            'imageGenere' => 'sometimes|string',
        ]);
        
        $design->update($data);
        
        if ($request->has('furniture_ids')) {
            $design->furniture()->sync($request->furniture_ids);
        }
        return response()->json($design);
    }

    /**
     * حذف تصميم
     */
    public function destroy(Design $design)
    {
        $design->delete();
        return response()->json(null, 204);
    }

    /**
     * الدالة الأساسية: توليد الصورة باستخدام AI وحفظها في السيرفر والداتابيز
     */
    public function generate(Request $request, $id = null)
    {
        // 1. البحث عن التصميم المستهدف للتحديث
        $design = $id ? Design::find($id) : null;

        // 2. رابط Colab (تأكدي ديما أنه هو اللي طالع عندك في Colab دابا)
        // ملاحظة: هاد الرابط كيتغير كل مرة كتحلي Colab
        $colabUrl = "https://7604f87fbe20a7c5cb.gradio.live/"; 

        // 3. التحقق من وجود الصورة الأصلية (Base64)
        if (!$request->has('image')) {
            return response()->json(['error' => 'Veuillez uploader une photo قبل البدء.'], 400);
        }

        try {
            // --- خطوة التيست: واش الرابط خدام؟ ---
            $check = Http::timeout(5)->get($colabUrl);
            if ($check->failed()) {
                return response()->json(['error' => 'رابط Colab طافي أو ميت! تأكد من الرابط الجديد في Colab.'], 500);
            }

            // 4. بناء رابط الـ API الخاص بـ Stable Diffusion
            $apiUrl = rtrim($colabUrl, '/') . '/sdapi/v1/img2img';
            
            // 5. إرسال الطلب لـ Stable Diffusion
            $response = Http::timeout(180)->post($apiUrl, [
                'prompt' => $request->prompt ?? "professional interior design, modern luxury, highly detailed, 8k, photorealistic",
                'negative_prompt' => "blurry, distorted, low quality, messy, ugly, lowres, monochrome",
                'init_images' => [$request->image],
                'steps' => 30,
                'sampler_name' => "DPM++ 2M",
                'cfg_scale' => 7,
                'denoising_strength' => 0.75,
                'width' => 1024,
                'height' => 1024,
                'seed' => -1,
                'override_settings' => [
                    'sd_model_checkpoint' => 'sd_xl_base_1.0.safetensors'
                ]
            ]);

            if ($response->successful()) {
                $base64Image = $response->json()['images'][0];
                $imageName = 'design_' . time() . '_' . Str::random(8) . '.png';
                $imageData = base64_decode($base64Image);
                
                // حفظ الصورة في storage/app/public/designs
                Storage::disk('public')->put('designs/' . $imageName, $imageData);
                $publicUrl = asset('storage/designs/' . $imageName);
                
                if ($design) {
                    $design->update([
                        'imageGenere' => $publicUrl
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'image' => $publicUrl,
                    'design' => $design
                ]);
            }

            return response()->json(['error' => 'Erreur Stable Diffusion: ' . $response->body()], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur de connexion: ' . $e->getMessage()], 500);
        }
    }
}