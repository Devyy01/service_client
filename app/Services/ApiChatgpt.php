<?php

// namespace App\Services;

// use GuzzleHttp\Client;
// use Illuminate\Support\Facades\Http;
// use LanguageDetection\Language;
// use GuzzleHttp\Exception\RequestException;
// use GuzzleHttp\Exception\ConnectException;
// use GuzzleHttp\Exception\TransferException;
// use GuzzleHttp\Exception\InvalidArgumentException;
// use Illuminate\Support\Facades\Log;

// class ApiChatgpt
// {
//     private $retryDelay = 1;
//     private $maxRetryDelay = 60;
//     private $defaultTimeoutChat = 10;
//     private $defaultTimeout = 60;
//     private $defaultConnectTimeout = 30;
//     private $openAiApiUrl = 'https://api.openai.com/v1/chat/completions';
//     private $model = 'gpt-4o';

//     public function generateCanva($prompt)
//     {
//         try {
//             $response = Http::withHeaders([
//                 'Content-Type' => 'application/json',
//                 'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
//             ])->post($this->openAiApiUrl, [
//                 'model' => $this->model,
//                 'messages' => [
//                     [
//                         'role' => 'user',
//                         'content' => $prompt,
//                     ],
//                 ],
//                 'temperature' => 1.0,
//                 'max_tokens' => 1500,
//             ]);
//              dd($response->successful());
//             if ($response->successful()) {
//                 $completions = $response->json('choices');
//                 if (isset($completions[0]['message']['content'])) {
//                     $canva = $completions[0]['message']['content'];
//                     $sortedCanva = $this->trierResponse($canva);
//                     if ($sortedCanva !== false) {
//                         return $sortedCanva;
//                     }
//                 } else {
//                     Log::error('Unexpected API response structure: ' . $response->body());
//                 }
//             } else {
//                 Log::error('OpenAI API request failed: ' . $response->body());
//             }
//         } catch (\Exception $e) {
//             Log::error('Error while generating reviews: ' . $e->getMessage());
//         }

//         return null;
//     }
// }


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiChatgpt
{
    private $openAiApiUrl = 'https://api.openai.com/v1/chat/completions';
    private $model = 'gpt-4o';

    public function generateCanva(string $prompt): ?string
    {
        $apiKey = env('OPENAI_API_KEY');
        
        if (empty($apiKey)) {
            Log::error('OPENAI_API_KEY is not set in the environment.');
            return null;
        }

        // try {
            // $response = Http::withHeaders([
            //     'Content-Type' => 'application/json',
            //     'Authorization' => 'Bearer ' . $apiKey,
            // ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            //     'model' => $this->model,
            //     'messages' => [
            //         [
            //             'role' => 'user',
            //             'content' => $prompt,
            //         ],
            //     ],
            //     'temperature' => 1.0,
            //     'max_tokens' => 1500,
            // ]);
            $response = Http::withOptions([
                'verify' => false, // Désactive SSL vérification temporairement
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->timeout(30)->post($this->openAiApiUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user',
                     'content' => $prompt
                    ],
                ],
                'temperature' => 1.0,
                'max_tokens' => 1500,
            ]);
            
            if ($response->successful()) {
                $choices = $response->json('choices');

                if (!empty($choices[0]['message']['content'])) {
                    return $choices[0]['message']['content'];
                } else {
                    Log::error('Unexpected API response structure: ' . $response->body());
                }
            } else {
                Log::error('OpenAI API request failed: ' . $response->status() . ' - ' . $response->body());
            }

        // } catch (\Throwable $e) {
        //     Log::error('Error while calling OpenAI API: ' . $e->getMessage());
        // }

        return null;
    }
}
