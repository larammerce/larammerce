<?php

namespace App\Models;

use App\Interfaces\ImageOwnerInterface;
use App\Interfaces\SeoSubjectInterface as SeoableContract;
use App\Traits\Seoable;
use App\Utils\CMS\Template\Contents\Audio;
use App\Utils\CMS\Template\Contents\File;
use App\Utils\CMS\Template\Contents\Image;
use App\Utils\CMS\Template\Contents\Link;
use App\Utils\CMS\Template\Contents\RichText;
use App\Utils\CMS\Template\Contents\Text;
use App\Utils\CMS\Template\Contents\Video;
use App\Utils\CMS\Template\ContentTypes;
use App\Utils\CMS\Template\Directives;
use App\Utils\CMS\Template\RelativeBladeType;
use App\Utils\CMS\Template\TemplateService;
use App\Utils\Common\ImageService;
use App\Utils\Common\RequestService;
use App\Utils\Translation\Traits\Translatable;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Yangqi\Htmldom\Htmldom;

/**
 * @property integer id
 * @property integer directory_id
 * @property string blade_name
 * @property string data
 * @property string image_path
 * @property string seo_description
 * @property string seo_keywords
 * @property string seo_title
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property array contents
 *
 * @property Directory directory
 * @property Tag[] tags
 *
 * Class WebPage
 * @package App\Models
 */
class WebPage extends BaseModel implements ImageOwnerInterface, SeoableContract
{
    use Seoable, Translatable;

    protected $table = 'web_pages';
    protected $fillable = [
        'directory_id', 'blade_name', 'data', 'seo_title', 'seo_keywords', 'seo_description',
    ];
    protected array $cached_attributes;

    protected static array $SORTABLE_FIELDS = ['id', 'blade_name', 'created_at'];
    protected static array $SEARCHABLE_FIELDS = ['blade_name', 'data'];
    protected static array $TRANSLATABLE_FIELDS = [
        "seo_title" => ["text", "textarea:normal"],
        "seo_keywords" => ["text", "textarea:normal"],
        "seo_description" => ["text", "textarea:normal"],
        "data" => ["text", "custom"]
    ];
    protected static string $TRANSLATION_EDIT_FORM = "admin.pages.web-page.translate";

    public function __construct(array $attributes = [])
    {
        $this->cached_attributes["contents"] = [];
        parent::__construct($attributes);
    }

    private function loadContents()
    {
        if (count(is_countable($this->cached_attributes["contents"]) ? $this->cached_attributes["contents"] : []) == 0) {
            if ($this->data != null and strlen($this->data) > 0) {
                try {
                    $this->cached_attributes["contents"] = unserialize($this->data);
                } catch (Exception $e) {
                    $this->cached_attributes["contents"] = [];
                }
            } else {
                $this->cached_attributes["contents"] = [];
            }
        }
    }

    public function getContentsAttribute(): array
    {
        $this->loadContents();
        return $this->cached_attributes["contents"];
    }

    public function putContent($key, $value)
    {
        $this->loadContents();
        $this->cached_attributes["contents"][$key] = $value;
    }

    public function setContentsAttribute($contents): void
    {
        $this->cached_attributes["contents"] = $contents;
    }

    public function getBladeNameAttribute()
    {
        if (!isset($this->attributes["blade_name"]))
            return null;
        $locale = $this->getDefaultLocale();
        $blade_name = $this->attributes["blade_name"];
        $blade_path = TemplateService::getBladePath($blade_name);
        if ($locale !== null) {
            $tmp_blade_name = $blade_name . "___locale_{$locale}";
            $tmp_blade_path = TemplateService::getBladePath($tmp_blade_name);
            if (!file_exists($tmp_blade_path))
                TemplateService::copyBlade($blade_path, $tmp_blade_path);
            return $tmp_blade_name;
        }
        return $blade_name;
    }

    public function setDataAttribute($data): void
    {
        $this->attributes["data"] = $this->fillData($data);
    }

