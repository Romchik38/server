<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;

use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Routers\Http\RouterHeadersInterface;
use Romchik38\Server\Api\Routers\Http\DynamicHeadersCollectionInterface;

/** 
 * this is not a service for the app
 * it used only in dynamic header
 */
class DynamicHeadersCollection implements DynamicHeadersCollectionInterface
{
    protected array $hash = [];

    public function __construct(
        array $headersList
    ) {
        /** @var RouterHeadersInterface $header */
        foreach ($headersList as $header) {
            $method = $header->getMethod();
            $methodList = $this->hash[$method] ?? [];
            $methodList[$header->getPath()] = $header;
            $this->hash[$method] =  $methodList;
        }
    }

    public function getHeader(string $method, string $path, string $actionType): RouterHeadersInterface|null
    {
        /** 1. Method not found */
        $headers = $this->hash[$method] ?? null;
        if ($headers === null) {
            return null;
        }

        /** 
         * 2. Default action check 
         * 
         * @var RouterHeadersInterface|null $header
         * */
        $header = $headers[$path] ?? null;
        if ($header !== null) {
            return $header;
        }

        /** 3. Dynamic action check */
        if ($header === null && $actionType === ActionInterface::TYPE_DYNAMIC_ACTION) {
            $elements = explode(ControllerInterface::PATH_SEPARATOR, $path);
            array_pop($elements);
            array_push($elements, ControllerInterface::PATH_DYNAMIC_ALL);
            $dynamicPath = implode(ControllerInterface::PATH_SEPARATOR, $elements);
            /** 
             * Example: en<>*
             * where en - root, <> - separator, * - dynamic marker
             * 
             * @var RouterHeadersInterface|null $header
             */
            $header = $headers[$dynamicPath] ?? null;
        }

        return $header;
    }
}
