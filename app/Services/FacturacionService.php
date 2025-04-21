<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FacturacionService
{
    protected $baseUrl = 'https://api-sandbox.factus.com.co';
    protected $clientId = 'TU_CLIENT_ID';
    protected $clientSecret = 'TU_CLIENT_SECRET';
    protected $username = 'TU_USUARIO';
    protected $password = 'TU_PASSWORD';

    public function getAccessToken()
    {
        try {
            $response = Http::post("{$this->baseUrl}/oauth/token", [
                'grant_type' => 'password',
                'client_id' => config('services.factus.client_id'),
                'client_secret' => config('services.factus.client_secret'),
                'username' => config('services.factus.username'),
                'password' => config('services.factus.password'),
            ]);

            Log::info('Respuesta de token:', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            throw new \Exception('No se pudo obtener token de acceso');
        } catch (\Exception $e) {
            Log::error('Error en getAccessToken(): ' . $e->getMessage());
            throw $e;
        }
    }


    protected function requestNewToken()
    {
        $response = Http::asForm()->withHeaders([
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}/oauth/token", [
            'grant_type' => 'password',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if (!$response->successful()) {
            throw new \Exception('No se pudo obtener token de acceso');
        }

        $data = $response->json();
        $this->storeTokens($data);

        return $data['access_token'];
    }

    protected function refreshToken()
    {
        $response = Http::asForm()->withHeaders([
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}/oauth/token", [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => Cache::get('factus_refresh_token'),
        ]);

        if (!$response->successful()) {
            // Si falla, eliminamos el refresh token y volvemos a pedir uno nuevo
            Cache::forget('factus_refresh_token');
            return $this->requestNewToken();
        }

        $data = $response->json();
        $this->storeTokens($data);

        return $data['access_token'];
    }

    protected function storeTokens(array $data)
    {
        Cache::put('factus_access_token', $data['access_token'], now()->addSeconds($data['expires_in'] - 60));
        Cache::put('factus_refresh_token', $data['refresh_token'], now()->addDays(30));
    }

    public function makeRequest($method, $endpoint, $data = [])
    {
        $token = $this->getAccessToken();

        return Http::withToken($token)
            ->acceptJson()
            ->{$method}("{$this->baseUrl}/{$endpoint}", $data);
    }

    public function crearFactura(array $datosFactura, string $accessToken)
    {
        $url = 'https://api-sandbox.factus.com.co/v1/bills/validate';

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post($url, $datosFactura);

        if ($response->successful()) {
            return [
                'ok' => true,
                'data' => $response->json()
            ];
        } else {
            return [
                'ok' => false,
                'status' => $response->status(),
                'error' => $response->json()
            ];
        }
    }
}