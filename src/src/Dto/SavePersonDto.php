<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class LocationItemDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Location name is required.', normalizer: 'trim')]
        public string $name,

        #[Assert\NotBlank(message: 'Location type is required.')]
        public int|string $typeId,
    ) {
    }
}

readonly class SavePersonDto
{
    public function __construct(
        public int|string|null $id = null,

        #[Assert\NotBlank(message: 'First name is required.', normalizer: 'trim')]
        #[Assert\Length(max: 255)]
        public string $firstName,

        #[Assert\NotBlank(message: 'Surname is required.', normalizer: 'trim')]
        #[Assert\Length(max: 255)]
        public string $surName,

        /** @var LocationItemDto[] */
        #[Assert\Valid]
        public array $locations = [],
    ) {
    }
}
