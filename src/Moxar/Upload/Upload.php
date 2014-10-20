<?php namespace Moxar\Upload;

use \Illuminate\Support\Facades\Config as Config;
use \Illuminate\Database\Eloquent\Model as Eloquent;
use \Illuminate\Support\Facades\File as File;
use \Illuminate\Support\Facades\Input as Input;

class Upload {
    
    public $allowedExtensions;
    public $sizeMax;
    public $path;
    
    public function into($config) {
        if(!is_array(Config::get("upload.".$config)) || empty(Config::get("upload.".$config))) {
            throw(new \Exception("config uploads.".$config." does not exist."));
        }
        foreach(Config::get("upload.".$config) as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }
    
    public function save($field, Eloquent $model) {
        $input = Input::file($field);
        if(!Input::hasFile($field)) return false;
        if(!$input->isValid()) throw(new \Exception("The uploaded file ".$field." is not valid."));
        if(!in_array($input->guessExtension(), $this->allowedExtensions)) throw(new \Exception("The uploaded file ".$field." has a forbidden extension."));
        if($input->getClientSize() > $this->sizeMax) throw(new \Exception("The uploaded file ".$field." is to big."));
        $this->delete($model->$field);
        $input = Input::file($field);
        $model->$field = $this->path.strtolower(substr($input->getRealPath(), -6))."-".$model->id.".".$input->guessExtension();
        $model->save();
        $input->move(public_path()."/".$this->path, $model->$field);
        return true;
    }
    
    public function delete($file) {
        if(File::exists(public_path().$file)) {
            File::delete(public_path().$file);
        }
    }
}
