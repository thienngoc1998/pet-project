<?php
declare(strict_types=1);

namespace App\Services\ToastrBuilder;

class ToastrBuilder
{
    private string $type;
    private string $message;

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function build(): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message
        ];
    }
}
