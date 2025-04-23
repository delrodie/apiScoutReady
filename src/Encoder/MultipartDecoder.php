<?php

namespace App\Encoder;

use App\Service\GestionMedia;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class MultipartDecoder implements DecoderInterface
{
    public const FORMAT = 'multipart';
    public const IMAGE_EXTENSION = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];

    public function __construct(
        private RequestStack $requestStack,
        private GestionMedia $gestionMedia
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function decode(string $data, string $format, array $context = []): mixed
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) return null;

        $photo = null;
        $photoProfile = $request->files->get('photo');
        if ($photoProfile){
            $mimeType = $photoProfile->getMimeType(); //dd($mimeType);
            if(in_array($mimeType, self::IMAGE_EXTENSION, true)){
                $photo = $this->gestionMedia->upload($photoProfile, 'profile');
            }else{
                throw new \Exception("Le fichier télécharger doit être une image (.PNG, .JPEG, .JPG");
            }
        }

        return array_map(static function ($element) {
                if (!is_string($element)) {
                    return $element;
                }

                $decoded = json_decode($element, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $element;
            }, $request->request->all()) + [
                'photo' => $photo ?: '',
            ];


    }

    /**
     * @inheritDoc
     */
    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}