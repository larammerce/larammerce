@if(isset(request()->related_model) || isset($related_model))
    @php
        $class = new \App\Utils\Reflection\ReflectiveClass(request()->related_model??$related_model);
        $entity_name = $entity_name ?? get_model_entity_name($class->getClassName());
        $lang_id = $lang_id ?? config("translation.fallback_locale");
    @endphp
    @if(\App\Utils\Translation\TranslationService::isTranslatable($class) and \App\Utils\CMS\Setting\Language\LanguageSettingService::isMultiLangSystem() and isset($$entity_name))
        <div class="language-container btn-group btn-group-sm">
            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                @lang('language.id.'.$lang_id) <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                @foreach(config("translation.locales") as $iter_lang_id)
                    @if($iter_lang_id !== $lang_id)
                        <li>
                            <a class="virt-form" data-method="POST"
                               data-action="{{route("admin.model-translation.edit")}}"
                               data-fields='{"related_model": "{{str_replace("\\", "\\\\", $class->getClassName())}}", "related_object_id": "{{$$entity_name?->id}}", "lang_id": "{{$iter_lang_id}}"}'>
                                @lang('language.id.'.$iter_lang_id)
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif
@endif
