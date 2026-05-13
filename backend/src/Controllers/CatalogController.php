<?php

namespace App\Controllers;

use App\Core\JsonResponse;
use App\Repositories\ProductRepository;

class CatalogController
{
    public function __construct(private ProductRepository $products)
    {
    }

    public function categories(): void
    {
        JsonResponse::send(['success' => true, 'data' => $this->products->categories()]);
    }

    public function products(?int $categoryId): void
    {
        JsonResponse::send(['success' => true, 'data' => $this->products->products($categoryId)]);
    }

    public function productById(int $id): void
    {
        $product = $this->products->findById($id);
        if (!$product) JsonResponse::send(['success'=>false,'message'=>'Produto nao encontrado.'],404);
        JsonResponse::send(['success'=>true,'data'=>$product]);
    }

    public function imageProxy(string $url): void
    {
        $url = trim($url);
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url)) {
            http_response_code(422);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'URL invalida';
            exit;
        }

        $imageData = null;
        $contentType = 'image/jpeg';

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 12,
                CURLOPT_USERAGENT => 'Mozilla/5.0 MiniCommerceImageProxy',
                CURLOPT_HTTPHEADER => ['Accept: image/*,*/*;q=0.8'],
            ]);
            $body = curl_exec($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $type = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($status >= 200 && $status < 300 && is_string($body) && $body !== '') {
                $imageData = $body;
                if ($type !== '') {
                    $contentType = explode(';', $type)[0];
                }
            }
        }

        if ($imageData === null) {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 12,
                    'header' => "User-Agent: Mozilla/5.0 MiniCommerceImageProxy\r\nAccept: image/*,*/*;q=0.8\r\n",
                ],
            ]);
            $body = @file_get_contents($url, false, $context);
            if (is_string($body) && $body !== '') {
                $imageData = $body;
                $headers = function_exists('http_get_last_response_headers')
                    ? (http_get_last_response_headers() ?: [])
                    : [];
                if (is_array($headers) && $headers) {
                    foreach ($headers as $h) {
                        if (stripos($h, 'Content-Type:') === 0) {
                            $contentType = trim(explode(':', $h, 2)[1]);
                            $contentType = explode(';', $contentType)[0];
                            break;
                        }
                    }
                }
            }
        }

        if ($imageData === null) {
            http_response_code(404);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Imagem indisponivel';
            exit;
        }

        header('Content-Type: ' . $contentType);
        header('Cache-Control: public, max-age=86400');
        echo $imageData;
        exit;
    }
}
