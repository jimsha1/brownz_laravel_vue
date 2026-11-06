<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use App\Models\ServiceCategory as Category;

class ServiceCategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\ServiceCategoryHunter
     */
    public function hunter()
    {
        return new \App\Hunters\ServiceCategoryHunter($this->model());
    }

    /**
    * $target 'banner_image' | 'square_image'
    */
    public function uploadCategoryImage(Category $service, $file, $target)
    {
        
        // Copy existing image name
        $existing_photo = $service->$target;

        if (!$file->isValid()) {
            return false;
        }
        $name = time().'.'.$file->getClientOriginalExtension();
    
        $destinationPath = public_path('services');
        
        $path = $file->store('public/' . Category::CATEGORY_IMAGE_UPLOAD_PATH);
        
       
        $array = explode("services/",$path);
        
        $file->move($destinationPath, $array[1]);
        $array = explode("public/",$path);
        $path = $array[1];
        $service->$target = $path;
        
        $service->save();

        // Delete disk image of existing banner
        $this->deleteDiskImage($existing_photo);

        return true;
    }

    public function deleteDiskImage($filename)
    {
        if (!empty($filename) && file_exists('storage/' . Category::CATEGORY_IMAGE_UPLOAD_PATH . '/' . $filename)) {
            unlink('storage/' . Category::CATEGORY_IMAGE_UPLOAD_PATH . '/' . $filename);
        }
    }
}
