<?php

namespace App\Models;

use App\Utils\CMS\Template\WebForm\FormField;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer id
 * @property string fields
 * @property string identifier
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property mixed fields_obj
 * @property WebFormMessage[] messages
 *
 * Class WebForm
 * @package App\Models
 */
class WebForm extends BaseModel
{
    protected $table = 'web_forms';

    protected $fillable = [
        'fields', 'identifier'
    ];

    /**
     * @var FormField[]
     */
    private $formFields;
    /*
     * Relational Methods
     */

    /**
     * @return HasMany
     */
    public function messages()
    {
        return $this->hasMany('\\App\\Models\\WebFormMessage', 'web_form_id');
    }

    /**
     * @return FormField[]
     */
    public function getGalleryFields()
    {
        if ($this->loadFormFields())
            return $this->formFields;
        return [];
    }

    private function loadFormFields()
    {
        $result = true;
        if (count(is_countable($this->formFields) ? $this->formFields : []) == 0) {
            if ($this->fields != null and strlen($this->fields) > 0) {
                try {
                    $this->formFields = unserialize($this->fields);
                } catch (Exception $e) {
                    $this->formFields = [];
                    $result = false;
                }
            } else {
                $this->formFields = [];
                $result = false;
            }
        }
        return $result;
    }

    public static function getRules($identifier)
    {
        $rules = [];
        $webForm = WebForm::where("identifier", $identifier)->first();
        if ($webForm == null)
            return $rules;
        $formFields = unserialize($webForm->fields);
        foreach ($formFields as $formField)
            $rules[$formField->getIdentifier()] = $formField->getValidationRules();
        return $rules;
    }

    /**
     * @param FormField[] $formFields
     */
    public function setFormFields($formFields)
    {
        $this->formFields = $formFields;
        $this->fields = serialize($this->formFields);
    }


    /*
     * Accessor Methods
     */
    public function getFieldsObjAttribute()
    {
        return json_decode($this->fields);
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
