<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Views\JsonView;
use Romchik38\Server\Models\DTO\DefaultView\DefaultViewDTO;
use Romchik38\Server\Views\Errors\CantCreateViewException;

class JsonViewTest extends TestCase
{
    /** 
     * success
     */
    public function testToString()
    {
        $name = 'some name';
        $description = 'some description';

        $dto = new DefaultViewDTO($name, $description);
        $jsonView = new JsonView();
        $jsonView->setControllerData($dto);
        $result = $jsonView->toString();

        $this->assertSame(json_encode($dto->getAllData()), $result);
    }

    /**
     * to early access
     */
    public function testToStringThrowsToEarly()
    {
        $this->expectException(CantCreateViewException::class);
        $this->expectExceptionMessage(JsonView::class . ': Controller data was not set');

        $jsonView = new JsonView();
        $jsonView->toString();
    }

    /**
     * json_encode return false
     */
    public function testToStringThrowsErrorEncoding()
    {
        $dto = new class('', '') extends DefaultViewDTO {
            public function __construct(string $name, string $description)
            {
                $this->data['error_key'] = NAN;
            }
        };

        $this->expectException(CantCreateViewException::class);
        $this->expectExceptionMessage(JsonView::class . ': error while encoding data to json');

        $jsonView = new JsonView();
        $jsonView->setControllerData($dto);
        $result = $jsonView->toString();
    }
}
