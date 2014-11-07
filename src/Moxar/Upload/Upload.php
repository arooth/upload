<?php namespace Moxar\Upload;

use \Illuminate\Support\Facades\Config;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Facades\File;
use \Illuminate\Support\Facades\Input;

class Upload {

    public $path;
    
    public function into($config) {
        if(!is_array(Config::get("upload::config.".$config)) || empty(Config::get("upload::config.".$config))) {
            throw(new \Exception("config uploads.".$config." does not exist."));
        }
        $this->path = Config::get("upload::config.".$config);
        return $this;
    }
    
    public function save($field, Model $model) {
        $input = Input::file($field);
        if(!Input::hasFile($field)) return false;
        if(!$input->isValid()) throw(new \Exception("The uploaded file ".$field." is not valid."));
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