    public function directory(): BelongsTo
    {
        return $this->belongsTo(Directory::class, 'directory_id');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function delete()
    {
        $this->tags()->detach();
        $this->review()->delete();

        return parent::delete();
    }

    public function getTitle()
    {
        return $this->directory->title;
    }

    public function getSeoTitle()
    {
        if ($this->seo_title !== null and strlen($this->seo_title) > 0)
            return $this->seo_title;
        return $this->directory->title;
    }

    public function hasImage()
    {
        return isset($this->image_path);
    }

    public function getImagePath()
    {
        return $this->image_path;
    }

    public function setImagePath()
    {
        $tmpImage = ImageService::saveImage($this->getImageCategoryName());
        $this->image_path = $tmpImage->destinationPath . '/' . $tmpImage->name;
        $this->save();
    }

    public function removeImage()
    {
        $this->image_path = null;
        $this->save();
    }

    public function getDefaultImagePath()
    {
        return '/admin_dashboard/images/No_image.jpg.png';
    }

    public function getImageCategoryName()
    {
        return 'web_page';
    }

    public function save(array $options = []): bool
    {
        $this->saveBladeContents();
        return parent::save($options);
    }

    public function fillData(array $data): string
    {
        if (isset($this->id) and $this->id != null) {
            $this->loadBladeContents();
            foreach ($data as $content_id => $values) {
                if (key_exists($content_id, $this->contents)) {
                    foreach ($values as $attr_name => $attr_value) {
                        $attr_value_type = RequestService::getType($attr_value);
                        if ($attr_value_type === RequestService::FILE_ATTRIBUTE) {
                            $tmp_image = ImageService::saveImage("not_categorized", file: $attr_value);
                            $this->contents[$content_id]->{Str::camel("set_" . $attr_name)}(
                                $tmp_image->destinationPath . '/' . $tmp_image->name);
                        } else if ($attr_value_type === RequestService::TEXT_ATTRIBUTE) {
                            $this->contents[$content_id]->{Str::camel("set_" . $attr_name)}($attr_value);
                        }
                    }
                }
            }
            return serialize($this->contents);
        }
        return serialize([]);
    }

    private function loadBladeContents()
    {
        $tmp_contents = $this->contents;
        $this->contents = [];
        if ($this->blade_name != null and strlen($this->blade_name) > 0) {
            $blade_path = TemplateService::getBladePath($this->blade_name);
            $bladeContent = TemplateService::getBladeContent($blade_path);
            $html = new Htmldom($bladeContent);
            $content_tags = $html->find("[" . Directives::CONTENT . "]");

            foreach (RelativeBladeType::values() as $relative_blade_postfix) {
                $relativeBladePath = TemplateService::getBladePath($this->blade_name . $relative_blade_postfix);
                $relativeBladeContent = TemplateService::getBladeContent($relativeBladePath);
                if ($relativeBladeContent != "") {
                    $relativeHtml = new Htmldom($relativeBladeContent);
                    $relativeContentTags = $relativeHtml->find("[" . Directives::CONTENT . "]");
                    $content_tags = array_merge($content_tags, $relativeContentTags);
                }
            }
            $this->loadContentTags($content_tags, $tmp_contents);
        }
    }

    private function loadContentTags($content_tags, $first_contents)
    {
        foreach ($content_tags as $content_tag) {
            $contentType = $content_tag->attr[Directives::CONTENT_TYPE];
            $content_id = $content_tag->attr[Directives::CONTENT];
            $content_title = $content_tag->attr[Directives::TITLE];
            //TODO: fix the issue with old content id's with changed content structure.
            if ($contentType != null and strlen($contentType) != 0
                and $content_id != null and strlen($content_id) != 0
                and $content_title != null and strlen($content_title) != 0) {
                if (!key_exists($content_id, $first_contents)) {
                    if ($contentType === ContentTypes::TEXT) {
                        $content = trim($content_tag->innertext);
                        if ((isset($content_tag->attr[Directives::UNSHARED])
                            and $content_tag->attr[Directives::UNSHARED] == "true"))
                            $content = get_unshared_content($content_id, $this);
                        $new_text = new Text($content_id, $content_title, $content);
                        $this->putContent($content_id, $new_text);

                    } else if ($contentType === ContentTypes::RICH_TEXT) {
                        $content = trim($content_tag->innertext);
                        if ((isset($content_tag->attr[Directives::UNSHARED])
                            and $content_tag->attr[Directives::UNSHARED] == "true"))
                            $content = get_unshared_content($content_id, $this);
                        $new_rich_text = new RichText($content_id, $content_title, $content);
                        $this->putContent($content_id, $new_rich_text);

                    } else if ($contentType === ContentTypes::LINK) {

                        $new_link = new Link($content_id, $content_title,
                            trim($content_tag->href), trim($content_tag->innertext));
                        $this->putContent($content_id, $new_link);

                    } else if ($contentType === ContentTypes::FILE) {

                        $new_file = new File($content_id, $content_title, $content_tag->innertext, $content_tag->href);
                        $this->putContent($content_id, $new_file);

                    } else if ($contentType === ContentTypes::IMAGE) {

                        $new_image = new Image($content_id, $content_title, $content_tag->alt, $content_tag->src);
                        $this->putContent($content_id, $new_image);

                    } else if ($contentType === ContentTypes::AUDIO) {
                        $new_audio = new Audio($content_id, $content_title, $content_tag->src, $content_tag->format);
                        $this->putContent($content_id, $new_audio);

                    } else if ($contentType === ContentTypes::VIDEO) {
                        $new_video = new Video($content_id, $content_title, $content_tag->src, $content_tag->format,
                            $content_tag->poster, $content_tag->controls, $content_tag->auto_play, $content_tag->loop);
                        $this->putContent($content_id, $new_video);
                    }
                } else {
                    $this->putContent($content_id, $first_contents[$content_id]);
                }
            }
        }
    }

    private function setContentTags(array $contents, array $content_tags)
    {
        foreach ($content_tags as $content_tag) {
            $content_type = $content_tag->attr[Directives::CONTENT_TYPE];
            $content_id = $content_tag->attr[Directives::CONTENT];
            $content_title = $content_tag->attr[Directives::TITLE];
            if ($content_type != null and strlen($content_type) != 0
                and $content_id != null and strlen($content_id) != 0
                and $content_title != null and strlen($content_title) != 0
                and key_exists($content_id, $contents)
                and !(isset($content_tag->attr[Directives::UNSHARED])
                    and $content_tag->attr[Directives::UNSHARED] == "true")) {
                if ($content_type === ContentTypes::TEXT) {

                    $content_tag->innertext = $contents[$content_id]->getContent();

                } else if ($content_type === ContentTypes::RICH_TEXT) {

                    $content_tag->innertext = $contents[$content_id]->getContent();

                } else if ($content_type === ContentTypes::LINK) {

                    $content_tag->href = $contents[$content_id]->getHref();
                    $content_tag->innertext = $contents[$content_id]->getContent();

                } else if ($content_type === ContentTypes::FILE) {

                    $content_tag->innertext = $contents[$content_id]->getName();
                    $content_tag->href = $contents[$content_id]->getHref();

                } else if ($content_type === ContentTypes::IMAGE) {

                    $content_tag->alt = $contents[$content_id]->getAlt();
                    $content_tag->src = $contents[$content_id]->getSrc();

                } else if ($content_type === ContentTypes::AUDIO) {

                    $content_tag->src = $contents[$content_id]->getSrc();
                    $content_tag->format = $contents[$content_id]->getFormat();

                } else if ($content_type === ContentTypes::VIDEO) {
                    $content_tag->src = $contents[$content_id]->getSrc();
                    $content_tag->format = $contents[$content_id]->getFormat();
                    $content_tag->poster = $contents[$content_id]->getPoster();
                    $content_tag->controls = $contents[$content_id]->hasControls();
                    $content_tag->auto_play = $contents[$content_id]->isAutoPlay();
                    $content_tag->loop = $contents[$content_id]->hasLoop();
                }
            }
        }
    }

    private function saveBladeContents()
    {
        if ($this->blade_name != null and strlen($this->blade_name) > 0) {
            $this->updateBladeContent($this->contents, TemplateService::getBladePath($this->blade_name));
            foreach (RelativeBladeType::values() as $relative_blade_postfix) {
                $relative_blade_path = TemplateService::getBladePath($this->blade_name . $relative_blade_postfix);
                $this->updateBladeContent($this->contents, $relative_blade_path, $relative_blade_postfix);
            }
        }
    }

    private function updateBladeContent(array $contents, $blade_path, $relative_blade_postfix = null)
    {
        $blade_content = TemplateService::getBladeContent($blade_path);
        $html = null;
        $content_tags = null;
        if ($blade_content != "" and strlen($blade_content) > 0) {
            $html = new Htmldom($blade_content);
            $content_tags = $html->find("[" . Directives::CONTENT . "]");
        }
        if ($content_tags != null)
            $this->setContentTags($contents, $content_tags);
        if ($html != null) {
            $blade_name = $this->blade_name;
            if ($relative_blade_postfix != null)
                $blade_name .= $relative_blade_postfix;
            TemplateService::setBladeContent(TemplateService::getViewPath($blade_name), $html);
        }
    }

    public function getContent($contentName)
    {
        return $this->contents[$contentName];
    }

    public function getSearchUrl(): string
    {
        return '';
    }

    public function getFrontUrl(): string
    {
        return $this->directory->getFrontUrl();
    }

    public function getSeoDescription(): ?string
    {
        return $this->seo_description;
    }

    public function getSeoKeywords(): ?string
    {
        return $this->seo_keywords;
    }


    public function isImageLocal(): bool
    {
        return true;
    }
}
