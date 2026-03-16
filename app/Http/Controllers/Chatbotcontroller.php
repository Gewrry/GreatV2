<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function message(Request $request)
    {
        $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:50'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:4000'],
        ]);

        $apiKey = config('services.groq.api_key');

        if (!$apiKey) {
            return response()->json([
                'reply' => '⚠️ Chatbot is not configured. Please contact your administrator.'
            ], 500);
        }

        try {
            $messages = $request->input('messages');

            // Load system instructions from file
            $instructionsPath = storage_path('app/chatbot_instructions.txt');
            $system = file_exists($instructionsPath)
                ? file_get_contents($instructionsPath)
                : 'You are a helpful assistant.';

            $contents = [];

            // Always inject system instructions first — cannot be overridden by frontend
            $contents[] = [
                'role' => 'system',
                'content' => $system,
            ];

            // Append conversation messages
            foreach ($messages as $msg) {
                $contents[] = [
                    'role' => $msg['role'], // 'user' or 'assistant'
                    'content' => $msg['content'],
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => $contents,
                    'max_tokens' => 1024,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content')
                    ?? 'Sorry, I could not generate a response.';
                return response()->json(['reply' => $reply]);
            }

            if ($response->status() === 429) {
                return response()->json([
                    'reply' => '⚠️ The AI is currently busy. Please wait a moment and try again.'
                ], 429);
            }

            Log::error('Groq API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'reply' => '⚠️ The AI service returned an error. Please try again.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Chatbot exception', ['error' => $e->getMessage()]);
            return response()->json([
                'reply' => '⚠️ Unable to reach the AI service right now.'
            ], 500);
        }
    }
}