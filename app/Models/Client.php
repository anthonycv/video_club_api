<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    public $table = 'clients';

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'email',
        'name',
        'tlf',
        'address',
        'loyalty_points',
        'enabled'
    ];

    /**
     * @param $email
     * @return mixed
     */
    public function getLoyaltyPointsClientByEmail($email)
    {
        return $this->select('loyalty_points')->where('email', $email)->where('enabled', true)->get();
    }

    /**
     * @param $email
     * @return null
     */
    public function getClientIdByEmail($email)
    {
        return $this->select('id')->where('email', $email)->where('enabled', true)->first()->id ?? null;
    }

    /**
     * @param $clientId
     * @param $loyaltyPoints
     * @return mixed
     */
    public function addLoyaltyPoints($clientId, $loyaltyPoints)
    {
        $client = $this->where('id', $clientId)->first();
        return $client->update(['loyalty_points' => $client->loyalty_points + $loyaltyPoints]);
    }
}
