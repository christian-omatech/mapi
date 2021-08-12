<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class InstanceDAO extends Model
{
    protected $table = 'mage_instances';

    protected $fillable = [
        'uuid',
    ];
}
