<?php

namespace App\AssistantScheduling;

class School implements \JsonSerializable
{
    private int $id;
    private string $name;

    /**
     * Array: [Group, Day, Capacity].
     */
    private array $capacity;

    /**
     * School constructor.
     */
    public function __construct(array $capacity, string $name, int $id)
    {
        $this->capacity = $capacity;
        $this->name = $name;
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function capacityLeftOnDay(int $group, string $day): int
    {
        return $this->capacity[$group][$day];
    }

    public function addAssistant(int $group, string $day)
    {
        --$this->capacity[$group][$day];
    }

    public function getCapacity(): array
    {
        return $this->capacity;
    }

    public function setCapacity(array $capacity)
    {
        $this->capacity = $capacity;
    }

    public function isFull(): bool
    {
        foreach ($this->capacity as $weekDayCapacity) {
            foreach ($weekDayCapacity as $day => $capacityLeft) {
                if ($capacityLeft > 0) {
                    return false;
                }
            }
        }

        return true;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'capacity' => $this->capacity,
        ];
    }
}
