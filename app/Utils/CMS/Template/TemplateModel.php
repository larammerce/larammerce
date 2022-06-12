<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 9/28/17
 * Time: 10:40 AM
 */

namespace App\Utils\CMS\Template;


use App\Models\Gallery;
use App\Models\WebForm;
use App\Utils\CMS\Template\Gallery\GalleryField;
use App\Utils\CMS\Template\WebForm\File;
use App\Utils\CMS\Template\WebForm\Option;
use App\Utils\CMS\Template\WebForm\Select;
use App\Utils\CMS\Template\WebForm\Text;
use App\Utils\CMS\Template\WebForm\Textarea;
use App\Utils\CMS\Template\WebForm\UndefinedFormFieldIdentifierException;
use stdClass;
use Yangqi\Htmldom\Htmldom;

class TemplateModel
{
    private $bladeName;
    private $bladePath;
    private $originalPath;
    private $html;
    private $content;

    public function __construct($bladeName, $bladePath, $originalPath = null)
    {
        $originalPath = $originalPath == null ? $bladePath : $originalPath;
        $this->bladeName = $bladeName;
        $this->bladePath = $bladePath;
        $this->originalPath = $originalPath;
        $this->content = $this->getBladeContent();
    }

    public function setContent($content){
        $this->content = $content;
    }

    public function createHtml(){
        return $this->html->save();
    }

    private function getBladeContent()
    {
        if (file_exists($this->originalPath)) {
            $bladeFile = fopen($this->originalPath, "r");
            if ($bladeFile !== false) {
                return fread($bladeFile, filesize($this->originalPath));
            }
            fclose($bladeFile);
        }
        return "";
    }

    private function cleanBlade()
    {
//        $regex = "/\\@(for|if|foreach|endfor|endforeach|endif|elseif|else)(\\(|.*).*(\\)|.*)/";
//        $this->content = preg_replace($regex, "", $this->content);
        $this->setContent(preg_replace("/(\n|\ )+/", " ", $this->content));
    }

    private function cleanAttributes()
    {
        $htmlTagRegex = "/<[a-z,A-Z]{1,20}(.*?)[^\%\-]>/";
        $htmlTags = null;
        preg_match_all($htmlTagRegex, $this->content, $htmlTags);
        if ($htmlTags != null) {
            $htmlTags = $htmlTags[0];
        }

        foreach ($htmlTags as $htmlTag) {
            $tmpTag = $htmlTag;
            $hctAttributes = null;

            preg_match_all("/ hct-attr-.*?=[\\\"\'].*?[\\\"\']/", $htmlTag, $hctAttributes);

            if ($hctAttributes != null) {
                $hctAttributes = $hctAttributes[0];
                foreach ($hctAttributes as $hctAttribute) {
                    $correctedAttribute = str_replace("hct-attr-", "", $hctAttribute);
                    $attributeParts = explode("=", $hctAttribute);
                    if (count($attributeParts) === 2) {
                        $attributeName = $attributeParts[0];
                        $realName = trim(str_replace("hct-attr-", "", $attributeName));
                        $tmpTag = preg_replace("/ ${realName}=[\\\"\'].*?[\\\"\']/", "", $tmpTag);
                        $tmpTag = str_replace($hctAttribute, $correctedAttribute, $tmpTag);
                    }
                }
                $this->setContent(str_replace($htmlTag, $tmpTag, $this->content));
            }
        }
    }

    private function correctPaths()
    {
        $regex = "/\@(extends|include)\([\\\"\\'].*?[\\\"\\'].*?\)/";
        $matches = null;
        preg_match_all($regex, $this->content, $matches);

        if ($matches != null)
            $matches = $matches[0];

        if (count($matches) > 0) {
            foreach ($matches as $match) {
                $separator = "";
                if (strpos($match, "\"") !== false)
                    $separator = "\"";
                else if (strpos($match, "'") !== false)
                    $separator = "'";

                if ($separator !== "") {
                    $exploded = explode($separator, $match);
                    if (count($exploded) > 1) {
                        $exploded[1] = "public." . $exploded[1];
                        $result = join("\"", $exploded);
                        $this->setContent(str_replace($match, $result, $this->content));
                    }
                }
            }
        }
    }


