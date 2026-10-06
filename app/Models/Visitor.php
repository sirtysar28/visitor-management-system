<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_CHECKED_OUT = 'checked_out';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_CHECKED_IN => 'Checked In',
        self::STATUS_CHECKED_OUT => 'Checked Out',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    public const VISIT_TYPES = [
        'pre_registered' => 'Pre-Registered',
        'walk_in' => 'Walk-In',
        'vendor' => 'Vendor / Contractor',
        'delivery' => 'Delivery / Kurir',
    ];

    protected $fillable = [
        'code', 'full_name', 'phone', 'email', 'company', 'position',
        'id_type', 'id_number', 'photo', 'person_count', 'visit_type',
        'purpose', 'visit_date', 'visit_time', 'duration_minutes',
        'vehicle_number', 'notes', 'host_id', 'department_id',
        'meeting_room', 'access_area', 'status', 'reject_reason',
        'approved_at', 'approved_by', 'checked_in_at', 'checked_in_by',
        'checked_out_at', 'checked_out_by', 'badge_code',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'visit_time' => 'datetime:H:i',
            'approved_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
        ];
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function visitTypeLabel(): string
    {
        return self::VISIT_TYPES[$this->visit_type] ?? $this->visit_type;
    }

    public function isInside(): bool
    {
        return $this->status === self::STATUS_CHECKED_IN;
    }

    public static function generateCode(): string
    {
        $prefix = 'VIS-' . now()->format('Ymd') . '-';
        $latest = self::where('code', 'like', $prefix . '%')
            ->orderByDesc('id')->first();

        $seq = $latest ? ((int) substr($latest->code, -5)) + 1 : 1;

        return $prefix . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }
}
