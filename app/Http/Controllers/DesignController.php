<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DesignController extends Controller
{
    public function generate(Request $request)
    {
        // 1. حل مشكل الوقت: كنعطيو للسيرفر 5 دقايق باش ما يقطعش حيت الـ AI كيتعطل
        set_time_limit(300);

        // تأكدي ديما أن هاد الرابط هو اللي خدام فـ Colab دابا
        $colabUrl = 'https://3829877b36bdaa25c0.gradio.live/';
        $apiUrl = rtrim($colabUrl, '/') . '/sdapi/v1/img2img';

        if (!$request->has('image')) {
            return response()->json(['error' => 'Veuillez uploader une photo.'], 400)
                             ->header('Access-Control-Allow-Origin', '*');
        }

        $imageRaw = $request->image;
        if (preg_match('/^data:image\/(\w+);base64,/', $imageRaw, $type)) {
            $imageRaw = substr($imageRaw, strpos($imageRaw, ',') + 1);
        }

        $userPrompt = $request->prompt ?? "modern luxury interior design";
        $enhancedPrompt = $userPrompt . ", ultra-realistic, photorealistic, 8k uhd, cinematic lighting, interior photography, highly detailed textures, masterpiece, professional architecture";
        
        $negativePrompt = "cgi, 3d render, cartoon, anime, blurry, distorted, messy, low quality, painting, drawing, sketch, hands, people, lowres, out of frame";

        try {
            // إرسال الطلب لـ Stable Diffusion
            $response = Http::withoutVerifying()
                ->timeout(300) 
                ->post($apiUrl, [
                    'prompt' => $enhancedPrompt,
                    'negative_prompt' => $negativePrompt,
                    'init_images' => [$imageRaw],
                    'sampler_name' => "DPM++ 2M",
                    'scheduler' => "Automatic",
                    'steps' => 30,
                    'cfg_scale' => 7,
                    'denoising_strength' => 0.75,
                    'width' => 1024,
                    'height' => 1024,
                    'batch_size' => 1,
                    'seed' => -1,
                    'send_images' => true,
                    'save_images' => false 
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (!isset($responseData['images'][0])) {
                    return response()->json(['error' => 'Stable Diffusion لم يرسل أي صورة.'], 500)
                                     ->header('Access-Control-Allow-Origin', '*');
                }

                $base64Image = $responseData['images'][0];
                $imageName = 'design_' . time() . '_' . Str::random(8) . '.png';
                
                Storage::disk('public')->put('designs/' . $imageName, base64_decode($base64Image));
                $publicUrl = asset('storage/designs/' . $imageName);
                
                $room = Room::firstOrCreate(
                    ['user_id' => $request->user()?->id ?? 1],
                    [
                        'largeur' => 4.0, 
                        'hauteur' => 2.8, 
                        'type' => 'chambre',
                    ]
                );

                $design = Design::create([
                    'room_id' => $room->id,
                    'style' => Str::limit($enhancedPrompt, 250), 
                    'imageGenere' => $publicUrl
                ]);

                // 2. حل مشكل CORS: كنرجعو النتيجة مع الـ Headers باش الـ React يقبلها
                return response()->json([
                    'success' => true,
                    'image' => $publicUrl,
                    'design' => $design
                ])
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            }

            Log::error("Stable Diffusion Server Error: " . $response->body());
            return response()->json(['error' => 'SD Status: ' . $response->status()], 500)
                             ->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error("Generate Exception: " . $e->getMessage());
            return response()->json(['error' => 'Erreur de connexion: ' . $e->getMessage()], 500)
                             ->header('Access-Control-Allow-Origin', '*');
        }
    }
}