    public function selectDirectiveTags($selectedDirective){
        $this->html = new Htmldom($this->content);
        return $this->html->find("[" . $selectedDirective . "]");
    }

    private function fetchUnsharedContents()
    {
        $contentTags = $this->selectDirectiveTags(Directives::CONTENT);
        foreach ($contentTags as $contentTag) {
            if(isset($contentTag->attr[Directives::UNSHARED]) and
                $contentTag->attr[Directives::UNSHARED] == "true") {
                $this->interpretUnsharedContent($contentTag);
                $this->setContent($this->createHtml());
            }
        }
    }

    private function interpretUnsharedContent($content_tag)
    {
        $content_id = $content_tag->attr[Directives::CONTENT];
        $content_tag->innertext = "{!! get_unshared_content(\"{$content_id}\", \$web_page) !!}" . $content_tag->innertext;
    }

    private function fetchGalleries()
    {
        $galleryTags = $this->selectDirectiveTags(Directives::GALLERY);
        foreach ($galleryTags as $galleryTag) {
            $this->interpretGallery($galleryTag);
            $this->setContent($this->createHtml());
        }
    }

    private function interpretGallery($galleryTag){
        $galleryName = $galleryTag->attr[Directives::GALLERY];
        foreach ($galleryTag->find("[" . Directives::INNER_TEXT . "]") as $innertextTag) {
            $innertextTag->innertext = $innertextTag->attr[Directives::INNER_TEXT];
            unset($innertextTag->attr[Directives::INNER_TEXT]);
        }

        if (strlen($galleryName) > 0) {
            if (!isset($galleryTag->attr[Directives::UNSHARED]) or
                (isset($galleryTag->attr[Directives::UNSHARED]) and
                    $galleryTag->attr[Directives::UNSHARED] == "false")) {
                $this->createGalleryModel($galleryTag, $galleryName);
            }

            $galleryItemTags = $galleryTag->find("[" . Directives::GALLERY_ITEM . "]");
            $galleryItemTag = count($galleryItemTags) > 0 ? $galleryItemTags[0] : null;

            if ($galleryItemTag != null) {
                $matches = null;
                preg_match_all("/{%-.*?%}/", $galleryItemTag->outertext, $matches);
                if ($matches != null) {
                    $matches = $matches[0];
                    foreach ($matches as $match) {
                        $prop = trim(str_replace("{%-", "",
                            str_replace("%}", "", $match)));
                        $explodedProp = explode(":", $prop);
                        if (count($explodedProp) == 2) {
                            $propName = $explodedProp[1];
                            $propType = $explodedProp[0];
                            if ($propType == PropertyTypes::EXTRA) {
                                if (strpos($propName, 'description') !== false) {
                                    $galleryItemTag->outertext = str_replace($match,
                                        "{!! \$galleryItem->getField('$propName')->getContent() !!}",
                                        $galleryItemTag->outertext);

                                } else {
                                    $galleryItemTag->outertext = str_replace($match,
                                        "{{ \$galleryItem->getField('$propName')->getContent() }}",
                                        $galleryItemTag->outertext);

                                }
                            } else if ($propType == PropertyTypes::PROP) {
                                $galleryItemTag->outertext = str_replace($match,
                                    "{{ \$galleryItem->$propName }}",
                                    $galleryItemTag->outertext);
                            }
                        }
                    }
                }

                $countString = key_exists(Directives::MAX_ENTRY, $galleryTag->attr) ?
                    ", " . $galleryTag->attr[Directives::MAX_ENTRY] : "";
                $randomSelect = key_exists(Directives::RANDOM_SELECT, $galleryTag->attr) ?
                    ", " . $galleryTag->attr[Directives::RANDOM_SELECT] : "";

                $underLine = "_";
                if ((isset($galleryTag->attr[Directives::UNSHARED]) and $galleryTag->attr[Directives::UNSHARED] == "true"))
                    $galleryItemTag->outertext = " @foreach(get_gallery_items(\"$galleryName$underLine\" .".'$directory->id'.
                        $countString . $randomSelect . ") as \$galleryItem) " . $galleryItemTag->outertext .
                        " @endforeach ";
                else
                    $galleryItemTag->outertext = " @foreach(get_gallery_items(\"$galleryName\"" .
                        $countString . $randomSelect . ") as \$galleryItem) " . $galleryItemTag->outertext .
                        " @endforeach ";
            }
        }
    }


