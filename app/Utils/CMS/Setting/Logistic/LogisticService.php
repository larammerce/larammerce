<?php


namespace App\Utils\CMS\Setting\Logistic;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Setting\BaseCMSConfigManager;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;
use function config;

/**
 * @method static LogisticModel getRecord(string $name = "", ?string $parent_id = null)
 */
class LogisticService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = 'logistic_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): LogisticModel
    {
        return new LogisticModel();
    }

    public static function selectDeliveryTableCell($cell_id, $invoice): bool
    {
        try {
            $data = static::explodeCellId($cell_id);
            return static::selectDeliveryTableCellExploded($data->date, $data->start, $data->finish, $invoice);
        } catch (Exception $e) {
            return false;
        }
    }

    private static function selectDeliveryTableCellExploded(Carbon $date, $start_time, $finish_time, $invoice = null): bool
    {
        $record = self::getRecord();
        $formatted_date = $date->format("Y-m-d");
        $cells = $record->getDeliveryTableCells();
        $hours = $record->getDeliveryHours();
        $days = $record->getDeliveryDays();
        $col = null;
        $row = null;

        for ($i = 0; $i < count($hours); $i++) {
            if ($hours[$i]['start_hour'] === $start_time) {
                $col = $i;
                break;
            }
        }

        for ($i = 0; $i < count($days); $i++) {
            if ($days[$i]['date'] === $formatted_date) {
                $row = $i;
                break;
            }
        }

        $cell = $cells[$row][$col];
        //return false;
        if ($row === 0 && Carbon::now()->greaterThan(Carbon::parse($formatted_date . ' ' . $finish_time))) {
            //so that the user can not cheat with fake time input
            return false;
        } elseif ($row === null || $col === null || !$cell['is_enabled']) {
            return false;
        } elseif ($invoice === null) {
            return true;
        } else {
            $invoice->delivery_date = $date;
            $invoice->delivery_start_time = $start_time;
            $invoice->delivery_finish_time = $finish_time;
            $max_items_count = $record->getMaxItemsCount();
            $max_total_price = $record->getMaxTotalPrice();
            $rows_offset = $record->getRowsOffset();
            $rows_available = $record->getRowsAvailable();

            self::update($cells, $hours, $max_items_count, $max_total_price, $rows_offset, $rows_available);
            return true;
        }
    }

    public static function update($cells = null, $hours = null, $max_items_count = null, $max_total_price = null,
                                  $rows_offset = null, $rows_available = null): void
    {
        try {
            $old_record = self::getRecord();
            $record = self::defaultRecord(self::$KEY_POSTFIX);
            if ($cells == null && count($old_record->getDeliveryTableCells())) {
                $hours = $old_record->getDeliveryHours();
                $cells = $old_record->getDeliveryTableCells();
                $rows_available = $old_record->getRowsAvailable();
                $rows_offset = $old_record->getRowsOffset();
                $max_items_count = $old_record->getMaxItemsCount();
                $max_total_price = $old_record->getMaxTotalPrice();
            }

            if($hours === null)
                return;

            $columns_count = count($hours);
            $old_rows_offset = $old_record->getRowsOffset();
            $old_first_delivery_date = $old_record->getDeliveryDays() ? $old_record->getDeliveryDays()[0]['date'] : Carbon::today()->toDateString();

            $record->setMaxItemsCount($max_items_count);
            $record->setMaxTotalPrice($max_total_price);
            $record->setRowsOffset($rows_offset);
            $record->setRowsAvailable($rows_available);

            for ($i = 0; $i < $columns_count; $i++) {
                $record->putDeliveryHour($hours[$i]["start_hour"], $hours[$i]["finish_hour"], $i);
            }


            $rows_diff = $rows_available - count($cells);
            if ($rows_diff > 0) {
                $pushed_cells = $record->pushDeliveryTableRows($rows_diff);
                $cells = array_merge($cells, $pushed_cells);
            } elseif ($rows_diff < 0) {
                $record->popDeliveryTableRows($rows_diff);
            }


            $old_first_date = Carbon::parse($old_first_delivery_date);
            $new_first_date = Carbon::parse(Carbon::today()->toDateString());
            if ($old_first_date->lessThan($new_first_date)) {
                $diff = $old_first_date->diff($new_first_date)->days;
                $remaining_rows = $rows_available - $diff;
                if ($remaining_rows > 0) {
                    for ($r = 0; $r < $rows_available; $r++) {
                        if ($r < $remaining_rows) {
                            for ($c = 0; $c < $columns_count; $c++) {
                                $cells[$r][$c] = ['row' => $r, 'column' => $c, 'is_enabled' => $cells[$diff + $r][$c]['is_enabled'],
                                    'is_enabled_by_admin' => $cells[$diff + $r][$c]['is_enabled_by_admin'],
                                    'items_count' => $cells[$diff + $r][$c]['items_count'],
                                    'total_price' => $cells[$diff + $r][$c]['total_price']];
                            }
                        } else {
                            for ($c = 0; $c < $columns_count; $c++) {
                                $cells[$r][$c] = ['row' => $r, 'column' => $c, 'is_enabled' => 1,
                                    'is_enabled_by_admin' => 0,
                                    'items_count' => 0,
                                    'total_price' => 0];
                            }
                        }
                    }
                } else {
                    for ($r = 0; $r < $rows_available; $r++) {
                        for ($c = 0; $c < $columns_count; $c++) {
                            $cells[$r][$c] = ['row' => $r, 'column' => $c, 'is_enabled' => 1,
                                'is_enabled_by_admin' => 0,
                                'items_count' => 0,
                                'total_price' => 0];
                        }
                    }
                }
            }

            for ($row = 0; $row < $rows_available; $row++) {
                $record->fillDeliveryTableRowHead($row);
                $row_date_string = $record->getDeliveryDays()[$row]->date;;
                for ($col = 0; $col < $columns_count; $col++) {
                    $start_hour = $hours[$col]['start_hour'];
                    $finish_hour = $hours[$col]['finish_hour'];

                    $items_count_query = DB::table('invoice_rows')
                        ->select(DB::raw('SUM(count) as count_sum'))
                        ->leftJoin('invoices', 'invoice_rows.invoice_id', '=', 'invoices.id')
                        ->where('delivery_date', $row_date_string)
                        ->whereBetween('delivery_finish_time', array($start_hour, $finish_hour))
                        ->get();

                    $total_price_query = DB::table('invoices')
                        ->select(DB::raw('SUM(sum) as total_sum'))
                        ->where('delivery_date', $row_date_string)
                        ->whereBetween('delivery_finish_time', array($start_hour, $finish_hour))
                        ->get();

                    $items_count = $items_count_query[0]->count_sum ?: 0;
                    $total_price = $total_price_query[0]->total_sum ?: 0;

                    if ($cells[$row][$col]['is_enabled_by_admin']) {
                        $record->putDeliveryTableCell($row, $col, 1, 1, $items_count, $total_price);
                    } elseif (($row < $rows_offset) || ($max_items_count > 0 && $max_items_count < $items_count) || ($max_total_price > 0 && $max_total_price < $total_price)) {
                        $record->putDeliveryTableCell($row, $col, 0, 0, $items_count, $total_price);
                    } elseif (($row < $old_rows_offset && $row >= $rows_offset) && (($max_items_count == 0 || $max_items_count > $items_count) || ($max_total_price == 0 || $max_total_price > $total_price))) {
                        $record->putDeliveryTableCell($row, $col, 1, 0, $items_count, $total_price);
                    } else {
                        $record->putDeliveryTableCell($row, $col, $cells[$row][$col]['is_enabled'], 0, $items_count, $total_price);
                    }
                }
            }
            self::setRecord($record);

        } catch (Exception $e) {
            Log::error("LogisticService.update.failed." . $e->getMessage());
        }
    }


    public static function getPublicTableCells(): array
    {
        $record = self::getRecord();
        return $record->getPublicDeliveryTableCells();
    }

    public function validateDeliveryPeriod($attribute, $value, $parameters, $validator): bool
    {
        try {
            $data = static::explodeCellId($value);
            return static::selectDeliveryTableCellExploded($data->date, $data->start, $data->finish);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public static function explodeCellId($cell_id): stdClass
    {
        $parts = explode("-", $cell_id);
        if (count($parts) !== 3)
            throw new Exception("Wrong cell ID passed.");
        $data = new stdClass();
        $data->date = Carbon::now()->addDays(intval($parts[0]));
        $data->start = $parts[1];
        $data->finish = $parts[2];
        return $data;
    }

    public static function isEnabled(): bool
    {
        return config("cms.logistics.enabled");
    }

}
