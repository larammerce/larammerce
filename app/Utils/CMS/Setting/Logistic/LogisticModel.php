<?php


namespace App\Utils\CMS\Setting\Logistic;


use App\Interfaces\SettingDataInterface;
use App\Utils\Jalali\JDate;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use JetBrains\PhpStorm\ArrayShape;
use stdClass;

class LogisticModel implements SettingDataInterface
{
    private int $max_items_count;
    private int $max_total_price;
    private int $rows_offset;
    private int $rows_available;
    private array $delivery_hours;
    private array $delivery_days;
    private array $delivery_table_cells;

    public function __construct()
    {
        $this->max_items_count = 0;
        $this->max_total_price = 0;
        $this->rows_offset = 0;
        $this->rows_available = 1;
        $this->delivery_hours = [];
        $this->delivery_days = [];
        $this->delivery_table_cells = [];
    }

    public function getMaxItemsCount(): int
    {
        return $this->max_items_count;
    }

    public function getMaxTotalPrice(): int
    {
        return $this->max_total_price;
    }

    public function getRowsOffset(): int
    {
        return $this->rows_offset;
    }

    public function getRowsAvailable(): int
    {
        return $this->rows_available;
    }

    public function setMaxItemsCount(int $max_items_count): void
    {
        $this->max_items_count = $max_items_count;
    }

    public function setMaxTotalPrice(int $max_total_price): void
    {
        $this->max_total_price = $max_total_price;
    }

    public function setRowsOffset(int $rows_offset): void
    {
        $this->rows_offset = $rows_offset;
    }

    public function setRowsAvailable(int $rows_available): void
    {
        $this->rows_available = $rows_available;
    }

    public function getDeliveryHours(): array
    {
        return $this->delivery_hours;
    }

    public function getDeliveryDays(): array
    {
        return $this->delivery_days;
    }

    public function getDeliveryTableCells(): array
    {
        return $this->delivery_table_cells;
    }

    public function getPublicDeliveryTableCells(): array
    {
        $cells = $this->delivery_table_cells;
        $hours = $this->delivery_hours;
        $days = $this->delivery_days;
        $public_cells = [];
        $columns_count = count($this->delivery_hours);
        $rows_count = $this->rows_available;
        for ($row = 0; $row < $rows_count; $row++) {
            for ($col = 0; $col < $columns_count; $col++) {
                $public_cells[$row][$col] = Arr::except($cells[$row][$col], ['total_price', 'items_count', 'is_enabled_by_admin']);
                $public_cells[$row][$col] = array_merge($public_cells[$row][$col], $this->delivery_hours[$col]);
                $public_cells[$row][$col]["id"] = "{$row}-{$public_cells[$row][$col]['start_hour']}-{$public_cells[$row][$col]['finish_hour']}";
                if ($row === 0 && Carbon::now()->greaterThan(Carbon::parse($days[$row]['date'] . ' ' . $hours[$col]['finish_hour']))) {
                    $public_cells[$row][$col]['is_enabled'] = 0;
                }

            }
        }
        return $public_cells;
    }

    public function putDeliveryHour(string $start_hour, string $finish_hour, int $order): array
    {
        $new_delivery_hour = new stdClass();
        $new_delivery_hour->start_hour = $start_hour;
        $new_delivery_hour->finish_hour = $finish_hour;
        $new_delivery_hour->order = $order;
        $this->delivery_hours[$order] = $new_delivery_hour;
        return $this->delivery_hours;
    }

    public function putDeliveryDay(string $week_day, string $date, string $jalali_date, int $order): array
    {
        $new_delivery_day = new stdClass();
        $new_delivery_day->week_day = $week_day;
        $new_delivery_day->date = $date;
        $new_delivery_day->jalali_date = $jalali_date;
        $new_delivery_day->order = $order;
        $this->delivery_days[$order] = $new_delivery_day;
        return $this->delivery_days;
    }

    public function putDeliveryTableCell(int $row, int $column, int $is_enabled, int $is_enabled_by_admin, int $items_count, int $total_price): array
    {
        $new_cell = new stdClass();
        $new_cell->row = $row;
        $new_cell->column = $column;
        $new_cell->is_enabled = $is_enabled;
        $new_cell->is_enabled_by_admin = $is_enabled_by_admin;
        $new_cell->items_count = $items_count;
        $new_cell->total_price = $total_price;
        $this->delivery_table_cells[$row][$column] = $new_cell;
        return $this->delivery_table_cells;
    }