    public function createGalleryModel($galleryTag, $galleryName = null){
        if ($galleryName == null)
            $galleryName = $galleryTag->attr[Directives::GALLERY];
        $galleryModel = get_gallery($galleryName);
        if ($galleryModel === false) {
            $galleryModel = new Gallery();
            $galleryModel->identifier = $galleryName;
        }

        $galleryFieldTagsContainers = $galleryTag->find("[" . Directives::GALLERY_FIELDS . "]");
        $galleryFieldTagsContainer = count($galleryFieldTagsContainers) > 0 ? $galleryFieldTagsContainers[0] : null;
        if ($galleryFieldTagsContainer != null) {

            $galleryFieldTags = $galleryFieldTagsContainer->find("[" . Directives::GALLERY_FIELD . "]");
            $galleryFields = [];

            foreach ($galleryFieldTags as $galleryFieldTag) {
                $fieldId = $galleryFieldTag->attr[Directives::GALLERY_FIELD];
                $fieldTitle = $galleryFieldTag->attr[Directives::TITLE];
                if ($fieldId != null and strlen($fieldId) != 0 and
                    $fieldTitle != null and strlen($fieldTitle) != 0) {
                    $newField = new GalleryField($fieldId, $fieldTitle);
                    $galleryFields[$fieldId] = $newField;
                }
            }

            $galleryFieldTagsContainer->outertext = "";
            $galleryModel->setGalleryFields($galleryFields);
            $galleryModel->save();

            if (key_exists(Directives::HAS_MOBILE, $galleryTag->attr)) {
                $mobileGalleryName = $galleryName . "_mobile";
                $galleryModelMobile = get_gallery($mobileGalleryName);
                if ($galleryModelMobile === false) {
                    $galleryModelMobile = new Gallery();
                    $galleryModelMobile->identifier = $mobileGalleryName;
                }
                $galleryModelMobile->setGalleryFields($galleryFields);
                $galleryModelMobile->save();
            }

        }
    }

