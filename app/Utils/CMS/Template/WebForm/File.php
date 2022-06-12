<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/19/17
 * Time: 7:43 PM
 */

namespace App\Utils\CMS\Template\WebForm;


use App\Utils\CMS\File\FileBadDestinationException;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;

class File extends FormField
{

    use Inputable;

    const FILE_UPLOAD_PATH = '/uploads/file-uploads/';

    public static function getTag()
    {
        return 'input';
    }

    /**
     * @param $request
     * @return string
     * @throws FileBadDestinationException
     */
    public function getValue(Request $request)
    {
        if ($request->hasFile($this->getIdentifier())) {
            try {
                $fileName = $request->file($this->getIdentifier())->getClientOriginalName();
                $fileName = str_replace(' ', '-', $fileName);
                $destinationPath = static::FILE_UPLOAD_PATH . (string)microtime(true) . Str::random(10);
                $request->file($this->getIdentifier())->move(public_path($destinationPath), $fileName);
                $mainFilePath = $destinationPath . '/' . $fileName;
                return $mainFilePath;
            } catch (\Exception $e) {
                Log::error('Something Went Wrong Related Message ' . $e->getMessage());
            }
        } else {
            throw new FileBadDestinationException("File NotFound");
        }
    }
}
