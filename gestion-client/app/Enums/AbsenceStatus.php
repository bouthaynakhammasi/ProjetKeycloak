<?php

namespace App\Enums;

enum AbsenceStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::APPROVED => 'Approuvée',
            self::REJECTED => 'Refusée',
        };
    }

    public function getBadgeClass(): string
    {
        return match($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-700',
            self::APPROVED => 'bg-green-100 text-green-700',
            self::REJECTED => 'bg-red-100 text-red-700',
        };
    }
}
