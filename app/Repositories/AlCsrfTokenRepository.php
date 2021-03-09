<?php

namespace App\Repositories;

use App\Models\AlCsrfToken;

class AlCsrfTokenRepository extends BaseRepository
{
    public $modelName = AlCsrfToken::class;

    public function deleteExpiredToken($currentTime, $expires_time)
    {
       $expiredTokens = $this->model
       ->where(function ($query) use ($currentTime) {
           $query->where('expires_at', '<', $currentTime)
               ->orWhereNull('expires_at');
       })->get();

       foreach ($expiredTokens as $token){
           $token->delete();
       }
    }
}
