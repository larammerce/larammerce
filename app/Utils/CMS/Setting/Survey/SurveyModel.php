<?php


namespace App\Utils\CMS\Setting\Survey;


use App\Interfaces\SettingDataInterface;
use App\Models\State;
use JetBrains\PhpStorm\ArrayShape;
use stdClass;

class SurveyModel implements SettingDataInterface
{
    private int $default_delay_hours;
    private int $default_delay_days;
    private string $default_survey_url;
    private array $custom_states;

    public function __construct()
    {
        $this->default_delay_hours = 0;
        $this->default_delay_days = 0;
        $this->default_survey_url = "";
        $this->custom_states = [];
    }

    public function getDefaultDelayHours(): int
    {
        return $this->default_delay_hours;
    }

    public function setDefaultDelayHours(int $default_delay_hours): void
    {
        $this->default_delay_hours = $default_delay_hours;
    }

    public function getDefaultDelayDays(): int
    {
        return $this->default_delay_days;
    }

    public function setDefaultDelayDays(int $default_delay_days): void
    {
        $this->default_delay_days = $default_delay_days;
    }

    public function getDefaultSurveyUrl(): string
    {
        return $this->default_survey_url;
    }

    public function setDefaultSurveyUrl(string $default_survey_url): void
    {
        $this->default_survey_url = $default_survey_url;
    }

    public function getCustomStates(): array
    {
        return $this->custom_states;
    }

    public function hasCustomState(string $state_id): bool
    {
        return isset($this->custom_states[$state_id]);
    }

    public function getCustomState(int $state_id): stdClass
    {
        return $this->custom_states[$state_id];
    }

    public function putCustomState(int $state_id, int $custom_delay_days, int $custom_delay_hours, string $custom_survey_url): array
    {
        $new_state = new stdClass();
        $new_state->custom_delay_days = $custom_delay_days;
        $new_state->custom_delay_hours = $custom_delay_hours;
        $new_state->custom_survey_url = $custom_survey_url;
        $new_state->state = State::find($state_id);

        $this->custom_states[$state_id] = $new_state;
        return $this->custom_states;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize(string $data): void
    {
        $tmp_data = json_decode($data, true);
        $this->default_delay_days = $tmp_data["default_delay_days"];
        $this->default_delay_hours = $tmp_data["default_delay_hours"];
        $this->default_survey_url = $tmp_data["default_survey_url"];
        $this->custom_states = $tmp_data["custom_states"];

    }

    public function validate(): bool
    {
        foreach ($this->custom_states as $state_id => $custom_state) {
            if (!$this->isValidStateId($state_id) or
                !$this->isValidDays($custom_state->custom_delay_days) or
                !$this->isValidHours($custom_state->custom_delay_hours))
                return false;
        }

        return $this->isValidHours($this->default_delay_hours) and
            $this->isValidDays($this->default_delay_days);
    }

    private function isValidHours($hours): bool
    {
        return is_integer($hours) and $hours >= 0 and $hours <= 24;
    }

    private function isValidDays($days): bool
    {
        return is_integer($days);
    }

    private function isValidStateId($state_id): bool
    {
        return State::where("id", $state_id)->count() > 0;
    }

    public function getPrimaryKey(): string
    {
        return "";
    }

    #[ArrayShape(["default_delay_hours" => "int", "default_delay_days" => "int", "default_survey_url" => "string", "custom_states" => "array"])]
    public function jsonSerialize(): array
    {
        return [
            "default_delay_hours" => $this->default_delay_hours,
            "default_delay_days" => $this->default_delay_days,
            "default_survey_url" => $this->default_survey_url,
            "custom_states" => $this->custom_states
        ];
    }
}