    public function getGalleries($directoryId = null)
    {
        $this->html = new Htmldom($this->content);
        $galleryTags = $this->html->find("[" . Directives::GALLERY . "]");
        $result = [];
        foreach ($galleryTags as $galleryTag) {
            $galleryName = $galleryTag->attr[Directives::GALLERY];
            if ($directoryId != null and (isset($galleryTag->attr[Directives::UNSHARED]) and $galleryTag->attr[Directives::UNSHARED] == "true"))
                $galleryName = $galleryName."_".$directoryId;
            $hasMobile = key_exists(Directives::HAS_MOBILE, $galleryTag->attr);
            if ($galleryName != null and strlen($galleryName) > 0) {
                $gallery = Gallery::whereIdentifier($galleryName)->first();
                $title = $galleryTag->attr[Directives::TITLE];
                $title = ($title != null and strlen($title) > 0) ? $title : "no name";

                if ($gallery != null) {
                    $newGallery = new stdClass();
                    $newGallery->title = $title;
                    $newGallery->model = $gallery;
                    $newGallery->attr = $galleryTag->attr;
                    $result[] = $newGallery;
                }

                if($hasMobile){
                    $galleryName .= "_mobile";
                    $gallery = Gallery::whereIdentifier($galleryName)->first();
                    $title = ($title != null and strlen($title) > 0) ? $title : "no name";
                    $title .= " (mobile) ";

                    if ($gallery != null) {
                        $newGallery = new stdClass();
                        $newGallery->title = $title;
                        $newGallery->model = $gallery;
                        $newGallery->attr = $galleryTag->attr;
                        $result[] = $newGallery;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @throws UndefinedFormFieldIdentifierException
     */
    private function fetchForms()
    {
        $formTags = $this->selectDirectiveTags(Directives::FORM);
        foreach ($formTags as $formTag) {
            $this->interpretForm($formTag);
        }
        $this->setContent($this->createHtml());
    }

    /**
     * @param $formTag
     * @throws UndefinedFormFieldIdentifierException
     */
    private function interpretForm($formTag){
        $formName = $formTag->attr[Directives::FORM];
        if (strlen($formName) > 0) {
            $formModel = WebForm::whereIdentifier($formName)->first();

            if ($formModel == null) {
                $formModel = new WebForm();
                $formModel->identifier = $formName;
            }
            $formTag->setAttribute("action", "{{route('message-save')}}");
            $formTag->setAttribute("method", "POST");
            $formTag->setAttribute("enctype", "multipart/form-data");
            $formTag->innertext = "\n<input type='hidden' name='identifier' value='".$formName."' hct-validation=\"required\"> \n".$formTag->innertext;
            $formTag->innertext = "\n{{ csrf_field() }}".$formTag->innertext;

            $formFieldTags = $formTag->find("[" . Directives::FORM_FIELD . "]");
            $formFields = [];
            foreach ($formFieldTags as $formFieldTag) {
                $fieldName = null;
                if(key_exists("name", $formFieldTag->attr))
                    $fieldName =  $formFieldTag->attr["name"];
                if ($fieldName != null) {

                    $filedTitle = $fieldName;
                    if (key_exists("title", $formFieldTag->attr))
                        $filedTitle = $formFieldTag->attr["title"];

                    $fieldRule = null;
                    if (key_exists(Directives::FORM_VALIDATION, $formFieldTag->attr))
                        $fieldRule = $formFieldTag->attr[Directives::FORM_VALIDATION];
                    $filedValue = null;
                    if (key_exists("value", $formFieldTag->attr))
                        $filedValue = $formFieldTag->attr["value"];

                    $attributes = ['name' => $fieldName, 'title' => $filedTitle, 'rules' => $fieldRule];
                    if ($filedValue != null)
                        $attributes['value'] = $filedValue;

                    if ($formFieldTag->tag == File::getTag() and
                        key_exists("type", $formFieldTag->attr) and
                        $formFieldTag->attr['type'] == 'file')
                        $formFields[$fieldName] = new File($attributes);
                    elseif ($formFieldTag->tag == Text::getTag())
                        $formFields[$fieldName] = new Text($attributes);
                    elseif ($formFieldTag->tag == Textarea::getTag())
                        $formFields[$fieldName] = new Textarea($attributes);
                    elseif ($formFieldTag->tag == Option::getTag())
                        $formFields[$fieldName] = new Option($attributes);
                    elseif ($formFieldTag->tag == Select::getTag())
                        $formFields[$fieldName] = new Option($attributes); //TODO: set $attributes['options']

                    if (strlen($fieldName) > 0) {
                        $label = $formTag->find("label[for='" . $fieldName . "']");
                        $label = count($label) > 0 ? $label[0] : $fieldName;
                        $label = str_replace("*", "", strip_tags($label->innertext));

                        echo $label . " - > " . $formFieldTag->attr["name"] . " -> "
                            . $formFieldTag->tag . " -> " . $formFieldTag->attr[Directives::FORM_VALIDATION]
                            . "<br/>\n";
                    }
                }else
                    throw new UndefinedFormFieldIdentifierException();
            }
            $formModel->setFormFields($formFields);
            $formModel->save();
        }
    }

    public function saveTemplate()
    {
        $bladeFile = fopen($this->bladePath, "w");
        if ($bladeFile !== false) {
            fwrite($bladeFile, $this->content);
        }
        fclose($bladeFile);
    }

    /**
     * @throws UndefinedFormFieldIdentifierException
     */
    public function initialize()
    {
        $this->cleanBlade();
        $this->correctPaths();
        $this->cleanAttributes();
        $this->fetchUnsharedContents();
        $this->fetchGalleries();
        $this->fetchForms();
        $this->saveTemplate();
    }

    public function savePublic($viewPath)
    {
        $bladeFile = fopen($viewPath, "w");
        if ($bladeFile !== false) {
            fwrite($bladeFile, $this->content);
        }
        fclose($bladeFile);
    }

}