<?php

namespace App\Tests\Entity;

use App\Entity\Event;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class EventDateValidationTest extends TestCase
{
    public function testEndDateMustBeAfterStartDate(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $event = (new Event())
            ->setStartAt(new \DateTimeImmutable('2026-01-01 10:00:00'))
            ->setEndAt(new \DateTimeImmutable('2026-01-01 09:00:00'));

        $violations = $validator->validate($event);

        self::assertCount(1, $violations);
        self::assertSame('endAt', $violations[0]->getPropertyPath());
    }

    public function testValidDateRangeHasNoViolations(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $event = (new Event())
            ->setStartAt(new \DateTimeImmutable('2026-01-01 09:00:00'))
            ->setEndAt(new \DateTimeImmutable('2026-01-01 10:00:00'));

        self::assertCount(0, $validator->validate($event));
    }
}
