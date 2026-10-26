<?php

namespace App\Http\Controllers;

use App\DTO\Telegram\TelegramStartData;
use App\Http\Requests\TelegramWebhookRequest;
use App\Services\Contracts\AdminTelegramConnectionServiceInterface;
use Illuminate\Http\JsonResponse;

class TelegramWebhookController extends Controller
{
    public function __construct(private readonly AdminTelegramConnectionServiceInterface $connections)
    {
    }

    public function __invoke(TelegramWebhookRequest $request): JsonResponse
    {
        if ($request->filled('message.chat.id')
            && $request->filled('message.chat.type')
            && $request->filled('message.text')) {
            $this->connections->completeFromStart(new TelegramStartData(
                chatId: (string) $request->integer('message.chat.id'),
                chatType: $request->string('message.chat.type')->value(),
                text: $request->string('message.text')->value(),
            ));
        }

        return response()->json(['ok' => true]);
    }
}
