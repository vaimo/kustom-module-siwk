<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Token;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Serialize\Serializer\Base64Json;

/**
 * @internal
 */
class Decoder
{
    /**
     * @var array
     */
    private array $payLoad = [];
    /**
     * @var array
     */
    private array $header = [];
    /**
     * @var Base64Json
     */
    private Base64Json $base64Json;

    /**
     * @param Base64Json $base64Json
     * @codeCoverageIgnore
     */
    public function __construct(Base64Json $base64Json)
    {
        $this->base64Json = $base64Json;
    }

    /**
     * Getting back the payload
     *
     * @return array
     */
    public function getPayLoad(): array
    {
        return $this->payLoad;
    }

    /**
     * Getting back the header
     *
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * Calculating the content of the ID token
     *
     * Customized code from: https://stackoverflow.com/a/68272911
     *
     * @param string $token
     * @throws Exception
     */
    public function calculate(string $token): void
    {
        $tokenParts = explode('.', $token);

        $allowedParts = 3;
        if (count($tokenParts) != $allowedParts) {
            throw new ValidatorException(__('Token is not valid'));
        }

        $this->payLoad = $this->getContent($tokenParts[1]);
        $this->header = $this->getContent($tokenParts[0]);
    }

    /**
     * Getting back the content
     *
     * @param string $input
     * @return array
     */
    private function getContent(string $input): array
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padLength = 4 - $remainder;
            $input .= str_repeat('=', $padLength);
        }

        $input = json_encode($this->base64Json->unserialize(strtr($input, '-_', '+/')));

        $max_int_length = strlen((string) PHP_INT_MAX) - 1;
        $json_without_bigints = preg_replace(
            '/:\s*(-?\d{'.$max_int_length.',})/',
            ': "$1"',
            $input
        );

        return json_decode($json_without_bigints, true);
    }
}
