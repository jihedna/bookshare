<?php

namespace App\Http\Controllers;

use App\Services\SimpleChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(SimpleChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Show the chatbot interface
     */
    public function index()
    {
        return view('front.chatbot.index');
    }

    /**
     * Process a chatbot message and return a response
     */
    public function message(Request $request)
    {
        try {
            $message = $request->input('message');
            
            if (empty($message)) {
                return response()->json([
                    'response' => 'Je n\'ai pas compris votre message. Pouvez-vous reformuler?'
                ]);
            }
            
            // Log the incoming message for debugging
            Log::info('Chatbot message received', ['message' => $message]);
            
            // Get a response from the service
            $response = $this->chatbotService->getChatbotResponse($message, []);
            
            // Log the response for debugging
            Log::info('Chatbot response', ['response' => $response]);
            
            return response()->json([
                'response' => $response
            ]);
        } catch (\Exception $e) {
            Log::error('Chatbot error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'response' => 'Désolé, une erreur s\'est produite. Veuillez réessayer plus tard.'
            ]);
        }
    }
}