    public function popDeliveryTableRows($number_of_rows = 1): array
    {
        $number_of_rows = abs($number_of_rows);
        $delivery_days = $this->delivery_days;
        $delivery_table_cells = $this->delivery_table_cells;
        for ($i = 0; $i < $number_of_rows; $i++) {
            array_pop($delivery_days);
            array_pop($delivery_table_cells);
        }
        $this->delivery_table_cells = $delivery_table_cells;
        $this->delivery_days = $delivery_days;
        return $this->delivery_table_cells;
    }

    public function pushDeliveryTableRows($number_of_rows = 1): array
    {
        $number_of_rows = abs($number_of_rows);
        $delivery_days = $this->delivery_days;
        $rows_count = count($delivery_days);
        $pushed_cells_array = [];
        for ($i = 0; $i < $number_of_rows; $i++) {
            $this->fillDeliveryTableRowHead($rows_count + $i);
            $pushed_cells_array = $this->initDeliveryTableRowCells($rows_count + $i);
        }
        return array_slice($pushed_cells_array, -$number_of_rows);

    }

    public function fillDeliveryTableRowHead(int $row): void
    {
        $day = Carbon::today()->addDays($row);
        $week_day = $day->englishDayOfWeek;
        $date_string = $day->toDateString();
        $jalali_date_string = JDate::forge($date_string)->format('%A, %d %B');
        $this->putDeliveryDay($week_day, $date_string, $jalali_date_string, $row);
    }

    public function initDeliveryTableRowCells($row)
    {
        $columns_count = count($this->delivery_hours);
        $cells = [];
        for ($col = 0; $col < $columns_count; $col++) {
            $cells = $this->putDeliveryTableCell($row, $col, 1, 0, 0, 0);
        }
        return json_decode(json_encode($cells), true);
    }


    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize(string $data): void
    {
        $tmp_data = json_decode($data, true);
        $this->max_items_count = $tmp_data["max_items_count"];
        $this->max_total_price = $tmp_data["max_total_price"];
        $this->rows_offset = $tmp_data["rows_offset"];
        $this->rows_available = $tmp_data["rows_available"];
        $this->delivery_hours = $tmp_data["delivery_hours"];
        $this->delivery_days = $tmp_data["delivery_days"];
        $this->delivery_table_cells = $tmp_data["delivery_table_cells"];

    }

    public function validate(): bool
    {
        foreach ($this->delivery_hours as $delivery_hour) {
            if (!$this->isValidHour($delivery_hour->start_hour) or
                !$this->isValidHour($delivery_hour->finish_hour))
                return false;
        }

        return $this->isValidItemsCount($this->max_items_count) and
            $this->isValidTotalPrice($this->max_total_price) and
            $this->isValidRowsOffSet($this->rows_offset) and
            $this->isValidRowsAvailable($this->rows_available);
    }

    private function isValidItemsCount($items): bool
    {
        return is_integer($items) and $items >= 0;
    }

    private function isValidTotalPrice($price): bool
    {
        return is_integer($price) and $price >= 0;
    }

    private function isValidRowsOffSet($rows): bool
    {
        return is_integer($rows) and $rows >= 0 and $rows <= 100;
    }

    private function isValidRowsAvailable($rows): bool
    {
        return is_integer($rows) and $rows >= 1 and $rows <= 100;
    }

    private function isValidHour($hour): bool
    {
        return preg_match('#^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#', $hour);
    }

    public function getPrimaryKey(): string
    {
        return "";
    }

    #[ArrayShape(["max_items_count" => "int", "max_total_price" => "int", "rows_offset" => "int",
        "rows_available" => "int", "delivery_hours" => "array", "delivery_days" => "array", "delivery_table_cells" => "array"])]
    public function jsonSerialize(): array
    {
        return [
            "max_items_count" => $this->max_items_count,
            "max_total_price" => $this->max_total_price,
            "rows_offset" => $this->rows_offset,
            "rows_available" => $this->rows_available,
            "delivery_hours" => $this->delivery_hours,
            "delivery_days" => $this->delivery_days,
            "delivery_table_cells" => $this->delivery_table_cells,
        ];
    }

}
