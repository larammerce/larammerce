<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/26/18
 * Time: 1:41 PM
 */

namespace App\Models;

use App\Utils\PaymentManager\Exceptions\PaymentCallbackInvalidParametersException;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException;
use App\Utils\PaymentManager\Factory;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property integer amount
 * @property string driver
 * @property string payment_data
 * @property integer invoice_id
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property integer status
 *
 * @property Invoice invoice
 *
 * @method static Payment find($id)
 * @method static Payment findOrFail($id)
 *
 * Class Payment
 * @package App\Models
 */
class Payment extends BaseModel {
    protected $table = 'payments';

    protected $fillable = [
        'invoice_id', 'amount', 'driver', 'payment_data', 'status'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'amount', 'created_at'];

    public function invoice(): BelongsTo {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function getTrackingCode() {
        try {
            return Factory::driver($this->driver)->getTrackingCode($this->payment_data);
        } catch (PaymentCallbackInvalidParametersException|PaymentInvalidDriverException $e) {
            return '-';
        }
    }

    public function getStatus(): int {
        return Factory::driver($this->driver)->getStatus($this->amount, $this->id, $this->payment_data);
    }

    public function setPaymentDataAttribute(?string $value): void {
        $this->attributes['payment_data'] = $value;
        $this->attributes['status'] = $this->getStatus();
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string {
        return '';
    }
}
