<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $userMessage = $request->input('message');

        $apiKey = "AIzaSyCXSkPtdJ6mGKfeS2w1FL82wXfz7Q15zhA";
        
        // التعديل السحري: استخدمنا الموديل الذي أخبرتنا جوجل أنه متاح لكِ
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        $systemInstruction = "Tu es l'assistant intelligent de RoomAI. Aide l'utilisateur avec des conseils de décoration et des prompts Stable Diffusion.";

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $systemInstruction . "\n\nUtilisateur: " . $userMessage]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'];
                    return response()->json(['reply' => $reply]);
                }
                
                return response()->json(['reply' => "Désolé, pas de réponse générée."], 200);
            }

            $errorDetail = $response->json()['error']['message'] ?? "Erreur inconnue";
            return response()->json(['reply' => "Détail: " . $errorDetail], $response->status());

        } catch (\Exception $e) {
            return response()->json(['reply' => "Erreur de connexion."], 500);
        }
    }
}